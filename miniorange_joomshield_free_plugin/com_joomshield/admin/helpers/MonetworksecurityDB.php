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

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class MonetworksecurityDB
{
	public static function getCustomerDetails()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__miniorange_networksecurity_customer'));
		$query->where($db->quoteName('id') . " = 1");
		$db->setQuery($query);
		$customerDetails = $db->loadAssoc();

		return $customerDetails;
	}
}
