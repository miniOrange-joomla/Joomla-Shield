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

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;

defined('_JEXEC') or die('Restricted access');
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

class JoomshieldControllerMoIpBlocking extends FormController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'moipblocking';

		parent::__construct($config, $factory, $app, $input);
	}

	public function ipLookUp()
	{
		$post = Factory::getApplication()->input->post->getArray();

		if (isset($post['clear_val']))
		{
			JoomShieldUtilities::clearIplookup();
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_IP_LOOKUP_CLEARED_SUCCESSFULLY'), 'success');

			return;
		}

		$ipAddress = $post['mo_lookupip'] ?? '';

		if (empty($ipAddress))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_PLEASE_ENTER_A_VALID_IP_ADDRESS'), 'warning');

			return;
		}

		if (!filter_var($ipAddress, FILTER_VALIDATE_IP))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_ENTER_A_VALID_IPV4_OR_IPV6_ADDRESS'), 'warning');

			return;
		}

		self::lookupIP($ipAddress);
	}

	private function lookupIP(string $ip): void
	{
		// Ignore invalid / local IPs
		if (!filter_var($ip, FILTER_VALIDATE_IP)
			|| $ip === '127.0.0.1'
			|| $ip === '::1'
		)
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_PLEASE_ENTER_A_VALID_IP_ADDRESS'), 'error');

			return;
		}

		try
		{
			$http = HttpFactory::getHttp(['timeout' => 5]);
			$response = $http->get("https://ipinfo.io/{$ip}/json");

			if ($response->code !== 200)
			{
				$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_IP_LOOKUP_FAILED'), 'error');

				return;
			}

			$details = json_decode($response->body, true);

			// Bogon IPs (private/reserved)
			if (empty($details) || !empty($details['bogon']))
			{
				$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_IP_LOOKUP_FAILED'), 'error');

				return;
			}

			// Prepare lookup data
			$ipLookup = [
				'IP Address' => $details['ip'] ?? $ip,
				'HostName'   => $details['hostname'] ?? (@gethostbyaddr($ip) ?: ''),
				'City'       => $details['city'] ?? '',
				'Region'     => $details['region'] ?? '',
				'Country'    => $details['country'] ?? '',
				'Postal'     => $details['postal'] ?? '',
				'Timezone'   => $details['timezone'] ?? '',
				'Location'   => $details['loc'] ?? '',
				'Org'        => $details['org'] ?? '',
			];

			// Store as JSON (NOT serialize)
			$db    = Factory::getContainer()->get('DatabaseDriver');
			$query = $db->getQuery(true)
				->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))
				->set(
					$db->quoteName('mo_ip_lookup_values') . ' = ' .
					$db->quote(json_encode($ipLookup))
				)
				->where($db->quoteName('id') . ' = 1');

			$db->setQuery($query)->execute();

			$this->setRedirect(
				'index.php?option=com_joomshield&tab=ip_blocking',
				Text::_('COM_JOOMSHIELD_IP_LOOKUP_SUCCESSFULLY'),
				'success'
			);
		}
		catch (Throwable $e)
		{
			Factory::getApplication()->enqueueMessage(
				'IP lookup failed: ' . $e->getMessage(),
				'error'
			);
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_blocking', Text::_('COM_JOOMSHIELD_IP_LOOKUP_FAILED'), 'error');

			return;
		}
	}

	public function clearReports()
	{
		$post = Factory::getApplication()->input->post->getArray();
		$loginReport = JoomShieldUtilities::getAllLoginAttemptsCount();

		if (isset($post['refresh_page']))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_reports', Text::_('COM_JOOMSHIELD_THE_LOGIN_REPORTS_HAS_BEEN_UPDATED_SUCCESSFULLY'));

			return;
		}

		if (isset($post['download_reports']) && $loginReport != 0)
		{
			JoomShieldUtilities::downloadReports();

			return;
		}

		if (isset($post['download_reports']) && $loginReport == 0)
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_reports', Text::_('COM_JOOMSHIELD_REPORT_IS_ALREADY_EMPTY'), 'error');

			return;
		}

		if (isset($post['clear_val']) && $loginReport != 0)
		{
			$db = Factory::getDbo();
			$db->truncateTable('#__miniorange_login_transactions_reports');
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_reports', Text::_('COM_JOOMSHIELD_THE_LOGIN_REPORTS_HAS_BEEN_CLEARED_SUCCESSFULLY'));

			return;
		}

		if (isset($post['clear_val']) && $loginReport == 0)
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=ip_reports', Text::_('COM_JOOMSHIELD_REPORT_IS_ALREADY_EMPTY'), 'error');

			return;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=ip_reports', Text::_('COM_JOOMSHIELD_SOME_ERROR_OCCURRED_PLEASE_TRY_AGAIN'), 'error');

		return;
	}
}
