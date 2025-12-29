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

class plgSystemJnspredirect extends JPlugin
{

    public function onUserAfterLogout($options){
        $login_config = NetworkSecurityUtilities::getLoginSecurityConfig();
        $root = JURI::root();
        $isAdmin = JFactory::getApplication()->isClient('administrator');

        if ($isAdmin) {
            $url_key = $login_config['access_lgn_urlky'] ?? '';
            $custom_link = $root . 'administrator/?' . $url_key;

            $app = JFactory::getApplication();
            $app->redirect($custom_link);
        }
    }

    public function onAfterInitialise()
    {
        $post = JFactory::getApplication()->input->post->getArray();

        $session = JFactory::getSession();
        $user_id = JFactory::getUser()->id;

        //For Session Timeout handling for customized admin URL.
        if (!($session->isActive()) && $user_id == 0)
        {
            $login_config = NetworkSecurityUtilities::getLoginSecurityConfig();
            $custom_admin_login_enabled = $login_config['enable_custom_admin_login'] ?? 0;
            $root = JURI::root();

            if ($custom_admin_login_enabled) {
                $url_key = $login_config['access_lgn_urlky'] ?? '';
                $custom_link = $root . 'administrator/?' . $url_key;
            }
            else{
                $custom_link = $root . 'administrator';
            }
            echo '<meta http-equiv="refresh" content="0; url=' . $custom_link . '">';exit();
        }


        if (isset($post['mojsp_feedback'])) {
            (new NetworkSecurityUtilities)->_get_feedback_form($post);
        } else {
            if (isset($post['option_change_password'])) {
                if ($post['option_change_password'] == 'mo_jnsp_change_password') {
                    NetworkSecurityUtilities::handle_change_password($post['username'], $post['new_password'], $post['confirm_password']);
                }
            }

            $userIpAddress = NetworkSecurityUtilities::get_client_ip();
            $is_ip_blocked = NetworkSecurityUtilities::_is_ip_blocked($userIpAddress);
            if ($is_ip_blocked) {
                NetworkSecurityUtilities::_show_error_message();
            }
            $val = 0;
            NetworkSecurityUtilities::_check_url($val);
        }
    }

    function onExtensionBeforeUninstall($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('extension_id');
        $query->from('#__extensions');
        $query->where($db->quoteName('name') . " = " . $db->quote('LIB_MINIORANGEJOOMLANETWORKSECURITYPLUGIN_NAME'));
        $db->setQuery($query);
        $result = $db->loadColumn();

        $tables = JFactory::getDbo()->getTableList();
        $tab = 0;
        foreach ($tables as $table) {
            if (strpos($table, "miniorange_networksecurity_customer"))
                $tab = $table;
        }
        if ($tab) {
            $current_user = JFactory::getUser();
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('uninstall_feedback') . "," . 'email';
            $query->from('#__miniorange_networksecurity_customer');
            $query->where($db->quoteName('id') . " = " . $db->quote(1));
            $db->setQuery($query);
            $fid = $db->loadColumn();

            $customerResult = $db->loadResult();
            $admin_email = $current_user->email ?? '';

            $post = JFactory::getApplication()->input->post->getArray();
            $tpostData = $post;

            foreach ($fid as $value) {
                if ($value == 0) {
                    foreach ($result as $results) {
                        if ($results == $id) {
                            ?>
                            <div class="form-style-6 ">
                                <h1><strong>Feedback for Joomla Web Security Lite</strong></h1>
                                <h3>Email: </h3>
                                <form name="f" method="post" action="" id="mojsp_feedback">
                                    <input type="hidden" name="mojsp_feedback" value="mojsp_feedback"/>
                                    <div class="mo_boot_col-sm-12">
                                        <div class="mo_boot_row">
                                            <input type="email" id="query_email" name="query_email"
                                                   style="border-radius: 4px;" value="<?php echo $admin_email; ?>"
                                                   placeholder="Enter your email" required/>
                                        </div>
                                        <h3>What Happened? </h3>
                                        <p style="margin-left:2%">
                                            <?php
                                            $deactivate_reasons = array(
                                                "Facing issues During Registration",
                                                "Not receiving OTP during Registration",
                                                "Does not have the features I'm looking for",
                                                "Not able to Configure",
                                                "Bugs in the plugin",
                                                "Other Reasons:"
                                            );
                                            foreach ($deactivate_reasons

                                            as $deactivate_reasons) { ?>
                                        <div class=" radio " style="padding:1px;margin-left:2%">
                                            <label style="font-weight:normal;font-size:14.6px"
                                                   for="<?php echo $deactivate_reasons; ?>">
                                                <input type="radio" name="deactivate_plugin" id="deactivate_plg_id"
                                                       value="<?php echo $deactivate_reasons; ?>" required>
                                                <?php echo $deactivate_reasons; ?></label>
                                        </div>
                                        <?php } ?>
                                        <br>
                                        <div class="mo_boot_row">
                                            <textarea id="query_feedback" name="query_feedback" rows="4" style="border-radius: 4px;resize: vertical;"
                                                      cols="50" placeholder="Write your query here"></textarea>
                                        </div>
                                        <?php
                                        if (isset($tpostData['cid'])){
                                            foreach ($tpostData['cid'] as $key) { ?>
                                                <input type="hidden" name="result[]" value=<?php echo $key ?>>

                                            <?php }
                                        } ?>

                                        <br><br>
                                        <div class="mojsp_modal-footer">
                                            <input type="submit" name="miniorange_feedback_submit" class="button" value="Submit"/>
                                        </div><br>
                                        <div>
                                            <input type="submit" id="skip" name="skip_feedback" class="button" onclick="skip_feedback_form();" value="Skip Feedback"/>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
                            <script>
                                function skip_feedback_form(){
                                    var radios = document.querySelectorAll("[id^='deactivate_plg_id']");
                                    for (var i = 0; i <6 ; i++) {
                                        radios[i].disabled = true;
                                    }
                                }

                                jQuery('input:radio[name="deactivate_plugin"]').click(function () {
                                    var reason = jQuery(this).val();
                                    jQuery('#query_feedback').removeAttr('required')
                                    if (reason == 'Facing issues During Registration') {
                                        jQuery('#query_feedback').attr("placeholder", "Can you please describe the issue in detail?");
                                    } else if (reason == "Not receving OTP during Registration") {
                                        jQuery('#query_feedback').attr("placeholder", "Could you please describe in detail");
                                    } else if (reason == "Does not have the features I'm looking for") {
                                        jQuery('#query_feedback').attr("placeholder", "Let us know what feature are you looking for");
                                    } else if (reason == "Bugs in the plugin") {
                                        jQuery('#query_feedback').attr("placeholder", "Could you please describe the bug in detail");
                                    } else if (reason == "Other Reasons:") {
                                        jQuery('#query_feedback').attr("placeholder", "Can you let us know the reason for deactivation");
                                        jQuery('#query_feedback').prop('required', true);
                                    } else if (reason == "Not able to Configure") {
                                        jQuery('#query_feedback').attr("placeholder", "Not able to Configure? let us know so that we can improve the interface");
                                    }
                                });
                            </script>
                            <style type="text/css">
                                .form-style-6 {
                                    font: 95% Arial, Helvetica, sans-serif;
                                    max-width: 400px;
                                    margin: 10px auto;
                                    padding: 16px;
                                    background: #F7F7F7;
                                }

                                .form-style-6 h1 {
                                    background: #43D1AF;
                                    padding: 20px 0;
                                    font-size: 140%;
                                    font-weight: 300;
                                    text-align: center;
                                    color: #fff;
                                    margin: -16px -16px 16px -16px;
                                }

                                .form-style-6 input[type="text"],
                                .form-style-6 input[type="date"],
                                .form-style-6 input[type="datetime"],
                                .form-style-6 input[type="email"],
                                .form-style-6 input[type="number"],
                                .form-style-6 input[type="search"],
                                .form-style-6 input[type="time"],
                                .form-style-6 input[type="url"],
                                .form-style-6 textarea,
                                .form-style-6 select {
                                    -webkit-transition: all 0.30s ease-in-out;
                                    -moz-transition: all 0.30s ease-in-out;
                                    -ms-transition: all 0.30s ease-in-out;
                                    -o-transition: all 0.30s ease-in-out;
                                    outline: none;
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    background: #fff;
                                    margin-bottom: 4%;
                                    border: 1px solid #ccc;
                                    padding: 3%;
                                    color: #555;
                                    font: 95% Arial, Helvetica, sans-serif;
                                }

                                .form-style-6 input[type="text"]:focus,
                                .form-style-6 input[type="date"]:focus,
                                .form-style-6 input[type="datetime"]:focus,
                                .form-style-6 input[type="email"]:focus,
                                .form-style-6 input[type="number"]:focus,
                                .form-style-6 input[type="search"]:focus,
                                .form-style-6 input[type="time"]:focus,
                                .form-style-6 input[type="url"]:focus,
                                .form-style-6 textarea:focus,
                                .form-style-6 select:focus {
                                    box-shadow: 0 0 5px #43D1AF;
                                    padding: 3%;
                                    border: 1px solid #43D1AF;
                                }

                                .form-style-6 input[type="submit"],
                                .form-style-6 input[type="button"] {
                                    box-sizing: border-box;
                                    -webkit-box-sizing: border-box;
                                    -moz-box-sizing: border-box;
                                    width: 100%;
                                    padding: 3%;
                                    background: #43D1AF;
                                    border-bottom: 2px solid #30C29E;
                                    border-radius: 4px;
                                    border-top-style: none;
                                    border-right-style: none;
                                    border-left-style: none;
                                    color: #fff;
                                }

                                .form-style-6 input[type="submit"]:hover,
                                .form-style-6 input[type="button"]:hover {
                                    background: #2EBC99;
                                }
                            </style>
                            <?php
                            exit;
                        }
                    }
                }
            }
        }
    }
}