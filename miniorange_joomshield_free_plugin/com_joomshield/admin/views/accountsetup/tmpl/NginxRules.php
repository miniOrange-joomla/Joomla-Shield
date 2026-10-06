<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

class NginxRules
{
	public static function moNetworksecurityNginxRules()
	{
		jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

		JoomShieldServerRules::writeGeneratedNginxFile();
		$config = JoomShieldSiteProtection::getConfig();
		$nginxSnippet = JoomShieldServerRules::getNginxSnippet();
		$snippetPath = JoomShieldServerRules::nginxSnippetPath();
		$includeDirective = JoomShieldServerRules::nginxIncludeDirective();
		$copiedLabel = Text::_('COM_JOOMSHIELD_COPIED');
		?>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3 class="mo_boot_mt-3"><?php echo Text::_('COM_JOOMSHIELD_TAB_NGINX_RULES'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_INTRO'); ?></p>
				<?php self::renderDetectedServerHint(); ?>
				<?php self::renderNotesAccordionOpen('joomshield-nginx-rules-steps', 'COM_JOOMSHIELD_NGINX_RULES_STEPS_TITLE'); ?>
					<ol class="mo_js_server_rules_list">
						<li><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_STEP_1'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_STEP_2'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_STEP_3'); ?></li>
						<li><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_STEP_4'); ?></li>
					</ol>
					<p class="mo_boot_mt-2"><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_INCLUDE_NOTE'); ?></p>
					<p class="mo_boot_mb-0"><?php echo Text::_('COM_JOOMSHIELD_NGINX_RULES_DISABLE_NOTE'); ?></p>
				<?php self::renderNotesAccordionClose(); ?>
				<div class="mo_js_server_rules_status mo_boot_mt-3">
					<p><strong><?php echo Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_TITLE'); ?></strong></p>
					<ul class="mo_js_server_rules_list">
						<li><?php echo Text::_('COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE'); ?>:
							<?php echo ((int) ($config['emergency_offline'] ?? 0) === 1)
								? Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_ON')
								: Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_OFF'); ?>
						</li>
						<li><?php echo Text::_('COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION'); ?>:
							<?php echo ((int) ($config['admin_http_auth'] ?? 0) === 1)
								? Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_ON')
								: Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_OFF'); ?>
						</li>
						<li><?php echo Text::_('COM_JOOMSHIELD_WRITE_APACHE_RULES_STATUS'); ?>:
							<?php echo ((int) ($config['server_rules_apache'] ?? 0) === 1)
								? Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_ON')
								: Text::_('COM_JOOMSHIELD_SERVER_RULES_STATUS_OFF'); ?>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row mo_js_nginx_card">
					<div class="mo_boot_col-sm-12 mo_boot_px-0">
						<strong><?php echo Text::_('COM_JOOMSHIELD_NGINX_SNIPPET'); ?></strong>
						<p><strong><?php echo Text::_('COM_JOOMSHIELD_NOTE'); ?> </strong><em><?php echo Text::_('COM_JOOMSHIELD_NGINX_SNIPPET_NOTE'); ?></em></p>
						<textarea id="joomshield_nginx_snippet" class="mo_boot_form-control mo_js_nginx_textarea" rows="16" readonly spellcheck="false"><?php echo htmlspecialchars($nginxSnippet, ENT_QUOTES, 'UTF-8'); ?></textarea>
						<div class="mo_js_nginx_actions">
							<button type="button" class="mo_websecurity_btn" data-copied-text="<?php echo htmlspecialchars($copiedLabel, ENT_QUOTES, 'UTF-8'); ?>" onclick="moJoomShieldCopySelector('#joomshield_nginx_snippet', this);"><?php echo Text::_('COM_JOOMSHIELD_COPY_SNIPPET'); ?></button>
							<a class="mo_websecurity_btn" href="<?php echo Route::_('index.php?option=com_joomshield&task=sitemaintenance.downloadNginxSnippet'); ?>"><?php echo Text::_('COM_JOOMSHIELD_NGINX_SNIPPET_DOWNLOAD'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mo_boot_mt-3">
			<div class="mo_boot_col-sm-12 mo_boot_mt-3">
				<h3><?php echo Text::_('COM_JOOMSHIELD_NGINX_GENERATED_FILE'); ?></h3>
			</div>
			<div class="mo_boot_col-sm-12"><hr></div>
			<div class="mo_boot_col-sm-12">
				<p><?php echo Text::_('COM_JOOMSHIELD_NGINX_GENERATED_PATH'); ?></p>
				<input class="mo_boot_form-control mo_boot_mb-3" type="text" id="joomshield_nginx_path" value="<?php echo htmlspecialchars($snippetPath, ENT_QUOTES, 'UTF-8'); ?>" readonly>
				<p><strong><?php echo Text::_('COM_JOOMSHIELD_NGINX_INCLUDE_DIRECTIVE'); ?></strong></p>
				<textarea id="joomshield_nginx_include" class="mo_boot_form-control mo_js_nginx_textarea" rows="2" readonly spellcheck="false"><?php echo htmlspecialchars($includeDirective, ENT_QUOTES, 'UTF-8'); ?></textarea>
				<div class="mo_js_nginx_actions">
					<button type="button" class="mo_websecurity_btn" data-copied-text="<?php echo htmlspecialchars($copiedLabel, ENT_QUOTES, 'UTF-8'); ?>" onclick="moJoomShieldCopySelector('#joomshield_nginx_include', this);"><?php echo Text::_('COM_JOOMSHIELD_COPY_INCLUDE_DIRECTIVE'); ?></button>
				</div>
			</div>
		</div>

		<?php
	}

	public static function renderApacheWriteOption($apacheChecked)
	{
		?>
		<div class="mo_boot_row mo_boot_mb-3">
			<div class="mo_boot_col-sm-12">
				<input class="checkbox_style" type="checkbox" name="server_rules_apache" value="1" <?php echo $apacheChecked; ?>>
				<?php echo Text::_('COM_JOOMSHIELD_WRITE_APACHE_RULES'); ?>
			</div>
		</div>
		<?php
	}

	public static function renderServerRestrictionNotes($featureKey = '')
	{
		$nginxUrl = Route::_('index.php?option=com_joomshield&tab=nginx_rules');
		$apacheNote = 'COM_JOOMSHIELD_SERVER_RESTRICTION_APACHE_NOTE';
		$nginxNote = 'COM_JOOMSHIELD_SERVER_RESTRICTION_NGINX_NOTE';
		$disableNote = 'COM_JOOMSHIELD_SERVER_RESTRICTION_DISABLE_NOTE';

		if ($featureKey === 'emergency_offline')
		{
			$apacheNote = 'COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_APACHE_NOTE';
			$nginxNote = 'COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_NGINX_NOTE';
			$disableNote = 'COM_JOOMSHIELD_EMERGENCY_SITE_OFFLINE_DISABLE_NOTE';
		}
		elseif ($featureKey === 'admin_http_auth')
		{
			$apacheNote = 'COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_APACHE_NOTE';
			$nginxNote = 'COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_NGINX_NOTE';
			$disableNote = 'COM_JOOMSHIELD_ADMINISTRATOR_ACCESS_PROTECTION_DISABLE_NOTE';
		}

		$accordionId = 'joomshield-server-notes';

		if ($featureKey !== '')
		{
			$accordionId .= '-' . preg_replace('/[^a-z0-9_\-]/i', '', $featureKey);
		}

		self::renderNotesAccordionOpen($accordionId, 'COM_JOOMSHIELD_SERVER_RESTRICTION_NOTES_TITLE');
		self::renderDetectedServerHint();
		?>
				<ul class="mo_js_server_rules_list">
					<li><strong><?php echo Text::_('COM_JOOMSHIELD_PHP_PROTECTION'); ?>: </strong><?php echo Text::_('COM_JOOMSHIELD_SERVER_RESTRICTION_PHP_NOTE'); ?></li>
					<li><strong><?php echo Text::_('COM_JOOMSHIELD_APACHE_SERVER'); ?>: </strong><?php echo Text::_($apacheNote); ?></li>
					<li><strong><?php echo Text::_('COM_JOOMSHIELD_NGINX_SERVER'); ?>: </strong><?php echo Text::_($nginxNote); ?></li>
				</ul>
				<p class="mo_boot_mt-2"><?php echo Text::_($disableNote); ?></p>
				<p class="mo_boot_mb-0">
					<a class="mo_websecurity_btn" href="<?php echo $nginxUrl; ?>"><?php echo Text::_('COM_JOOMSHIELD_OPEN_NGINX_RULES'); ?></a>
				</p>
		<?php
		self::renderNotesAccordionClose();
	}

	private static function renderNotesAccordionOpen($accordionId, $titleKey)
	{
		?>
		<details id="<?php echo htmlspecialchars($accordionId, ENT_QUOTES, 'UTF-8'); ?>" class="mo_js_server_rules_note mo_js_server_notes_accordion mo_boot_mb-3">
			<summary class="mo_js_server_notes_summary">
				<span class="mo_js_server_notes_toggle" aria-hidden="true"></span>
				<span class="mo_js_server_notes_title"><?php echo Text::_($titleKey); ?></span>
			</summary>
			<div class="mo_js_server_notes_body">
		<?php
	}

	private static function renderNotesAccordionClose()
	{
		?>
			</div>
		</details>
		<?php
	}

	private static function renderDetectedServerHint()
	{
		$kind = JoomShieldServerRules::detectedServerKind();
		$software = JoomShieldServerRules::detectedServerSoftware();
		$label = Text::_('COM_JOOMSHIELD_DETECTED_SERVER_UNKNOWN');

		if ($kind === 'nginx')
		{
			$label = Text::_('COM_JOOMSHIELD_DETECTED_SERVER_NGINX');
		}
		elseif ($kind === 'apache')
		{
			$label = Text::_('COM_JOOMSHIELD_DETECTED_SERVER_APACHE');
		}
		?>
		<p class="mo_js_detected_server"><strong><?php echo Text::_('COM_JOOMSHIELD_DETECTED_SERVER'); ?></strong>
			<?php echo $label; ?>
			<em>(<?php echo htmlspecialchars($software, ENT_QUOTES, 'UTF-8'); ?>)</em>
		</p>
		<?php
	}
}

