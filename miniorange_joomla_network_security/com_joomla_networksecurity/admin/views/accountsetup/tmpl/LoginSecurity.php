<?php
/**
 * @package    miniOrange
 * @subpackage Plugins
 * @license    GNU/GPLv3
 * @copyright  Copyright 2015 miniOrange. All Rights Reserved.
 */
defined('_JEXEC') or die;

class LoginSecurity
{
    public static function mo_networksecurity_login_security_form()
    {
        jimport('zminiorangejoomlanetworksecurityplugin.utility.NetworkSecurityUtilities');

        $config_result = NetworkSecurityUtilities::getLoginSecurityConfig();
        $time_of_blocking_type = "days";
        $time_of_blocking_val = 0;

        $is_custom_admin_loign = $config_result['enable_custom_admin_login'] ?? 0;
        $login_url_key         = $config_result['access_lgn_urlky'] ?? '';
        $after_failure         = $config_result['after_adm_failure_response'] ?? '';
        $custom_dest           = $config_result['custom_failure_destination'] ?? '';
        $custom_err_message    = $config_result['custom_message_after_fail'] ?? 'Some error has occurred.';
        $strong_password       = $config_result['enforce_strong_password_login'] ?? 0;

        if (empty(trim($custom_err_message))) {
            $custom_err_message = 'Some error has been occurred.';
        }

        $base_url = JUri::root();
        $current_admin_login_url = $base_url . 'administrator';
        $custom_admin_loign_url = $current_admin_login_url . '/?' . $login_url_key;
        ?>

        <div id="import_export_form" class="mo_boot_col-sm-12" style="display:none;">
            <div class="mo_boot_row mo_boot_mt-4">
                <div class="mo_boot_col-sm-10 mo_boot_text-center">
                    <h3>Import /Export Configuration</h3>
                </div>
                <div class="mo_boot_col-sm-2 mo_boot_text-right">
                    <button type="button" class="mo_boot_btn mo_boot_btn-danger" onclick="hide_import_export()">Cancel</button>
                </div>
            </div><hr>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12">
                    <h3>Export configuration file</h3>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-3">
                <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=loginsecurity.importexport'); ?>">
                    <div class="mo_boot_col-sm-12">
                        <input type="submit" class="mo_boot_btn mo_boot_btn-primary" name="sub" value="Export Configuration" />
                    </div>
                </form>
            </div><br><hr>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12">
                    <h3>Import Configurations</h3>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-3">
                <div class="mo_boot_col-sm-12">
                    <form name="f" method="post" enctype="multipart/form-data" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=loginsecurity.import'); ?>">
                        <input type="file" name="configuration_file" class="mo_btn mo_import_file mo_boot_mr-1" required>
                        <input type="submit" name="submit" onclick="submit()" class="mo_boot_btn mo_boot_btn-primary" value="Import"/>
                    </form>
                </div>
            </div><br>
        </div>

        <form name="mo_login_security" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=loginsecurity.saveLoginSecuritySettings'); ?> ">

            <input type="hidden" name="mo_customize_admin_url" value="custom_adn_url" id="ctm_adm_url">
            <div id="main-div">
                <div class="mo_boot_row mo_boot_mt-3">
                    <div class="mo_boot_col-sm-12">
                        <h3 style="display: inline-block;">Customize Admin Login Page URL</h3>
                        <button type="button" id="import_export_btn" class="mo_boot_btn mo_boot_btn-primary mo_boot_float-right" onclick="show_import_export();">Import/Export</button>
                    </div>
                </div>
                <div class="mo_boot_row"><div class="mo_boot_col-sm-12"><hr></div></div>
                <div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        This protects your admin login page from attacks which tries to gain access / login to a admin site.
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Enable Custom Login Page URL:
                        <p><strong>Note: </strong>After enabling this you won't be able to login using<code>/administrator</code></p>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input type="checkbox" class="enable_custom_login" value="1" onclick="enable_checkbox_url()" name="enable_custom_admin_login"
                            <?php if ($is_custom_admin_loign == 1) echo "checked"; ?> >
                    </div>
                </div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Access Key for your Admin login URL :
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="admin_log_url mo_boot_form-control" id="custom_login_url_key"
                               type="text" name="access_lgn_urlky" onchange="login_url_key(this.value)" onkeyup="nospaces(this,'Please enter the URL without spaces.');login_url_key(this.value);"
                               placeholder="Enter Key" value="<?php echo $login_url_key; ?>" required/>
                    </div>
                    <div class="mo_boot_col-sm-1">
                        <em class="fa fa-pull-right fa-lg fa-copy mo_copy copytooltip" onclick="copyElement('#custom_login_url_key');" style="color: black;background:#ccc;" ;>
                            <span class="copytooltiptext">Copied!</span>
                        </em>
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Admin Login URL:
                    </div>
                    <div class="mo_boot_col-sm-5" id="currentAdminUrl"><?php echo $current_admin_login_url;?></div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Current Admin Login URL:
                    </div>
                    <div class="mo_boot_col-sm-5" id="custom_admin_url"><?php echo $custom_admin_loign_url; ?>
                    </div>
                    <div class="mo_boot_col-sm-1">
                        <em class="fa fa-pull-right fa-lg fa-copy mo_copy copytooltip" onclick="copyToClipboard('#custom_admin_url');" style="color: black;background:#ccc;" ;>
                            <span class="copytooltiptext">Copied!</span>
                        </em>
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-5">
                        Redirect after Failure Response :
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <select class="mo_security_dropdown redirect_after_failure mo_boot_form-control" id="failure_response"
                                name="after_adm_failure_response" onchange="redirect_after_failure_dropdown(this.value);" style="width: 100% !important;">
                            <option value="redirect_homepage" <?php if ($after_failure == "redirect_homepage") echo "selected"; ?>>
                                Homepage
                            </option>
                            <option value="404_custom_message" <?php if ($after_failure == "404_custom_message") echo "selected"; ?>>
                                Custom 404 Message
                            </option>
                            <option value="custom_redirect_url" <?php if ($after_failure == "custom_redirect_url") echo "selected"; ?>>
                                Custom Redirect URL
                            </option>
                        </select>
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row" id="custom_fail_dest" <?php if ($after_failure != "custom_redirect_url") echo 'style="display:none;"'; ?> >
                    <div class="mo_boot_col-sm-5">
                        Custom redirect URL after failure:
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="mo_boot_form-control mo_security_textfield custom_url_after_failure" id="custom_fail_dest_id" type="text" name="custom_failure_destination" style="width: 100% !important;" value="<?php echo $custom_dest; ?>" placeholder="Enter the redirect URL"/>
                    </div>
                </div>

                <div class="mo_boot_row"
                     id="custom_message" <?php if ($after_failure != "404_custom_message") echo 'style="display:none;"'; ?>>
                    <div class="mo_boot_col-sm-5">
                        Custom error message after failure:
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <textarea class="form-control mo_security_textfield 404_message" name="custom_message_after_fail" style="width: 100% !important;"><?php echo $custom_err_message; ?></textarea>
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save">
                    </div>
                </div>
                </form>
                <br><br>

        <form name="mo_login_security" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=loginsecurity.saveLoginSecuritySettings'); ?> ">

            <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <h3>Enforce Strong Passwords</h3>
                    </div>
                </div>
                <div class="mo_boot_row"><div class="mo_boot_col-sm-12"><hr></div></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        Checks the password strength of admin and other users to enhance login security
                    </div>
                </div>
                <div><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                    <input class="checkbox_style" type="checkbox" name="enforce_strong_password_login" value="1"
                        <?php if ($strong_password == 1) echo "checked"; ?> style="">Enable strong passwords.<br><br>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                        <a href="#!enforce_strong_pass_instr_login" onclick="collapse_link('enforce_strong_pass_instr_login')">
                            <strong>You can restrict the following conditions to user for strong password</strong></a>
                    </div>
                </div>

            <div id="enforce_strong_pass_instr_login" class="mo_boot_row" style="display:none">
                <div class="mo_boot_col-sm-12">
                    <ul><br>
                        <li>Password should contain at least one Capital and one Small Letter.</li>
                        <li>Password should be Minimum 12 Characters</li>
                        <li>Password should contain at least one Numeric Character.</li>
                        <li>Password should contain at least one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>
                    </ul>
                </div>
            </div>

                <div class="mo_boot_row"><br></div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save">
                    </div>
                </div>

                <div class="mo_boot_row"><br></div>
            </div>
        </form><br>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-12">
                <h3>Brute Force Protection ( Login Protection )<sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup>
                </h3>
            </div>
        </div><div class="mo_boot_row"><div class="mo_boot_col-sm-12"><hr></div></div><br>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-12">
            This protects your site from attacks which tries to gain access / login to a site with random usernames and passwords.
            </div>
        </div>
        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-5">
                Enable Brute force protection:
            </div>
            <div class="mo_boot_col-sm-7">
                <input type="checkbox" name="enable_brute_force_protection" class="enable_brute" value="1" disabled>
            </div>
        </div>
        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
                <div class="mo_boot_col-sm-5">
                    Allowed login attempts before blocking an IP :
                </div>
                <div class="mo_boot_col-sm-3">
                    <input class="form-control mo_security_textfield allowed_login_attempts mo_boot_form-control" type="number" id="allowed_login_attempts"
                           min="1" name="allwoed_no_of_login_attempts" placeholder="Enter no of login attempts" value="0" disabled/>
                </div>
        </div>

        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-5">
                Time period for which IP should be blocked :
            </div>
            <div class="mo_boot_col-sm-3">
                <select name="time_of_blocking_type" class="mo_security_dropdown mo_boot_form-control" disabled>
                    <option value="permanent" <?php if ($time_of_blocking_type == "permanent") echo "selected"; ?>>
                        Permanently
                    </option>
                    <option value="months" <?php if ($time_of_blocking_type == "months") echo "selected"; ?>>
                        Months
                    </option>
                    <option value="days" <?php if ($time_of_blocking_type == "days") echo "selected"; ?>>
                        Days
                    </option>
                    <option value="hours" <?php if ($time_of_blocking_type == "hours") echo "selected"; ?>>
                        Hours
                    </option>
                </select>
            </div>
            <div class="mo_boot_col-sm-3">
                <input class="form-control mo_security_textfield how_many_text mo_boot_form-control" type="number" name="time_of_blocking_value" min="1" placeholder="How many?"
                       <?php ($time_of_blocking_type == "permanent" ? "hidden" : "") ?>value="<?php echo $time_of_blocking_val; ?>" disabled/>
            </div>
        </div>

        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-5">
                Show remaining login attempts to user:
            </div>
            <div class="mo_boot_col-sm-7">
                <input type="checkbox" class="show_remaining_log" name="show_remaining_login_attempts" disabled/>
            </div>
        </div>

        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-5">
                Bypass for Admin console login:
            </div>
            <div class="mo_boot_col-sm-7">
                <input type="checkbox" class="bypass_for_admin" name="bf_bypass_for_admin_login" disabled/>
            </div>
        </div>
        <div class="mo_boot_row"><br></div>

        <div class="mo_boot_row">
            <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save" disabled>
            </div>
        </div>

        <div class="mo_boot_row"><br></div>

    </div><br>

        <script>
            enable_checkbox_url();

            function enable_checkbox_url() {
                var custom_log_url = document.getElementsByClassName('enable_custom_login')[0];
                var admin_log_url = document.getElementsByClassName('admin_log_url')[0];
                var custom_redirect_url = document.getElementsByClassName('redirect_after_failure')[0];
                var custom_url_failure = document.getElementsByClassName('custom_url_after_failure')[0];
                var error_msg = document.getElementsByClassName('404_message')[0];

                admin_log_url.disabled = custom_log_url.checked !== true;
                custom_redirect_url.disabled = custom_log_url.checked !== true;
                custom_url_failure.disabled = custom_log_url.checked !== true;
                error_msg.disabled = custom_log_url.checked !== true;
            }
        </script>
        <?php
    }
}