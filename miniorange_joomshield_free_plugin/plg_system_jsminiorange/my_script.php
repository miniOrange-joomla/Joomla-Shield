<?php

/**
 * Script file of miniorange_joomshield_plugin.
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Factory;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class PlgSystemJsminiorangeInstallerScript
{
	public function install($parent)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('element') . ' = ' . $db->quote('jsminiorange'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();

		$this->ensureJoomShieldSchema($db);
	}

	public function uninstall($parent)
	{
		$db = Factory::getDbo();
		$this->cleanupServerRules();
		$this->expireTemporaryUsers($db);
	}

	public function update($parent)
	{
		$this->ensureJoomShieldSchema(Factory::getDbo());
	}

	public function preflight($type, $parent)
	{
	}

	public function postflight($type, $parent)
	{
		if ($type === 'install' || $type === 'update')
		{
			$this->ensureJoomShieldSchema(Factory::getDbo());
		}
	}

	private function ensureJoomShieldSchema($db)
	{
		$db->setQuery(
			"CREATE TABLE IF NOT EXISTS `#__miniorange_jnsp_site_protection` (
				`id` int(11) UNSIGNED NOT NULL,
				`emergency_offline` tinyint(1) DEFAULT 0,
				`offline_whitelist_ips` text,
				`admin_http_auth` tinyint(1) DEFAULT 0,
				`admin_http_user` VARCHAR(255) DEFAULT '',
				`admin_http_hash` VARCHAR(255) DEFAULT '',
				`admin_http_whitelist_ips` text,
				`feature_lock_enabled` tinyint(1) DEFAULT 0,
				`feature_lock_hash` VARCHAR(255) DEFAULT '',
				`feature_lock_items` text,
				`server_rules_apache` tinyint(1) DEFAULT 0,
				`link_migration_live` tinyint(1) DEFAULT 0,
				`link_migration_old_hosts` text,
				PRIMARY KEY(`id`)
			) DEFAULT COLLATE=utf8_general_ci"
		);
		$db->execute();

		$db->setQuery(
			"CREATE TABLE IF NOT EXISTS `#__miniorange_jnsp_temp_users` (
				`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`user_id` int(11) UNSIGNED NOT NULL,
				`created_by` int(11) UNSIGNED NOT NULL DEFAULT 0,
				`expires_at` datetime NOT NULL,
				`status` VARCHAR(32) NOT NULL DEFAULT 'active',
				`created_at` datetime NOT NULL,
				PRIMARY KEY (`id`)
			) DEFAULT COLLATE=utf8_general_ci"
		);
		$db->execute();

		$columns = [
			'emergency_offline' => 'tinyint(1) DEFAULT 0',
			'offline_whitelist_ips' => 'text',
			'admin_http_auth' => 'tinyint(1) DEFAULT 0',
			'admin_http_user' => "VARCHAR(255) DEFAULT ''",
			'admin_http_hash' => "VARCHAR(255) DEFAULT ''",
			'admin_http_whitelist_ips' => 'text',
			'feature_lock_enabled' => 'tinyint(1) DEFAULT 0',
			'feature_lock_hash' => "VARCHAR(255) DEFAULT ''",
			'feature_lock_items' => 'text',
			'server_rules_apache' => 'tinyint(1) DEFAULT 0',
			'link_migration_live' => 'tinyint(1) DEFAULT 0',
			'link_migration_old_hosts' => 'text',
		];

		foreach ($columns as $column => $definition)
		{
			$this->ensureColumn($db, '#__miniorange_jnsp_site_protection', $column, $definition);
		}

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__miniorange_jnsp_site_protection'))
			->where($db->quoteName('id') . ' = 1');
		$db->setQuery($query);

		if ((int) $db->loadResult() === 0)
		{
			$insert = $db->getQuery(true)
				->insert($db->quoteName('#__miniorange_jnsp_site_protection'))
				->columns($db->quoteName('id'))
				->values('1');
			$db->setQuery($insert);
			$db->execute();
		}
	}

	private function ensureColumn($db, $table, $column, $definition)
	{
		try
		{
			$fields = $db->getTableColumns($table);
		}
		catch (\Throwable $e)
		{
			return;
		}

		if (isset($fields[$column]))
		{
			return;
		}

		$db->setQuery(
			'ALTER TABLE ' . $db->quoteName($table)
			. ' ADD ' . $db->quoteName($column) . ' ' . $definition
		);
		$db->execute();
	}

	private function cleanupServerRules()
	{
		$htaccess = JPATH_ROOT . DIRECTORY_SEPARATOR . '.htaccess';

		if (is_file($htaccess) && is_readable($htaccess) && is_writable($htaccess))
		{
			$contents = (string) file_get_contents($htaccess);
			$pattern = '/# BEGIN miniOrange JoomShield.*?# END miniOrange JoomShield\s*/s';
			$updated = preg_replace($pattern, '', $contents);

			if ($updated !== null && $updated !== $contents)
			{
				file_put_contents($htaccess, $updated);
			}
		}

		$passwd = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . '.joomshield_passwd';

		if (is_file($passwd))
		{
			@unlink($passwd);
		}
	}

	private function expireTemporaryUsers($db)
	{
		try
		{
			$tables = $db->getTableList();
			$tempTable = $db->replacePrefix('#__miniorange_jnsp_temp_users');

			if (!in_array($tempTable, $tables, true))
			{
				return;
			}

			$query = $db->getQuery(true)
				->select($db->quoteName('user_id'))
				->from($db->quoteName('#__miniorange_jnsp_temp_users'))
				->where($db->quoteName('status') . ' = ' . $db->quote('active'));
			$db->setQuery($query);
			$userIds = $db->loadColumn();

			if (!empty($userIds))
			{
				$updateUsers = $db->getQuery(true)
					->update($db->quoteName('#__users'))
					->set($db->quoteName('block') . ' = 1')
					->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $userIds)) . ')');
				$db->setQuery($updateUsers);
				$db->execute();
			}

			$updateTemp = $db->getQuery(true)
				->update($db->quoteName('#__miniorange_jnsp_temp_users'))
				->set($db->quoteName('status') . ' = ' . $db->quote('revoked'))
				->where($db->quoteName('status') . ' = ' . $db->quote('active'));
			$db->setQuery($updateTemp);
			$db->execute();
		}
		catch (\Throwable $e)
		{
			return;
		}
	}
}
