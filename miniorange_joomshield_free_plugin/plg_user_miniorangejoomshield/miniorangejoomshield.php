<?php

/**
 * @package    Joomla.Plugins
 * @subpackage plg_user_miniorangejoomshield
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Event\User\LoginEvent;
use Joomla\CMS\Event\User\LoginFailureEvent;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserHelper;

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');
jimport('miniorangejoomshieldplugin.utility.JoomShieldTempAdmin');

if (defined('_JEXEC'))
{
	class PlgUserMiniorangejoomshield extends CMSPlugin
	{
		/**
		 * This method should handle any authentication and report back to the subject
		 *
		 * @access public
		 * @param  array|LoginFailureEvent  $response  Authentication response or login failure event
		 * @param  array                    $options   Array of extra options
		 * @return boolean
		 */
		public function onUserLoginFailure($response = null, $options = [])
		{
			$failureReason = '';

			if ($response instanceof LoginFailureEvent)
			{
				$authResponseData = $response->getAuthenticationResponse();
				$authResponse = (object) $authResponseData;
				$options = $response->getOptions();
				$failureReason = $authResponseData['error_message'] ?? '';
			}
			elseif (is_array($response))
			{
				$authResponse = (object) $response;
				$failureReason = $response['error_message'] ?? '';
			}
			else
			{
				$authResponse = null;
			}

			$val = 1;
			JoomShieldUtilities::checkUrl($val);
			$post = Factory::getApplication()->input->post->getArray();

			$username = $post['username'] ?? ($options['username'] ?? '');

			if (empty($username) && $authResponse !== null && !empty($authResponse->username))
			{
				$username = $authResponse->username;
			}

			$userIp = JoomShieldUtilities::getClientIp();

			$requestedUri = JoomShieldUtilities::getLoginRequestUrl();

			if (strpos($requestedUri, 'administrator') !== false)
			{
				$isadmin = 'Admin Login Page';
			}
			else
			{
				$isadmin = 'End user login page';
			}

			if (empty($failureReason))
			{
				$failureReason = 'Invalid username or password';
			}

			$countryName = JoomShieldUtilities::getCountryName($userIp);

			$browserName = JoomShieldUtilities::getCurrentUserBrowser();
			$os = JoomShieldUtilities::getOsInfo();

			JoomShieldUtilities::addTransactionDetails($userIp, $username, 'User Login', 'failed', $requestedUri);
			JoomShieldUtilities::addLoginTransactionDetails(
				$userIp,
				$username,
				'User Login',
				'failed',
				$isadmin,
				$countryName,
				$browserName,
				$os,
				$requestedUri,
				'',
				$failureReason
			);
		}


		public function onUserBeforeSave($user, $isNew, $data = [])
		{
			Factory::getLanguage()->load('com_joomshield', JPATH_ADMINISTRATOR);

			if (JoomShieldTempAdmin::isCurrentUserTemporary())
			{
				$messageKey = $isNew
					? 'COM_JOOMSHIELD_TEMP_ADMIN_USER_CREATE_DENIED'
					: 'COM_JOOMSHIELD_TEMP_ADMIN_USER_EDIT_DENIED';
				Factory::getApplication()->enqueueMessage(Text::_($messageKey), 'error');

				return false;
			}

			$post = Factory::getApplication()->input->post->getArray();
			$config = JoomShieldUtilities::getRegisterSecurityConfig();
			$isEnforceStrongPasswd = $config['enforce_strong_password_register'] ?? 0;

			if ($isEnforceStrongPasswd && array_key_exists('password1', $post['jform'] ?? []))
			{
				$passwd = $post['jform']['password1'] ?? '';
				$isValidPasswd = JoomShieldUtilities::checkPasswdStrength($passwd);

				if ($isValidPasswd === 'false')
				{
					JoomShieldUtilities::redirectWithStrongPasswordErrors(null, $passwd);
				}
			}

			$isRest = $config['block_fake_emails'] ?? '';

			if ($isRest == 1)
			{
				$email = $post['jform']['email1'] ?? '';
				$isNvalid = JoomShieldUtilities::isValidEmail($email);

				if ($isNvalid)
				{
					$message = 'The email domain you have entered is not allowed to register. Please try with different email address.';
					JoomShieldUtilities::redirectWithErrorMessage($message);
				}
			}
		}

		public function onUserBeforeDelete($user)
		{
			JoomShieldTempAdmin::denyUserDelete();

			return true;
		}

		public function onUserLogin($user, $options = [])
		{
			if (class_exists(LoginEvent::class) && $user instanceof LoginEvent)
			{
				$options = $user->getOptions();
				$user = $user->getAuthenticationResponse();
			}

			JoomShieldTempAdmin::expireDueUsers();
			$username = is_array($user) ? ($user['username'] ?? '') : '';

			if ($username === '')
			{
				return true;
			}

			$userId = UserHelper::getUserId($username);

			if ($userId && JoomShieldTempAdmin::isTemporaryUserId($userId) && !JoomShieldTempAdmin::getActiveRecord($userId))
			{
				return false;
			}

			$resetUser = JoomShieldUtilities::getForcedPasswordResetUser(null, $options);

			if ($resetUser !== null)
			{
				try
				{
					Factory::getApplication()->logout((int) $resetUser->id);
				}
				catch (\Throwable $e)
				{
				}

				JoomShieldUtilities::beginForcedPasswordReset($resetUser->username, (int) $resetUser->id);
			}

			return true;
		}
	}
}
