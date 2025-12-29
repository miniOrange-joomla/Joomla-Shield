<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 */
defined('_JEXEC') or die;

class IpBlocking
{
    public static function mo_networksecurity_ip_blocking_form()
    {
        jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');

        $details = NetworkSecurityUtilities::getLoginSecurityConfig();
        $details = $details['mo_ip_lookup_values'] ?? '';
        $attrs = unserialize($details);
        ?>

        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
        <h3>IP LookUP</h3><hr><br>

        <form name="mo_ip_lookup" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=moipblocking.ipLookUp'); ?>">
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-5">
                    Enter IP Address :
                </div>
                <div class="mo_boot_col-sm-7">
                    <input class="form-control mo_security_textfield mo_boot_form-control" id="mo_lookupip" type="text"
                           name="mo_lookupip" placeholder="Enter an IP address in IPv4/IPv6 format" value="" width="100%;">
                </div>
            </div>
            <div class="mo_boot_row"><br></div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                    <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Lookup IP" name="submit">
                    <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Clear" name="clear_val">
                </div>
            </div>

            <?php
            echo '<br><div style="background: #e1dfdf;">
                <table style="border-collapse:collapse; margin-left: 33px; border-spacing:0; display:table;width:50%; font-size:10pt;">';
                    if (!empty($attrs)) {
                        foreach ($attrs as $key => $value)
                            echo "<tr>
                                     <td style='padding:2%;'>" . $key . " </td>
                                     <td style=' word-wrap:break-word;'> : " . implode('<br/>', (array)$value) . "</td>
                                  </tr>";
                    }
                    echo '</table>
                </div><br>';
            ?>

        <strong>Note: </strong>With IP Lookup you can trace the IP address of suspicious users, you can get an idea of what part of the country or world they are accessing your website from?<br>
        <hr>
        </form>

        <br>

        <details>
            <form name="mo_ip" method="post">
                <br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        You can block an IP address manually here:
                    </div>
                    <div class="mo_boot_col-sm-7">
                        <input class="form-control mo_security_textfield mo_boot_form-control" type="text" name="mo_manual_ip" disabled
                               placeholder="Enter an IP address" value="" pattern="((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}">
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                     <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" class="mo_boot_btn mo_boot_btn-primary" name="submit" value="Save" disabled>
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" placeholder="Search IP in the below table" id="myInput" disabled>
                    </div>
                </div>
            </form>

            <div class="table-responsive mo_boot_row" style="font-family: sans-serif;font-size: 12px;" id="mo_idp_vt_conf_table">
                <table class="mo_myTable mo_boot_mx-3" id="myTable">
                    <tr class="header mo_boot_text-white" style="line-height: 10px;background-color: #001b4c;">
                        <th class="mo_boot_text-center ipblock_style">IP Address</th>
                        <th class="mo_boot_text-center ipblock_style">Reason</th>
                        <th class="mo_boot_text-center ipblock_style">Blocked Until</th>
                        <th class="mo_boot_text-center ipblock_style">Blocked Date</th>
                        <th class="mo_boot_text-center ipblock_style">Action</th>
                    </tr>
                    <tbody style="font-size: 15px;color: #495057;">
                    <tr>
                        <td colspan="5" class="mo_boot_text-center" style="user-select: none !important;">
                            No IPs are blacklisted at this moment.
                        </td>
                    </tr>
                    </tbody>
                </table><br>
            </div><br>
            <summary style="cursor: pointer;">
                <strong>Manual Block IPs <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></strong>
            </summary>
        </details>

        <details class="mo_boot_mt-4">
            <form name="mo_save_whitelist_ips" method="post">
                <br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        You can manually whitelist IP address here:
                    </div>
                    <div class="mo_boot_col-sm-7">
                        <input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" name="mo_whitelist_ip" placeholder="Enter an IP address" value="" disabled>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save" disabled>
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_myInput mo_boot_form-control" type="text" placeholder="Search IP in the below table" id="moinput" disabled>
                    </div>
                </div>
            </form>

            <div class="table-responsive mo_boot_row" style="font-family: sans-serif;font-size: 12px;" id="mo_idp_vt_conf_table">
                <table class="mo_myTable mo_boot_mx-3" id="motable">
                    <tr class="header mo_boot_text-white" style="line-height: 15px;background-color: #001b4c;">
                        <th class="mo_boot_text-center ipwhitelist_style">IP Address</th>
                        <th class="mo_boot_text-center ipwhitelist_style">Whitelist Date</th>
                        <th class="mo_boot_text-center ipwhitelist_style">Action</th>
                    </tr>
                    <tbody style="font-size: 15px;color: #495057;">
                    <tr>
                        <td colspan="3" class="mo_boot_text-center" style="user-select: none !important;">
                            No IPs are whitelisted at this moment.
                        </td>
                    </tr>
                </table><br>
            </div><br>

            <summary style="cursor: pointer;">
                <strong>Whitelist IPs <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></strong>
            </summary>
        </details>
    </div><br>
        <?php
    }
}