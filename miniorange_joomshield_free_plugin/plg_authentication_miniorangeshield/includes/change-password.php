<?php

/**
 * @package    Joomla.Plugins
 * @subpackage plg_authentication_miniorangeshield
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

JoomShieldUtilities::loadJoomShieldLanguage();

$returnUrl = $returnUrl ?? Uri::getInstance()->toString();
$formAction = $formAction ?? htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');
$resetUsername = $resetUsername ?? '';
$resetToken = $resetToken ?? '';
$resetPasswordError = $resetPasswordError ?? '';
$loginUrl = htmlspecialchars($returnUrl, ENT_QUOTES, 'UTF-8');
$cssUrl = htmlspecialchars(Uri::root() . 'plugins/authentication/miniorangeshield/media/css/mo_customer_validation_style.css', ENT_QUOTES, 'UTF-8');
$pageTitle = htmlspecialchars(Text::_('COM_JOOMSHIELD_RESET_YOUR_PASSWORD'), ENT_QUOTES, 'UTF-8');
$showPasswordLabel = htmlspecialchars(Text::_('COM_JOOMSHIELD_SHOW_PASSWORD'), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(Factory::getApplication()->getLanguage()->getTag(), ENT_QUOTES, 'UTF-8'); ?>">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $pageTitle; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo $cssUrl; ?>">
	</head>
	<body class="mo_js_reset_page">
		<div class="mo_js_reset_wrap">
			<div class="mo_js_reset_card">
				<div class="mo_js_reset_icon" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="45" height="40" viewBox="0 0 122.879 118.662">
					  <path fill="#163A8A" fill-rule="evenodd" clip-rule="evenodd" d="M43.101,54.363h4.138v-8.738c0-4.714,1.93-8.999,5.034-12.105v-0.004 c3.105-3.105,7.392-5.034,12.108-5.034c4.714,0,8.999,1.929,12.104,5.034l0.004,0.004c3.104,3.105,5.034,7.392,5.034,12.104v8.738 l3.297,0.001c0.734,0,1.335,0.601,1.335,1.335v28.203c0,0.734-0.602,1.335-1.336,1.335H43.101c-0.734,0-1.336-0.602-1.336-1.335 V55.698C41.765,54.964,42.366,54.363,43.101,54.363L43.101,54.363z M16.682,22.204c-1.781,2.207-3.426,4.551-5.061,7.457 c-5.987,10.645-8.523,22.731-7.49,34.543c1.01,11.537,5.432,22.827,13.375,32.271c2.853,3.392,5.914,6.382,9.132,8.968 c11.112,8.935,24.276,13.341,37.405,13.216c13.134-0.125,26.209-4.784,37.145-13.981c3.189-2.682,6.179-5.727,8.915-9.13 c6.396-7.957,10.512-17.29,12.071-27.138c1.532-9.672,0.595-19.829-3.069-29.655c-3.487-9.355-8.814-17.685-15.775-24.206 C96.695,8.333,88.593,3.755,79.196,1.483c-2.943-0.712-5.939-1.177-8.991-1.374c-3.062-0.197-6.193-0.131-9.401,0.224 c-2.011,0.222-3.459,2.03-3.238,4.041c0.222,2.01,2.03,3.459,4.04,3.237c2.783-0.308,5.495-0.366,8.141-0.195 c2.654,0.171,5.23,0.568,7.731,1.174c8.106,1.959,15.104,5.914,20.838,11.288c6.138,5.751,10.847,13.125,13.941,21.427 c3.212,8.613,4.035,17.505,2.696,25.959c-1.36,8.589-4.957,16.739-10.553,23.699c-2.469,3.071-5.121,5.78-7.912,8.127 c-9.591,8.067-21.031,12.153-32.502,12.263c-11.473,0.109-23.001-3.762-32.764-11.61c-2.895-2.328-5.621-4.983-8.129-7.966 c-6.917-8.224-10.771-18.092-11.655-28.202c-0.908-10.375,1.317-20.988,6.572-30.331c1.586-2.82,3.211-5.071,5.013-7.241 l0.533,14.696c0.071,2.018,1.765,3.596,3.782,3.524s3.596-1.765,3.524-3.782l-0.85-23.419c-0.071-2.019-1.765-3.596-3.782-3.525 c-0.126,0.005-0.25,0.016-0.372,0.032v-0.003L3.157,16.715c-2.001,0.277-3.399,2.125-3.122,4.126 c0.276,2.002,2.124,3.4,4.126,3.123L16.682,22.204L16.682,22.204L16.682,22.204z M53.899,54.363h20.963v-8.834 c0-2.883-1.18-5.504-3.077-7.403l-0.002,0.001c-1.899-1.899-4.521-3.08-7.402-3.08c-2.883,0-5.504,1.18-7.404,3.078 c-1.898,1.899-3.077,4.521-3.077,7.404V54.363L53.899,54.363L53.899,54.363z M64.465,69.795l2.116,9.764l-5.799,0.024l1.701-9.895 c-1.584-0.509-2.733-1.993-2.733-3.747c0-2.171,1.76-3.931,3.932-3.931c2.17,0,3.931,1.76,3.931,3.931 C67.612,67.845,66.261,69.433,64.465,69.795L64.465,69.795L64.465,69.795z"/>
					</svg>
				</div>
				<h1 class="mo_js_reset_title"><?php echo $pageTitle; ?></h1>
				<p class="mo_js_reset_note"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_STRONG_PASSWORD_RESET_INTRO'), ENT_QUOTES, 'UTF-8'); ?></p>

<?php if ($resetPasswordError !== '')
{
	?>
					<div class="mo_js_reset_error" id="error_message"><?php echo $resetPasswordError; ?></div>
	<?php
}
else
{
	?>
					<div class="mo_js_reset_error" id="error_message" hidden></div>
	<?php
}
?>

<?php if ($resetUsername !== '')
{
	?>
					<form name="f" method="post" id="change_password_form" class="mo_js_reset_form" action="<?php echo $formAction; ?>">
						<?php echo HTMLHelper::_('form.token'); ?>
						<input type="hidden" name="option_change_password" value="mo_jnsp_change_password">
						<input type="hidden" name="return_url" value="<?php echo $loginUrl; ?>">
						<input type="hidden" name="username" value="<?php echo htmlspecialchars($resetUsername, ENT_QUOTES, 'UTF-8'); ?>">
						<input type="hidden" name="joomshield_reset_token" value="<?php echo htmlspecialchars($resetToken, ENT_QUOTES, 'UTF-8'); ?>">
						<label class="mo_js_reset_label" for="new_password"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_NEW_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?></label>
						<div class="mo_js_reset_field">
							<input type="password" name="new_password" id="new_password" class="mo_js_reset_input" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_PLACEHOLDER'), ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="new-password">
							<button type="button" class="mo_js_reset_toggle" data-target="new_password" aria-label="<?php echo $showPasswordLabel; ?>">
								<span class="mo_js_reset_eye mo_js_reset_eye_show" aria-hidden="true">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M2.5 12s3.6-6.5 9.5-6.5S21.5 12 21.5 12 17.9 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.6"/></svg>
								</span>
								<span class="mo_js_reset_eye mo_js_reset_eye_hide" aria-hidden="true">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 3l18 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M9.5 6.2A9.6 9.6 0 0 1 12 5.5c5.9 0 9.5 6.5 9.5 6.5a16 16 0 0 1-3.2 3.8M6.4 8.7A15.6 15.6 0 0 0 2.5 12S6.1 18.5 12 18.5c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
								</span>
							</button>
						</div>
						<div class="mo_js_reset_meter" aria-hidden="true">
							<span class="mo_js_reset_meter_bar" data-bar="1"></span>
							<span class="mo_js_reset_meter_bar" data-bar="2"></span>
							<span class="mo_js_reset_meter_bar" data-bar="3"></span>
							<span class="mo_js_reset_meter_bar" data-bar="4"></span>
						</div>
						<p class="mo_js_reset_strength" id="password_strength_label"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH'), ENT_QUOTES, 'UTF-8'); ?>: <span id="password_strength_value"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_EMPTY'), ENT_QUOTES, 'UTF-8'); ?></span></p>
						<div class="mo_js_reset_rules">
							<div class="mo_js_reset_rule" data-rule="length">
								<span class="mo_js_reset_check" aria-hidden="true"></span>
								<span><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_CRITERIA_LENGTH'), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
							<div class="mo_js_reset_rule" data-rule="case">
								<span class="mo_js_reset_check" aria-hidden="true"></span>
								<span><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_CRITERIA_CASE'), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
							<div class="mo_js_reset_rule" data-rule="number">
								<span class="mo_js_reset_check" aria-hidden="true"></span>
								<span><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_CRITERIA_NUMBER'), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
							<div class="mo_js_reset_rule" data-rule="special">
								<span class="mo_js_reset_check" aria-hidden="true"></span>
								<span><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_PASSWORD_CRITERIA_SPECIAL'), ENT_QUOTES, 'UTF-8'); ?></span>
							</div>
						</div>
						<label class="mo_js_reset_label" for="confirm_password"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_CONFIRM_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?></label>
						<div class="mo_js_reset_field">
							<input type="password" name="confirm_password" id="confirm_password" class="mo_js_reset_input" placeholder="<?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_CONFIRM_PASSWORD_PLACEHOLDER'), ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="new-password">
							<button type="button" class="mo_js_reset_toggle" data-target="confirm_password" aria-label="<?php echo $showPasswordLabel; ?>">
								<span class="mo_js_reset_eye mo_js_reset_eye_show" aria-hidden="true">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M2.5 12s3.6-6.5 9.5-6.5S21.5 12 21.5 12 17.9 18.5 12 18.5 2.5 12 2.5 12Z" stroke="currentColor" stroke-width="1.6"/><circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.6"/></svg>
								</span>
								<span class="mo_js_reset_eye mo_js_reset_eye_hide" aria-hidden="true">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M3 3l18 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><path d="M9.5 6.2A9.6 9.6 0 0 1 12 5.5c5.9 0 9.5 6.5 9.5 6.5a16 16 0 0 1-3.2 3.8M6.4 8.7A15.6 15.6 0 0 0 2.5 12S6.1 18.5 12 18.5c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
								</span>
							</button>
						</div>
						<div class="mo_js_reset_match" id="divCheckPasswordMatch"></div>
						<button type="submit" name="change_password_btn" id="change_password_btn" class="mo_js_reset_submit">
							<span><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_UPDATE_PASSWORD'), ENT_QUOTES, 'UTF-8'); ?></span>
						</button>
					</form>
	<?php
}
?>
				<a class="mo_js_reset_back" href="<?php echo $loginUrl; ?>"><?php echo htmlspecialchars(Text::_('COM_JOOMSHIELD_BACK_TO_LOGIN'), ENT_QUOTES, 'UTF-8'); ?></a>
			</div>
		</div>
		<script>
			(function () {
				var matchLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORDS_MATCH')); ?>;
				var mismatchLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORDS_DO_NOT_MATCH')); ?>;
				var strongLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PLEASE_SELECT_STRONG_PASSWORD')); ?>;
				var lengthLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_BE_MINIMUM_12_CHARACTERS')); ?>;
				var caseLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_CAPITAL_AND_ONE_SMALL_LETTER')); ?>;
				var numberLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_NUMERIC_CHARACTER')); ?>;
				var specialLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_SHOULD_CONTAIN_AT_LEAST_ONE_SPECIAL_CHARACTER')); ?>;
				var showLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_SHOW_PASSWORD')); ?>;
				var hideLabel = <?php echo json_encode(Text::_('COM_JOOMSHIELD_HIDE_PASSWORD')); ?>;
				var strengthEmpty = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_EMPTY')); ?>;
				var strengthWeak = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_WEAK')); ?>;
				var strengthFair = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_FAIR')); ?>;
				var strengthGood = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_GOOD')); ?>;
				var strengthStrong = <?php echo json_encode(Text::_('COM_JOOMSHIELD_PASSWORD_STRENGTH_STRONG')); ?>;

				function getRules(password) {
					return {
						length: password.length >= 12,
						case: /[a-z]/.test(password) && /[A-Z]/.test(password),
						number: /\d/.test(password),
						special: /[!@#$%^&*?_~()\-]/.test(password)
					};
				}

				function showError(message) {
					var box = document.getElementById('error_message');
					if (!box) {
						return;
					}
					box.innerHTML = message;
					box.hidden = false;
				}

				function updateStrength(password) {
					var rules = getRules(password);
					var score = 0;
					['length', 'case', 'number', 'special'].forEach(function (rule) {
						var row = document.querySelector('.mo_js_reset_rule[data-rule="' + rule + '"]');
						if (row) {
							row.classList.toggle('is-valid', rules[rule]);
						}
						if (rules[rule]) {
							score++;
						}
					});

					document.querySelectorAll('.mo_js_reset_meter_bar').forEach(function (bar) {
						bar.classList.toggle('is-on', score >= parseInt(bar.getAttribute('data-bar'), 10));
						bar.className = 'mo_js_reset_meter_bar' + (score >= parseInt(bar.getAttribute('data-bar'), 10) ? ' is-on is-score-' + score : '');
					});

					var value = document.getElementById('password_strength_value');
					if (!value) {
						return;
					}
					if (password === '') {
						value.textContent = strengthEmpty;
					} else if (score <= 1) {
						value.textContent = strengthWeak;
					} else if (score === 2) {
						value.textContent = strengthFair;
					} else if (score === 3) {
						value.textContent = strengthGood;
					} else {
						value.textContent = strengthStrong;
					}
				}

				document.querySelectorAll('.mo_js_reset_toggle').forEach(function (button) {
					button.addEventListener('click', function () {
						var field = document.getElementById(button.getAttribute('data-target'));
						if (!field) {
							return;
						}
						var isPassword = field.getAttribute('type') === 'password';
						field.setAttribute('type', isPassword ? 'text' : 'password');
						button.classList.toggle('is-visible', isPassword);
						button.setAttribute('aria-label', isPassword ? hideLabel : showLabel);
					});
				});

				var passwordField = document.getElementById('new_password');
				var confirmField = document.getElementById('confirm_password');
				var matchBox = document.getElementById('divCheckPasswordMatch');

				if (passwordField) {
					passwordField.addEventListener('input', function () {
						updateStrength(this.value);
						if (confirmField && matchBox && confirmField.value !== '') {
							matchBox.textContent = this.value === confirmField.value ? matchLabel : mismatchLabel;
							matchBox.className = 'mo_js_reset_match' + (this.value === confirmField.value ? ' is-match' : ' is-mismatch');
						}
					});
				}

				if (confirmField) {
					confirmField.addEventListener('input', function () {
						if (!matchBox || !passwordField) {
							return;
						}
						matchBox.textContent = passwordField.value === this.value ? matchLabel : mismatchLabel;
						matchBox.className = 'mo_js_reset_match' + (passwordField.value === this.value ? ' is-match' : ' is-mismatch');
					});
				}

				var form = document.getElementById('change_password_form');
				if (!form) {
					return;
				}

				form.addEventListener('submit', function (event) {
					var password = document.getElementById('new_password').value;
					var confirmPassword = document.getElementById('confirm_password').value;
					var errors = [];

					if (password !== confirmPassword) {
						event.preventDefault();
						showError(mismatchLabel);
						return;
					}

					var rules = getRules(password);
					if (!rules.length) {
						errors.push(lengthLabel);
					}
					if (!rules.case) {
						errors.push(caseLabel);
					}
					if (!rules.number) {
						errors.push(numberLabel);
					}
					if (!rules.special) {
						errors.push(specialLabel);
					}

					if (errors.length > 0) {
						event.preventDefault();
						showError('<b>' + strongLabel + '</b><ul><li>' + errors.join('</li><li>') + '</li></ul>');
					}
				});
			})();
		</script>
	</body>
</html>
