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

class MoJsImportExport
{
	public static function moJsImportExport()
	{
		?>
		<div id="import_export_form" class="mo_boot_col-sm-12">
			<div class="mo_boot_row mo_boot_mt-4">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_IMPORT_EXPORT_CONFIGURATION'); ?></h3>
				</div>
			</div><hr>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_EXPORT_CONFIGURATION'); ?></h3>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-3">
				<form name="f" method="post" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.importexport'); ?>">
					<div class="mo_boot_col-sm-12">
						<input type="submit" class="mo_websecurity_btn" name="sub" value="<?php echo Text::_('COM_JOOMSHIELD_EXPORT_CONFIGURATION_FILE'); ?>" />
					</div>
				</form>
			</div><br><hr>

			<div class="mo_boot_row">
				<div class="mo_boot_col-sm-12">
					<h3><?php echo Text::_('COM_JOOMSHIELD_IMPORT_CONFIGURATIONS'); ?></h3>
				</div>
			</div>

			<div class="mo_boot_row mo_boot_mt-3">
				<div class="mo_boot_col-sm-12">
					<form name="f" method="post" enctype="multipart/form-data" action="<?php echo Route::_('index.php?option=com_joomshield&task=loginsecurity.import'); ?>">
						<input type="file" name="configuration_file" class="mo_btn mo_import_file mo_boot_mr-1" required>
						<input type="submit" name="submit" class="mo_websecurity_btn" value="<?php echo Text::_('COM_JOOMSHIELD_IMPORT_CONFIGURATION_FILE'); ?>"/>
					</form>
				</div>
			</div><br>
		</div>

		<?php
	}
}
