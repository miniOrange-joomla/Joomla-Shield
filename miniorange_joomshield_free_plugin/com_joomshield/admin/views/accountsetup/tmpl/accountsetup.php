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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

// No direct access to this file

defined('_JEXEC') or die('Restricted Access');

$document = Factory::getApplication()->getDocument();
HTMLHelper::_('jquery.framework');
$document->addStyleSheet(Uri::base() . 'components/com_joomshield/assets/css/miniorange_netsecurity.css');
$document->addStyleSheet(Uri::base() . 'components/com_joomshield/assets/css/mo_jnsp_phone.css');
$document->addScript(Uri::base() . 'components/com_joomshield/assets/js/utility.js');
$document->addScriptDeclaration(
	'var moJoomShieldPaginationUrl = ' . json_encode(
		Route::_('index.php?option=com_joomshield&task=loginsecurity.joomlapagination', false)
	) . ';'
);
$document->addScript(Uri::base() . 'components/com_joomshield/assets/js/mo_jnsp_phone.js');
$document->addStyleSheet(Uri::base() . 'components/com_joomshield/assets/css/miniorange_boot.css');

$tab = Factory::getApplication()->input->get->getArray();

$shieldActiveTab = $tab['tab'] ?? 'login_security';

$joomShieldWarnings = [];

if (!MoNetworkSecurityUtility::isCurlInstalled())
{
	$joomShieldWarnings[] = [
		'message' => Text::_('COM_JOOMSHIELD_CURL_EXTENSION_DISABLED'),
		'link'    => 'https://www.php.net/manual/en/curl.installation.php',
		'linkText' => Text::_('COM_JOOMSHIELD_CURL_INSTALLATION_GUIDE'),
	];
}

$requiredPlugins = [
	[
		'folder'  => 'authentication',
		'element' => 'miniorangeshield',
		'label'   => Text::_('COM_JOOMSHIELD_AUTH_PLUGIN_NAME'),
	],
	[
		'folder'  => 'system',
		'element' => 'jsminiorange',
		'label'   => Text::_('COM_JOOMSHIELD_SYSTEM_PLUGIN_NAME'),
	],
	[
		'folder'  => 'user',
		'element' => 'miniorangejoomshield',
		'label'   => Text::_('COM_JOOMSHIELD_USER_PLUGIN_NAME'),
	],
];

foreach ($requiredPlugins as $requiredPlugin)
{
	if (!PluginHelper::isEnabled($requiredPlugin['folder'], $requiredPlugin['element']))
	{
		$joomShieldWarnings[] = [
			'message' => Text::sprintf(
				'COM_JOOMSHIELD_PLUGIN_DISABLED_WARNING',
				$requiredPlugin['label']
			),
			'link' => Route::_(
				'index.php?option=com_plugins&view=plugins&filter[folder]='
				. $requiredPlugin['folder']
				. '&filter[element]='
				. $requiredPlugin['element'],
				false
			),
			'linkText' => Text::_('COM_JOOMSHIELD_ENABLE_PLUGIN'),
		];
	}
}

$joomShieldWarningsHtml = '';

if (!empty($joomShieldWarnings))
{
	$joomShieldWarningsHtml .= '<div class="mo_boot_row mo_boot_p-2 mo_boot_mb-2">';
	$joomShieldWarningsHtml .= '<div class="mo_boot_col-sm-12">';
	$joomShieldWarningsHtml .= '<div class="mo_boot_row alert alert-warning mo_boot_mb-0">';
	$joomShieldWarningsHtml .= '<div class="mo_boot_col-sm-12">';
	$joomShieldWarningsHtml .= '<strong>' . Text::_('COM_JOOMSHIELD_SYSTEM_REQUIREMENTS_WARNING') . '</strong>';
	$joomShieldWarningsHtml .= '<ul class="mo_boot_mb-0 mo_boot_mt-2">';

	foreach ($joomShieldWarnings as $warning)
	{
		$joomShieldWarningsHtml .= '<li>' . $warning['message'];

		if (!empty($warning['link']))
		{
			$targetAttr = str_starts_with($warning['link'], 'http') ? ' target="_blank" rel="noopener noreferrer"' : '';
			$joomShieldWarningsHtml .= '<a href="' . $warning['link'] . '"' . $targetAttr . '>';
			$joomShieldWarningsHtml .= $warning['linkText'];
			$joomShieldWarningsHtml .= '</a>';
		}

		$joomShieldWarningsHtml .= '</li>';
	}

	$joomShieldWarningsHtml .= '</ul>';
	$joomShieldWarningsHtml .= '</div>';
	$joomShieldWarningsHtml .= '</div>';
	$joomShieldWarningsHtml .= '</div>';
	$joomShieldWarningsHtml .= '</div>';
}
?>
<?php echo $joomShieldWarningsHtml; ?>
	<div class="mo_boot_row mo_boot_p-2 mo_boot_mb-2">
		<div class="mo_boot_col-sm-12 mo_boot_d-flex mo_js_justify_content_end mo_js_gap-12 mo_js_align_items_center">
			<a href="<?php echo Uri::base(); ?>index.php?option=com_joomshield&tab=import_export"
			   class="mo_boot_mt-1 mo_websecurity_btn"><strong><?php echo Text::_('COM_JOOMSHIELD_IMPORT_EXPORT'); ?></strong></a>
			<a href="<?php echo Uri::base(); ?>index.php?option=com_joomshield&tab=support"
			   class="mo_boot_mt-1 mo_websecurity_btn"><strong><?php echo Text::_('COM_JOOMSHIELD_SUPPORT'); ?></strong></a>
		</div>
	</div>

	<div class="mo_boot_container-fluid mo_boot_websecurity-container">
		<div class="mo_boot_row">
			<div class="mo_boot_col-sm-2 mo_boot_websecurity-row mo_boot_px-0">
				<div class="mo_boot_row">
					<div onclick="mo_show_tab('websecurity_tab_2')" style="<?php echo ($shieldActiveTab == 'login_security') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_2" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB2_LOGIN_SECURITY'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_3')" style="<?php echo ($shieldActiveTab == 'register_security') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_3" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong style="font-size: 16px;">  <?php echo Text::_('COM_JOOMSHIELD_TAB4_IP_REGISTER_SECURITY'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_4')" style="<?php echo ($shieldActiveTab == 'ip_blocking') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_4" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB3_IP_BLOCKING'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_5')" style="<?php echo ($shieldActiveTab == 'ip_reports') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_5" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB3_IP_REPORTS'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_6')" style="<?php echo ($shieldActiveTab == 'advanced_ip_blocking') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_6" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB5_ADVANCED_BLOCKING'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_7')" style="<?php echo ($shieldActiveTab == 'db_backup') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_7" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB6_DB_BACKUP'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_8')" style="<?php echo ($shieldActiveTab == 'email_notifications') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_8" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB7_NOTIFICATIONS'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_11')" style="<?php echo ($shieldActiveTab == 'license_plans') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_11" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB9_LICENSE_PLANS'); ?></strong>
					</div>

					<div onclick="mo_show_tab('websecurity_tab_10')" style="<?php echo ($shieldActiveTab == 'advanced_features') ? 'background:white;color:black' : 'background:none;color:white;'; ?>" id="mo_websecurity_tab_10" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
						<strong class="mo_jnsp_tab"><?php echo Text::_('COM_JOOMSHIELD_TAB8_ADVANCED_FEATURES'); ?></strong>
					</div>
				</div>
			</div>

			<div class="mo_boot_col-sm-10">
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_2" style="<?php echo (($shieldActiveTab == 'login_security') ? 'display:block;' : 'display:none;'); ?>">
							<?php
							LoginSecurity::moNetworksecurityLoginSecurityForm();
							?>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_3" style="<?php echo (($shieldActiveTab == 'register_security') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							RegisterSecurity::moNetworksecurityRegisterSecurityForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_4" style="<?php echo (($shieldActiveTab == 'ip_blocking') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							IpBlocking::moNetworksecurityIpBlockingForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_5" style="<?php echo (($shieldActiveTab == 'ip_reports') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							IpReports::moNetworksecurityLoginReportsForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_6" style="<?php echo (($shieldActiveTab == 'advanced_ip_blocking') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							AdvancedBlocking::moNetworksecurityAdvancedIpBlockingForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_7" style="<?php echo (($shieldActiveTab == 'db_backup') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							DB_Backup::moNetworksecurityDbBackupForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_8" style="<?php echo (($shieldActiveTab == 'email_notifications') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							Notifications::moNetworksecurityEmailNotificationsForm();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_10" style="<?php echo (($shieldActiveTab == 'advanced_features') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							AdvancedFeatures::moNetworksecurityAdvancedFeatures();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_11" style="<?php echo (($shieldActiveTab == 'license_plans') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							LicensePlans::moJoomshieldLicensePlans();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_9" style="<?php echo (($shieldActiveTab == 'support') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							MoJnspSupport::moJnspSupport();
							?>
						</div>
					</div>

					<div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_12" style="<?php echo (($shieldActiveTab == 'import_export') ? 'display:block;' : 'display:none;'); ?>">
						<div class="mo_boot_row mo_boot_m-2">
							<?php
							MoJsImportExport::moJsImportExport();
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
