<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class LoginSecurity
{
	public static function moNetworksecurityLoginSecurityForm()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$config = JoomShieldUtilities::getLoginSecurityConfig();
		$strongPasswordChecked = ((int) ($config['enforce_strong_password_login'] ?? 0) === 1) ? 'checked' : '';
		?>
		<div id="main-div">
			<form name="mo_enforce_strong_password_form" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.saveLoginSecuritySettings'); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
				<input type="hidden" name="mo_enforce_strong_password" value="mo_jnsp_enforce_strong_password">
				<div class="mo_boot_row mo_boot_mt-3">
					<div class="mo_boot_col-sm-12">
						<h3><?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS'); ?></h3>
					</div>
				</div>
				<hr>
				<p><?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS_NOTE'); ?></p>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<input class="checkbox_style" type="checkbox" name="enforce_strong_password_login" value="1" <?php echo $strongPasswordChecked; ?>>
						<?php echo Text::_('COM_JOOMSHIELD_ENABLE_STRONG_PASSWORDS'); ?>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mt-2">
					<div class="mo_boot_col-sm-12">
						<a href="#!enforce_strong_pass_instr_login" onclick="collapse_link('enforce_strong_pass_instr_login')">
							<strong><?php echo Text::_('COM_JOOMSHIELD_YOU_CAN_RESTRICT_THE_FOLLOWING_CONDITIONS_TO_USER_FOR_STRONG_PASSWORD'); ?></strong>
						</a>
						<div id="enforce_strong_pass_instr_login" style="display:none;">
							<ul>
								<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_CAPITAL_AND_ONE_SMALL_LETTER'); ?></li>
								<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_BE_MINIMUM_12_CHARACTERS'); ?></li>
								<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_NUMERIC_CHARACTER'); ?></li>
								<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_SPECIAL_CHARACTER'); ?></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mt-3">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>">
					</div>
				</div>
			</form>
			<div class="mo_boot_row mo_boot_mt-4">
				<div class="mo_boot_col-sm-12">
					<h3>
						<?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION'); ?>
						<sup class="mo_js_premium_crown"><a href="index.php?option=com_joomshield&tab=license_plans" rel="noopener noreferrer" onclick="event.stopPropagation();" aria-label="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'), ENT_QUOTES, 'UTF-8'); ?>"><img src="<?php echo Uri::base(); ?>components/com_joomshield/assets/images/premium_crown.png" alt="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'), ENT_QUOTES, 'UTF-8'); ?>" class="mo_js_premium_crown_img"><span class="mo_js_premium_tooltip"><?php echo Text::_('COM_JOOMSHIELD_AVAILABLE_IN_PREMIUM_PLAN'); ?></span></a></sup>
					</h3>
					<hr>
					<p><?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION_LOGIN_PROTECTION_NOTE'); ?></p>
				</div>
			</div>
			<form name="mo_brute_force_protection" method="post">
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ENABLE_BRUTE_FORCE_PROTECTION'); ?></div>
					<div class="mo_boot_col-sm-7">
						<input class="checkbox_style" type="checkbox" name="enable_brute_force_protection" value="1" disabled>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ALLOWED_LOGIN_ATTEMPTS_BEFORE_BLOCKING_AN_IP'); ?></div>
					<div class="mo_boot_col-sm-7">
						<input class="mo_boot_form-control" type="number" name="allowed_login_attempts" min="1" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_ENTER_NO_OF_LOGIN_ATTEMPTS'), ENT_QUOTES, 'UTF-8'); ?>" disabled>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_TIME_PERIOD_FOR_WHICH_IP_SHOULD_BE_BLOCKED'); ?></div>
					<div class="mo_boot_col-sm-7">
						<div class="mo_boot_d-flex mo_js_gap-12 mo_js_align_items_center">
							<select class="mo_security_dropdown mo_boot_form-control" name="ip_block_time_unit" disabled>
								<option value="" selected></option>
								<option value="minutes"><?php echo Text::_('COM_JOOMSHIELD_MINUTES'); ?></option>
								<option value="hours"><?php echo Text::_('COM_JOOMSHIELD_HOURS'); ?></option>
								<option value="days"><?php echo Text::_('COM_JOOMSHIELD_DAYS'); ?></option>
								<option value="permanently"><?php echo Text::_('COM_JOOMSHIELD_PERMANENTLY'); ?></option>
							</select>
							<input class="mo_boot_form-control" type="number" name="ip_block_time_value" min="1" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_HOW_MANY'), ENT_QUOTES, 'UTF-8'); ?>" disabled>
						</div>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_SHOW_REMAINING_LOGIN_ATTEMPTS_TO_USER'); ?></div>
					<div class="mo_boot_col-sm-7">
						<input class="checkbox_style" type="checkbox" name="show_remaining_login_attempts" value="1" disabled>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_BYPASS_FOR_ADMIN_CONSOLE_LOGIN'); ?></div>
					<div class="mo_boot_col-sm-7">
						<input class="checkbox_style" type="checkbox" name="bypass_admin_console_login" value="1" disabled>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-12 mo_boot_text-center">
						<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>" disabled>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}
