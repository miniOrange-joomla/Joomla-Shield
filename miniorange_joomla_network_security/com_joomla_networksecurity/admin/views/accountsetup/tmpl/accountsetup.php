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
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomlaidp
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file

defined('_JEXEC') or die('Restricted Access');
JHtml::_('jquery.framework');
JHtml::_('stylesheet', JUri::base() .'components/com_joomla_networksecurity/assets/css/miniorange_netsecurity.css');
JHtml::_('stylesheet', JUri::base() .'components/com_joomla_networksecurity/assets/css/mo_jnsp_phone.css');
JHtml::_('script', JUri::base() . 'components/com_joomla_networksecurity/assets/js/utility.js');
JHtml::_('script', JUri::base() . 'components/com_joomla_networksecurity/assets/js/mo_jnsp_phone.js');
$tab = JFactory::getApplication()->input->get->getArray();

$network_active_tab = $tab['tab'] ?? 'login_security';
?>

    <div class="mo_boot_container-fluid mo_boot_websecurity-container">
        <div class="mo_boot_row mo_boot_p-2 mo_boot_text-center mo_websecurity_main_row">
            <div class="mo_boot_col-sm-2">
                    <h2 class="mo_boot_mt-2" style="color: white;font-size: large">Web Security</h2>
                </div>
                <div class="mo_boot_col-sm-2 mo_boot_offset-sm-7 mo_boot_text-right">
                    <a  href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>" class="mo_boot_btn mo_boot_mt-1 mo_websecurity_btn"><strong>Licensing Plans</strong></a>
                </div>
            <div class="mo_boot_col-sm-1 ">
                <a href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&tab=support'?>" class="mo_boot_btn mo_boot_mt-1 mo_websecurity_btn"><strong>Support</strong></a>
            </div>
        </div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-2 mo_boot_websecurity-row mo_boot_px-0">
                <div class="mo_boot_row">
                    <div onclick="mo_show_tab('websecurity_tab_2')" style="<?php echo ($network_active_tab=='login_security')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_2" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Login Security</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_3')" style="<?php echo ($network_active_tab=='register_security')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_3" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong style="font-size: 16px;">Registration Security</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_4')" style="<?php echo ($network_active_tab=='ip_blocking')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_4" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">IP Blocking</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_5')" style="<?php echo ($network_active_tab=='ip_reports')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_5" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Reports</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_6')" style="<?php echo ($network_active_tab=='advanced_ip_blocking')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_6" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Advanced Blocking</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_7')" style="<?php echo ($network_active_tab=='db_backup')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_7" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">DB Backup</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_8')" style="<?php echo ($network_active_tab=='email_notifications')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_8" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Notification/Alert</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_10')" style="<?php echo ($network_active_tab=='advanced_features')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_10" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Advanced Features</strong>
                    </div>

                    <div onclick="mo_show_tab('websecurity_tab_1')" style="<?php echo ($network_active_tab=='account_setup')?'background:white;color:black':'background:none;color:white;'?>" id="mo_websecurity_tab_1" class="mini_websecurity_tab mo_boot_col-sm-12 mo_boot_p-3 mo_boot_border-1 mo_boot_websecurity-tab">
                        <strong class="mo_jnsp_tab">Account Setup</strong>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-10">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_2" style="<?php echo (($network_active_tab=='login_security')?'display:block;':'display:none;');?>">
                            <?php
                            LoginSecurity::mo_networksecurity_login_security_form();
                            ?>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_3" style="<?php echo (($network_active_tab=='register_security')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            RegisterSecurity::mo_networksecurity_register_security_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_4" style="<?php echo (($network_active_tab=='ip_blocking')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            IpBlocking::mo_networksecurity_ip_blocking_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_5" style="<?php echo (($network_active_tab=='ip_reports')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            IpReports::mo_networksecurity_login_reports_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_6" style="<?php echo (($network_active_tab=='advanced_ip_blocking')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            AdvancedBlocking::mo_networksecurity_advanced_ip_blocking_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_7" style="<?php echo (($network_active_tab=='db_backup')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            DB_Backup::mo_networksecurity_db_backup_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_8" style="<?php echo (($network_active_tab=='email_notifications')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            Notifications::mo_networksecurity_email_notifications_form();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_10" style="<?php echo (($network_active_tab=='advanced_features')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            AdvancedFeatures::mo_networksecurity_advanced_features();
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_1" style="<?php echo (($network_active_tab=='account_setup')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            $customer_details = MonetworksecurityDB::getCustomerDetails();
                            $login_status = $customer_details['login_status'] ?? 0;
                            $registration_status = $customer_details['registration_status'] ?? '';
                            $class_name = "Register";
                            if ($login_status) {
                                Register::mo_networksecurity_local_login_page();
                            }else if($registration_status == 'MO_OTP_DELIVERED_SUCCESS' || $registration_status == 'MO_OTP_VALIDATION_FAILURE' || $registration_status == 'MO_OTP_DELIVERED_FAILURE'){
                                Register::mo_networksecurity_show_otp();
                            }else if (! MoNetworkSecurityUtility::is_customer_registered()) {
                                Register::mo_networksecurity_registration_page();
                            } else {
                                Register::mo_networksecurity_local_account_page();
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mo_boot_col-sm-12 mo_websecurity_tab" id="websecurity_tab_9" style="<?php echo (($network_active_tab=='support')?'display:block;':'display:none;');?>">
                        <div class="mo_boot_row mo_boot_m-2">
                            <?php
                            mo_jnsp_support();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>