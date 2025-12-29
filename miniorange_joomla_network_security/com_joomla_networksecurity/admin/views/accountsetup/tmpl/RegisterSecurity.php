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

class RegisterSecurity
{
    public static function mo_networksecurity_register_security_form()
    {

        $config_result     = NetworkSecurityUtilities::getRegisterSecurityConfig();
        $block_fake_emails = $config_result['block_fake_emails'] ?? 0;
        $extra_email       = $config_result['mo_email_domains'] ?? '';
        $strong_password   = $config_result['enforce_strong_password_register'] ?? 0;

        ?>
        <div class="mo_boot_col-sm-12">
        <form name="mo_register_security" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=registersecurity.saveRegisterSecuritySettings'); ?>">

            <input type="hidden" name="mo_block_fake_registration" value="block_fake_registration" id="block_fake_registration_id">
            <div class="mo_boot_row">
                <div class="mo_boot-col-sm-12 mo_boot_mt-3">
                    <h3>Block Registrations from fake users</h3>
                </div>
            </div>
            <hr><br>

            <div>To block specific email domains from registering as users.</div>
            <br>

            <input type="checkbox" class="mo_blk_fake_emails checkbox_style" name="block_fake_emails" onclick="enable_checkbox_blk_email()" value="1"
                <?php if ($block_fake_emails == 1) echo "checked"; ?> > Enable blocking of registrations from specific email domains.<br>

            <div class="mo_boot_row"><br></div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-5">Enter email domains you want to block :</div>
                <div class="mo_boot_col-sm-7"><textarea rows="4" cols="55" name="mo_email_domains" style="width: 77%;border: 1px solid #868383 !important;border-radius: 3px;" class="mo_enable_blk_email" required
                              placeholder="Enter semicolon(;) seperated domains. For example:nuevomail.com;finxmail.com" onkeyup="nospaces(this,'Please enter the email domains without spaces.');"><?php echo $extra_email; ?></textarea>
                </div>
            </div>
            <div class="mo_boot_row"><br></div>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                    <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save">
                </div>
            </div><br>
            <script>
                enable_checkbox_blk_email();
                function enable_checkbox_blk_email() {
                    var block_fake_emails      = document.getElementsByClassName('mo_blk_fake_emails')[0];
                    var mo_enable_blk_email       = document.getElementsByClassName('mo_enable_blk_email')[0];

                    mo_enable_blk_email.disabled = block_fake_emails.checked !== true;
                }
            </script>
        </form>

        <form name="mo_register_security" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=registersecurity.saveRegisterSecuritySettings'); ?>">

            <h3>Enforce Strong Passwords</h3>
            <hr><br>
            <div>To enforce the new user to enter strong password to enhance registration security</div>
            <br>

            <input class="checkbox_style" type="checkbox" name="enforce_strong_password_register" value="1"
                <?php if ($strong_password == 1) echo "checked"; ?> >Enable strong passwords.<br><br>

            <a href="#!enforce_strong_pass_instr_register" onclick="collapse_link('enforce_strong_pass_instr_register')">
                <strong>You can restrict the following conditions to user for strong password</strong></a>
            <div id="enforce_strong_pass_instr_register" style="display: none;">
                <ul><br>
                    <li>Password should contain at least one Capital and one Small Letter.</li>
                    <li>Password should be Minimum 12 Characters</li>
                    <li>Password should contain at least one Numeric Character.</li>
                    <li>Password should contain at least one Special Character (!,@,#,$,%,^,&,*,?,_,~,-) .</li>
                </ul>
            </div>

            <div class="mo_boot_row"><br></div>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                    <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save">
                </div>
            </div><br>
        </form>
        </div>
        <?php
    }
}