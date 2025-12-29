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

defined('_JEXEC') or die('Restricted access');
jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');

class Joomla_networksecurityControllerAdvanceIPBlocking extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'advanceipblocking';
        parent::__construct();
    }

    function _save_browser_blocking()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        if (empty($post)) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=advanced_ip_blocking', 'Please select at least one browser.', 'warning');
        }

        $result = NetworkSecurityUtilities::__save_browser_blocking($post);
        if ($result == 0) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=advanced_ip_blocking', 'Please select at least one browser.', 'error');
            return;
        }
        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=advanced_ip_blocking', 'Settings have been saved successfully.');

    }
}