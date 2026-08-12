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
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class AdvancedFeatures
{
	public static function moNetworksecurityAdvancedFeatures()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');
		?>

		<div class="mo_boot_col-sm-12 mo_boot_mt-3">
			<h3 class="mo_boot_mt-3"><?php echo Text::_('COM_JOOMSHIELD_ADVANCED_FEATURES'); ?> <a class="advnc_style" href="<?php echo Uri::base() . 'index.php?option=com_joomshield&tab=license_plans'; ?>"> <sup> ( Premium Feature )</sup> </a></h3>
		</div>
		<div class="mo_boot_col-sm-12"><hr></div>

		<div class="mo_boot_col-sm-12">
			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_CROSS_SITE_SCRIPTING'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_CROSS_SITE_SCRIPTING_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_BLOCK_SQL_INJECTION'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_BLOCK_SQL_INJECTION_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_DISABLE_MOUSE_RIGHT_CLICK'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_DISABLE_MOUSE_RIGHT_CLICK_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_BACKEND_PASSWORD'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_BACKEND_PASSWORD_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_DISABLE_THE_CREATION_OF_NEW_ADMINISTRATOR'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_DISABLE_THE_CREATION_OF_NEW_ADMINISTRATOR_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_PROTECT_ADMINISTRATOR_DETAILS'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_PROTECT_ADMINISTRATOR_DETAILS_NOTE'); ?></em></p>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-2 alert alert-info">
				<div class="mo_boot_col-sm-12">
					<strong><?php echo Text::_('COM_JOOMSHIELD_NOTIFICATIONS_ALERTS_TO_ADMIN_FOR_ANY_CHANGES'); ?></strong><br>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_MONITOR_EXTENSIONS_AND_BE_NOTIFIED_WHEN_THE_PLUGIN_HAVE_BEEN_ADDED_OR_REMOVED_TO_PARTICULAR_EMAIL_ADDRESS'); ?></em></p>
				</div>
			</div>
		</div>
		<?php
	}
}
