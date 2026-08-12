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

defined('_JEXEC') or die('Restricted access');

class JoomshieldControllerLoginSecurity extends FormController
{
	private const TAB_CLASS_NAMES = [
		'login_security'    => 'Mo_Login_Security',
		'register-security' => 'Mo_Register_Security',
		'advance_blocking'  => 'Mo_Advance_Blocking',
	];

	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'loginsecurity';

		parent::__construct($config, $factory, $app, $input);
	}

	public function saveLoginSecuritySettings()
	{
		$post = Factory::getApplication()->input->post->getArray();

		if (empty($post))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=login_security', Text::_('COM_JOOMSHIELD_PLEASE_ENABLE_THE_CHECKBOX'), 'warning');
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		if (isset($post['mo_customize_admin_url']))
		{
			$isAdminLoginEnable = $post['enable_custom_admin_login'] ?? 0;
			$accessLgnKy        = $post['access_lgn_urlky'] ?? '';
			$afterFailure       = $post['after_adm_failure_response'] ?? '';
			$customDestination  = $post['custom_failure_destination'] ?? '';
			$customErrMessage   = $post['custom_message_after_fail'] ?? '';

			$fields = [
				$db->quoteName('enable_custom_admin_login') . ' = ' . $db->quote($isAdminLoginEnable),
				$db->quoteName('access_lgn_urlky') . ' = ' . $db->quote($accessLgnKy),
				$db->quoteName('after_adm_failure_response') . ' = ' . $db->quote($afterFailure),
				$db->quoteName('custom_failure_destination') . ' = ' . $db->quote($customDestination),
				$db->quoteName('custom_message_after_fail') . ' = ' . $db->quote($customErrMessage),
			];
			$msg = Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL_CONFIGURATION_HAS_BEEN_SAVED_SUCCESSFULLY');
		}
		elseif (isset($post['mo_enforce_strong_password']))
		{
			$strongPasswdLogin = $post['enforce_strong_password_login'] ?? 0;

			$fields = [
				$db->quoteName('enforce_strong_password_login') . ' = ' . $db->quote($strongPasswdLogin),
			];
			$msg = Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORD_CONFIGURATION_HAS_BEEN_SAVED_SUCCESSFULLY');
		}
		else
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=login_security', Text::_('COM_JOOMSHIELD_PLEASE_ENABLE_THE_CHECKBOX'), 'warning');

			return;
		}

		$conditions = [
			$db->quoteName('id') . ' = 1',
		];

		$query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$this->setRedirect('index.php?option=com_joomshield&tab=login_security', $msg);
	}

	public function importExport($jsonInString = false)
	{
		include_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'export.php';

		$tabNames = [
			'login_security'    => '#__miniorange_jnsp_loginsecurity_setup',
			'register-security' => '#__miniorange_jnsp_registersecurity_setup',
			'advance_blocking'  => '#__miniorange_jnsp_advance_blocking',
		];

		$customerResult = [];

		foreach ($tabNames as $key => $value)
		{
			$customerResult[$key] = JoomShieldUtilities::isNetworkRegistered($value);
		}

		$loginSecurity    = $customerResult['login_security'];
		$registerSecurity = $customerResult['register-security'];
		$advanceBlocking  = $customerResult['advance_blocking'];

		$i = 0;

		if (($loginSecurity['enable_custom_admin_login'] == 0) && ($loginSecurity['mo_ip_lookup_values'] == null)
			&& ($loginSecurity['enforce_strong_password_login'] == 0)
		)
		{
			$i++;
		}

		if (($registerSecurity['block_fake_emails'] == 0) && ($registerSecurity['enforce_strong_password_register'] == 0))
		{
			$i++;
		}

		if (($advanceBlocking['mo_enable_browser_blocking'] == 0))
		{
			$i++;
		}

		if ($i != 3)
		{
			if ($customerResult)
			{
				$jsonString = json_encode($customerResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

				if ($jsonInString)
				{
					return $jsonString;
				}

				header('Content-Disposition: attachment; filename=miniorange-joomshield-config.json');
				echo $jsonString;
				exit;
			}

			return;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=import_export', Text::_('COM_JOOMSHIELD_EXPORT_DEFAULT_CONFIGURATIONS'), 'error');
	}

	public function import()
	{
		$file = $this->input->files->get('configuration_file', null, 'array');

		if ($file === null || empty($file['name']))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=import_export', Text::_('COM_JOOMSHIELD_PLEASE_SELECT_THE_FILE_FIRST_TO_IMPORT_EMPTY_FILE_CANNOT_BE_IMPORTED'), 'error');

			return;
		}

		$tmpName = $file['tmp_name'] ?? '';
		$error   = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

		if ($tmpName === '' || $error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=import_export', Text::_('COM_JOOMSHIELD_UPLOAD_FAILED_OR_INVALID_FILE'), 'error');

			return;
		}

		$string = @file_get_contents($tmpName);
		$json   = json_decode((string) $string, true);

		if (!is_array($json))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=import_export', Text::_('COM_JOOMSHIELD_INVALID_CONFIGURATION_JSON'), 'error');

			return;
		}

		$this->updateConfigurationArray($json);
		$this->setRedirect('index.php?option=com_joomshield&tab=import_export', Text::_('COM_JOOMSHIELD_CONFIGURATION_FILE_IMPORTED_SUCCESSFULLY'), 'success');
	}

	protected function updateConfigurationArray($configurationArray)
	{
		include_once JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'export.php';

		foreach (self::TAB_CLASS_NAMES as $tabName => $className)
		{
			$db     = Factory::getDbo();
			$query  = $db->getQuery(true);
			$fields = [];
			$varEnter = 0;

			foreach ($configurationArray[$tabName] as $key => $value)
			{
				$varEnter   = 1;
				$fields[] = $db->quoteName($key) . ' = ' . $db->quote($value);
			}

			if ($className === 'Mo_Login_Security' && $varEnter === 1)
			{
				$conditions = [
					$db->quoteName('id') . ' = 1',
				];
				$query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}

			if ($className === 'Mo_Register_Security' && $varEnter === 1)
			{
				$conditions = [
					$db->quoteName('id') . ' = 1',
				];
				$query->update($db->quoteName('#__miniorange_jnsp_registersecurity_setup'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}

			if ($className === 'Mo_Advance_Blocking' && $varEnter === 1)
			{
				$conditions = [
					$db->quoteName('id') . ' = 1',
				];
				$query->update($db->quoteName('#__miniorange_jnsp_advance_blocking'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	public function joomlaPagination()
	{
		$totalEntries = JoomShieldUtilities::getAllLoginAttemptsCount();

		$post      = Factory::getApplication()->input->post->getArray();
		$start     = $post['page'] ?? 0;
		$order     = $post['orderBY'] ?? 'down';
		$noOfEntry = $post['no_of_entry'] ?? '20';
		$start     = (int) $start;
		$showTrans = (int) $noOfEntry;
		$lowId     = $start * $showTrans;
		$firstVal  = ($totalEntries === 0) ? 0 : $lowId + 1;
		$lastVal   = min($lowId + $showTrans, $totalEntries);

		if ($lastVal === $totalEntries && $totalEntries > 0)
		{
			echo '<script>
                document.getElementById("next_btn").style.display = "none";
            </script>';
		}

		$listOfLoginTrans = JoomShieldUtilities::getLoginAttemptsCount($showTrans, $lowId, $order);
		$result           = '';
		$result          .= '<div class="mo_login_reports_wrap mo_table_div" id="mo_idp_vt_conf_table">
            <table id="myTable" class="mo_login_reports_table mo_myTable">
            <colgroup>
                <col class="mo_col-sr">
                <col class="mo_col-username">
                <col class="mo_col-url">
                <col class="mo_col-ip">
                <col class="mo_col-status">
                <col class="mo_col-failure">
                <col class="mo_col-date">
            </colgroup>
            <thead>
                <tr class="header mo_boot_text-white">
                    <th class="mo_td_values mo_col-sr">Sr ID</th>
                    <th class="mo_td_values mo_col-username">Username</th>
                    <th class="mo_td_values mo_col-url">Site URL</th>
                    <th class="mo_td_values mo_col-ip">IP Address</th>
                    <th class="mo_td_values mo_col-status">Status</th>
                    <th class="mo_td_values mo_col-failure">Failure Reason</th>
                    <th class="mo_td_values mo_col-date">Date &amp; Time<br>(in UTC)&nbsp;<span class="fa fa-sort mo_login_reports_sort" onclick=sort("on",true)><input type="hidden" value="1" id="hidden_input"></span></th>
                </tr>
            </thead>
                <tbody>';

		$srNo = $firstVal;

		foreach ($listOfLoginTrans as $list2)
		{
			foreach ($list2 as $list)
			{
				if (empty($list['ip_address']) || empty($list['username']))
				{
					continue;
				}

				$siteUrl = htmlspecialchars(
					JoomShieldUtilities::getLoginReportSiteUrl($list),
					ENT_QUOTES,
					'UTF-8'
				);

				$statusClass   = ($list['status'] === 'success') ? 'mo_boot_text-success' : 'mo_boot_text-danger';
				$statusLabel   = htmlspecialchars(ucfirst($list['status'] ?? ''), ENT_QUOTES, 'UTF-8');
				$failureReason = ($list['status'] === 'failed')
					? htmlspecialchars($list['failure_reason'] ?? '-', ENT_QUOTES, 'UTF-8')
					: '-';

				$result .= '<tr>
                    <td class="mo_guide_text-center mo_col-sr"><span class="mo_login_reports_cell">' . $srNo . '</span></td>
                    <td class="mo_guide_text-center mo_col-username"><span class="mo_login_reports_cell">' . htmlspecialchars($list['username'], ENT_QUOTES, 'UTF-8') . '</span></td>
                    <td class="mo_col-url"><span class="mo_login_reports_cell mo_login_reports_url">' . $siteUrl . '</span></td>
                    <td class="mo_guide_text-center mo_col-ip"><span class="mo_login_reports_cell">' . htmlspecialchars($list['ip_address'], ENT_QUOTES, 'UTF-8') . '</span></td>
                    <td class="mo_guide_text-center mo_col-status"><span class="mo_login_reports_cell ' . $statusClass . '"><b>' . $statusLabel . '</b></span></td>
                    <td class="mo_col-failure"><span class="mo_login_reports_cell">' . $failureReason . '</span></td>
                    <td class="mo_guide_text-center mo_col-date"><span class="mo_login_reports_cell mo_login_reports_date">' . date('M j, Y, g:i:s a', $list['created_timestamp']) . '</span></td>
                </tr>';
				$srNo++;
			}
		}

		if ($totalEntries === 0)
		{
			$result .= '<tr><td colspan="7" class="mo_guide_text-center mo_login_reports_empty">No login reports found.</td></tr>';
		}

		$result .= '</tbody>
                        </table>
                    </div><br>
                    <div>' . Text::_('COM_JOOMSHIELD_SHOWING') . ' ' . $firstVal . ' - ' . $lastVal . ' ' . Text::_('COM_JOOMSHIELD_OF') . ' ' . $totalEntries . ' ' . Text::_('COM_JOOMSHIELD_ENTRIES') . ' (max 20 stored)</div>';
		echo $result;
		exit;
	}
}
