<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange Joomla Network / Website Security plugin.
 *
 * miniOrange Joomla Network / Website Security plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange Joomla Network / Website Security plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange Joomla Network / Website Security plugin.  If not, see <http://www.gnu.org/licenses/>.
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/views/accountsetup/tmpl/Register.php';
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



// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_joomla_networksecurity')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('joomla_networksecurity', JPATH_COMPONENT_ADMINISTRATOR);

$controller = JControllerLegacy::getInstance('joomla_networksecurity');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();