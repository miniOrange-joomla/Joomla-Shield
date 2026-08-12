<?php

/**
 * @package    Joomla.Plugins
 * @subpackage plg_authentication_miniorangeshield
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserHelper;
defined('_JEXEC') or die;

if (defined('_JEXEC'))
{
	class PlgAuthenticationMiniorangeShield extends CMSPlugin
	{
		public function onUserAfterLogin($options)
		{
			$object = $options['user'];

			$array = (array) $object;
			$username = $array['username'];

			jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

			$userIpAddress = JoomShieldUtilities::getClientIp();

			// JoomShieldUtilities::updateTransactionTable($userIpAddress);
			$requestedUri = JoomShieldUtilities::getLoginRequestUrl();

			if (strpos($requestedUri, 'administrator') !== false)
			{
				$isadmin = 'Admin Login Page';
			}
			else
			{
				$isadmin = 'End user login page';
			}

			// Some customer are facing some environment issue due to below call. Will fix this soon.
			// $countryName = JoomShieldUtilities::getCountryName($userIpAddress);
			$countryName = "";
			$browserName = JoomShieldUtilities::getCurrentUserBrowser();

			$os = JoomShieldUtilities::getOsInfo();

			JoomShieldUtilities::addTransactionDetails($userIpAddress, $username, 'User Login', 'success', $requestedUri);
			JoomShieldUtilities::addLoginTransactionDetails(
				$userIpAddress,
				$username,
				'User Login',
				'success',
				$isadmin,
				$countryName,
				$browserName,
				$os,
				$requestedUri
			);
		}

		public function onUserAuthenticate($credentials, $options, &$response)
		{
			$username = $credentials['username'] ?? '';
			$password = $credentials['password'] ?? '';

			$result = JoomShieldUtilities::getUserCredentials($username);

			$requestedUri = JoomShieldUtilities::getLoginRequestUrl();

			if (str_contains($requestedUri, 'administrator'))
			{
				$isAdmin = true;
			}
			else
			{
				$isAdmin = false;
			}

			// For handling Customized admin url on wrong credentials.
			$config = JoomShieldUtilities::getLoginSecurityConfig();
			$loginUrlKey = $config['access_lgn_urlky'] ?? '';
			$baseUrl = Uri::root();
			$currentAdminLoginUrl = $baseUrl . 'administrator';
			$customAdminLoginUrl = $currentAdminLoginUrl . '/?' . $loginUrlKey;
			$msg = Text::_('JGLOBAL_AUTH_INVALID_PASS');

			if ($result != null)
			{
				$match = UserHelper::verifyPassword($password, $result->password, $result->id);

				if ($match == true)
				{
					$configResult = JoomShieldUtilities::getLoginSecurityConfig();
					$enforceStrongPasswd = $configResult['enforce_strong_password_login'] ?? 0;

					if ($enforceStrongPasswd)
					{
						$status = JoomShieldUtilities::checkPasswdStrength($password);

						if ($status == 'false')
						{
							$returnUrl = $requestedUri;

							if ($isAdmin)
							{
								$customAdminEnabled = $config['enable_custom_admin_login'] ?? 0;

								if ($customAdminEnabled)
								{
									$returnUrl = $customAdminLoginUrl;
								}
								else
								{
									$returnUrl = $currentAdminLoginUrl;
								}
							}

							include __DIR__ . '/includes/change-password.php';
							exit();
						}
					}
				}
				else
				{
					if ($isAdmin)
					{
						JoomShieldUtilities::customRedirectUrl($customAdminLoginUrl, $msg, 'warning');
					}
				}
			}
			else
			{
				if ($isAdmin)
				{
					JoomShieldUtilities::customRedirectUrl($customAdminLoginUrl, $msg, 'warning');
				}
			}
		}
	}
}
