<?php
defined('_JEXEC') or die;
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

function mo_jnsp_support()
{
    $current_user = JFactory::getUser();
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName('#__miniorange_networksecurity_customer'));
    $query->where($db->quoteName('id') . " = 1");

    $db->setQuery($query);
    $result = $db->loadAssoc();

    $admin_email = $result['email'];
    $admin_phone = $result['admin_phone'];

    if ($admin_email == '')
        $admin_email = $current_user->email;


    ?>

    <div id="sp_support" class="mo_boot_col-sm-12">
        <div class="mo_boot_row mo_boot_mt-3">
            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                <h2>Support/Feature Request</h2>
            </div>
        </div><hr>

        <form name="f" method="post" action="<?php echo JRoute::_('index.php?option=com_joomla_networksecurity&task=mocontactus._contactUs'); ?>">
            <div class="mo_boot_text-center">
                Need any help? We can help you with configuring the plugin. Just send us a query, and we will get back to you soon.
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-1 mo_boot_offset-sm-3">
                    <strong>Email:<span class="mo_boot_text-red">*</span></strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input type="email" class="mo_jnsp_table_textbox mo_boot_form-control mo_boot_px-2" name="query_email" value="<?php echo $admin_email; ?>" placeholder="Enter your email" required/>
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2">
                <div class="mo_boot_col-sm-1 mo_boot_offset-sm-3">
                    <strong>Phone:</strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input type="tel" name="query_phone" class="mo_jnsp_query_phone mo_boot_form-control mo_boot_px-2"  id="mo_jnsp_query_phone" placeholder="Enter your phone with country code" pattern="[\+]\d{11,14}|[\+]\d{1,4}([\s]{0,1})(\d{0}|\d{9,10})" value="<?php echo $admin_phone; ?>" />
                </div>
            </div>

            <div class="mo_boot_row mo_boot_mt-2">
                <div class="mo_boot_col-sm-1 mo_boot_offset-sm-3">
                    <strong>Query:<span class="mo_boot_text-red">*</span></strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <textarea class="mo_boot_textarea-control mo_boot_px-2" name="query" style="border-radius:4px;resize: vertical;width:100%" cols="52" rows="5" placeholder="Write your query here"></textarea>
                </div>
            </div><br>

            <input type="hidden" name="option1" value="mo_jnsp_send_query"/>
            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-12 mo_boot_text-center">
                    <input type="submit" name="send_query" value="Submit Query" class="mo_boot_btn mo_boot_btn-primary"/>
                </div>
            </div><br>
            <p class="mo_boot_text-center">If you want custom features in the plugin, just drop an email to <a href="mailto:joomlasupport@xecurify.com"> joomlasupport@xecurify.com</a></p>
        </form>

    </div>
    <script>
        //jQuery("#query_phone").intlTelInput();
        function mo_jnsp_valid(f) {
            !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
        }
    </script>
    <?php
}