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
use Joomla\CMS\Version;

defined('_JEXEC') or die;

require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomshield' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'mo_networksecurity_utility.php';
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

class Joomla_NetworksecurityCustomer
{
	/**
	 * Customer email address.
	 *
	 * @var    string
	 */
	public $email;

	/**
	 * Customer phone number.
	 *
	 * @var    string
	 */
	public $phone;

	/**
	 * Customer key identifier.
	 *
	 * @var    string
	 */
	public $customerKey;

	/**
	 * Transaction identifier.
	 *
	 * @var    string
	 */
	public $transactionId;

	private static function sendMail($fields)
	{
		$url = MoNetworkSecurityUtility::getHostname() . '/moas/api/notify/send';

		return MoNetworkSecurityUtility::sendNotifyApiRequest($url, $fields);
	}

	public function submitContactUs($qEmail, $qPhone, $query)
	{
		$customerKey = MoNetworkSecurityUtility::getNotifyCustomerKey();

		$fromEmail            = $qEmail;
		$phpVersion           = phpversion();
		$currentUser          = Factory::getUser();
		$jVersion             = new Version;
		$jCmsVersion          = $jVersion->getShortVersion();
		$moPluginVersion      = JoomShieldUtilities::getPluginVersion();
		$osInfo               = JoomShieldUtilities::getOsInfo();
		$timezoneInfo         = JoomShieldUtilities::getUserTimezoneInfo();
		$adminEmail           = $currentUser->email;

		$systemInfo           = "Joomla: " . $jCmsVersion . " | PHP: " . $phpVersion . " | Plugin: " . $moPluginVersion . " | OS: " . $osInfo . " | Timezone " . $timezoneInfo;
		$query                = '[JoomShield Free Plugin]: ' . $query . '<br>' . $systemInfo;
		$subject              = "Query for miniOrange JoomShield [Free] Plugin  - " . $qEmail;
		$content             = '<div >Hello, <br><br>
                                <strong>Company:</strong> <a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br><br>
                                <strong>Admin Email:</strong> <a href="mailto:' . $adminEmail . '" target="_blank">' . $adminEmail . '</a><br><br>
                                <strong>Email:</strong> <a href="mailto:' . $qEmail . '" target="_blank">' . $qEmail . '</a><br><br>
                                <strong>Phone Number:</strong> ' . $qPhone . '<br><br>
                                <strong>Query:</strong> ' . $query . '</div>';

		$fields = array(
		'customerKey'    => $customerKey,
		'sendEmail'     => true,
		'email'         => array(
			'customerKey'   => $customerKey,
			'fromEmail'     => $fromEmail,
			'fromName'      => 'miniOrange',
			'toEmail'       => 'joomlasupport@xecurify.com',
			'toName'        => 'joomlasupport@xecurify.com',
			'subject'       => $subject,
			'content'       => $content
			),
		);

		return self::sendMail($fields);
	}

	public static function installationMessage()
	{
		$customerKey          = MoNetworkSecurityUtility::getNotifyCustomerKey();
		$phpVersion           = phpversion();
		$currentUser          = Factory::getUser();
		$jVersion             = new Version;
		$jCmsVersion          = $jVersion->getShortVersion();
		$moPluginVersion      = JoomShieldUtilities::getPluginVersion();
		$osInfo               = JoomShieldUtilities::getOsInfo();
		$timezoneInfo         = JoomShieldUtilities::getUserTimezoneInfo();
		$email                = $currentUser->email;
		$systemInfo           = "Joomla: " . $jCmsVersion . " | PHP: " . $phpVersion . " | Plugin: " . $moPluginVersion . " | OS: " . $osInfo . " | Timezone " . $timezoneInfo;
		$subject = "Installation of JoomShield [Free]";
		$content = '<div >Hello, <br>
                        <strong>Company:</strong> <a href="' . $_SERVER['SERVER_NAME'] . '" target="_blank" >' . $_SERVER['SERVER_NAME'] . '</a><br>
                        <strong>Admin Email:</strong> <a href="mailto:' . $email . '" target="_blank">' . $email . '</a><br>
                        <strong>System Info:</strong> ' . $systemInfo . '</div>';

		$fields = array(
		'customerKey'    => $customerKey,
		'sendEmail'     => true,
		'email'         => array(
			'customerKey'   => $customerKey,
			'fromEmail'     => 'joomlasupport@xecurify.com',
			'fromName'      => 'miniOrange',
			'toEmail'       => 'nutan.barad@xecurify.com',
			'toName'        => 'nutan.barad@xecurify.com',
			'bccEmail'      => 'nikhil.bhot@xecurify.com',
			'subject'       => $subject,
			'content'       => $content
			),
		);

		return self::sendMail($fields);
	}

}
