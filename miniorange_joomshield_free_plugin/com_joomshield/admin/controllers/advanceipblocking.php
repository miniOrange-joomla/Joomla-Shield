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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');
jimport('miniorangejoomshieldplugin.utility.JoomShieldUtilities');

class JoomshieldControllerAdvanceIPBlocking extends FormController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'advanceipblocking';

		parent::__construct($config, $factory, $app, $input);
	}

	public function saveBrowserBlocking()
	{
		$post = Factory::getApplication()->input->post->getArray();

		if (empty($post))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=advanced_ip_blocking', Text::_('COM_JOOMSHIELD_PLEASE_SELECT_AT_LEAST_ONE_BROWSER'), 'warning');
		}

		$result = JoomShieldUtilities::saveBrowserBlocking($post);

		if ($result == 0)
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=advanced_ip_blocking', Text::_('COM_JOOMSHIELD_PLEASE_SELECT_AT_LEAST_ONE_BROWSER'), 'error');

			return;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=advanced_ip_blocking', Text::_('COM_JOOMSHIELD_SETTINGS_HAVE_BEEN_SAVED_SUCCESSFULLY'), 'success');

	}
}
