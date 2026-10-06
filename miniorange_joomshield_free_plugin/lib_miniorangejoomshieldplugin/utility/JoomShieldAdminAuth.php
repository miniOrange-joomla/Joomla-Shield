<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class JoomShieldAdminAuth
{
	public const SESSION_KEY = 'joomshield_admin_basic_ok';
	public const REALM = 'JoomShield Administrator Access Protection';

	public static function enforce()
	{
		if (JoomShieldCompat::isCli())
		{
			return;
		}

		if (!JoomShieldCompat::isAdministrator())
		{
			return;
		}

		$config = JoomShieldSiteProtection::getConfig();

		if ((int) ($config['admin_http_auth'] ?? 0) !== 1)
		{
			return;
		}

		$userName = trim((string) ($config['admin_http_user'] ?? ''));
		$hash = (string) ($config['admin_http_hash'] ?? '');

		if ($userName === '' || $hash === '')
		{
			return;
		}

		$ip = JoomShieldUtilities::getClientIp();
		$whitelist = JoomShieldCompat::parseIpList($config['admin_http_whitelist_ips'] ?? '');

		if (JoomShieldCompat::ipInList($ip, $whitelist))
		{
			return;
		}

		$session = Factory::getSession();

		if ((int) $session->get(self::SESSION_KEY, 0) === 1)
		{
			return;
		}

		if (self::isServerAuthenticated($userName))
		{
			$session->set(self::SESSION_KEY, 1);

			return;
		}

		$credentials = self::getBasicCredentials();

		if ($credentials !== null
			&& self::stringEquals($userName, $credentials['username'])
			&& password_verify($credentials['password'], $hash)
		)
		{
			$session->set(self::SESSION_KEY, 1);

			return;
		}

		self::sendUnauthorized();
	}

	public static function getBasicCredentials()
	{
		$username = (string) ($_SERVER['PHP_AUTH_USER'] ?? '');
		$password = (string) ($_SERVER['PHP_AUTH_PW'] ?? '');

		if ($username === '' && $password === '')
		{
			$header = self::getAuthorizationHeader();

			if (stripos($header, 'basic ') === 0)
			{
				$decoded = base64_decode(substr($header, 6), true);

				if (is_string($decoded) && strpos($decoded, ':') !== false)
				{
					[$username, $password] = explode(':', $decoded, 2);
				}
			}
		}

		if ($username === '' && $password === '')
		{
			return null;
		}

		return [
			'username' => $username,
			'password' => $password,
		];
	}

	private static function isServerAuthenticated($userName)
	{
		foreach (['REMOTE_USER', 'REDIRECT_REMOTE_USER'] as $key)
		{
			$remoteUser = trim((string) ($_SERVER[$key] ?? ''));

			if ($remoteUser !== '' && self::stringEquals($userName, $remoteUser))
			{
				return true;
			}
		}

		return false;
	}

	private static function getAuthorizationHeader()
	{
		foreach (['HTTP_AUTHORIZATION', 'REDIRECT_HTTP_AUTHORIZATION', 'Authorization'] as $key)
		{
			$value = $_SERVER[$key] ?? '';

			if (is_string($value) && $value !== '')
			{
				return $value;
			}
		}

		foreach (['apache_request_headers', 'getallheaders'] as $reader)
		{
			if (!function_exists($reader))
			{
				continue;
			}

			$headers = $reader();

			if (!is_array($headers))
			{
				continue;
			}

			foreach ($headers as $name => $value)
			{
				if (strtolower((string) $name) === 'authorization')
				{
					return (string) $value;
				}
			}
		}

		return '';
	}

	private static function stringEquals($expected, $actual)
	{
		$expected = (string) $expected;
		$actual = (string) $actual;

		if (strlen($expected) !== strlen($actual))
		{
			return false;
		}

		return hash_equals($expected, $actual);
	}

	private static function sendUnauthorized()
	{
		if (!headers_sent())
		{
			http_response_code(401);
			header('WWW-Authenticate: Basic realm="' . self::REALM . '"');
			header('Cache-Control: no-store');
		}

		echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Authorization Required</title></head>';
		echo '<body><h1>Authorization Required</h1>';
		echo '<p>Access to the Joomla administrator area is protected by JoomShield.</p></body></html>';
		exit;
	}
}
