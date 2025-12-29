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
JHtml::_('script', JUri::base() . 'components/com_joomla_networksecurity/assets/js/utility.js');

class Register
{
    public static function mo_networksecurity_registration_page()
    {
        $current_user = JFactory::getUser(); ?>

        <!--Register with miniOrange-->

        <form name="mo_register" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&view=accountsetup&task=accountsetup.registerCustomer'); ?>">
            <h3 class="mo_boot_mt-3 mo_boot_text-center">Register / Login with mini<span style="color: orange;">O</span>range</h3><hr>
            <p class='alert alert-info mo_boot_text-center'><?php echo JText::_('COM_JOOMLANETWORKSECURITY_REGISTER_MESSAGE'); ?></p><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong><span class="mo_boot_text-red">*</span>Email:</strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" type="email" name="email" required
                           placeholder="person@example.com" value="<?php echo $current_user->email; ?>"/>
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong>Phone number:</strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input type="tel" name="phone" class="mo_jnsp_query_phone mo_boot_form-control" style="width: 80% !important;" id="mo_jnsp_query_phone" placeholder="Enter your phone with country code" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" />
                        <p><em>We will call only if you call for support</em></p>
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong><span class="mo_boot_text-red">*</span>Password:</strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" required type="password" id="myPassword" name="password"
                               placeholder="Choose your password (Min. length 12)"/>
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong><span class="mo_boot_text-red">*</span>Confirm Password:</strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" required type="password"
                               name="confirmPassword" placeholder="Confirm your password"/>
                    </div>
                </div>
                <div class="mo_boot_row"><br></div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" value="Register" class="mo_boot_btn mo_boot_btn-primary" />
                        <a class="mo_boot_btn mo_boot_btn-primary" onclick="acc_exist()" >Login</a>
                    </div>
                </div>
            <script>
                function acc_exist(){
                    jQuery('#login_form').submit();
                }
            </script>
        </form>
        <form name="f" id="login_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&view=accountsetup&task=accountsetup.customerLoginForm'); ?> "></form>
        <?php
    }

    /* Show OTP verification page*/
    public static function mo_networksecurity_show_otp()
    {
        ?>
        <div class="mo_boot_col-sm-12">
        <!-- Enter otp -->
            <form name="f" method="post" id="mo_network_form" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.validateOtp'); ?>">
                <h2 class="mo_boot_mt-3 mo_boot_text-center">Register / Login with mini<span style="color: orange;">O</span>range</h2><hr><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <span><strong>Enter OTP:<span style="color: #ff0000;">*</span></strong></span>
                    </div>
                    <div class="mo_boot_col-sm-4">
                        <input class="mo_boot_form-control" style="width: fit-content" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP"/>
                    </div>
                </div><br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" value="Validate OTP" class="mo_boot_btn mo_boot_btn-primary"/>
                        <a style="cursor:pointer;" class="mo_boot_btn mo_boot_btn-primary" onclick="document.getElementById('resend_otp_form').submit();">Resend OTP over Email</a>
                        <a id="goBack" type="submit" class="mo_boot_btn mo_boot_btn-danger" onclick="resend_otp();">Back</a>
                    </div>
                </div><br><br><br>
            </form>
            <script>
                document.getElementById("goBack").disabled = false;
            </script>
            <form method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.cancelform'); ?>" id="mo_otp_cancel_form"></form>
            <form name="f" id="resend_otp_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.resendOtp'); ?>">
            </form>
        </div>
        <?php
    }

    public static function mo_networksecurity_local_account_page() {
        $result         = MonetworksecurityDB::getCustomerDetails();
        $email          = $result['email'];
        $phpVersion     = phpversion();
        $jVersion       = new JVersion();
        $jCmsVersion    = $jVersion->getShortVersion();
        $moPluginVersion= NetworkSecurityUtilities::GetPluginVersion();

        ?>
        <div class="mo_boot_col-sm-12">
                <p class="mo_security_welcome_message mo_boot_py-2"><strong>Thank You for registering with miniOrange.</strong><p>
        </div>
        <div class="mo_boot_col-sm-12">
            <br>
        </div>
        <div class="mo_boot_col-sm-12 mo_boot_text-center">
            <h3>YOUR PROFILE</h3>
        </div>
        <div class="mo_boot_col-sm-12">
            <hr>
        </div>
        <table class="table table-striped table-hover table-bordered idp-table">
            <tr>
                <td class="profile_style"><strong>Username/Email</strong></td>
                <td class="profile_style"><?php echo $email?></td>
            </tr>
            <tr>
                <td class="profile_style"><strong>Plugin version</strong></td>
                <td class="profile_style"><?php echo $moPluginVersion ?></td>
            </tr>
            <tr>
                <td class="profile_style"><strong>PHP version</strong></td>
                <td class="profile_style"><?php echo $phpVersion ?></td>
            </tr>
            <tr>
                <td class="profile_style"><strong>Joomla version</strong></td>
                <td class="profile_style"><?php echo $jCmsVersion ?></td>
            </tr>
        </table
        <div class="mo_boot_col-sm-12"><br></div>

        <div class="mo_boot_col-sm-12">
            <form method="post" id="saml-sp-key-form" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.removeAccount'); ?>">
                <div class="mo_boot_row">
                    <h3>Remove Your Account</h3>
                </div>
                <hr>
                <div class="mo_boot_row">
                    <strong>Note: By clicking the button, you can remove your account and all the configurations saved for this site won't be deleted.</strong>
                </div>
                <br>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <button id="myBtn" class="mo_boot_btn mo_boot_btn-danger">Remove Account</button>
                    </div>
                </div><br>
            </form>
        </div>
    <?php
}

    public static function mo_networksecurity_local_login_page() {
        $admin_email = MonetworksecurityDB::getCustomerDetails();
        $admin_email = $admin_email['email'] ?? '';
        ?>
        <div class="mo_boot_col-sm-12">
            <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.verifyCustomer');?>">
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <h3>Login with mini<span style="color: orange;">O</span>range</h3>
                    </div>
                </div>
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12">
                    <hr>
                    </div>
                </div>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-12 mo_boot_text-center">
                        <p class="alert alert-info">Please enter your miniOrange account credentials. If you forgot your password then enter your email and click on <strong>Forgot your password</strong> link. If you are not registered with miniOrange then click on <strong>Back To Registration</strong> link. </p>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                        <strong>Email:<span class="mo_boot_text-red"> *</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_security_textfield mo_boot_form-control" type="email" name="email" id="email" placeholder="person@example.com" value="<?php echo $admin_email; ?>" required />
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-2 mo_boot_offset-sm-2">
                        <strong>Password:<span class="mo_boot_text-red"> *</span></strong>
                    </div>
                    <div class="mo_boot_col-sm-6">
                        <input class="mo_security_textfield mo_boot_form-control" type="password" name="password" placeholder="Enter your miniOrange password" required />
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-8 mo_boot_offset-sm-4">
                        <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Login"/>
                        <a class="mo_boot_btn mo_boot_btn-primary" onclick="back_reg()">Back</a>
                        <input type="submit" class="mo_boot_btn mo_boot_btn-danger" onclick="window.open('https://login.xecurify.com/moas/idp/resetpassword');" value="Forgot your password?">
                    </div>
                </div>
            </form>

            <form id="mo_forgot_password_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.forgotpassword');?>">
                <input type="hidden" name="current_admin_email" id="current_admin_email" value="" />
            </form>

            <form id="mo_jnsp_redirect_register_page" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.cancelform');?>">
                <input type="hidden" name="register_page_redirect" value="" />
            </form>

            <form id="mo_otp_cancel_form" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=accountsetup.cancelform');?>">
            </form>

    </div>
    <script>
        function back_reg(){
            jQuery('#mo_jnsp_redirect_register_page').submit();
        }

        jQuery('a[href=#mo_otp_cancel_form]').click(function(){
            jQuery('#mo_otp_cancel_form').submit();
        });
    </script>
    <?php
    }
}