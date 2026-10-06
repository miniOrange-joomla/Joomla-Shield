<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;

defined('_JEXEC') or die;

class JoomShieldTempAdmin
{
	public static function tableExists()
	{
		try
		{
			$db = Factory::getDbo();
			$tables = $db->getTableList();
			$name = $db->replacePrefix('#__miniorange_jnsp_temp_users');

			return in_array($name, $tables, true);
		}
		catch (\Throwable $e)
		{
			return false;
		}
	}

	public static function isCurrentUserTemporary()
	{
		$user = JoomShieldCompat::getUser();

		if (empty($user) || empty($user->id))
		{
			return false;
		}

		return self::isTemporaryUserId((int) $user->id);
	}

	public static function isTemporaryUserId($userId)
	{
		if (!self::tableExists() || $userId <= 0)
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__miniorange_jnsp_temp_users'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);
		$db->setQuery($query);

		return (int) $db->loadResult() > 0;
	}

	public static function getActiveRecord($userId)
	{
		if (!self::tableExists() || $userId <= 0)
		{
			return null;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__miniorange_jnsp_temp_users'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId)
			->where($db->quoteName('status') . ' = ' . $db->quote('active'));
		$db->setQuery($query);

		return $db->loadAssoc();
	}

	public static function expireDueUsers()
	{
		if (!self::tableExists())
		{
			return;
		}

		$db = Factory::getDbo();
		$now = gmdate('Y-m-d H:i:s');
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__miniorange_jnsp_temp_users'))
			->where($db->quoteName('status') . ' = ' . $db->quote('active'))
			->where($db->quoteName('expires_at') . ' <= ' . $db->quote($now));
		$db->setQuery($query);
		$rows = $db->loadAssocList();

		if (empty($rows))
		{
			return;
		}

		foreach ($rows as $row)
		{
			self::deactivateUser((int) $row['user_id'], 'expired');
		}
	}

	public static function deactivateUser($userId, $status = 'revoked', $requireActive = false)
	{
		if ($userId <= 0)
		{
			return false;
		}

		if ($requireActive && self::getActiveRecord($userId) === null)
		{
			return false;
		}

		$db = Factory::getDbo();
		$updateUser = $db->getQuery(true)
			->update($db->quoteName('#__users'))
			->set($db->quoteName('block') . ' = 1')
			->where($db->quoteName('id') . ' = ' . (int) $userId);
		$db->setQuery($updateUser);
		$db->execute();

		$mapDelete = $db->getQuery(true)
			->delete($db->quoteName('#__user_usergroup_map'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);
		$db->setQuery($mapDelete);
		$db->execute();

		$registeredId = self::getRegisteredGroupId();

		if ($registeredId > 0)
		{
			$insert = $db->getQuery(true)
				->insert($db->quoteName('#__user_usergroup_map'))
				->columns([$db->quoteName('user_id'), $db->quoteName('group_id')])
				->values((int) $userId . ',' . (int) $registeredId);
			$db->setQuery($insert);
			$db->execute();
		}

		if (self::tableExists())
		{
			$updateTemp = $db->getQuery(true)
				->update($db->quoteName('#__miniorange_jnsp_temp_users'))
				->set($db->quoteName('status') . ' = ' . $db->quote($status))
				->where($db->quoteName('user_id') . ' = ' . (int) $userId);
			$db->setQuery($updateTemp);
			$db->execute();
		}

		self::purgeUserSessions($userId);

		return true;
	}

	public static function createUser($username, $email, $password, $hours, $createdBy)
	{
		$username = trim((string) $username);
		$email = trim((string) $email);
		$password = (string) $password;
		$hours = (int) $hours;

		$allowedHours = [1, 4, 8, 24, 72, 168];

		if ($username === '' || $email === '' || $password === '' || !in_array($hours, $allowedHours, true))
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_INVALID'];
		}

		if (UserHelper::getUserId($username))
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_USERNAME_EXISTS'];
		}

		$superGroupId = self::getSuperUserGroupId();

		if ($superGroupId < 1)
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_NO_GROUP'];
		}

		$user = new User;
		$data = [
			'name' => $username,
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'password2' => $password,
			'groups' => [$superGroupId],
			'block' => 0,
		];

		if (!$user->bind($data) || !$user->save())
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_CREATE_FAILED'];
		}

		$expiresAt = gmdate('Y-m-d H:i:s', time() + ($hours * 3600));
		$createdAt = gmdate('Y-m-d H:i:s');

		try
		{
			$db = Factory::getDbo();
			$insert = $db->getQuery(true)
				->insert($db->quoteName('#__miniorange_jnsp_temp_users'))
				->columns([
					$db->quoteName('user_id'),
					$db->quoteName('created_by'),
					$db->quoteName('expires_at'),
					$db->quoteName('status'),
					$db->quoteName('created_at'),
					]
				)
				->values(
					(int) $user->id . ','
							. (int) $createdBy . ','
							. $db->quote($expiresAt) . ','
							. $db->quote('active') . ','
							. $db->quote($createdAt)
				);
					$db->setQuery($insert);
					$db->execute();
		}
		catch (\Throwable $e)
		{
			$user->delete();

			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_CREATE_FAILED'];
		}

		return ['ok' => true, 'user_id' => (int) $user->id];
	}

	public static function getList()
	{
		if (!self::tableExists())
		{
			return [];
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				[
					't.*',
					'u.username',
					'u.email',
					'u.block',
					'c.username AS created_by_username',
				]
			)
			->from($db->quoteName('#__miniorange_jnsp_temp_users', 't'))
			->join('LEFT', $db->quoteName('#__users', 'u') . ' ON u.id = t.user_id')
			->join('LEFT', $db->quoteName('#__users', 'c') . ' ON c.id = t.created_by')
			->order('t.id DESC');
		$db->setQuery($query);

		return $db->loadAssocList() ?: [];
	}

	public static function deleteInactiveUser($userId, $actorId)
	{
		if ($userId <= 0 || $userId === $actorId || !self::tableExists())
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_INVALID_TARGET'];
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName('#__miniorange_jnsp_temp_users'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId)
			->where($db->quoteName('status') . ' IN (' . $db->quote('revoked') . ', ' . $db->quote('expired') . ')');
		$db->setQuery($query);
		$record = $db->loadAssoc();

		if (!is_array($record))
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_INVALID_TARGET'];
		}

		self::purgeUserSessions($userId);
		$user = new User($userId);

		if (!empty($user->id) && !$user->delete())
		{
			return ['ok' => false, 'error' => 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_DELETE_FAILED'];
		}

		$delete = $db->getQuery(true)
			->delete($db->quoteName('#__miniorange_jnsp_temp_users'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);
		$db->setQuery($delete);
		$db->execute();

		return ['ok' => true, 'error' => ''];
	}

	public static function isUserDeleteRequest($app)
	{
		if ($app->input->getCmd('option', '') !== 'com_users')
		{
			return false;
		}

		$task = strtolower((string) $app->input->get('task', ''));
		$view = strtolower((string) $app->input->getCmd('view', ''));
		$controller = strtolower((string) $app->input->getCmd('controller', ''));
		$taskName = $task;

		if (strpos($task, '.') !== false)
		{
			[$controllerFromTask, $taskName] = explode('.', $task, 2);

			if ($controller === '')
			{
				$controller = strtolower($controllerFromTask);
			}
		}

		if ($taskName !== 'delete')
		{
			return false;
		}

		if (in_array($controller, ['user', 'users'], true) || in_array($view, ['user', 'users'], true))
		{
			return true;
		}

		return $controller === '' && $view === '';
	}

	public static function denyUserManagement()
	{
		if (self::isCurrentUserTemporary())
		{
			throw new \RuntimeException('Temporary administrator users cannot create or update other users.', 403);
		}
	}

	public static function denyUserDelete()
	{
		if (!self::isCurrentUserTemporary())
		{
			return;
		}

		Factory::getLanguage()->load('com_joomshield', JPATH_ADMINISTRATOR);
		$message = Text::_('COM_JOOMSHIELD_TEMP_ADMIN_USER_DELETE_DENIED');
		Factory::getApplication()->enqueueMessage($message, 'error');

		throw new \RuntimeException($message, 403);
	}

	public static function denyExtensionChange()
	{
		if (self::isCurrentUserTemporary())
		{
			throw new \RuntimeException('Temporary administrator users cannot install or remove extensions.', 403);
		}
	}

	private static function purgeUserSessions($userId)
	{
		try
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__session'))
				->where($db->quoteName('userid') . ' = ' . (int) $userId);
			$db->setQuery($query);
			$db->execute();
		}
		catch (\Throwable $e)
		{
			return;
		}
	}

	private static function getSuperUserGroupId()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__usergroups'))
			->where($db->quoteName('title') . ' = ' . $db->quote('Super Users'));
		$db->setQuery($query);
		$id = (int) $db->loadResult();

		return $id > 0 ? $id : 8;
	}

	private static function getRegisteredGroupId()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__usergroups'))
			->where($db->quoteName('title') . ' = ' . $db->quote('Registered'));
		$db->setQuery($query);
		$id = (int) $db->loadResult();

		return $id > 0 ? $id : 2;
	}
}
