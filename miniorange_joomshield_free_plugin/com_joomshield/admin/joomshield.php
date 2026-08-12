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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/helpers/mo_networksecurity_utility.php';
require_once JPATH_COMPONENT . '/helpers/mo_networksecurity_customer_setup.php';
require_once JPATH_COMPONENT . '/helpers/MonetworksecurityDB.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/LoginSecurity.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/RegisterSecurity.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/IpBlocking.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/IpReports.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/mo_jnsp_support.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/AdvancedBlocking.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/DB_Backup.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/Notifications.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/AdvancedFeatures.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/licensePlans.php';
require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/mo_js_import_export.php';



// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_joomshield'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('joomshield', JPATH_COMPONENT_ADMINISTRATOR);

$controller = BaseController::getInstance('joomshield');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
