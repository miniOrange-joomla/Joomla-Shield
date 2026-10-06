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
class Notifications
{
	public static function moNetworksecurityEmailNotificationsForm()
	{
		?>
		<div class="mo_boot_col-sm-12">
			<h3 class="mo_boot_mt-3"> <?php echo Text::_('COM_JOOMSHIELD_EMAIL_NOTIFICATIONS'); ?> <sup class="mo_js_premium_crown"><a href="index.php?option=com_joomshield&tab=license_plans" rel="noopener noreferrer" onclick="event.stopPropagation();" aria-label="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'), ENT_QUOTES, 'UTF-8'); ?>"><img src="<?php echo Uri::base(); ?>components/com_joomshield/assets/images/premium_crown.png" alt="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'), ENT_QUOTES, 'UTF-8'); ?>" class="mo_js_premium_crown_img"><span class="mo_js_premium_tooltip"><?php echo Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'); ?></span></a></sup></h3>
			<hr>
			<form name="mo_email_notifications" method="post">
				<input type="checkbox" class="check_email_notify_admin checkbox_style" value="1" name="check_email_notify_admin" onclick="enable_checkbox_notification()" disabled>
					<?php echo Text::_('COM_JOOMSHIELD_NOTIFY_ADMINISTRATOR_IF_IP_ADDRESS_IS_BLOCKED'); ?><br><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<p><?php echo Text::_('COM_JOOMSHIELD_ENTER_THE_ADMINISTRATOR_EMAIL'); ?></p>
					</div>
					<div class="mo_boot_col-sm-7">
						<textarea id="email_id" name="admin_email_address" class="mo_js_textarea" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_AN_ADMIN_EMAIL_ADDRESS_HERE'); ?>" disabled></textarea>
						</div>
				</div><br>

				<input class="checkbox_style" type="checkbox" value="1" name="check_email_notify_user" disabled><?php echo Text::_('COM_JOOMSHIELD_NOTIFY_USERS_FOR_UNUSUAL_ACTIVITY_WITH_THEIR_ACCOUNT'); ?><br><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12 mo_boot_text-center">
						<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>" disabled>
					</div>
				</div>
			<script>
				function enable_checkbox_notification() {
					var email_notify = document.getElementsByClassName('check_email_notify_admin')[0];
					var admin_email = document.getElementsByClassName('admin_email_address')[0];

					admin_email.disabled = email_notify.checked !== true;
				}
			</script>
			</form>
		</div>
		<?php
	}
}
