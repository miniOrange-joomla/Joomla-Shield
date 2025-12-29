<?php
defined('_JEXEC') or die;
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 *
 *
 * This file is part of miniOrange SAML plugin.
 *
 * miniOrange Network Security plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * miniOrange Network Security plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with miniOrange Network Security plugin.  If not, see <http://www.gnu.org/licenses/>.
 */

if (defined('_JEXEC')) {

    class plgauthenticationminiorangenetworksecurity extends JPlugin
    {
        function onUserAfterLogin($options)
        {
            $object = $options['user'];
            
            $array = (array)$object;
            $username = $array['username'];
            
            jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');
            $userIpAddress = NetworkSecurityUtilities::get_client_ip();
            
            //NetworkSecurityUtilities::update_transaction_table($userIpAddress);
            $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            
            if (strpos($requested_uri, 'administrator') !== false) {
                $isadmin = 'Admin Login Page';
            } else {
                $isadmin = 'End user login page';
            }
            // Some customer are facing some environment issue due to below call. Will fix this soon.
            //$country_name = NetworkSecurityUtilities::_get_country_name($userIpAddress);
            $country_name = "";
            $browser_name = NetworkSecurityUtilities::_get_current_user_browser();
            
            $os = NetworkSecurityUtilities::_get_os_info();
            
            NetworkSecurityUtilities::addTransactionDetails($userIpAddress, $username, 'User Login', 'success');
            NetworkSecurityUtilities::_add_login_transaction_details($userIpAddress, $username, 'User Login', 'success', $isadmin, $country_name, $browser_name, $os, '');
        }

        function onUserLogin($options)
        {

        }

        public function onUserAuthenticate($credentials, $options, &$response)
        {
            $username = $credentials['username'] ?? '';
            $password = $credentials['password'] ?? '';

            $result = NetworkSecurityUtilities::getUserCredentials($username);

            $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            if (str_contains($requested_uri, 'administrator'))
                $isAdmin = true;
            else
                $isAdmin = false;

            //For handling Customized admin url on wrong credentials.
            $config = NetworkSecurityUtilities::getLoginSecurityConfig();
            $login_url_key = $config['access_lgn_urlky'] ?? '';
            $base_url = JUri::root();
            $current_admin_login_url = $base_url . 'administrator';
            $custom_admin_login_url = $current_admin_login_url . '/?' . $login_url_key;
            $msg = JText::_('JGLOBAL_AUTH_INVALID_PASS');

            if ($result != null) {

                $match = JUserHelper::verifyPassword($password, $result->password, $result->id);
                if ($match == true) {
                    $config_result = NetworkSecurityUtilities::getLoginSecurityConfig();
                    $enforce_strong_passwd = $config_result['enforce_strong_password_login'] ?? 0;
                    if ($enforce_strong_passwd) {
                        $status = NetworkSecurityUtilities::_check_passwd_strength($password);
                        if ($status == 'false') {
                            include 'change-password.php';
                            exit();
                        }
                    }
                }
                else {
                    if ($isAdmin){
                        NetworkSecurityUtilities::_custom_redirect_url($custom_admin_login_url, $msg, 'warning');
                    }
                }
            }
            else {
                if ($isAdmin){
                    NetworkSecurityUtilities::_custom_redirect_url($custom_admin_login_url, $msg, 'warning');
                }
            }
        }
    }
}