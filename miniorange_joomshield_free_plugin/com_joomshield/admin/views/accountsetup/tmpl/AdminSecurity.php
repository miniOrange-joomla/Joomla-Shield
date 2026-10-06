<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

class AdminSecurity
{
	public static function renderFeatureAccessBanner($tab)
	{
		if (!JoomShieldFeatureLock::isEnabled())
		{
			return;
		}

		if (JoomShieldFeatureLock::isUnlocked())
		{
			$expiresAt = JoomShieldFeatureLock::getExpiresAt();
			?>
			<div class="mo_boot_row mo_boot_mb-3">
				<div class="mo_boot_col-sm-12 mo_boot_px-0">
					<div class="mo_js_alert mo_js_alert-success mo_boot_mb-0 mo_js_feature_lock_status" data-expires-at="<?php echo (int) $expiresAt; ?>">
						<strong><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_UNLOCKED'); ?></strong>
						<span class="mo_js_feature_lock_countdown"></span>
					</div>
				</div>
			</div>
			<?php

			return;
		}
		?>
		<div class="mo_boot_row mo_boot_mb-2">
			<div class="mo_boot_col-sm-12 mo_boot_px-0">
				<div class="mo_js_alert mo_js_alert-warning">
					<strong><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD'); ?></strong>
					<p class="mo_boot_mb-2"><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_UNLOCK_NOTE'); ?></p>
					<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.unlockFeatureAccess'); ?>">
						<?php echo HTMLHelper::_('form.token'); ?>
						<input type="hidden" name="return_tab" value="<?php echo htmlspecialchars($tab, ENT_QUOTES, 'UTF-8'); ?>">
						<div class="mo_boot_d-flex mo_js_gap-12 mo_js_align_items_center ">
							<div class="mo_boot_col-sm-6 mo_boot_px-0">
								<input id="feature_lock_unlock" class="mo_boot_form-control" type="password" name="feature_lock_unlock" autocomplete="off" required
									aria-label="<?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD'); ?>"
									placeholder="<?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD'); ?>">
							</div>
							<div class="mo_boot_col-sm-2">
								<input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_UNLOCK'); ?>">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	public static function render()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		$loginConfig = JoomShieldUtilities::getLoginSecurityConfig();
		$config = JoomShieldSiteProtection::getConfig();
		$isCustomAdminLogin = (int) ($loginConfig['enable_custom_admin_login'] ?? 0);
		$loginUrlKey = (string) ($loginConfig['access_lgn_urlky'] ?? '');
		$afterFailure = (string) ($loginConfig['after_adm_failure_response'] ?? '');
		$customDestination = (string) ($loginConfig['custom_failure_destination'] ?? '');
		$customErrorMessage = (string) ($loginConfig['custom_message_after_fail'] ?? 'Some error has occurred.');
		$currentAdminLoginUrl = Uri::root() . 'administrator';
		$customAdminLoginUrl = $currentAdminLoginUrl . '/?' . $loginUrlKey;
		$httpChecked = ((int) ($config['admin_http_auth'] ?? 0) === 1) ? 'checked' : '';
		$apacheChecked = ((int) ($config['server_rules_apache'] ?? 0) === 1) ? 'checked' : '';
		$lockChecked = ((int) ($config['feature_lock_enabled'] ?? 0) === 1) ? 'checked' : '';
		$hasFeaturePassword = trim((string) ($config['feature_lock_hash'] ?? '')) !== '';
		$lockedItems = JoomShieldSiteProtection::getLockedItems();
		?>
		<div id="main-div">
			<div class="mo_boot_mt-3">
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-12">
						<h3><?php echo Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL'); ?></h3>
					</div>
					<div class="mo_boot_col-sm-12"><hr></div>
					<div class="mo_boot_col-sm-12">
						<p><?php echo Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL_NOTE'); ?></p>
						<p><em><?php echo Text::_('COM_JOOMSHIELD_CUSTOMIZE_ADMIN_LOGIN_PAGE_URL_SERVER_NOTE'); ?></em></p>
					</div>
				</div>
				<form name="mo_login_security" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.saveLoginSecuritySettings'); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<input type="hidden" name="mo_customize_admin_url" value="custom_adn_url">
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ENABLE_CUSTOM_LOGIN_PAGE_URL'); ?></div>
						<div class="mo_boot_col-sm-5">
							<input type="checkbox" class="enable_custom_login" value="1" onclick="enable_checkbox_url()" name="enable_custom_admin_login" <?php echo $isCustomAdminLogin === 1 ? 'checked' : ''; ?>>
						</div>
					</div>

					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5">
							<?php echo Text::_('COM_JOOMSHIELD_ACCESS_KEY_FOR_YOUR_ADMIN_LOGIN_URL'); ?>
							<span id="custom_login_url_required" class="mo_js_required_marker">*</span>
						</div>
						<div class="mo_boot_col-sm-7">
							<input class="admin_log_url mo_boot_form-control" id="custom_login_url_key" type="text" name="access_lgn_urlky" maxlength="20"
								onchange="login_url_key(this.value)" onkeyup="nospaces(this, 'Please enter the URL without spaces.'); login_url_key(this.value);"
								value="<?php echo htmlspecialchars($loginUrlKey, ENT_QUOTES, 'UTF-8'); ?>" required>
							<small id="custom_login_url_key_limit" class="mo_js_field_limit" hidden><?php echo Text::_('COM_JOOMSHIELD_ACCESS_KEY_MAX_LENGTH'); ?></small>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_CURRENT_ADMIN_LOGIN_URL'); ?></div>
						<div class="mo_boot_col-sm-7" id="custom_admin_url"><?php echo htmlspecialchars($customAdminLoginUrl, ENT_QUOTES, 'UTF-8'); ?></div>
					</div>

					<div id="currentAdminUrl" hidden><?php echo htmlspecialchars($currentAdminLoginUrl, ENT_QUOTES, 'UTF-8'); ?></div>

					<div class="mo_boot_row mo_boot_mb-3">
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_REDIRECT_AFTER_FAILURE_RESPONSE'); ?></div>
						<div class="mo_boot_col-sm-7">
							<select class="mo_security_dropdown mo_boot_form-control" id="failure_response" name="after_adm_failure_response" onchange="redirect_after_failure_dropdown(this.value);">
								<option value="redirect_homepage" <?php echo $afterFailure === 'redirect_homepage' ? 'selected' : ''; ?>><?php echo Text::_('COM_JOOMSHIELD_HOMEPAGE'); ?></option>
								<option value="404_custom_message" <?php echo $afterFailure === '404_custom_message' ? 'selected' : ''; ?>><?php echo Text::_('COM_JOOMSHIELD_CUSTOM_404_MESSAGE'); ?></option>
								<option value="custom_redirect_url" <?php echo $afterFailure === 'custom_redirect_url' ? 'selected' : ''; ?>><?php echo Text::_('COM_JOOMSHIELD_CUSTOM_REDIRECT_URL'); ?></option>
							</select>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3" id="custom_fail_dest" <?php echo $afterFailure === 'custom_redirect_url' ? '' : 'style="display:none;"'; ?>>
						<div class="mo_boot_col-sm-5">
							<?php echo Text::_('COM_JOOMSHIELD_CUSTOM_REDIRECT_URL_AFTER_FAILURE'); ?>
							<span id="custom_fail_dest_required" class="mo_js_required_marker" <?php echo $afterFailure === 'custom_redirect_url' ? '' : 'hidden'; ?>>*</span>
						</div>
						<div class="mo_boot_col-sm-7">
							<input class="mo_boot_form-control" id="custom_fail_dest_id" type="url" name="custom_failure_destination"
								value="<?php echo htmlspecialchars($customDestination, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $afterFailure === 'custom_redirect_url' ? 'required' : ''; ?>>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3" id="custom_message" <?php echo $afterFailure === '404_custom_message' ? '' : 'style="display:none;"'; ?>>
						<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_CUSTOM_ERROR_MESSAGE_AFTER_FAILURE'); ?></div>
						<div class="mo_boot_col-sm-7">
							<textarea class="mo_js_textarea" name="custom_message_after_fail"><?php echo htmlspecialchars($customErrorMessage, ENT_QUOTES, 'UTF-8'); ?></textarea>
						</div>
					</div>
					<div class="mo_boot_row mo_boot_mb-3"><div class="mo_boot_col-sm-12 mo_boot_text-center"><input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>"></div></div>
				</form>
			</div>

			<?php self::renderAdministratorProtection($config, $httpChecked, $apacheChecked); ?>
			<?php self::renderFeaturePassword($hasFeaturePassword, $lockChecked, $lockedItems); ?>
			<?php self::renderTemporaryAdministrators(); ?>
		</div>
		<?php
	}

	private static function renderAdministratorProtection($config, $httpChecked, $apacheChecked)
	{
		$httpEnabled = ((int) ($config['admin_http_auth'] ?? 0) === 1);
		$hasPassword = trim((string) ($config['admin_http_hash'] ?? '')) !== '';
		?>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_row mo_boot_mb-3">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION'); ?></h3>
				</div>
				<div class="mo_boot_col-sm-12"><hr></div>
				<div class="mo_boot_col-sm-12">
					<p><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_NOTE'); ?></p>
				</div>
			</div>
			<?php NginxRules::renderServerRestrictionNotes('admin_http_auth'); ?>
			<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.saveAdminAccessProtection'); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_ENABLE'); ?></div>
					<div class="mo_boot_col-sm-7"><input id="admin_http_auth" class="checkbox_style" type="checkbox" name="admin_http_auth" value="1" onchange="moJoomShieldToggleAdminHttpRequired();" <?php echo $httpChecked; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5 mo_js_admin_cred_label"><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_USERNAME'); ?> <span id="admin_http_user_required" class="mo_js_required_marker" <?php echo $httpEnabled ? '' : 'hidden'; ?>>*</span></div>
					<div class="mo_boot_col-sm-7"><input id="admin_http_user" class="mo_boot_form-control mo_js_visible_input" type="text" name="admin_http_user" value="<?php echo htmlspecialchars((string) ($config['admin_http_user'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_USERNAME'), ENT_QUOTES, 'UTF-8'); ?>" <?php echo $httpEnabled ? 'required' : ''; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5 mo_js_admin_cred_label"><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_PASSWORD'); ?> <span id="admin_http_password_required" class="mo_js_required_marker" <?php echo $httpEnabled && !$hasPassword ? '' : 'hidden'; ?>>*</span></div>
					<div class="mo_boot_col-sm-7">
						<div class="mo_js_password_wrap">
							<input id="admin_http_password" class="mo_boot_form-control mo_js_visible_input" type="password" name="admin_http_password" autocomplete="new-password" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?>" data-has-password="<?php echo $hasPassword ? '1' : '0'; ?>" <?php echo $httpEnabled && !$hasPassword ? 'required' : ''; ?>>
							<button type="button" class="mo_js_password_toggle" id="admin_http_password_toggle" aria-controls="admin_http_password" aria-pressed="false" data-show-label="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_SHOW_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?>" data-hide-label="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_HIDE_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo Text::_('COM_JOOMSHIELD_SHOW_PASSWORD'); ?></button>
						</div>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_WHITELIST'); ?></div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="text" name="admin_http_whitelist_ips" value="<?php echo htmlspecialchars((string) ($config['admin_http_whitelist_ips'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo htmlspecialchars(JoomShieldUtilities::getClientIp(), ENT_QUOTES, 'UTF-8'); ?>"></div>
				</div>
				<?php NginxRules::renderApacheWriteOption($apacheChecked); ?>
				<div class="mo_boot_row mo_boot_mb-3"><div class="mo_boot_col-sm-12 mo_boot_text-center"><input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>"></div></div>
			</form>
		</div>
		<?php
	}

	private static function renderFeaturePassword($hasFeaturePassword, $lockChecked, $lockedItems)
	{
		?>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_row mo_boot_mb-3">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD'); ?></h3>
				</div>
				<div class="mo_boot_col-sm-12"><hr></div>
				<div class="mo_boot_col-sm-12">
					<p><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_NOTE'); ?></p>
				</div>
				<div class="mo_boot_col-sm-12 ">
					<div class="mo_js_alert mo_js_alert-info"><?php echo Text::_($hasFeaturePassword ? 'COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_EXISTING_GUIDE' : 'COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_FIRST_TIME_GUIDE'); ?></div>
				</div>
			</div>

			<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.saveFeatureAccessPassword'); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_ENABLE'); ?></div>
					<div class="mo_boot_col-sm-7"><input class="checkbox_style" type="checkbox" name="feature_lock_enabled" value="1" <?php echo $lockChecked; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5">
		<?php
		echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_CURRENT');

		if ($hasFeaturePassword)
		{
			echo ' <span class="mo_js_required_marker">*</span>';
		}
		?>
					</div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="password" name="feature_lock_current" autocomplete="off" <?php echo $hasFeaturePassword ? 'required' : ''; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5">
		<?php
		echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_NEW');

		if (!$hasFeaturePassword)
		{
			echo ' <span class="mo_js_required_marker">*</span>';
		}
		?>
					</div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="password" name="feature_lock_password" autocomplete="new-password" minlength="8" <?php echo !$hasFeaturePassword ? 'required' : ''; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5">
		<?php
		echo Text::_('COM_JOOMSHIELD_CONFIRM_PASSWORD');

		if (!$hasFeaturePassword)
		{
			echo ' <span class="mo_js_required_marker">*</span>';
		}
		?>
					</div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="password" name="feature_lock_password_confirm" autocomplete="new-password" minlength="8" <?php echo !$hasFeaturePassword ? 'required' : ''; ?>></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-12"><strong><?php echo Text::_('COM_JOOMSHIELD_FEATURE_ACCESS_PASSWORD_ITEMS'); ?></strong></div>
				</div>
		<?php
		foreach (JoomShieldFeatureLock::items() as $key => $label)
		{
			echo '<div class="mo_boot_row mo_boot_mb-3">';
			echo '<div class="mo_boot_col-sm-12">';
			echo '<input class="checkbox_style" type="checkbox" name="feature_lock_items[]" value="';
			echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
			echo '" ';
			echo (in_array($key, $lockedItems, true) ? 'checked' : '');
			echo '>';
			echo Text::_($label);
			echo '</div>';
			echo '</div>';
		}
		?>
				<div class="mo_boot_row mo_boot_mb-3"><div class="mo_boot_col-sm-12 mo_boot_text-center"><input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_SAVE'); ?>"></div></div>
			</form>
		</div>

		<?php
	}

	private static function renderTemporaryAdministrators()
	{
		$rows = JoomShieldTempAdmin::getList();
		?>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_row mo_boot_mb-3">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS'); ?></h3>
				</div>
				<div class="mo_boot_col-sm-12"><hr></div>
				<div class="mo_boot_col-sm-12">
					<p><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_NOTE'); ?></p>
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_PLAN_NOTE'); ?></em></p>
				</div>
			</div>

			<form method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.createTempAdmin'); ?>">
				<?php echo HTMLHelper::_('form.token'); ?>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_USERNAME'); ?> <span class="mo_js_required_marker">*</span></div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="text" name="temp_admin_username" required></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_EMAIL'); ?> <span class="mo_js_required_marker">*</span></div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="email" name="temp_admin_email" required></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_PASSWORD'); ?> <span class="mo_js_required_marker">*</span></div>
					<div class="mo_boot_col-sm-7"><input class="mo_boot_form-control" type="password" name="temp_admin_password" required autocomplete="new-password"></div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-5"><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_HOURS'); ?></div>
					<div class="mo_boot_col-sm-7">
						<select class="mo_boot_form-control" name="temp_admin_hours">
		<?php
		foreach ([1, 4, 8, 24, 72, 168] as $hours)
		{
			echo '<option value="' . $hours . '" ';
			echo ($hours === 24 ? 'selected' : '');
			echo '>' . $hours . '</option>';
		}
		?>
						</select>
					</div>
				</div>
				<div class="mo_boot_row mo_boot_mb-3"><div class="mo_boot_col-sm-12 mo_boot_text-center"><input type="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_CREATE'); ?>"></div></div>
			</form>
			<div class="mo_boot_table-responsive mo_boot_mb-3">
				<table class="table table-striped">
					<thead><tr>
						<th><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_USERNAME'); ?></th>
						<th><?php echo Text::_('COM_JOOMSHIELD_EMAIL'); ?></th>
						<th><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_EXPIRES'); ?></th>
						<th><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_CREATED_BY'); ?></th>
						<th><?php echo Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_STATUS'); ?></th>
						<th><?php echo Text::_('COM_JOOMSHIELD_ACTION'); ?></th>
					</tr></thead>
					<tbody>
		<?php
		if (empty($rows))
		{
			echo '<tr><td colspan="6">' . Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_EMPTY') . '</td></tr>';
		}

		foreach ($rows as $row)
		{
			echo '<tr>';
			echo '<td>' . htmlspecialchars((string) ($row['username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars((string) ($row['email'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars((string) ($row['expires_at'] ?? ''), ENT_QUOTES, 'UTF-8') . ' UTC</td>';
			echo '<td>' . htmlspecialchars((string) ($row['created_by_username'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>' . htmlspecialchars((string) ($row['status'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
			echo '<td>';

			if (($row['status'] ?? '') === 'active')
			{
				echo '<form method="post" action="' . Route::_('index.php?option=com_joomshield&task=sitemaintenance.revokeTempAdmin') . '">';
				echo HTMLHelper::_('form.token');
				echo '<input type="hidden" name="temp_user_id" value="' . (int) $row['user_id'] . '">';
				echo '<input type="submit" class="mo_websecurity_btn_danger" value="' . Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_REVOKE') . '" onclick="return confirm(\'' . Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_REVOKE_CONFIRM') . '\');">';
				echo '</form>';
			}
			else
			{
				echo '<form method="post" action="' . Route::_('index.php?option=com_joomshield&task=sitemaintenance.deleteTempAdmin') . '">';
				echo HTMLHelper::_('form.token');
				echo '<input type="hidden" name="temp_user_id" value="' . (int) $row['user_id'] . '">';
				echo '<input type="submit" class="mo_websecurity_btn_danger" value="' . Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_DELETE') . '" onclick="return confirm(\'' . Text::_('COM_JOOMSHIELD_TEMPORARY_ADMINISTRATOR_ACCESS_DELETE_CONFIRM') . '\');">';
				echo '</form>';
			}

			echo '</td>';
			echo '</tr>';
		}
		?>
					</tbody>
				</table>
			</div>
		</div>

		<?php
	}
}
