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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$document = Factory::getApplication()->getDocument();
HTMLHelper::_('jquery.framework');
$document->addScript('https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js');
$document->addScript('https://code.jquery.com/jquery-3.5.1.js');
$document->addScript('https://use.fontawesome.com/19afe6f2b6.js');
$document->addScriptDeclaration(
	'var moJoomShieldPaginationUrl = ' . json_encode(
		Route::_('index.php?option=com_joomshield&task=loginsecurity.joomlapagination', false)
	) . ';'
);


class IpReports
{
	public static function moNetworksecurityLoginReportsForm()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');
		$loginReport    = JoomShieldUtilities::getAllLoginAttemptsCount();
		$reportsDisabled = ('0' === $loginReport) ? 'disabled' : '';
		?>
		<div class="mo_boot_col-sm-12">
			<h3 class=" mo_boot_mt-3"><?php echo Text::_('COM_JOOMSHIELD_LOGIN_TRANSACTIONS_REPORT'); ?></h3><hr>
			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-1 mo_boot_px-0">
					<select class="mo_boot_form-control" id="select_number" onchange="list_of_entry()" style="height: 35px !important;">
						<option value="10">10</option>
						<option value="20" selected>20</option>
						<option value="50">50</option>
						<option value="100">100</option>
					</select>
				</div>
				<div class="mo_boot_col-sm-3">
					<input type="text" id="search_text" class="mo_boot_form-control" onkeyup="search()" placeholder="Search" style="height: 35px !important;"/> <br>
				</div>
				<div class="mo_boot_col-sm-8 mo_boot_text-right">
					<form name="mo_ip_login" method="post" id="jnsp_clear_values" action="<?php echo Route::_('index.php?option=com_joomshield&task=moipblocking.clearReports'); ?>">
						<div class="mo_boot_row">
							<div class="mo_boot_col-sm-12 mo_boot_px-0">
								<input type="submit" name="refresh_page" class="mo_websecurity_btn"  value="<?php echo Text::_('COM_JOOMSHIELD_REFRESH_PAGE'); ?>">
								<input type="submit" name="clear_val" class="mo_websecurity_btn_danger" value="<?php echo Text::_('COM_JOOMSHIELD_CLEAR_REPORTS'); ?>" onclick="return confirm('Do you really want to clear the report?');" <?php echo $reportsDisabled; ?>>
								<input type="submit" name="download_reports" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_DOWNLOAD_REPORTS'); ?>" <?php echo $reportsDisabled; ?>>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div id="show_paginations"></div>

			<input type="hidden" id="next_page" value="0"><br>

			<div class="mo_boot_mb-3">
				<div id="next_btn">
					<input type="submit" name="mo_next" class="mo_websecurity_btn"
					   style="float: right;" onclick="next_or_prev_page('next','preserve');" value="<?php echo Text::_('COM_JOOMSHIELD_NEXT'); ?>">
				</div>
				<div id="pre_btn">
					<input type="submit" name="mo_next" class="mo_websecurity_btn mo_boot_mr-1"
					   style="float: right;" onclick="next_or_prev_page('pre','preserve');" value="<?php echo Text::_('COM_JOOMSHIELD_PREV'); ?>">
				</div>
			</div>
		</div>
		<?php
	}
}
