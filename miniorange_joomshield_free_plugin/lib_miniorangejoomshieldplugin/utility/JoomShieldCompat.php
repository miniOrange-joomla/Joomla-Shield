<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

class JoomShieldCompat
{
	public static function getApplication()
	{
		return Factory::getApplication();
	}

	public static function getUser()
	{
		$app = self::getApplication();

		if (method_exists($app, 'getIdentity'))
		{
			$user = $app->getIdentity();

			if (!empty($user))
			{
				return $user;
			}
		}

		return Factory::getUser();
	}

	public static function isAdministrator()
	{
		$app = self::getApplication();

		if (method_exists($app, 'isClient'))
		{
			return (bool) $app->isClient('administrator');
		}

		if (method_exists($app, 'isAdmin'))
		{
			return (bool) $app->isAdmin();
		}

		return false;
	}

	public static function isCli()
	{
		$app = self::getApplication();

		if (method_exists($app, 'isClient'))
		{
			return (bool) $app->isClient('cli');
		}

		return (PHP_SAPI === 'cli');
	}

	public static function checkToken($method = 'post')
	{
		if (class_exists(Session::class) && method_exists(Session::class, 'checkToken'))
		{
			return Session::checkToken($method);
		}

		if (class_exists('JSession') && method_exists('JSession', 'checkToken'))
		{
			return \JSession::checkToken($method);
		}

		return true;
	}

	public static function isSuperUser($user = null)
	{
		if ($user === null)
		{
			$user = self::getUser();
		}

		if (empty($user) || empty($user->id))
		{
			return false;
		}

		return (bool) $user->authorise('core.admin');
	}

	public static function parseIpList($value)
	{
		$parts = preg_split('/[\s,;]+/', (string) $value);
		$ips = [];

		foreach ($parts as $part)
		{
			$part = self::canonicalIp($part);

			if ($part !== '')
			{
				$ips[] = $part;
			}
		}

		return array_values(array_unique($ips));
	}

	public static function canonicalIp($value)
	{
		$value = trim((string) $value, " \t\"'");

		if ($value === '')
		{
			return '';
		}

		// Drop an IPv4 port. An IPv6 address contains more than one colon.
		if (strpos($value, '.') !== false && substr_count($value, ':') === 1)
		{
			$value = strstr($value, ':', true);
		}

		if (filter_var($value, FILTER_VALIDATE_IP) === false)
		{
			return '';
		}

		$packed = @inet_pton($value);

		if ($packed === false)
		{
			return $value;
		}

		$normalized = inet_ntop($packed);

		return $normalized === false ? $value : $normalized;
	}

	public static function isTrustedProxy($ip)
	{
		$ip = self::canonicalIp($ip);

		if ($ip === '')
		{
			return false;
		}

		// Private and reserved ranges are the proxy hop, not the visitor.
		return filter_var(
			$ip,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
		) === false;
	}

	public static function ipInList($ip, $list)
	{
		$ip = self::canonicalIp($ip);

		if ($ip === '')
		{
			return false;
		}

		foreach ((array) $list as $allowed)
		{
			if ($ip === self::canonicalIp($allowed))
			{
				return true;
			}
		}

		return false;
	}
}
