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

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class JoomShieldViewAccountSetup extends HtmlView
{

	public function display($tpl = null)
	{
		$this->setLayout('accountsetup');
		$this->addToolBar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since 1.6
	 */
	protected function addToolBar()
	{
		ToolbarHelper::title(Text::_('miniOrange JoomShield'), 'mo_jnsp_logo mo_jnsp_icon');
	}
}
