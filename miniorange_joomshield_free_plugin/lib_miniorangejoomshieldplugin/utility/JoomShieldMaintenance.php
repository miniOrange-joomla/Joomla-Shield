<?php

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class JoomShieldMaintenance
{
	public static function clearTemporaryFiles()
	{
		$config = Factory::getConfig();
		$tmpPath = $config->get('tmp_path');

		if (empty($tmpPath) || !is_dir($tmpPath))
		{
			$tmpPath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp';
		}

		$keep = ['index.html', '.htaccess', 'web.config'];
		$deleted = 0;
		$skipped = 0;

		self::deleteDirectoryContents($tmpPath, $keep, $deleted, $skipped);

		return [
			'deleted' => $deleted,
			'skipped' => $skipped,
			'path' => $tmpPath,
		];
	}

	public static function purgeActiveSessions()
	{
		$config = Factory::getConfig();
		$handler = strtolower((string) $config->get('session_handler', 'database'));
		$session = Factory::getSession();
		$currentId = method_exists($session, 'getId') ? (string) $session->getId() : '';
		$result = [
			'handler' => $handler,
			'purged' => 0,
			'warning' => '',
		];

		if ($handler === 'database' || $handler === '')
		{
			$result['purged'] = self::purgeDatabaseSessions($currentId);

			return $result;
		}

		if ($handler === 'filesystem' || $handler === 'none')
		{
			$sessionPath = $config->get('session_path', $config->get('tmp_path'));

			if (empty($sessionPath) || !is_dir($sessionPath))
			{
				$sessionPath = session_save_path();
			}

			$result['purged'] = self::purgeFilesystemSessions($sessionPath, $currentId);

			return $result;
		}

		$result['warning'] = 'COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS_HANDLER_UNSUPPORTED';

		return $result;
	}

	public static function repairAndOptimize()
	{
		$db = Factory::getDbo();
		$prefix = $db->getPrefix();
		$db->setQuery('SHOW TABLE STATUS');
		$tables = $db->loadAssocList();
		$results = [];

		if (!is_array($tables))
		{
			return $results;
		}

		$batch = 0;

		foreach ($tables as $table)
		{
			$name = $table['Name'] ?? '';
			$engine = strtoupper((string) ($table['Engine'] ?? ''));

			if ($name === '' || strpos($name, $prefix) !== 0)
			{
				continue;
			}

			$batch++;

			if ($batch % 10 === 0)
			{
				if (function_exists('set_time_limit'))
				{
					@set_time_limit(60);
				}
			}

			$row = [
				'name' => $name,
				'engine' => $engine,
				'repair' => 'n/a',
				'optimize' => '',
			];

			$quoted = $db->quoteName($name);

			if (in_array($engine, ['MYISAM', 'ARCHIVE', 'CSV'], true))
			{
				try
				{
					$db->setQuery('REPAIR TABLE ' . $quoted);
					$repair = $db->loadAssoc();
					$row['repair'] = strtolower((string) ($repair['Msg_text'] ?? 'ok'));
				}
				catch (\Throwable $e)
				{
					$row['repair'] = 'error';
				}
			}

			try
			{
				$db->setQuery('OPTIMIZE TABLE ' . $quoted);
				$optimize = $db->loadAssoc();
				$row['optimize'] = strtolower((string) ($optimize['Msg_text'] ?? 'ok'));
			}
			catch (\Throwable $e)
			{
				$row['optimize'] = 'error';
			}

			$results[] = $row;
		}

		return $results;
	}

	private static function purgeDatabaseSessions($currentId)
	{
		$db = Factory::getDbo();
		$columns = $db->getTableColumns('#__session');
		$sessionColumn = isset($columns['session_id']) ? 'session_id' : (isset($columns['session_id']) ? 'session_id' : '');

		if (isset($columns['session_id']))
		{
			$sessionColumn = 'session_id';
		}
		elseif (isset($columns['userid']))
		{
			$sessionColumn = '';
		}

		$query = $db->getQuery(true)->delete($db->quoteName('#__session'));

		if ($sessionColumn !== '' && $currentId !== '')
		{
			$query->where($db->quoteName($sessionColumn) . ' != ' . $db->quote($currentId));
		}

		$db->setQuery($query);
		$db->execute();

		return $db->getAffectedRows();
	}

	private static function purgeFilesystemSessions($path, $currentId)
	{
		$purged = 0;

		if ($path === '' || !is_dir($path))
		{
			return 0;
		}

		$files = @scandir($path);

		if (!is_array($files))
		{
			return 0;
		}

		foreach ($files as $file)
		{
			if ($file === '.' || $file === '..')
			{
				continue;
			}

			$full = $path . DIRECTORY_SEPARATOR . $file;

			if (!is_file($full))
			{
				continue;
			}

			if ($currentId !== '' && strpos($file, $currentId) !== false)
			{
				continue;
			}

			if (@unlink($full))
			{
				$purged++;
			}
		}

		return $purged;
	}

	private static function deleteDirectoryContents($directory, $keep, &$deleted, &$skipped)
	{
		$entries = @scandir($directory);

		if (!is_array($entries))
		{
			return;
		}

		foreach ($entries as $entry)
		{
			if ($entry === '.' || $entry === '..')
			{
				continue;
			}

			$full = $directory . DIRECTORY_SEPARATOR . $entry;

			if (in_array($entry, $keep, true) && is_file($full))
			{
				continue;
			}

			if (is_dir($full))
			{
				self::deleteDirectoryContents($full, $keep, $deleted, $skipped);

				if (@rmdir($full))
				{
					$deleted++;
				}
				else
				{
					$skipped++;
				}

				continue;
			}

			if (@unlink($full))
			{
				$deleted++;
			}
			else
			{
				$skipped++;
			}
		}
	}
}

