<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 */
defined('_JEXEC') or die;

class AdvancedFeatures
{
    public static function mo_networksecurity_advanced_features()
    {
        jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');
        ?>

        <div class="mo_boot_col-sm-12 mo_boot_mt-3">
            <h3>Advanced Features</h3>
        </div>
        <div class="mo_boot_col-sm-12"><hr></div>

        <div class="mo_boot_col-sm-12">
            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="block_xss_attack" disabled>
                    <strong>Cross Site scripting(XSS) Protection<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>Cross Site scripting is used for script attacks. This will block illegal scripting on website.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="block_sql_injection" disabled>
                    <strong>Block SQL Injection<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>SQL Injection attacks are used for attack on database. This option will block all illegal requests which tries to access your database.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="disable_right_click" disabled>
                    <strong>Disable Mouse Right Click<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>" ><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>If you enable this feature then it will disable site selecting text or images and copy, completely disable right click and
                        show a custom popup with the message you want.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="backend_password" disabled>
                    <strong>Backend Password<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>If you enable this feature, then you will be asked to enter the password on hitting the admin URL. This way the security of your site will increase.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="disable_create_new_admin" disabled>
                    <strong>Disable the creation of new administrator<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>If you enable this feature it will disable the creation of the new administrator.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="protect_administrator" disabled>
                    <strong>Protect administrator details<a  class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>If you enable this feature it will protect the selected administrator from any changes - including password change.</em></p>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2 alert alert-info">
                <div class="mo_boot_col-sm-12">
                    <input class="mo_boot_mr-2" type="checkbox" name="monitor_tracking" disabled>
                    <strong>Notifications/Alerts to admin for any changes<a class="advnc_style" href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&view=licensing'?>"><sup> Premium Feature</sup></a></strong><br>
                    <p class="mo_boot_ml-4"><strong>Note: </strong><em>Monitor extensions and be notified when the plugin have been added or removed to particular email address.</em></p>
                </div>
            </div>
        </div>
<?php
    }
}