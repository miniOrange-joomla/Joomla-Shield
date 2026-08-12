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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

class IpBlocking
{
	public static function moNetworksecurityIpBlockingForm()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$details = JoomShieldUtilities::getLoginSecurityConfig();
		$details = $details['mo_ip_lookup_values'] ?? '';
		$attrs = [];

		if (!empty($details))
		{
			$attrs = json_decode($details, true);
		}

		if (json_last_error() !== JSON_ERROR_NONE)
		{
			$attrs = [];
		}

		$lookupResultsHtml = '';

		if (!empty($attrs))
		{
			$lookupResultsHtml .= '<div class="mo-ip-lookup-wrapper">';
			$lookupResultsHtml .= '<table class="mo-ip-lookup-table">';
			$lookupResultsHtml .= '<tbody>';

			foreach ($attrs as $key => $value)
			{
				$lookupResultsHtml .= '<tr>';
				$lookupResultsHtml .= '<th>' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '</th>';
				$lookupResultsHtml .= '<td>' . implode('<br>', array_map('htmlspecialchars', (array) $value)) . '</td>';
				$lookupResultsHtml .= '</tr>';
			}

			$lookupResultsHtml .= '</tbody>';
			$lookupResultsHtml .= '</table>';
			$lookupResultsHtml .= '</div>';
		}
		?>

		<div class="mo_boot_col-sm-12 mo_boot_mt-3">
			<h3><?php echo Text::_('COM_JOOMSHIELD_IP_LOOKUP'); ?></h3>
			<hr>

			<form name="mo_ip_lookup" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=moipblocking.ipLookUp'); ?>">
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3 mo_boot_px-0">
						<?php echo Text::_('COM_JOOMSHIELD_ENTER_IP_ADDRESS'); ?>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" id="mo_lookupip" type="text"
							   name="mo_lookupip" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_IP_ADDRESS_NOTE'); ?>" value="" width="100%;">
					</div>
				</div>
				<div class="mo_boot_row"><br></div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-10 mo_boot_d-flex mo_js_align_items_center mo_js_justify-content-center mo_js_gap-12">
						<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_LOOKUP_IP'); ?>" name="submit">
						<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_CLEAR'); ?>" name="clear_val">
					</div>
				</div>

				<?php echo $lookupResultsHtml; ?>

				<strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?></strong> <?php echo Text::_('COM_JOOMSHIELD_IP_LOOKUP_NOTE'); ?><br>
				<hr>
			</form>

			<br>

			<details>
				<form name="mo_ip" method="post">
					<br>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-5">
							<?php echo Text::_('COM_JOOMSHIELD_IP_LOOKUP_NOTE2'); ?>
						</div>
						<div class="mo_boot_col-sm-7">
							<input class="form-control mo_security_textfield mo_boot_form-control" type="text" name="mo_manual_ip" disabled
								   placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_IP_ADDRESS_PLACEHOLDER'); ?>" value="" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}">
						</div>
					</div>
					<div class="mo_boot_row"><br></div>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
							<input type="submit" class="mo_websecurity_btn" name="submit" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>" disabled>
						</div>
					</div>

					<div class="mo_boot_row"><br></div>

					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-5">
							<input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_SEARCH_IP_IN_TABLE_PLACEHOLDER'); ?>" id="myInput" disabled>
						</div>
					</div>
				</form>

				<div class="table-responsive mo_boot_row mo_table_div" id="mo_idp_vt_conf_table">
					<table class="mo_myTable mo_boot_mx-3" id="myTable">
						<tr class="header mo_boot_text-white" style="line-height: 10px;">
							<th class="mo_boot_text-center ipblock_style"><?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS'); ?></th>
							<th class="mo_boot_text-center ipblock_style"><?php echo Text::_('COM_JOOMSHIELD_REASON'); ?></th>
							<th class="mo_boot_text-center ipblock_style"><?php echo Text::_('COM_JOOMSHIELD_BLOCKED_UNTIL'); ?></th>
							<th class="mo_boot_text-center ipblock_style"><?php echo Text::_('COM_JOOMSHIELD_BLOCKED_DATE'); ?></th>
							<th class="mo_boot_text-center ipblock_style"><?php echo Text::_('COM_JOOMSHIELD_ACTION'); ?></th>
						</tr>
						<tbody class="mo_ip_table">
						<tr>
							<td colspan="5" class="mo_boot_text-center" style="user-select: none !important;">
								<?php echo Text::_('COM_JOOMSHIELD_NO_IPS_BLACKLISTED'); ?>
							</td>
						</tr>
						</tbody>
					</table><br>
				</div><br>
				<summary style="cursor: pointer;">
					<strong><?php echo Text::_('COM_JOOMSHIELD_MANUAL_BLOCK_IPS'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans' target="_blank"><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></strong>
				</summary>
			</details>

			<details class="mo_boot_mt-4">
				<form name="mo_save_whitelist_ips" method="post">
					<br>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-5">
							<?php echo Text::_('COM_JOOMSHIELD_MANUAL_WHITELIST_IPS'); ?>
						</div>
						<div class="mo_boot_col-sm-7">
							<input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" name="mo_whitelist_ip" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_IP_ADDRESS_PLACEHOLDER'); ?>" value="" disabled>
						</div>
					</div>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
							<input type="submit" name="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>" disabled>
						</div>
					</div>
					<div class="mo_boot_row"><br></div>
					<div class="mo_boot_row">
						<div class="mo_boot_col-sm-5">
							<input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_SEARCH_IP_IN_TABLE_PLACEHOLDER'); ?>" id="moinput" disabled>
						</div>
					</div>
				</form>

				<div class="table-responsive mo_boot_row mo_table_div" id="mo_idp_vt_conf_table">
					<table class="mo_myTable mo_boot_mx-3" id="motable">
						<tr class="header mo_boot_text-white" style="line-height: 15px;">
							<th class="mo_boot_text-center ipwhitelist_style"><?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS'); ?></th>
							<th class="mo_boot_text-center ipwhitelist_style"><?php echo Text::_('COM_JOOMSHIELD_WHITELIST_DATE'); ?></th>
							<th class="mo_boot_text-center ipwhitelist_style"><?php echo Text::_('COM_JOOMSHIELD_ACTION'); ?></th>
						</tr>
						<tbody class="mo_ip_table">
						<tr>
							<td colspan="3" class="mo_boot_text-center" style="user-select: none !important;">
								<?php echo Text::_('COM_JOOMSHIELD_NO_IPS_WHITELISTED'); ?>
							</td>
						</tr>
					</table><br>
				</div><br>

				<summary style="cursor: pointer;">
					<strong><?php echo Text::_('COM_JOOMSHIELD_WHITELIST_IPS'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans'><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></strong>
				</summary>
			</details>
		</div><br>
		<?php
	}
}
