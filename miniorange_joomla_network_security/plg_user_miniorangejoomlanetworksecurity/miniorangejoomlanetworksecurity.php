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

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');


class plgUserMiniorangejoomlanetworksecurity extends JPlugin
{
    /**
     * This method should handle any authentication and report back to the subject
     *
     *
     * @access    public
     * @param array $credentials Array holding the user credentials ('username' and 'password')
     * @param array $options Array of extra options
     * @param object $response Authentication response object
     * @return    boolean
     */

    public function onUserLoginFailure()
    {
        $val = 1;
        NetworkSecurityUtilities::_check_url($val);
        $post = JFactory::getApplication()->input->post->getArray();

        $username = $post['username'] ?? null;
        $userIp = NetworkSecurityUtilities::get_client_ip();

        $requested_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        if (strpos($requested_uri, 'administrator') !== false) {
            $isadmin = 'Admin Login Page';
        } else {
            $isadmin = 'End user login page';
        }

        $country_name = NetworkSecurityUtilities::_get_country_name($userIp);

        $browser_name = NetworkSecurityUtilities::_get_current_user_browser();
        $os = NetworkSecurityUtilities::_get_os_info();

        NetworkSecurityUtilities::addTransactionDetails($userIp, $username, 'User Login', 'failed');
        NetworkSecurityUtilities::_add_login_transaction_details($userIp, $username, 'User Login', 'failed', $isadmin, $country_name, $browser_name, $os, '');

    }


    public function onUserBeforeSave()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        $config = NetworkSecurityUtilities::getRegisterSecurityConfig();
        $is_enforce_strong_passwd = $config['enforce_strong_password_register'] ?? 0;
        if($is_enforce_strong_passwd)
        {
            $passwd = $post['jform']['password1'] ?? null;
            $is_valid_passwd = NetworkSecurityUtilities::_check_passwd_strength($passwd);
            if ($is_valid_passwd == 'false')
            {
                $message = '<b>Please select strong password.</b><br>                             
                            <li>Password should contain at least one Capital and one Small Letter.</li>
                            <li>Password Should be Minimum 12 Characters</li>
                            <li>Password should contain at least one Numeric Character.</li>
                            <li>Password should contain at least one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>';

                NetworkSecurityUtilities::_redirect_with_error_message($message);
            }
        }
        $is_rest = $config['block_fake_emails'] ?? '';
        if ($is_rest == 1) {
            $email = $post['jform']['email1'] ?? '';
            $is_Nvalid = NetworkSecurityUtilities::_is_valid_email($email);
            if ($is_Nvalid) {
                $message = 'The email domain you have entered is not allowed to register. Please try with different email address.';
                NetworkSecurityUtilities::_redirect_with_error_message($message);
            }
        }
    }
}