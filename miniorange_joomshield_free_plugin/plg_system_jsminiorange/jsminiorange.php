<?php

/**
 * @package    Joomla.System
 * @subpackage plg_system_jsminiorange
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

if (defined('_JEXEC'))
{
	class PlgSystemJsminiorange extends CMSPlugin
	{
		public function onUserAfterLogout($options)
		{
			$loginConfig = JoomShieldUtilities::getLoginSecurityConfig();
			$root = Uri::root();
			$isAdmin = Factory::getApplication()->isClient('administrator');

			if ($isAdmin)
			{
				$urlKey = $loginConfig['access_lgn_urlky'] ?? '';
				$customLink = $root . 'administrator/?' . $urlKey;

				$app = Factory::getApplication();
				$app->redirect($customLink);
			}
		}

		public function onBeforeCompileHead()
		{
			$app = Factory::getApplication();
			$document = $app->getDocument();

			if (method_exists($document, 'getType') && $document->getType() !== 'html')
			{
				return;
			}

			$document->addScript(Uri::root() . 'administrator/components/com_joomshield/assets/js/utility.js');
		}

		public function onAfterInitialise()
		{
			JoomShieldTempAdmin::expireDueUsers();
			JoomShieldAdminAuth::enforce();
			JoomShieldSiteOffline::enforce();

			$post = Factory::getApplication()->input->post->getArray();

			$session = Factory::getSession();
			$userId = Factory::getUser()->id;

			// For Session Timeout handling for customized admin URL.
			if (!($session->isActive()) && $userId == 0)
			{
				$loginConfig = JoomShieldUtilities::getLoginSecurityConfig();
				$customAdminLoginEnabled = $loginConfig['enable_custom_admin_login'] ?? 0;
				$root = Uri::root();

				if ($customAdminLoginEnabled)
				{
					$urlKey = $loginConfig['access_lgn_urlky'] ?? '';
					$customLink = $root . 'administrator/?' . $urlKey;
				}
				else
				{
					$customLink = $root . 'administrator';
				}

				echo '<meta http-equiv="refresh" content="0; url=' . $customLink . '">';
				exit();
			}

			if (isset($post['mojsp_feedback']) && $this->isUninstallFeedbackAllowed())
			{
				if (!Session::checkToken('post'))
				{
					throw new \RuntimeException(Text::_('JINVALID_TOKEN_NOTICE'), 403);
				}

				JoomShieldUtilities::getFeedbackForm($post);

				Factory::getApplication()->redirect(Uri::base() . 'index.php?option=com_installer&view=manage');
			}
			else
			{
				if (isset($post['option_change_password']) && $post['option_change_password'] === 'mo_jnsp_change_password')
				{
					JoomShieldUtilities::handleChangePassword();
				}

				JoomShieldUtilities::enforceStrongPasswordLogin();

				$userIpAddress = JoomShieldUtilities::getClientIp();
				$isIpBlocked = JoomShieldUtilities::isIpBlocked($userIpAddress);

				if ($isIpBlocked)
				{
					JoomShieldUtilities::showErrorMessage();
				}

				$val = 0;
				JoomShieldUtilities::checkUrl($val);
			}
		}

		// The uninstall feedback form removes extensions, so it is limited to a signed-in
		// back-end user who is already allowed to uninstall through com_installer.
		private function isUninstallFeedbackAllowed()
		{
			$app = Factory::getApplication();

			if (!$app->isClient('administrator'))
			{
				return false;
			}

			$user = $this->getRequestUser();

			if (empty($user) || empty($user->id) || !empty($user->guest))
			{
				return false;
			}

			return (bool) $user->authorise('core.delete', 'com_installer');
		}

		private function getRequestUser()
		{
			$app = Factory::getApplication();

			if (method_exists($app, 'getIdentity'))
			{
				$user = $app->getIdentity();

				if (!empty($user))
				{
					return $user;
				}
			}

			return Factory::getUser();
		}

		public function onAfterRender()
		{
			JoomShieldUrlRewrite::rewriteFrontend();
		}

		public function onAfterRoute()
		{
			if (!JoomShieldCompat::isAdministrator())
			{
				return;
			}

			if (!JoomShieldTempAdmin::isCurrentUserTemporary())
			{
				return;
			}

			$app = Factory::getApplication();
			$lang = Factory::getLanguage();
			$lang->load('com_joomshield', JPATH_ADMINISTRATOR);
			$user = JoomShieldCompat::getUser();

			if (!JoomShieldTempAdmin::getActiveRecord((int) $user->id))
			{
				$app->logout((int) $user->id);
				$app->enqueueMessage(Text::_('COM_JOOMSHIELD_TEMP_ADMIN_ACCESS_ENDED'), 'warning');
				$loginConfig = JoomShieldUtilities::getLoginSecurityConfig();
				$redirect = Uri::root() . 'administrator';
				$urlKey = trim((string) ($loginConfig['access_lgn_urlky'] ?? ''));

				if (!empty($loginConfig['enable_custom_admin_login']) && $urlKey !== '')
				{
					$redirect .= '/?' . rawurlencode($urlKey);
				}

				$app->redirect($redirect);

				return;
			}

			$option = $app->input->getCmd('option', '');

			if ($option === 'com_joomshield')
			{
				$app->enqueueMessage(Text::_('COM_JOOMSHIELD_TEMP_ADMIN_COMPONENT_DENIED'), 'error');
				$app->redirect('index.php');
			}

			if (JoomShieldTempAdmin::isUserDeleteRequest($app))
			{
				$app->enqueueMessage(Text::_('COM_JOOMSHIELD_TEMP_ADMIN_USER_DELETE_DENIED'), 'error');
				$app->redirect('index.php?option=com_users');
			}
		}

		public function onExtensionBeforeInstall($method, $type, $manifest, $eid)
		{
			JoomShieldTempAdmin::denyExtensionChange();
		}


		public function onUserLogin($user, $options = [])
		{
			JoomShieldTempAdmin::expireDueUsers();
		}

		public function onExtensionBeforeUninstall($id)
		{
			JoomShieldTempAdmin::denyExtensionChange();

			if (!Factory::getApplication()->isClient('administrator'))
			{
				return;
			}

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('extension_id');
			$query->from('#__extensions');
			$query->where($db->quoteName('element') . " = " . $db->quote('miniorangeshield'));
			$db->setQuery($query);
			$result = $db->loadColumn();

			$tables = Factory::getDbo()->getTableList();
			$tab = 0;

			foreach ($tables as $table)
			{
				if (strpos($table, "miniorange_networksecurity_customer"))
				{
					$tab = $table;
				}
			}

			if ($tab)
			{
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('uninstall_feedback');
				$query->from('#__miniorange_networksecurity_customer');
				$query->where($db->quoteName('id') . " = " . $db->quote(1));
				$db->setQuery($query);
				$fid = $db->loadColumn();

				$post = Factory::getApplication()->input->post->getArray();
				$tpostData = $post;

				foreach ($fid as $value)
				{
					if ((int) $value === 0)
					{
						foreach ($result as $results)
						{
							if ((int) $results === (int) $id)
							{
								?>

							<div class="form-style-6 ">
								<h1><strong>Feedback for JoomShield</strong></h1>
								<h3>Email: <span style="color:#c52827">*</span></h3>
								<form name="f" method="post" action="" id="mojsp_feedback">
									<input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
									<?php echo HTMLHelper::_('form.token'); ?>
									<div class="mo_boot_col-sm-12">
										<div class="mo_boot_row">
											<input type="email" id="query_email" name="query_email"
												   style="border-radius: 4px;"
												   value=""
												   placeholder="Enter your email" required/>
										</div>
										<h3>What Happened? <span style="color:#c52827">*</span></h3>
										<p style="margin-left:2%">
								<?php
								$deactivateReason = [
									"Does not have the features I'm looking for",
									'Not able to Configure',
									'Bugs in the plugin',
									'Other Reasons:',
								];

								foreach ($deactivateReason as $reasonIndex => $deactivateReasonOption)
								{
									$reasonId = 'deactivate_plg_id_' . (int) $reasonIndex;
									$reasonText = htmlspecialchars($deactivateReasonOption, ENT_QUOTES, 'UTF-8');
									?>
										<div class=" radio " style="padding:1px;margin-left:2%">
											<label style="font-weight:normal;font-size:14.6px"
												   for="<?php echo $reasonId; ?>">
												<input type="radio" name="deactivate_plugin" id="<?php echo $reasonId; ?>"
													   value="<?php echo $reasonText; ?>" required>
												<?php echo $reasonText; ?></label>
										</div>
									<?php
								}
								?>
										<br>
										<div class="mo_boot_row">
											<label for="query_feedback">Additional feedback <span id="query_feedback_required" style="color:#c52827">*</span></label>
											<textarea id="query_feedback" name="query_feedback" rows="4" style="border-radius: 4px;resize: vertical;"
													  cols="50" placeholder="Write your query here" minlength="10" required></textarea>
										</div>
								<?php
								if (isset($tpostData['cid']))
								{
									foreach ($tpostData['cid'] as $key)
									{
										?>
											<input type="hidden" name="result[]" value="<?php echo (int) $key; ?>">

										<?php
									}
								}
								?>

										<br><br>
									   <div class="mojsp_modal-footer">
										   <input type="submit" name="miniorange_feedback_submit" class="button" value="Submit"/>

											<button type="submit"
													id="skip"
													name="skip_feedback"
													value="1"
													class="skip-link"
													formnovalidate
													onclick="skip_feedback_form();">
												Skip feedback
											</button>
										</div>
									</div>
								</form>
							</div>
							<script>
								function skip_feedback_form() {
									var radios = document.querySelectorAll("[id^='deactivate_plg_id']");
									Array.prototype.forEach.call(radios, function (radio) {
										radio.required = false;
										radio.disabled = true;
									});

									var email = document.getElementById("query_email");
									if (email) {
										email.required = false;
									}

									var feedback = document.getElementById("query_feedback");
									if (feedback) {
										feedback.required = false;
									}
								}

								(function () {
									var placeholders = {
										"Does not have the features I'm looking for": "Let us know what feature are you looking for",
										"Not able to Configure": "Not able to Configure? let us know so that we can improve the interface",
										"Bugs in the plugin": "Could you please describe the bug in detail",
										"Other Reasons:": "Can you let us know the reason for deactivation"
									};

									var feedback = document.getElementById("query_feedback");
									var radios = document.querySelectorAll("input[name='deactivate_plugin']");

									Array.prototype.forEach.call(radios, function (radio) {
										radio.addEventListener("change", function () {
											if (!feedback) {
												return;
											}

											feedback.placeholder = placeholders[radio.value] || "Write your query here";
											feedback.required = radio.value === "Other Reasons:";
											var feedbackRequired = document.getElementById("query_feedback_required");
											if (feedbackRequired) {
												feedbackRequired.hidden = !feedback.required;
											}
										});
									});
								})();
							</script>
							<style type="text/css">
								.form-style-6 {
									font: 95% Arial, Helvetica, sans-serif;
									max-width: 400px;
									margin: 10px auto;
									padding: 16px;
									background: #F7F7F7;
								}

								.form-style-6 h1 {
									background: #3D618F;
									padding: 20px 0;
									font-size: 140%;
									font-weight: 300;
									text-align: center;
									color: #fff;
									margin: -16px -16px 16px -16px;
								}

								.form-style-6 input[type="text"],
								.form-style-6 input[type="date"],
								.form-style-6 input[type="datetime"],
								.form-style-6 input[type="email"],
								.form-style-6 input[type="number"],
								.form-style-6 input[type="search"],
								.form-style-6 input[type="time"],
								.form-style-6 input[type="url"],
								.form-style-6 textarea,
								.form-style-6 select {
									-webkit-transition: all 0.30s ease-in-out;
									-moz-transition: all 0.30s ease-in-out;
									-ms-transition: all 0.30s ease-in-out;
									-o-transition: all 0.30s ease-in-out;
									outline: none;
									box-sizing: border-box;
									-webkit-box-sizing: border-box;
									-moz-box-sizing: border-box;
									width: 100%;
									background: #fff;
									margin-bottom: 4%;
									border: 1px solid #ccc;
									padding: 3%;
									color: #555;
									font: 95% Arial, Helvetica, sans-serif;
								}

								.form-style-6 input[type="text"]:focus,
								.form-style-6 input[type="date"]:focus,
								.form-style-6 input[type="datetime"]:focus,
								.form-style-6 input[type="email"]:focus,
								.form-style-6 input[type="number"]:focus,
								.form-style-6 input[type="search"]:focus,
								.form-style-6 input[type="time"]:focus,
								.form-style-6 input[type="url"]:focus,
								.form-style-6 textarea:focus,
								.form-style-6 select:focus {
									box-shadow: 0 0 5px #BFD1EA;
									padding: 3%;
									border: 3% solid #DBE4EF;
								}

								.form-style-6 input[type="submit"],
								.form-style-6 input[type="button"] {
									box-sizing: border-box;
									-webkit-box-sizing: border-box;
									-moz-box-sizing: border-box;
									width: 100%;
									padding: 3%;
									background: #3D618F;
									border-bottom: 2px solid #3D618F;
									border-radius: 4px;
									border-top-style: none;
									border-right-style: none;
									border-left-style: none;
									color: #fff;
								}

								.form-style-6 input[type="submit"]:hover,
								.form-style-6 input[type="button"]:hover {
									background: #1F3047;
									cursor:pointer;
								}
								.mojsp_modal-footer {
									position: relative;
									margin: 15px 0px;

								}
								.primary-submit {
									width: 100%;
								}
								.skip-link {
									position: absolute;
									right: 0;
									bottom: -28px;
									background: none;
									border: none;
									padding: 0;
									font-size: 13px;
									color: #6b7280;
									cursor: pointer;
									text-decoration: underline;
								}
								.skip-link:hover {
									color: #3D618F;
								}
							</style>
								<?php
									exit;
							}
						}
					}
				}
			}
		}
	}
}
