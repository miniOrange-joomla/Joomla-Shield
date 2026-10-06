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

class MoJsImportExport
{
	public static function moJsImportExport()
	{
		?>
		<div id="import_export_form" class="mo_boot_col-sm-12">
			<div class="mo_boot_mt-3">
				<div class="mo_boot_row mo_boot_mb-3">
					<div class="mo_boot_col-sm-12 mo_boot_px-0">
						<h3><?php echo Text::_('COM_JOOMSHIELD_IMPORT_EXPORT_CONFIGURATION'); ?></h3>
					</div>
					<div class="mo_boot_col-sm-12 mo_boot_px-0"><hr></div>
				</div>
			</div>

			<div class="mo_js_ie_panel">
				<h3><?php echo Text::_('COM_JOOMSHIELD_EXPORT_CONFIGURATION'); ?></h3>
				<p><?php echo Text::_('COM_JOOMSHIELD_EXPORT_CONFIGURATION_NOTE'); ?></p>
				<form name="mo_js_export_form" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.importexport'); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<button type="submit" class="mo_websecurity_btn" name="sub">
						<span class="icon-download" aria-hidden="true"></span>
						<?php echo Text::_('COM_JOOMSHIELD_EXPORT_CONFIGURATION_FILE'); ?>
					</button>
				</form>
			</div>

			<div class="mo_js_ie_panel">
				<h3><?php echo Text::_('COM_JOOMSHIELD_IMPORT_CONFIGURATIONS'); ?></h3>
				<p><?php echo Text::_('COM_JOOMSHIELD_IMPORT_CONFIGURATION_NOTE'); ?></p>
				<form name="mo_js_import_form" method="post" enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.import'); ?>">
					<?php echo HTMLHelper::_('form.token'); ?>
					<div class="mo_js_ie_upload_row">
						<label class="mo_js_ie_upload_label" for="configuration_file">
							<?php echo Text::_('COM_JOOMSHIELD_CONFIGURATION_FILE'); ?>
							<span class="mo_js_required_marker">*</span>
						</label>
						<input id="configuration_file" class="mo_js_file_input" type="file" name="configuration_file" accept=".json,application/json" required>
					</div>
					<button type="submit" class="mo_websecurity_btn">
						<span class="icon-upload" aria-hidden="true"></span>
						<?php echo Text::_('COM_JOOMSHIELD_IMPORT_CONFIGURATION_FILE'); ?>
					</button>
				</form>
			</div>
		</div>
		<?php
	}
}
