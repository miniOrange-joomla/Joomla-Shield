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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

class MoJnspSupport
{
	public static function moJnspSupport()
	{
		?>

	<div id="sp_support" class="mo_boot_col-sm-12">
		<div class="mo_boot_row mo_boot_mt-3">
			<div class="mo_boot_col-sm-12">
				<h2><?php echo Text::_('COM_JOOMSHIELD_SUPPORT_FEATURE_REQUEST'); ?></h2>
			</div>
		</div><hr>

		<form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=mocontactus.contactUs'); ?>">
			<?php echo HTMLHelper::_('form.token'); ?>
			<div class="mo_boot_col-sm-12">
				<?php echo Text::_('COM_JOOMSHIELD_NEED_ANY_HELP_WE_CAN_HELP_YOU_WITH_CONFIGURING_THE_PLUGIN'); ?>
			</div><br>

			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-2">
						<strong><?php echo Text::_('COM_JOOMSHIELD_EMAIL'); ?><span class="mo_boot_text-red">*</span></strong>
					</div>
					<div class="mo_boot_col-sm-8">
						<input type="email" class="mo_boot_form-control" name="query_email" placeholder="Enter your email" required/>
					</div>
				</div>

				<div class="mo_boot_row mo_boot_mt-2">
					<div class="mo_boot_col-sm-2">
						<strong><?php echo Text::_('COM_JOOMSHIELD_PHONE'); ?></strong>
					</div>
					<div class="mo_boot_col-sm-8">
						<input type="tel" name="query_phone" class="mo_boot_form-control mo_jnsp_query_phone" id="mo_jnsp_query_phone" placeholder="Enter your phone with country code" inputmode="tel" autocomplete="tel" pattern="[0-9+ ]*" oninput="mo_jnsp_valid(this)" />
					</div>
				</div>

				<div class="mo_boot_row mo_boot_mt-2">
					<div class="mo_boot_col-sm-2">
						<strong><?php echo Text::_('COM_JOOMSHIELD_QUERY'); ?><span class="mo_boot_text-red">*</span></strong>
					</div>
					<div class="mo_boot_col-sm-8">
						<textarea class="mo_js_textarea" name="query" rows="5" placeholder="Write your query here" required></textarea>
					</div>
				</div><br>
			</div>


			<input type="hidden" name="option1" value="mo_jnsp_send_query"/>
			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12 mo_boot_text-center">
					<input type="submit" name="send_query" value="<?php echo Text::_('COM_JOOMSHIELD_SUBMIT_QUERY'); ?>" class=" mo_websecurity_btn"/>
				</div>
			</div><br>
			<p class="mo_boot_text-center"><?php echo Text::_('COM_JOOMSHIELD_IF_YOU_WANT_CUSTOM_FEATURES_IN_THE_PLUGIN_JUST_DROP_AN_EMAIL_TO'); ?> <a href="mailto:joomlasupport@xecurify.com"> joomlasupport@xecurify.com</a></p>
		</form>

	</div>
	<script>
		//jQuery("#query_phone").intlTelInput();
		function mo_jnsp_valid(f) {
			f.value = f.value.replace(/[^0-9+ ]/g, '');
		}
	</script>
		<?php
	}
}
