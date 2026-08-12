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

class JoomshieldControllerRegisterSecurity extends FormController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'registersecurity';

		parent::__construct($config, $factory, $app, $input);
	}

	public function saveRegisterSecuritySettings()
	{
		$post = Factory::getApplication()->input->post->getArray();

		if (isset($post['option_change_password']))
		{
			return;
		}

		if (isset($post['mo_block_fake_registration']))
		{
			$blockFakeEmails    = $post['block_fake_emails'] ?? 0;
			$blockEmailDomains  = preg_replace('/\s+/', '', $post['mo_email_domains'] ?? '');

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('block_fake_emails') . ' = ' . $db->quote($blockFakeEmails),
				$db->quoteName('mo_email_domains') . ' = ' . $db->quote($blockEmailDomains),
			);
			$msg = Text::_('COM_JOOMSHIELD_BLOCK_REGISTRATION_FROM_FAKE_USERS_CONFIGURATION_HAS_BEEN_SAVED_SUCCESSFULLY');
		}
		else
		{
			$enforceStrongPass = $post['enforce_strong_password_register'] ?? 0;

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$fields = array(
				$db->quoteName('enforce_strong_password_register') . ' = ' . $db->quote($enforceStrongPass),
			);
			$msg = Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORD_CONFIGURATION_HAS_BEEN_SAVED_SUCCESSFULLY');
		}

		// Conditions for which records should be updated.
		$conditions = array(
			$db->quoteName('id') . ' = 1'
		);

		$query->update($db->quoteName('#__miniorange_jnsp_registersecurity_setup'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$db->execute();

		$this->setRedirect('index.php?option=com_joomshield&tab=register_security', $msg);
	}
}
