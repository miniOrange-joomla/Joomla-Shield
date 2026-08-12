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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

class LoginSecurity
{
	public static function moNetworksecurityLoginSecurityForm()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$configResult         = JoomShieldUtilities::getLoginSecurityConfig();
		$timeOfBlockingType   = 'days';
		$timeOfBlockingVal    = 0;

		$isCustomAdminLoign   = $configResult['enable_custom_admin_login'] ?? 0;
		$loginUrlKey          = $configResult['access_lgn_urlky'] ?? '';
		$afterFailure         = $configResult['after_adm_failure_response'] ?? '';
		$customDest           = $configResult['custom_failure_destination'] ?? '';
		$customErrMessage     = $configResult['custom_message_after_fail'] ?? 'Some error has occurred.';
		$strongPassword       = $configResult['enforce_strong_password_login'] ?? 0;

		if (empty(trim($customErrMessage)))
		{
			$customErrMessage = 'Some error has been occurred.';
		}

		$baseUrl              = Uri::root();
		$currentAdminLoginUrl = $baseUrl . 'administrator';
		$customAdminLoignUrl  = $currentAdminLoginUrl . '/?' . $loginUrlKey;

		$customLoginChecked       = (1 == $isCustomAdminLoign) ? 'checked' : '';
		$redirectHomepageSelected = ('redirect_homepage' === $afterFailure) ? 'selected' : '';
		$custom404Selected        = ('404_custom_message' === $afterFailure) ? 'selected' : '';
		$customRedirectSelected   = ('custom_redirect_url' === $afterFailure) ? 'selected' : '';
		$customFailDestStyle      = ('custom_redirect_url' !== $afterFailure) ? 'style="display:none;"' : '';
		$customMessageStyle       = ('404_custom_message' !== $afterFailure) ? 'style="display:none;"' : '';
		$strongPasswordChecked    = (1 == $strongPassword) ? 'checked' : '';
		$permanentSelected        = ('permanent' === $timeOfBlockingType) ? 'selected' : '';
		$monthsSelected           = ('months' === $timeOfBlockingType) ? 'selected' : '';
		$daysSelected             = ('days' === $timeOfBlockingType) ? 'selected' : '';
		$hoursSelected            = ('hours' === $timeOfBlockingType) ? 'selected' : '';
		$timeBlockingInputAttr    = ('permanent' === $timeOfBlockingType) ? 'hidden' : '';
		?>

		<div id="main-div">
			<div class="mo_boot_row mo_boot_mt-3">
				<div class="mo_boot_col-sm-12">
					<h3 style="display: inline-block;">
						<?php echo Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL'); ?>
					</h3>
				</div>
			</div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12"><hr></div>
			</div>

			<form name="mo_login_security" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.saveLoginSecuritySettings'); ?> ">
				<input type="hidden" name="mo_customize_admin_url" value="custom_adn_url" id="ctm_adm_url">

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<?php echo Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL_NOTE'); ?>
					</div>
				</div><br>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_ENABLE_CUSTOM_LOGIN_PAGE_URL'); ?> :
						<p><small>(<strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?></strong><?php echo Text::_('COM_JOOMSHIELD_AFTER_ENABLING_THIS_YOU_WON_T_BE_ABLE_TO_LOGIN_USING'); ?>)</small></p>
					</div>
					<div class="mo_boot_col-sm-5">
						<input type="checkbox" class="enable_custom_login" value="1" onclick="enable_checkbox_url()" name="enable_custom_admin_login"
							<?php echo $customLoginChecked; ?> >
					</div>
				</div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_ACCESS_KEY_FOR_YOUR_ADMIN_LOGIN_URL'); ?>
					</div>
					<div class="mo_boot_col-sm-5">
						<input class="admin_log_url mo_boot_form-control" id="custom_login_url_key"
							   type="text" name="access_lgn_urlky" onchange="login_url_key(this.value)" onkeyup="nospaces(this,'Please enter the URL without spaces.');login_url_key(this.value);"
							   placeholder="Enter Key" value="<?php echo $loginUrlKey; ?>" required/>
					</div>
					<div class="mo_boot_col-sm-1">
						<em class="fa-solid fa-copy mo_copy copytooltip" onclick="copyElement('#custom_login_url_key');" ;>
							<span class="copytooltiptext"><?php echo Text::_('COM_JOOMSHIELD_COPIED'); ?></span>
						</em>
					</div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_ADMIN_LOGIN_URL'); ?>
					</div>
					<div class="mo_boot_col-sm-5" id="currentAdminUrl"><?php echo $currentAdminLoginUrl;?></div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_CURRENT_ADMIN_LOGIN_URL'); ?>
					</div>
					<div class="mo_boot_col-sm-5" id="custom_admin_url"><?php echo $customAdminLoignUrl; ?>
					</div>
					<div class="mo_boot_col-sm-1">
						<em class="fa-solid fa-copy mo_copy copytooltip" onclick="copyToClipboard('#custom_admin_url');" >
							<span class="copytooltiptext"><?php echo Text::_('COM_JOOMSHIELD_COPIED'); ?></span>
						</em>
					</div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_REDIRECT_AFTER_FAILURE_RESPONSE'); ?>
					</div>
					<div class="mo_boot_col-sm-5">
						<select class="mo_security_dropdown redirect_after_failure mo_boot_form-control" id="failure_response"
								name="after_adm_failure_response" onchange="redirect_after_failure_dropdown(this.value);" style="width: 100% !important;">
							<option value="redirect_homepage" <?php echo $redirectHomepageSelected; ?>>
								<?php echo Text::_('COM_JOOMSHIELD_HOMEPAGE'); ?>
							</option>
							<option value="404_custom_message" <?php echo $custom404Selected; ?>>
								<?php echo Text::_('COM_JOOMSHIELD_CUSTOM_404_MESSAGE'); ?>
							</option>
							<option value="custom_redirect_url" <?php echo $customRedirectSelected; ?>>
								<?php echo Text::_('COM_JOOMSHIELD_CUSTOM_REDIRECT_URL'); ?>
							</option>
						</select>
					</div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row" id="custom_fail_dest" <?php echo $customFailDestStyle; ?> >
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_CUSTOM_REDIRECT_URL_AFTER_FAILURE'); ?>
					</div>
					<div class="mo_boot_col-sm-5">
						<input class="mo_boot_form-control mo_security_textfield custom_url_after_failure" id="custom_fail_dest_id" type="text" name="custom_failure_destination" style="width: 100% !important;" value="<?php echo $customDest; ?>" placeholder="Enter the redirect URL"/>
					</div>
				</div>

				<div class="mo_boot_row"
					 id="custom_message" <?php echo $customMessageStyle; ?>>
					<div class="mo_boot_col-sm-5">
						<?php echo Text::_('COM_JOOMSHIELD_CUSTOM_ERROR_MESSAGE_AFTER_FAILURE'); ?>
					</div>
					<div class="mo_boot_col-sm-5">
						<textarea class="form-control mo_security_textfield 404_message" name="custom_message_after_fail" style="width: 100% !important;"><?php echo $customErrMessage; ?></textarea>
					</div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="Save">
					</div>
				</div>
			</form>

			<br><br>

			<form name="mo_enforce_strong_password_form" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.saveLoginSecuritySettings'); ?> ">
				<input type="hidden" name="mo_enforce_strong_password" value="mo_jnsp_enforce_strong_password" id="mo_enforce_strong_password">

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<h3><?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS'); ?></h3>
					</div>
				</div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12"><hr></div>
				</div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORDS_NOTE'); ?>
					</div>
				</div>
				<div><br></div>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<input class="checkbox_style" type="checkbox" name="enforce_strong_password_login" value="1"
							<?php echo $strongPasswordChecked; ?> style=""><?php echo Text::_('COM_JOOMSHIELD_ENABLE_STRONG_PASSWORDS'); ?><br><br>
					</div>
				</div>
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12">
						<a href="#!enforce_strong_pass_instr_login" onclick="collapse_link('enforce_strong_pass_instr_login')">
							<strong><?php echo Text::_('COM_JOOMSHIELD_YOU_CAN_RESTRICT_THE_FOLLOWING_CONDITIONS_TO_USER_FOR_STRONG_PASSWORD'); ?></strong></a>
						</div>
					</div>
				<div id="enforce_strong_pass_instr_login" class="mo_boot_row" style="display:none">
					<div class="mo_boot_col-sm-12">
						<ul>
							<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_CAPITAL_AND_ONE_SMALL_LETTER'); ?></li>
							<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_BE_MINIMUM_12_CHARACTERS'); ?></li>
							<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_NUMERIC_CHARACTER'); ?></li>
							<li><?php echo Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_SPECIAL_CHARACTER'); ?></li>
						</ul>
					</div>
				</div>

				<div class="mo_boot_row"><br></div>

				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
						<input type="submit" name="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>">
					</div>
				</div>

				<div class="mo_boot_row"><br></div>
			</form><br>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION_LOGIN_PROTECTION'); ?> <sup><a href='index.php?option=com_joomshield&tab=license_plans'><strong>( Premium Feature )</strong></a></sup>
					</h3>
				</div>
			</div>

			<hr>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12">
					<?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION_LOGIN_PROTECTION_NOTE'); ?>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-5">
					<?php echo Text::_('COM_JOOMSHIELD_ENABLE_BRUTE_FORCE_PROTECTION'); ?>
				</div>
				<div class="mo_boot_col-sm-7">
					<input type="checkbox" name="enable_brute_force_protection" class="enable_brute" value="1" disabled>
				</div>
			</div>
			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-5">
					<?php echo Text::_('COM_JOOMSHIELD_ALLOWED_LOGIN_ATTEMPTS_BEFORE_BLOCKING_AN_IP'); ?>
				</div>
				<div class="mo_boot_col-sm-3">
					<input class="form-control mo_security_textfield allowed_login_attempts mo_boot_form-control" type="number" id="allowed_login_attempts"
					   min="1" name="allwoed_no_of_login_attempts" placeholder="<?php echo Text::_('COM_JOOMSHIELD_ENTER_NO_OF_LOGIN_ATTEMPTS'); ?>" value="0" disabled/>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-5">
					<?php echo Text::_('COM_JOOMSHIELD_TIME_PERIOD_FOR_WHICH_IP_SHOULD_BE_BLOCKED'); ?>
				</div>
				<div class="mo_boot_col-sm-3">
					<select name="time_of_blocking_type" class="mo_security_dropdown mo_boot_form-control" disabled>
						<option value="permanent" <?php echo $permanentSelected; ?>>
							<?php echo Text::_('COM_JOOMSHIELD_PERMANENTLY'); ?>
						</option>
						<option value="months" <?php echo $monthsSelected; ?>>
							<?php echo Text::_('COM_JOOMSHIELD_MONTHS'); ?>
						</option>
						<option value="days" <?php echo $daysSelected; ?>>
							<?php echo Text::_('COM_JOOMSHIELD_DAYS'); ?>
						</option>
						<option value="hours" <?php echo $hoursSelected; ?>>
							<?php echo Text::_('COM_JOOMSHIELD_HOURS'); ?>
						</option>
					</select>
				</div>
				<div class="mo_boot_col-sm-3">
					<input class="form-control mo_security_textfield how_many_text mo_boot_form-control" type="number" name="time_of_blocking_value" min="1" placeholder="<?php echo Text::_('COM_JOOMSHIELD_HOW_MANY'); ?>"
						   <?php echo $timeBlockingInputAttr; ?>value="<?php echo $timeOfBlockingVal; ?>" disabled/>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-5">
					<?php echo Text::_('COM_JOOMSHIELD_SHOW_REMAINING_LOGIN_ATTEMPTS_TO_USER'); ?>
				</div>
				<div class="mo_boot_col-sm-7">
					<input type="checkbox" class="show_remaining_log" name="show_remaining_login_attempts" disabled/>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-5">
					<?php echo Text::_('COM_JOOMSHIELD_BYPASS_FOR_ADMIN_CONSOLE_LOGIN'); ?>
				</div>
				<div class="mo_boot_col-sm-7">
					<input type="checkbox" class="bypass_for_admin" name="bf_bypass_for_admin_login" disabled/>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
					<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>" disabled>
				</div>
			</div>

			<div class="mo_boot_row"><br></div>

		</div><br>
		<?php
	}
}
