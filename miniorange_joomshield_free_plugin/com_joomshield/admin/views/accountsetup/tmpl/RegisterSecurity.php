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

class RegisterSecurity
{
	public static function moNetworksecurityRegisterSecurityForm()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$configResult    = JoomShieldUtilities::getRegisterSecurityConfig();
		$blockFakeEmails = $configResult['block_fake_emails'] ?? 0;
		$extraEmail      = preg_replace('/\s+/', '', $configResult['mo_email_domains'] ?? '');
		$strongPassword  = $configResult['enforce_strong_password_register'] ?? 0;

		$blockFakeEmailsChecked = (1 == $blockFakeEmails) ? 'checked' : '';
		$strongPasswordChecked  = (1 == $strongPassword) ? 'checked' : '';

		?>
		<div class="mo_boot_col-sm-12">
			<form name="mo_register_security" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=registersecurity.saveRegisterSecuritySettings'); ?>">

				<input type="hidden" name="mo_block_fake_registration" value="block_fake_registration" id="block_fake_registration_id">
				<div class="mo_boot_row">
					<div class="mo_boot-col-sm-12 mo_boot_mt-3">
						<h3><?php echo Text::_('COM_JOOMSHIELD_BLOCK_REGISTRATIONS_FROM_FAKE_USERS'); ?></h3>
					</div>
				</div>
				<hr>

				<div><?php echo Text::_('COM_JOOMSHIELD_TO_BLOCK_SPECIFIC_EMAIL_DOMAINS_FROM_REGISTERING_AS_USERS'); ?></div>
				<br>

				<input type="checkbox" class="mo_blk_fake_emails checkbox_style" name="block_fake_emails" onclick="enable_checkbox_blk_email()" value="1" <?php echo $blockFakeEmailsChecked; ?> > <?php echo Text::_('COM_JOOMSHIELD_ENABLE_BLOCKING_OF_REGISTRATIONS_FROM_SPECIFIC_EMAIL_DOMAINS'); ?><br>

				<div class="mo_boot_row"><br></div>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ENTER_EMAIL_DOMAINS_YOU_WANT_TO_BLOCK'); ?></div>
					<div class="mo_boot_col-sm-7">
						<textarea rows="4" name="mo_email_domains" class="mo_boot_px-2 mo_enable_blk_email form-control mo_security_textfield" required
							placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_SEMICOLON_SEPERATED_DOMAINS_PLACEHOLDER'); ?>"
							oninput="nospaces(this,'Please enter the email domains without spaces.');"><?php echo htmlspecialchars($extraEmail, ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>
				</div>
				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="Save">
					</div>
				</div><br>
			</form>

			<form name="mo_register_security" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=registersecurity.saveRegisterSecuritySettings'); ?>">

				<h3><?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS'); ?></h3>
				<hr>
				<div><?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS_NOTE'); ?></div>
				<br>

				<input class="checkbox_style" type="checkbox" name="enforce_strong_password_register" value="1" <?php echo $strongPasswordChecked; ?> ><?php echo Text::_('COM_JOOMSHIELD_ENABLE_STRONG_PASSWORDS'); ?><br><br>

				<a href="#!enforce_strong_pass_instr_register" onclick="collapse_link('enforce_strong_pass_instr_register')">
					<strong><?php echo Text::_('COM_JOOMSHIELD_YOU_CAN_RESTRICT_THE_FOLLOWING_CONDITIONS_TO_USER_FOR_STRONG_PASSWORD'); ?></strong></a>
				<div id="enforce_strong_pass_instr_register" style="display: none;">
					<ul><br>
						<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_CAPITAL_AND_ONE_SMALL_LETTER'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_BE_MINIMUM_12_CHARACTERS'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_NUMERIC_CHARACTER'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_SPECIAL_CHARACTER'); ?></li>
					</ul>
				</div>

				<div class="mo_boot_row"><br></div>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>">
					</div>
				</div><br>
			</form>
		</div>
		<?php
	}
}
