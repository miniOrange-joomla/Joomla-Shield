<?php

/**
 * @package    Joomla.Administrator
 * @subpackage com_joomshield
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

defined('_JEXEC') or die('Restricted access');

jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

class JoomshieldControllerSitemaintenance extends FormController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'accountsetup';

		parent::__construct($config, $factory, $app, $input);
	}

	public function saveAdminAccessProtection()
	{
		if (!$this->guardRequest('admin_http_auth', 'admin_security'))
		{
			return;
		}

		$input = Factory::getApplication()->input;
		$post = $input->post->getArray();
		$enabled = isset($post['admin_http_auth']) ? 1 : 0;
		$username = trim((string) ($post['admin_http_user'] ?? ''));
		$password = (string) $input->post->get('admin_http_password', '', 'raw');
		$whitelist = (string) ($post['admin_http_whitelist_ips'] ?? '');
		$apache = isset($post['server_rules_apache']) ? 1 : 0;
		$config = JoomShieldSiteProtection::getConfig();

		if ($enabled === 1 && $username === '')
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=admin_security',
				Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_USERNAME_REQUIRED'),
				'error'
			);

			return;
		}

		$values = [
			'admin_http_auth' => $enabled,
			'admin_http_user' => $username,
			'admin_http_whitelist_ips' => $whitelist,
			'server_rules_apache' => $apache,
		];

		if ($password !== '')
		{
			$values['admin_http_hash'] = password_hash($password, PASSWORD_DEFAULT);

			if ($enabled === 1)
			{
				JoomShieldServerRules::writeApachePassword($username, $password);
			}
		}
		elseif ($enabled === 1 && empty($config['admin_http_hash']))
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=admin_security',
				Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_PASSWORD_REQUIRED'),
				'error'
			);

			return;
		}

		JoomShieldSiteProtection::saveConfig($values);
		$warning = $this->syncServerRules();
		$msg = Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_SAVED')
			. ' ' . Text::_('COM_JOOMSHIELD_SERVER_RULES_SAVED_NOTE');

		if ($warning !== '')
		{
			$msg .= ' ' . $warning;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=admin_security', $msg);
	}

	public function saveFeatureAccessPassword()
	{
		if (!$this->guardRequest('temp_admin', 'admin_security', false))
		{
			return;
		}

		if (JoomShieldTempAdmin::isCurrentUserTemporary())
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=admin_security',
				Text::_('COM_JOOMSHIELD_TEMP_ADMIN_COMPONENT_DENIED'),
				'error'
			);

			return;
		}

		$input = Factory::getApplication()->input;
		$post = $input->post->getArray();
		$config = JoomShieldSiteProtection::getConfig();
		$enabled = isset($post['feature_lock_enabled']) ? 1 : 0;
		$current = (string) $input->post->get('feature_lock_current', '', 'raw');
		$newPassword = (string) $input->post->get('feature_lock_password', '', 'raw');
		$confirm = (string) $input->post->get('feature_lock_password_confirm', '', 'raw');
		$items = JoomShieldFeatureLock::filterItems($post['feature_lock_items'] ?? []);

		$hasHash = trim((string) ($config['feature_lock_hash'] ?? '')) !== '';

		if ($hasHash && !password_verify($current, $config['feature_lock_hash']))
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=admin_security',
				Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_CURRENT_INVALID'),
				'error'
			);

			return;
		}

		$values = [
			'feature_lock_enabled' => $enabled,
			'feature_lock_items' => json_encode(array_values($items)),
		];

		if ($newPassword !== '')
		{
			if ($newPassword !== $confirm || strlen($newPassword) < 8)
			{
				$this->setRedirect(
					'index.php?option=com_joomshield&tab=admin_security',
					Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_MISMATCH'),
					'error'
				);

				return;
			}

			$values['feature_lock_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
		}
		elseif ($enabled === 1 && !$hasHash)
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=admin_security',
				Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_REQUIRED_SET'),
				'error'
			);

			return;
		}

		JoomShieldSiteProtection::saveConfig($values);
		JoomShieldFeatureLock::lockSession();
		$this->setRedirect(
			'index.php?option=com_joomshield&tab=admin_security',
			Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_SAVED')
		);
	}

	public function unlockFeatureAccess()
	{
		$this->checkToken();

		if (JoomShieldTempAdmin::isCurrentUserTemporary())
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=login_security',
				Text::_('COM_JOOMSHIELD_TEMP_ADMIN_COMPONENT_DENIED'),
				'error'
			);

			return;
		}

		$password = (string) Factory::getApplication()->input->post->get('feature_lock_unlock', '', 'raw');
		$ok = JoomShieldFeatureLock::unlock($password);
		$tab = Factory::getApplication()->input->post->getCmd('return_tab', 'admin_security');

		if ($ok)
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=' . $tab);

			return;
		}

		$this->setRedirect(
			'index.php?option=com_joomshield&tab=' . $tab,
			Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_CURRENT_INVALID'),
			'error'
		);
	}

	public function createTempAdmin()
	{
		if (!$this->guardRequest('temp_admin', 'admin_security'))
		{
			return;
		}

		$input = Factory::getApplication()->input;
		$post = $input->post->getArray();
		$result = JoomShieldTempAdmin::createUser(
			$post['temp_admin_username'] ?? '',
			$post['temp_admin_email'] ?? '',
			$input->post->get('temp_admin_password', '', 'raw'),
			(int) ($post['temp_admin_hours'] ?? 24),
			(int) JoomShieldCompat::getUser()->id
		);

		$this->setRedirect(
			'index.php?option=com_joomshield&tab=admin_security',
			Text::_($result['ok'] ? 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_CREATED' : $result['error']),
			$result['ok'] ? 'message' : 'error'
		);
	}

	public function revokeTempAdmin()
	{
		if (!$this->guardRequest('temp_admin', 'admin_security'))
		{
			return;
		}

		$userId = (int) Factory::getApplication()->input->post->getInt('temp_user_id', 0);
		$result = JoomShieldTempAdmin::deactivateUser($userId, 'revoked', true);
		$this->setRedirect(
			'index.php?option=com_joomshield&tab=admin_security',
			Text::_($result ? 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_REVOKED' : 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_INVALID_TARGET'),
			$result ? 'message' : 'error'
		);
	}

	public function deleteTempAdmin()
	{
		if (!$this->guardRequest('temp_admin', 'admin_security'))
		{
			return;
		}

		$userId = (int) Factory::getApplication()->input->post->getInt('temp_user_id', 0);
		$result = JoomShieldTempAdmin::deleteInactiveUser($userId, (int) JoomShieldCompat::getUser()->id);
		$this->setRedirect(
			'index.php?option=com_joomshield&tab=admin_security',
			Text::_($result['ok'] ? 'COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_DELETED' : $result['error']),
			$result['ok'] ? 'message' : 'error'
		);
	}

	public function saveEmergencyOffline()
	{
		if (!$this->guardRequest('emergency_offline', 'advanced_features'))
		{
			return;
		}

		$post = Factory::getApplication()->input->post->getArray();
		$enabled = isset($post['emergency_offline']) ? 1 : 0;
		$whitelist = (string) ($post['offline_whitelist_ips'] ?? '');
		$ips = JoomShieldCompat::parseIpList($whitelist);

		if ($enabled === 1 && empty($ips))
		{
			$currentIp = JoomShieldUtilities::getClientIp();

			if (filter_var($currentIp, FILTER_VALIDATE_IP))
			{
				$ips[] = $currentIp;
				$whitelist = implode(', ', $ips);
			}
		}

		JoomShieldSiteProtection::saveConfig([
			'emergency_offline' => $enabled,
			'offline_whitelist_ips' => $whitelist,
			'server_rules_apache' => isset($post['server_rules_apache']) ? 1 : 0,
			]
		);

		$warning = $this->syncServerRules();
		$msg = Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_SAVED')
			. ' ' . Text::_('COM_JOOMSHIELD_SERVER_RULES_SAVED_NOTE');

		if ($warning !== '')
		{
			$msg .= ' ' . $warning;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=advanced_features', $msg);
	}

	public function clearTemporaryFiles()
	{
		if (!$this->guardRequest('clear_tmp', 'advanced_features'))
		{
			return;
		}

		$result = JoomShieldMaintenance::clearTemporaryFiles();
		$msg = Text::sprintf(
			'COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES_DONE',
			(int) $result['deleted'],
			(int) $result['skipped']
		);
		$this->setRedirect('index.php?option=com_joomshield&tab=advanced_features', $msg);
	}

	public function purgeActiveSessions()
	{
		if (!$this->guardRequest('purge_sessions', 'advanced_features'))
		{
			return;
		}

		$result = JoomShieldMaintenance::purgeActiveSessions();

		if (!empty($result['warning']))
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=advanced_features',
				Text::_($result['warning']),
				'warning'
			);

			return;
		}

		$this->setRedirect(
			'index.php?option=com_joomshield&tab=advanced_features',
			Text::sprintf('COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS_DONE', (int) $result['purged'])
		);
	}

	public function repairOptimizeDatabase()
	{
		if (!$this->guardRequest('repair_db', 'advanced_features'))
		{
			return;
		}

		$results = JoomShieldMaintenance::repairAndOptimize();
		Factory::getSession()->set('joomshield_db_maintain_results', $results);
		$this->setRedirect(
			'index.php?option=com_joomshield&tab=advanced_features',
			Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_DONE')
		);
	}

	public function saveStaleUrlRewrite()
	{
		if (!$this->guardRequest('link_migration', 'advanced_features'))
		{
			return;
		}

		$post = Factory::getApplication()->input->post->getArray();
		$enabled = isset($post['stale_url_enabled']) ? 1 : 0;
		$sanitized = JoomShieldUrlRewrite::sanitizeList((string) ($post['stale_url_hosts'] ?? ''));

		if ($enabled === 1 && $sanitized === '')
		{
			$this->setRedirect(
				'index.php?option=com_joomshield&tab=advanced_features',
				Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_EMPTY'),
				'error'
			);

			return;
		}

		JoomShieldSiteProtection::saveConfig([
			'link_migration_live' => $enabled,
			'link_migration_old_hosts' => $sanitized,
			]
		);

		$this->setRedirect(
			'index.php?option=com_joomshield&tab=advanced_features',
			Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_SAVED')
		);
	}

	public function downloadNginxSnippet()
	{
		if (!JoomShieldCompat::isSuperUser() || JoomShieldTempAdmin::isCurrentUserTemporary())
		{
			throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$snippet = JoomShieldServerRules::getNginxSnippet();
		JoomShieldServerRules::writeGeneratedNginxFile();
		header('Content-Type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename=joomshield-nginx.conf');
		echo $snippet;
		exit;
	}

	private function guardRequest($featureKey, $tab, $checkLock = true)
	{
		$this->checkToken();

		if ($checkLock)
		{
			$error = JoomShieldFeatureLock::assertAllowed($featureKey);

			if ($error !== '')
			{
				$this->setRedirect('index.php?option=com_joomshield&tab=' . $tab, $error, 'error');

				return false;
			}
		}

		if (!JoomShieldCompat::isSuperUser())
		{
			throw new \RuntimeException(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		return true;
	}

	private function syncServerRules()
	{
		$result = JoomShieldServerRules::applyFromConfig();

		if (!empty($result['warning']))
		{
			return Text::_('COM_JOOMSHIELD_SERVER_RULES_PHP_FALLBACK') . ' ' . $result['warning'];
		}

		return '';
	}
}
