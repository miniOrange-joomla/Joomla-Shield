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

class Joomla_networksecurityControllerMoIpBlocking extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'moipblocking';
        parent::__construct();
    }

    function ipLookUp()
    {
        jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');
        $post = JFactory::getApplication()->input->post->getArray();

        $clear = $post['clear_val'] ?? '';
        if($clear == 'Clear')
        {
           NetworkSecurityUtilities::_clear_iplookup();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_blocking');

        }
        else{
            $ip_address = $post['mo_lookupip'] ?? "";

            if(!filter_var($ip_address, FILTER_VALIDATE_IP)) {
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_blocking','Enter a valid IPV4 or IPV6 address.', 'warning');
                return;
            }
            if(empty($ip_address)){
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_blocking','Please enter a valid IP address.', 'warning');
                return;
            }
            self::lookupIP($ip_address);
        }
    }

    private function lookupIP($ip)
    {
        //To get accurate IP location.
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));

        $result     = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip),true);
        $hostname 	= gethostbyaddr($result["geoplugin_request"]);

        try{
            $timeoffset	= timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]),new DateTime('now'));
            $timeoffset = $timeoffset/3600;

        }catch(Exception $e){
            $result["geoplugin_timezone"]="";
            $timeoffset="";
        }

        if($result['geoplugin_request'] == $ip) {

            $ipLookup['IP Address']         = $result["geoplugin_request"];
            $ipLookup['HostName']           = $hostname;
            $ipLookup['TimeZone']           = $result["geoplugin_timezone"];
            $ipLookup['Time Difference']    = $timeoffset;
            $ipLookup['Latitude']           = $result["geoplugin_latitude"];
            $ipLookup['Longitude']          = $result["geoplugin_longitude"];
            $ipLookup['Continent']          = $result["geoplugin_continentName"];
            $ipLookup['Country']            = $result["geoplugin_countryName"];
            $ipLookup['Region']             = $details->region;
            $ipLookup['City']               = $details->city;
            $ipLookup['Postal Code']        = $details->postal;
            $ipLookup['Currency Code']      = $result["geoplugin_currencyCode"];
            $ipLookup['Currency Symbol']    = $result["geoplugin_currencySymbol"];
            $ipLookup['Per Dollar Value	']  = $result["geoplugin_currencyConverter"];
            $ipLookup['Code']               = $result["geoplugin_countryCode"];
            $result_det                     = $ipLookup;
        }else{
            $result["ipDetails"]["status"]="ERROR";
        }
        $result_det = serialize($result_det);
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $fields = array(
            $db->quoteName('mo_ip_lookup_values')      . ' = ' . $db->quote($result_det),
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_jnsp_loginsecurity_setup'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_blocking');
    }


    function clear_reports(){
        $post = JFactory::getApplication()->input->post->getArray();
        $login_report = NetworkSecurityUtilities::_get_all_login_attempts_count();
        $login_report_count = NetworkSecurityUtilities::_get_login_transaction_reports_val();

        $refresh = $post['refresh_page'] ?? '';
        if ($refresh == 'Refresh Page')
        {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_reports','The login reports has been updated successfully.');
            return;
        }

        $download = $post['download_reports'] ?? '';
        if ($download == 'Download Reports' && $login_report_count['username'] != '' && $login_report != 0) {
            NetworkSecurityUtilities::_download_reports();
        }
        else{
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_reports', 'The User Login Report is empty. An empty login report could not be downloaded.','error');
        }

        $clear_reports = $post['clear_val'] ?? '';
        if ($login_report_count['username'] != '' && $login_report != 0) {
            if ($clear_reports == 'Clear Reports') {
                $db = JFactory::getDbo();
                $db->truncateTable('#__miniorange_login_transactions_reports');
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_reports', 'The login reports has been cleared successfully.','success');
            } else {
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_reports', 'Some error occured while processing your request, please try agin later.', 'warning');
            }
        }
        else {
            if ($clear_reports == 'Clear Reports')
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=ip_reports', 'The User Login Report is already empty.', 'error');
        }
    }
}