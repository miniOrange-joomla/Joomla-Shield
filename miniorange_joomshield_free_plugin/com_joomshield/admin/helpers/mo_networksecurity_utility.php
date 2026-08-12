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

defined('_JEXEC') or die;

require_once rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomshield' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'MonetworksecurityDB.php';
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

class MoNetworkSecurityUtility
{

	public static function isCurlInstalled()
	{
		if (in_array('curl', get_loaded_extensions()))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	public static function getHostname()
	{
		return 'https://login.xecurify.com';
	}

	public static function getNotifyCustomerKey()
	{
		$envKey = getenv('JOOMSHIELD_NOTIFY_CUSTOMER_KEY');

		if (false !== $envKey && '' !== $envKey)
		{
			return $envKey;
		}

		$customer = MonetworksecurityDB::getCustomerDetails();

		if (!empty($customer['customer_key']))
		{
			return $customer['customer_key'];
		}

		return '';
	}

	public static function getNotifyApiKey()
	{
		$envKey = getenv('JOOMSHIELD_NOTIFY_API_KEY');

		if (false !== $envKey && '' !== $envKey)
		{
			return $envKey;
		}

		$customer = MonetworksecurityDB::getCustomerDetails();

		if (!empty($customer['api_key']))
		{
			return $customer['api_key'];
		}

		return '';
	}

	public static function applySecureCurlOptions($ch)
	{
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	}

	public static function buildNotifyRequestHeaders()
	{
		$customerKey = self::getNotifyCustomerKey();
		$apiKey = self::getNotifyApiKey();
		$currentTimeInMillis = round(microtime(true) * 1000);
		$timestamp = number_format($currentTimeInMillis, 0, '', '');
		$hashValue = hash('sha512', $customerKey . $timestamp . $apiKey);

		return [
			'Customer-Key: ' . $customerKey,
			'Timestamp: ' . $timestamp,
			'Authorization: ' . $hashValue,
		];
	}

	public static function sendNotifyApiRequest($url, array $fields)
	{
		if (!self::isCurlInstalled())
		{
			return json_encode(
				[
					'status' => 'CURL_ERROR',
					'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.',
				]
			);
		}

		$ch = curl_init($url);
		$headers = array_merge(['Content-Type: application/json'], self::buildNotifyRequestHeaders());
		$fieldString = json_encode($fields);

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, '');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		self::applySecureCurlOptions($ch);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldString);

		$content = curl_exec($ch);

		if (curl_errno($ch))
		{
			$error = json_encode(['status' => 'ERROR', 'statusMessage' => curl_error($ch)]);

			return $error;
		}

		return $content;
	}

	public static function isIpBlocked($ipAddress)
	{
		$isIpBlocked = false;

		if (self::isBrowserBlocked($ipAddress))
		{
			$isIpBlocked = true;
		}

		return $isIpBlocked;
	}

	public static function isBrowserBlocked($userIp)
	{
		$attributes = JoomShieldUtilities::getAdvanceIp();

		$browserBlkEnable = $attributes['mo_enable_browser_blocking'] ?? 0;
		$msEdge = $attributes['mo_medge_blocking'] ?? 0;

		if ($browserBlkEnable)
		{
			$userBrowser = JoomShieldUtilities::getCurrentUserBrowser();

			if ($msEdge == 1 && ($userBrowser == 'edge' || $userBrowser == 'edg'))
			{
				return true;
			}
		}

		return false;
	}


}
