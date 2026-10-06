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

use Joomla\CMS\Event\User\AfterLoginEvent;
use Joomla\CMS\Event\User\AuthenticationEvent;
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
			if (class_exists(AfterLoginEvent::class) && $options instanceof AfterLoginEvent)
			{
				$options = $options->getOptions();
			}

			$object = $options['user'] ?? null;
			$array = is_object($object) ? (array) $object : [];
			$username = $array['username'] ?? '';

			jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

			JoomShieldUtilities::blockWeakPasswordAfterLogin($options);

			$userIpAddress = JoomShieldUtilities::getClientIp();
			$requestedUri = JoomShieldUtilities::getLoginRequestUrl();

			if (strpos($requestedUri, 'administrator') !== false)
			{
				$isadmin = 'Admin Login Page';
			}
			else
			{
				$isadmin = 'End user login page';
			}

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

		public function onUserAuthenticate($credentials, $options = [], &$response = null)
		{
			jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

			if (class_exists(AuthenticationEvent::class) && $credentials instanceof AuthenticationEvent)
			{
				$event = $credentials;
				$credentials = $event->getCredentials();
				$options = $event->getOptions();
				$response = $event->getAuthenticationResponse();
			}

			JoomShieldUtilities::enforceStrongPasswordLogin($credentials, $options);

			$username = $credentials['username'] ?? '';
			$password = $credentials['password'] ?? '';
			$result = JoomShieldUtilities::getUserCredentials($username);
			$requestedUri = JoomShieldUtilities::getLoginRequestUrl();
			$isAdmin = str_contains($requestedUri, 'administrator');
			$config = JoomShieldUtilities::getLoginSecurityConfig();
			$loginUrlKey = is_array($config) ? ($config['access_lgn_urlky'] ?? '') : '';
			$baseUrl = Uri::root();
			$currentAdminLoginUrl = $baseUrl . 'administrator';
			$customAdminLoginUrl = $currentAdminLoginUrl . '/?' . $loginUrlKey;
			$msg = Text::_('JGLOBAL_AUTH_INVALID_PASS');

			if ($result !== null)
			{
				$match = UserHelper::verifyPassword($password, $result->password, $result->id);

				if ($match !== true && $isAdmin)
				{
					JoomShieldUtilities::customRedirectUrl($customAdminLoginUrl, $msg, 'warning');
				}
			}
			elseif ($isAdmin)
			{
				JoomShieldUtilities::customRedirectUrl($customAdminLoginUrl, $msg, 'warning');
			}
		}
	}
}
