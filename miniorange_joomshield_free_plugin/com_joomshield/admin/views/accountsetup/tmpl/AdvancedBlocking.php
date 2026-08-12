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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/BasicEnum.php';

$document = Factory::getApplication()->getDocument();
$document->addStyleSheet(Uri::base() . 'components/com_joomshield/assets/css/miniorange_boot.css');

class AdvancedBlocking
{
	public static function moNetworksecurityAdvancedIpBlockingForm()
	{
		$dbVal = JoomShieldUtilities::getAdvanceIp();

		$browserBlkEnable = $dbVal['mo_enable_browser_blocking'] ?? 0;
		$medge            = $dbVal['mo_medge_blocking'] ?? 0;
		$country          = BasicEnum::getCountryList();

		if (!is_array($country))
		{
			$country = [];
		}

		$browserBlockingChecked = ($browserBlkEnable == 1) ? 'checked' : '';
		$medgeBlockingChecked   = ($medge == 1 && $browserBlkEnable == 1) ? 'checked' : '';
		$countryOptionsHtml     = '';

		foreach ($country as $key => $value)
		{
			$countryOptionsHtml .= '<label class="mo_country_multiselect_option" data-country-name="'
				. htmlspecialchars(strtolower($value), ENT_QUOTES, 'UTF-8') . '">';
			$countryOptionsHtml .= '<input type="checkbox" name="mo_blocked_countries[]" value="'
				. htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '">';
			$countryOptionsHtml .= htmlspecialchars(ucwords(strtolower($value)), ENT_QUOTES, 'UTF-8');
			$countryOptionsHtml .= '</label>';
		}

		?>
		<div class="mo_boot_col-sm-12">
		<h3 class="mo_boot_mt-3"> <?php echo Text::_('COM_JOOMSHIELD_BROWSER_BLOCKING'); ?> </h3>
		<hr>
		<form name="mo_jnsp_browser_blocking" method="post"
			  action="<?php echo Route::_('index.php?option=com_joomshield&task=advanceipblocking.saveBrowserBlocking'); ?>">

					<input type="checkbox" <?php echo $browserBlockingChecked; ?>
						   class="mo_enable_brwoser_blocking checkbox_style" value="1" name="mo_enable_browser_blocking"
						   onclick="enable_checkbox_advance_ip()"><?php echo Text::_('COM_JOOMSHIELD_ENABLE_BROWSER_BLOCKING'); ?><br>
					<div class="mo_boot_mt-2"><?php echo Text::_('COM_JOOMSHIELD_SELECT_BROWSER'); ?> <span class="mo_js_note"> ( <span class="mo_boot_text-red"><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?></span><?php echo Text::_('COM_JOOMSHIELD_SELECT_BROWSER_NOTE'); ?> ) </span></div>
					<br>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-3 mo_boot_text-center">
							<input type="checkbox" name="mo_medge_blocking" class="mo_medge_blocking" value="1" <?php echo $medgeBlockingChecked; ?>>
							<img src="<?php echo Uri::base() . '/components/com_joomshield/assets/images/ms-edge.png' ?>" style="width: 28px;"/>
						</div>
					</div><br>

					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-3 mo_boot_text-center">
							<input type="checkbox" name="mo_chrome_blocking" class="mo_chrome_blocking" value="1" disabled>
							<img src="<?php echo Uri::base() . '/components/com_joomshield/assets/images/chrome.png' ?>" style="width: 25px;"/>
						</div>

						<div class="mo_boot_col-sm-3 mo_boot_text-center">
							<input type="checkbox" name="mo_firefox_blocking" class="mo_firefox_blocking" value="1" disabled>
							<img src="<?php echo Uri::base() . '/components/com_joomshield/assets/images/firefox.png' ?>" style="width: 28px;"/>
						</div>

						<div class="mo_boot_col-sm-3 mo_boot_text-center">
							<input type="checkbox" name="mo_safari_blocking" class="mo_safari_blocking" value="1" disabled>
							<img src="<?php echo Uri::base() . '/components/com_joomshield/assets/images/safari.png' ?>" style="width: 35px;"/>
						</div>

						<div class="mo_boot_col-sm-3 mo_boot_text-center">
							<input type="checkbox" name="mo_opera_blocking" class="mo_opera_blocking" value="1" disabled>
							<img src="<?php echo Uri::base() . '/components/com_joomshield/assets/images/opera.png' ?>" style="width: 30px;"/>
						</div>
					</div>
					<br>

					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
							<input type="submit" name="submit" value="Save" class="mo_websecurity_btn">
						</div>
					</div>
		</form><br><br>

		<h3><?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS_RANGE_BLOCKING'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans'><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></h3>
		<hr>
		<div><?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS_RANGE_BLOCKING_NOTE'); ?></div>
		<br>
		<form class="hidden" id="unblockiprange" method="POST">
			<input type="hidden" name="option" value="mo_unblock_ip_range">
			<input type="hidden" name="entryidr" value="" id="unblockiprangeval">
		</form>
		<form name="mo_ip_range_blocking" method="post">
			<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_ENTER_IP_ADDRESS_RANGE'); ?>
					</div>
					<div class="mo_boot_col-sm-6">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="ip_address_range_blocking"
							   type="text" placeholder="IP address range" disabled />
					</div>
				</div><br>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="Block" disabled>
					</div>
				</div><br>

			<div class="table-responsive mo_table_div">
				<table class="mo_myTable">
					<tr class="header mo_boot_text-white mo_boot_text-center" style="line-height: 10px;">
						<th class="mo_boot_text-center" width="33%"><?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS'); ?></th>
						<th class="mo_boot_text-center" width="33%"><?php echo Text::_('COM_JOOMSHIELD_DATE'); ?></th>
						<th class="mo_boot_text-center" width="33%"><?php echo Text::_('COM_JOOMSHIELD_ACTION'); ?></th>
					</tr>
					<tr>
						<td class="mo_boot_text-center" colspan="3" style="user-select: none;">
							<?php echo Text::_('COM_JOOMSHIELD_NO_IP_RANGE_BLOCKED'); ?>
						</td>
					</tr>
				</table>
			</div>
		</form><br><br>

		<h3><?php echo Text::_('COM_JOOMSHIELD_COUNTRY_BLOCKING'); ?> <sup><a href="index.php?option=com_joomshield&tab=license_plans"><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></h3>
		<hr>
		<p><?php echo Text::_('COM_JOOMSHIELD_SELECT_COUNTRIES_FROM_BELOW'); ?></p>
		<form name="mo_jnsp_country_blocking" method="post" id="countryblockingform">
			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-8">
					<div class="mo_country_multiselect" id="mo_country_multiselect">
						<div class="mo_country_multiselect_trigger mo_boot_form-control" id="mo_country_multiselect_trigger" tabindex="0">
							<span class="mo_country_multiselect_placeholder" id="mo_country_multiselect_label"><?php echo Text::_('COM_JOOMSHIELD_SELECT_COUNTRIES_PLACEHOLDER'); ?></span>
							<span class="mo_country_multiselect_arrow">&#9662;</span>
						</div>
						<div class="mo_country_multiselect_panel mo_boot_d-none" id="mo_country_multiselect_panel">
							<input type="text"
								   class="mo_boot_form-control mo_country_multiselect_search"
								   id="mo_country_multiselect_search"
								   placeholder="<?php echo Text::_('COM_JOOMSHIELD_SEARCH_COUNTRIES_PLACEHOLDER'); ?>"
								   autocomplete="off">
							<div class="mo_country_multiselect_options" id="mo_country_multiselect_options">
								<?php echo $countryOptionsHtml; ?>
							</div>
							<div class="mo_country_multiselect_no_results mo_boot_d-none" id="mo_country_multiselect_no_results">
								<?php echo Text::_('COM_JOOMSHIELD_NO_COUNTRIES_FOUND'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
					<input type="submit" class="mo_websecurity_btn" value="Save" disabled>
				</div>
			</div>
		</form><br><br>
		</div>
		<?php

	}
}
