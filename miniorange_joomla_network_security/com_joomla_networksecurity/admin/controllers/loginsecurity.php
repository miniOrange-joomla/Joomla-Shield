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

class Joomla_networksecurityControllerLoginSecurity extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'loginsecurity';
        parent::__construct();
    }


    function saveLoginSecuritySettings()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        if (empty($post)) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=login_security', 'Please enable the checkbox.', 'warning');
        }

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        if (isset($post['mo_customize_admin_url'])){
            $is_admin_login_enable = $post['enable_custom_admin_login'] ?? 0;
            $access_lgn_ky = $post['access_lgn_urlky'] ?? '';
            $after_failure = $post['after_adm_failure_response'] ?? '';
            $custom_destination = $post['custom_failure_destination'] ?? '';
            $custom_err_message = $post['custom_message_after_fail'] ?? '';

            $fields = array(
                $db->quoteName('enable_custom_admin_login') . ' = ' . $db->quote($is_admin_login_enable),
                $db->quoteName('access_lgn_urlky') . ' = ' . $db->quote($access_lgn_ky),
                $db->quoteName('after_adm_failure_response') . ' = ' . $db->quote($after_failure),
                $db->quoteName('custom_failure_destination') . ' = ' . $db->quote($custom_destination),
                $db->quoteName('custom_message_after_fail') . ' = ' . $db->quote($custom_err_message),
            );
            $msg="Customize Admin Login page URL Configuration has been saved successfully.";
        }
        else{
            $strong_passwd_login = $post['enforce_strong_password_login'] ?? 0;

            $fields = array(
                $db->quoteName('enforce_strong_password_login') . ' = ' . $db->quote($strong_passwd_login),
            );
            $msg="Enforce Strong Password Configuration has been saved successfully.";
        }

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();

        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=login_security', $msg);
    }

    function importexport($json_in_string = false) {

        require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'export.php';
        $tab_names = array(
            "login_security" => '#__miniorange_jnsp_loginsecurity_setup',
            "register-security" => '#__miniorange_jnsp_registersecurity_setup',
            "advance_blocking" => '#__miniorange_jnsp_advance_blocking',
        );
        foreach ($tab_names as $key => $value ) {
            $customer_result[$key] = NetworkSecurityUtilities::is_network_registered($value);
        }

        $LoginSecurity    = $customer_result['login_security'];
        $RegisterSecurity = $customer_result['register-security'];
        $AdvanceBlocking  = $customer_result['advance_blocking'];

        $i = 0;
        //Checking if there is any cofiguration done in Login, Register & Advance setting tab.
        if (($LoginSecurity['enable_custom_admin_login'] == 0) && ($LoginSecurity['mo_ip_lookup_values'] == null)
            && ($LoginSecurity['enforce_strong_password_login'] == 0))
            $i++;

        if (($RegisterSecurity['block_fake_emails'] == 0) && ($RegisterSecurity['enforce_strong_password_register'] == 0))
            $i++;

        if (($AdvanceBlocking['mo_enable_browser_blocking'] == 0))
            $i++;

        if ($i != 3) {
            if ($customer_result) {
                $json_string = (json_encode($customer_result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                if ($json_in_string)
                    return $json_string;
                header("Content-Disposition: attachment; filename=miniorange-web-security-config.json");
                echo $json_string;
                exit;
            }
            return;
        }
        else
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=login_security', 'You are exporting the plugin\'s default configurations. Configure first before exporting.', 'error');
    }

    function import(){
        if ($_FILES['configuration_file']['name'] == ""){
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=login_security', 'Please select the file first to import. Empty file cannot be imported.','error');
            return;
        }else {
            $string = @file_get_contents($_FILES['configuration_file']['tmp_name']);
            $json = json_decode($string, true);
        }
        $this->mo_update_configuration_array($json);
        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=login_security','Configuration file imported successfully');
    }

    function mo_update_configuration_array($configuration_array){

        require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'export.php';

        define("Tab_class_names", serialize(array(
            "login_security" => 'mo_login_security',
            "register-security" => 'mo_register_security',
            "advance_blocking" => 'mo_advance_blocking'
        )));
        $tab_class_names = unserialize(Tab_class_names);

        foreach ($tab_class_names as $tab_name => $class_name) {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array();
            $varenter = 0;

            foreach ($configuration_array[$tab_name] as $key => $value) {
                $varenter = 1;
                $fields[] = $db->quoteName($key) . ' = ' . $db->quote($value);
            }
            if ($class_name == 'mo_login_security' && $varenter == 1) {
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );
                $query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
            if ($class_name == 'mo_register_security' && $varenter == 1) {
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );
                $query->update($db->quoteName('#__miniorange_jnsp_registersecurity_setup'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
            if ($class_name == 'mo_advance_blocking' && $varenter == 1) {
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );
                $query->update($db->quoteName('#__miniorange_jnsp_advance_blocking'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        }
    }

    function joomlapagination()
    {
        $total_entries = NetworkSecurityUtilities::_get_all_login_attempts_count();
        $post       = JFactory::getApplication()->input->post->getArray();
        $start      = $post['page'] ?? '';
        $order      = $post['orderBY'] ?? 'down';
        $no_of_entry= $post['no_of_entry'] ?? '10';
        $start_val  = $start + 1;
        $show_trans = $no_of_entry;
        $first_val  = ($start_val - 1) * $show_trans;
        $first_val  = ($total_entries == 0) ? -1 : $first_val;
        $last_val   = $show_trans + $first_val;
        if($last_val >= $total_entries)
        {
            $last_val = $total_entries;
        }
        $low_id    = $start * $no_of_entry;
        $upper_id  = $low_id + $no_of_entry;
        $first_val = $first_val + 1;

        $data = NetworkSecurityUtilities::_get_login_transaction_reports();

        if($last_val == $total_entries)
        {
            echo '<script>
                document.getElementById("next_btn").style.display = "none";              
            </script>';
        }

        $list_of_login_trnas = NetworkSecurityUtilities::_get_login_attempts_count($show_trans, $low_id,$order);
        $result = '';
        $result .= '<div class="table-responsive" style="font-family: sans-serif;font-size: 12px;" id="mo_idp_vt_conf_table">
            <table id="myTable" class="mo_myTable" >
            <thead>
                <tr class="header mo_boot_text-white" style="line-height: 14px;background-color: #001b4c;">
                    <th class="mo_td_values" width="8%">IP Address</th>
                    <th class="mo_td_values" width="8%">End User/Admin</th>
                    <th class="mo_td_values" width="8%">Username</th>
                    <th class="mo_td_values" width="8%">Status</th>
                    <th class="mo_td_values" width="11%">Date & Time<br>(in UTC)&nbsp;<span class="fa fa-sort" style="cursor: pointer;" onclick=sort("on",true)><input type="hidden" value="1" id="hidden_input"></span></th>
                    <th class="mo_td_values" width="8%">Country</th>
                    <th class="mo_td_values" width="8%">Browser</th>
                    <th class="mo_td_values" width="8%">Operating System</th>
                </tr>
            </thead>
                <tbody style="font-size: 12px;color:gray;">
                <tr style="line-height: 25px;">';


        foreach ($list_of_login_trnas as $list2)
             {
                foreach ($list2 as $list)
                {
                    if (!empty($list['ip_address']) && !empty($list['username']))
                    {
                        $result .= '<tr style="line-height: 14px;">
                                                        <td class="mo_guide_text-center"><span class="blacktext">' . $list['ip_address'] . '</td>';
                        if ($list['isadmin_user'] == 'Admin Login Page') {
                            $result .= '<td class="mo_guide_text-center"><span class="blacktext"><b>' . $list['isadmin_user'] . '</b></td>';
                        } else {
                            $result .= '<td class="mo_guide_text-center"><span class="blacktext">' . $list['isadmin_user'] . '</td>';
                        }
                        $result .= '<td class="mo_guide_text-center"><span class="blacktext">' . $list['username'] . '</td>';
                        if ($list['status'] == 'success') {
                            $result .= '<td class="mo_guide_text-center"><span class="greentext">' . $list['status'] . '</td>';
                        } elseif ($list['status'] == 'failed') {
                            $result .= '<td class="mo_guide_text-center"><span class="redtext">' . $list['status'] . '</td>';
                        } else {
                            $result .= '<td class="mo_guide_text-center"><span class="browntext">' . $list['status'] . '</td>';
                        }
                        $result .= '<td class="mo_guide_text-center"><span class="blacktext">' . date("M j, Y, g:i:s a", $list['created_timestamp']) . '</td>                                                      
                                            <td class="mo_guide_text-center"><span class="blacktext">' . $list['country_name'] . '</td>                          
                                            <td class="mo_guide_text-center"><span class="blacktext">' . $list['browser_name'] . '</td>                          
                                            <td class="mo_guide_text-center"><span class="blacktext">' . $list['operating_system'] . '</td>                          
                                        </tr>';
                    }
                    else{
                        $db = JFactory::getDbo();
                        $db->truncateTable('#__miniorange_login_transactions_reports');
                        $first_val = 0;
                        $last_val = 0;
                        $total_entries = 0;
                    }
                }
             }
            $result .= '</tr>
                            </tbody>
                        </table>
                    </div><br>
                    <div>Showing '.$first_val .' - '. $last_val .' of '. $total_entries.' entries</div>';
            echo $result;
            exit;
    }
}