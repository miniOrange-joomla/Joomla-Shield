<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class JoomShieldSiteProtection
{
	public static function tableExists()
	{
		try
		{
			$db = Factory::getDbo();
			$tables = $db->getTableList();
			$name = $db->replacePrefix('#__miniorange_jnsp_site_protection');

			return in_array($name, $tables, true);
		}
		catch (\Throwable $e)
		{
			return false;
		}
	}

	public static function getConfig()
	{
		$defaults = [
			'id' => 1,
			'emergency_offline' => 0,
			'offline_whitelist_ips' => '',
			'admin_http_auth' => 0,
			'admin_http_user' => '',
			'admin_http_hash' => '',
			'admin_http_whitelist_ips' => '',
			'feature_lock_enabled' => 0,
			'feature_lock_hash' => '',
			'feature_lock_items' => '',
			'server_rules_apache' => 0,
			'link_migration_live' => 0,
			'link_migration_old_hosts' => '',
		];

		if (!self::tableExists())
		{
			return $defaults;
		}

		self::ensureSchema();

		try
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select('*')
				->from($db->quoteName('#__miniorange_jnsp_site_protection'))
				->where($db->quoteName('id') . ' = 1');
			$db->setQuery($query);
			$row = $db->loadAssoc();

			if (!is_array($row))
			{
				return $defaults;
			}

			return array_merge($defaults, $row);
		}
		catch (\Throwable $e)
		{
			return $defaults;
		}
	}

	public static function saveConfig($values)
	{
		if (!self::tableExists())
		{
			return false;
		}

		self::ensureSchema();

		$db = Factory::getDbo();
		$fields = [];

		foreach ($values as $key => $value)
		{
			$fields[] = $db->quoteName($key) . ' = ' . $db->quote($value);
		}

		if (empty($fields))
		{
			return false;
		}

		$query = $db->getQuery(true)
			->update($db->quoteName('#__miniorange_jnsp_site_protection'))
			->set($fields)
			->where($db->quoteName('id') . ' = 1');
		$db->setQuery($query);
		$db->execute();

		return true;
	}

	public static function getLockedItems()
	{
		$config = self::getConfig();
		$raw = trim((string) ($config['feature_lock_items'] ?? ''));

		if ($raw === '')
		{
			return JoomShieldFeatureLock::defaultItems();
		}

		$decoded = json_decode($raw, true);

		if (!is_array($decoded))
		{
			return JoomShieldFeatureLock::defaultItems();
		}

		return JoomShieldFeatureLock::filterItems($decoded);
	}

	private static function ensureSchema()
	{
		static $done = false;

		if ($done)
		{
			return;
		}

		$done = true;

		try
		{
			$db = Factory::getDbo();
			$fields = $db->getTableColumns('#__miniorange_jnsp_site_protection');
			$needed = [
				'link_migration_live' => 'tinyint(1) DEFAULT 0',
				'link_migration_old_hosts' => 'text',
			];

			foreach ($needed as $column => $definition)
			{
				if (isset($fields[$column]))
				{
					continue;
				}

				$db->setQuery(
					'ALTER TABLE ' . $db->quoteName('#__miniorange_jnsp_site_protection')
					. ' ADD ' . $db->quoteName($column) . ' ' . $definition
				);
				$db->execute();
			}
		}
		catch (\Throwable $e)
		{
			return;
		}
	}
}
