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
use Joomla\CMS\Event\User\LoginFailureEvent;

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');


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


		public function onUserBeforeSave()
		{
			$post = Factory::getApplication()->input->post->getArray();
			$config = JoomShieldUtilities::getRegisterSecurityConfig();
			$isEnforceStrongPasswd = $config['enforce_strong_password_register'] ?? 0;

			if ($isEnforceStrongPasswd)
			{
				$passwd = $post['jform']['password1'] ?? null;
				$isValidPasswd = JoomShieldUtilities::checkPasswdStrength($passwd);

				if ($isValidPasswd === 'false')
				{
					JoomShieldUtilities::redirectWithStrongPasswordErrors();
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
	}
}
