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

defined('_JEXEC') or die;

class DB_Backup
{
	public static function moNetworksecurityDbBackupForm()
	{
		$dbSyncInterval   = '';
		$hourlySelected   = ('hourly' === $dbSyncInterval) ? 'selected' : '';
		$dailySelected    = ('daily' === $dbSyncInterval) ? 'selected' : '';
		$weeklySelected   = ('weekly' === $dbSyncInterval) ? 'selected' : '';
		$monthlySelected  = ('monthly' === $dbSyncInterval) ? 'selected' : '';
		?>

		<div class="mo_boot_col-sm-12">
			<h3 class="mo_boot_mt-3"><?php echo Text::_('COM_JOOMSHIELD_DATABASE_BACKUP'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans'><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></h3>
			<hr>
			<div><?php echo Text::_('COM_JOOMSHIELD_DATABASE_BACKUP_NOTE'); ?></div><br>

			<form name="mo_take_db_backup" method="post">
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_HOST_NAME'); ?>: </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_host_name" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_HOST_NAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_USERNAME'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_username" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_USERNAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_PASSWORD'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_password" type="password" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_PASSWORD'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_NAME'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_name" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_NAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12 mo_boot_d-flex mo_js_justify-content-center">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="Backup" disabled>
					</div>
				</div>
			</form><br><br>
			<h3> <?php echo Text::_('COM_JOOMSHIELD_AUTOMATIC_SCHEDULED_DATABASE_BACKUP'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans'><strong>( <?php echo Text::_('COM_JOOMSHIELD_PREMIUM_FEATURE'); ?> )</strong></a></sup></h3><hr>

			<form name="mo_take_db_backup" method="post">

				<input type="checkbox" class="mo_enable_automatic_bckp checkbox_style" value="true" name="mo_enable_automatic_backup" disabled>
				<?php echo Text::_('COM_JOOMSHIELD_AUTOMATIC_SCHEDULED_DATABASE_BACKUP_NOTE'); ?><br><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_HOST_NAME'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_host_name" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_HOST_NAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_USERNAME'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_username" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_USERNAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_PASSWORD'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_password" type="password" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_PASSWORD'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_DATABASE_NAME'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_name" type="text" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_YOUR_DATABASE_NAME'); ?>" disabled/>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-3">
						<strong><?php echo Text::_('COM_JOOMSHIELD_SELECT_HOW_OFTEN_YOU_WANT_TO_TAKE_THE_DATABASE_BACKUP'); ?> </strong>
					</div>
					<div class="mo_boot_col-sm-7">
						<select class="mo_db_backup_dropdown mo_boot_form-control" name="sync_interval">
							<option value="hourly" <?php echo $hourlySelected; ?>><?php echo Text::_('COM_JOOMSHIELD_HOURLY'); ?></option>
							<option value="daily" <?php echo $dailySelected; ?>><?php echo Text::_('COM_JOOMSHIELD_DAILY'); ?></option>
							<option value="weekly" <?php echo $weeklySelected; ?>><?php echo Text::_('COM_JOOMSHIELD_WEEKLY'); ?></option>
							<option value="monthly" <?php echo $monthlySelected; ?>><?php echo Text::_('COM_JOOMSHIELD_MONTHLY'); ?></option>
						</select>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12 mo_boot_d-flex mo_js_justify-content-center">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_BACKUP_DATABASE'); ?>" disabled>
					</div>
				</div><br>
			</form>
		</div>
		<?php
	}
}
