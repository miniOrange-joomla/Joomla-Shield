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

class AdvancedFeatures
{
	public static function moNetworksecurityAdvancedFeatures()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$config = JoomShieldSiteProtection::getConfig();
		$offlineChecked = ((int) ($config['emergency_offline'] ?? 0) === 1) ? 'checked' : '';
		$apacheChecked = ((int) ($config['server_rules_apache'] ?? 0) === 1) ? 'checked' : '';
		$currentIp = JoomShieldUtilities::getClientIp();
		$dbResults = Factory::getSession()->get('joomshield_db_maintain_results', []);
		$staleUrlEnabled = ((int) ($config['link_migration_live'] ?? 0) === 1) ? 'checked' : '';
		$staleUrlHosts = (string) ($config['link_migration_old_hosts'] ?? '');
		$currentSiteAddress = JoomShieldUrlRewrite::currentLocation();
		?>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3 class="mo_boot_mt-3"><?php echo Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_NOTE'); ?></em></p>
				<?php NginxRules::renderServerRestrictionNotes('emergency_offline'); ?>
				<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.saveEmergencyOffline'); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_ENABLE'); ?></div>
						<div class="mo_boot_col-sm-7"><input class="checkbox_style" type="checkbox" name="emergency_offline" value="1" <?php echo $offlineChecked; ?>></div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_WHITELIST'); ?></div>
						<div class="mo_boot_col-sm-7">
							<input class="mo_boot_form-control" type="text" name="offline_whitelist_ips" value="<?php echo htmlspecialchars((string) $config['offline_whitelist_ips'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo htmlspecialchars($currentIp, ENT_QUOTES, 'UTF-8'); ?>">
						</div>
					</div>
					<?php NginxRules::renderApacheWriteOption($apacheChecked); ?>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-12 mo_boot_text-center">
							<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3><?php echo Text::_('COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES_NOTE'); ?></em></p>
				<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.clearTemporaryFiles'); ?>" onsubmit="return confirm('<?php echo Text::_('COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES_CONFIRM'); ?>');">
					<?php echo HTMLHelper::_('form.token'); ?>
					<input type="submit" class="mo_websecurity_btn_danger" value="<?php echo Text::_('COM_JOOMSHIELD_CLEAR_TEMPORARY_FILES_BUTTON'); ?>">
				</form>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3><?php echo Text::_('COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS_NOTE'); ?></em></p>
				<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.purgeActiveSessions'); ?>" onsubmit="return confirm('<?php echo Text::_('COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS_CONFIRM'); ?>');">
					<?php echo HTMLHelper::_('form.token'); ?>
					<input type="submit" class="mo_websecurity_btn_danger" value="<?php echo Text::_('COM_JOOMSHIELD_PURGE_ACTIVE_SESSIONS_BUTTON'); ?>">
				</form>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_NOTE'); ?></em></p>
				<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.saveStaleUrlRewrite'); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_ENABLE'); ?></div>
						<div class="mo_boot_col-sm-7"><input class="checkbox_style" type="checkbox" name="stale_url_enabled" value="1" <?php echo $staleUrlEnabled; ?>></div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_HOSTS'); ?></div>
						<div class="mo_boot_col-sm-7">
							<textarea class="mo_js_textarea" name="stale_url_hosts" rows="4" placeholder="staging.example.com"><?php echo htmlspecialchars($staleUrlHosts, ENT_QUOTES, 'UTF-8'); ?></textarea>
							<p><em><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_HOSTS_TIP'); ?></em></p>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_STALE_URL_REWRITE_CURRENT'); ?></div>
						<div class="mo_boot_col-sm-7">
							<input class="mo_boot_form-control" type="text" value="<?php echo htmlspecialchars($currentSiteAddress, ENT_QUOTES, 'UTF-8'); ?>" readonly>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-12 mo_boot_text-center">
							<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>">
						</div>
					</div>
				</form>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_NOTE'); ?></em></p>
				<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.repairOptimizeDatabase'); ?>" onsubmit="return confirm('<?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_CONFIRM'); ?>');">
					<?php echo HTMLHelper::_('form.token'); ?>
					<input type="submit" class="mo_websecurity_btn mo_boot_mb-3" value="<?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_BUTTON'); ?>">
				</form>
		<?php if (!empty($dbResults) && is_array($dbResults))
		{
			?>
				<div class="mo_boot_row mo_boot_mt-3">
					<div class="mo_boot_col-sm-12">
						<table class="table table-striped">
							<thead>
							<tr>
								<th><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_TABLE'); ?></th>
								<th><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_ENGINE'); ?></th>
								<th><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_REPAIR'); ?></th>
								<th><?php echo Text::_('COM_JOOMSHIELD_REPAIR_OPTIMIZE_DATABASE_OPTIMIZE'); ?></th>
							</tr>
							</thead>
							<tbody>
			<?php foreach ($dbResults as $row)
			{
				?>
								<tr>
									<td><?php echo htmlspecialchars((string) ($row['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo htmlspecialchars((string) ($row['engine'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo htmlspecialchars((string) ($row['repair'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
									<td><?php echo htmlspecialchars((string) ($row['optimize'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
								</tr>
				<?php
			}
			?>
							</tbody>
						</table>
					</div>
				</div>
			<?php
		}
		?>
			</div>
		</div>

		<?php
	}
}
