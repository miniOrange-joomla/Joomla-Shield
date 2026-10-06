<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class JoomShieldFeatureLock
{
	public const SESSION_KEY = 'joomshield_feature_lock_ok';
	public const SESSION_TIME_KEY = 'joomshield_feature_lock_time';
	public const TTL_SECONDS = 900;

	public static function items()
	{
		return [
			'custom_admin_url' => 'COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL',
			'login_password_policy' => 'COM_JOOMSHIELD_LOGIN_PASSWORD_POLICY',
			'admin_http_auth' => 'COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION',
			'temp_admin' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS',
			'registration_domains' => 'COM_JOOMSHIELD_REGISTRATION_DOMAIN_POLICY',
			'registration_password_policy' => 'COM_JOOMSHIELD_REGISTRATION_PASSWORD_POLICY',
			'browser_blocking' => 'COM_JOOMSHIELD_BROWSER_BLOCKING',
			'ip_lookup' => 'COM_JOOMSHIELD_IP_LOOKUP',
			'clear_reports' => 'COM_JOOMSHIELD_CLEAR_REPORTS',
			'import_configuration' => 'COM_JOOMSHIELD_IMPORT_CONFIGURATIONS',
			'emergency_offline' => 'COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE',
			'clear_tmp' => 'COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES',
			'purge_sessions' => 'COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS',
			'repair_db' => 'COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE',
			'link_migration' => 'COM_JOOMSHIELD_STALE_URL_REWRITE',
		];
	}

	public static function defaultItems()
	{
		return array_keys(self::items());
	}

	public static function filterItems($items)
	{
		if (!is_array($items))
		{
			return [];
		}

		return array_values(array_intersect(array_keys(self::items()), array_map('strval', $items)));
	}

	public static function isEnabled()
	{
		$config = JoomShieldSiteProtection::getConfig();

		return ((int) ($config['feature_lock_enabled'] ?? 0) === 1)
			&& trim((string) ($config['feature_lock_hash'] ?? '')) !== '';
	}

	public static function isItemLocked($featureKey)
	{
		if (!self::isEnabled())
		{
			return false;
		}

		$items = JoomShieldSiteProtection::getLockedItems();

		return in_array($featureKey, $items, true);
	}

	public static function isUnlocked()
	{
		if (!self::isEnabled())
		{
			return true;
		}

		$session = Factory::getSession();
		$flag = (int) $session->get(self::SESSION_KEY, 0);
		$time = (int) $session->get(self::SESSION_TIME_KEY, 0);

		if ($flag !== 1 || $time <= 0)
		{
			return false;
		}

		if ((time() - $time) >= self::TTL_SECONDS)
		{
			self::lockSession();

			return false;
		}

		return true;
	}

	public static function getExpiresAt()
	{
		if (!self::isUnlocked())
		{
			return 0;
		}

		return (int) Factory::getSession()->get(self::SESSION_TIME_KEY, 0) + self::TTL_SECONDS;
	}

	public static function unlock($password)
	{
		$config = JoomShieldSiteProtection::getConfig();
		$hash = (string) ($config['feature_lock_hash'] ?? '');

		if ($hash === '' || !password_verify((string) $password, $hash))
		{
			self::logFailure();

			return false;
		}

		$session = Factory::getSession();
		$session->set(self::SESSION_KEY, 1);
		$session->set(self::SESSION_TIME_KEY, time());

		return true;
	}

	public static function lockSession()
	{
		$session = Factory::getSession();
		$session->set(self::SESSION_KEY, 0);
		$session->set(self::SESSION_TIME_KEY, 0);
	}

	public static function assertAllowed($featureKey)
	{
		if (class_exists('JoomShieldTempAdmin') && JoomShieldTempAdmin::isCurrentUserTemporary())
		{
			return Text::_('COM_JOOMSHIELD_TEMP_ADMIN_COMPONENT_DENIED');
		}

		if (!JoomShieldCompat::isSuperUser())
		{
			return Text::_('JERROR_ALERTNOAUTHOR');
		}

		if (!self::isItemLocked($featureKey))
		{
			return '';
		}

		if (self::isUnlocked())
		{
			return '';
		}

		return Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_REQUIRED');
	}

	private static function logFailure()
	{
		try
		{
			$user = JoomShieldCompat::getUser();
			$username = $user->username ?? '';
			$ip = JoomShieldUtilities::getClientIp();
			JoomShieldUtilities::addTransactionDetails($ip, $username, 'Feature Access', 'failed', JoomShieldUtilities::getLoginRequestUrl());
			JoomShieldUtilities::addLoginTransactionDetails(
				$ip,
				$username,
				'Feature Access',
				'failed',
				'Admin Login Page',
				JoomShieldUtilities::getCountryName($ip),
				JoomShieldUtilities::getCurrentUserBrowser(),
				JoomShieldUtilities::getOsInfo(),
				JoomShieldUtilities::getLoginRequestUrl(),
				'',
				'Invalid feature access password'
			);
		}
		catch (\Throwable $e)
		{
			return;
		}
	}
}
