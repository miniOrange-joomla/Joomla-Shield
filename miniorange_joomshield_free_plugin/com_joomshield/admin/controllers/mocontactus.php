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

class JoomshieldControllerMoContactUS extends FormController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['view_list'] = $config['view_list'] ?? 'mocontactus';

		parent::__construct($config, $factory, $app, $input);
	}

	public function contactUs()
	{
		$input = Factory::getApplication()->input;
		$email = trim($input->get('query_email', '', 'string'));
		$query = trim($input->get('query', '', 'raw'));
		$phone = trim($input->get('query_phone', '', 'string'));

		if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=support', Text::_('COM_JOOMSHIELD_PLEASE_PROVIDE_A_VALID_EMAIL_ADDRESS'), 'error');

			return;
		}

		if (empty($query))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=support', Text::_('COM_JOOMSHIELD_EMPTY_QUERY_COULD_NOT_BE_SUBMITTED'), 'error');

			return;
		}

		$customer = new Joomla_NetworksecurityCustomer;
		$response = $customer->submitContactUs($email, $phone, $query);
		$result = json_decode($response, true);

		if (json_last_error() !== JSON_ERROR_NONE || empty($result))
		{
			$this->setRedirect('index.php?option=com_joomshield&tab=support', Text::_('COM_JOOMSHIELD_YOUR_QUERY_COULD_NOT_BE_SUBMITTED'), 'error');

			return;
		}

		if (isset($result['status']) && strcasecmp($result['status'], 'error') === 0)
		{
			$message = isset($result['message']) ? $result['message'] : Text::_('COM_JOOMSHIELD_AN_ERROR_OCCURRED_WHILE_SUBMITTING_YOUR_QUERY');
			$this->setRedirect('index.php?option=com_joomshield&tab=support', $message, 'error');

			return;
		}

		$this->setRedirect('index.php?option=com_joomshield&tab=support', Text::_('COM_JOOMSHIELD_THANKS_FOR_GETTING_IN_TOUCH_WE_SHALL_GET_BACK_TO_YOU_SHORTLY'), 'success');
	}
}
