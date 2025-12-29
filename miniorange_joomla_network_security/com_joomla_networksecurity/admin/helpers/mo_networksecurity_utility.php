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
defined('_JEXEC') or die;

include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomla_networksecurity' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'MonetworksecurityDB.php');
jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');

class MoNetworkSecurityUtility
{

    public static function is_curl_installed()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else
            return 0;
    }

    public static function getHostname()
    {
        return 'https://login.xecurify.com';
    }

    public static function check_curl_installed()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return 1;
        } else {
            return json_encode(array("status" => 'CURL_ERROR', 'statusMessage' => '<a href="http://php.net/manual/en/curl.installation.php">PHP cURL extension</a> is not installed or disabled.'));
        }
    }

    public static function is_customer_registered()
    {

        $result = MonetworksecurityDB::getCustomerDetails();
        $email = $result['email'];
        $customerKey = $result['customer_key'];
        if (!$email || !$customerKey || !is_numeric(trim($customerKey))) {
            return 0;
        } else {
            return 1;
        }
    }

    public static function _is_ip_blocked($ip_address)
    {
        $is_ip_blocked = false;
        if (self::_is_browser_blocked($ip_address))
            $is_ip_blocked = true;
        return $is_ip_blocked;
    }

    public static function _is_browser_blocked($user_ip)
    {
        $attributes = NetworkSecurityUtilities::_get_advance_ip();

        $browser_blk_enable = $attributes['mo_enable_browser_blocking'] ?? 0;
        $ms_edge = $attributes['mo_medge_blocking'] ?? 0;

        if ($browser_blk_enable) {
            $user_browser = NetworkSecurityUtilities::_get_current_user_browser();

            if ($ms_edge == 1 && ($user_browser == 'edge' || $user_browser == 'edg')) {
                return true;
            }
        }
        return false;
    }
}