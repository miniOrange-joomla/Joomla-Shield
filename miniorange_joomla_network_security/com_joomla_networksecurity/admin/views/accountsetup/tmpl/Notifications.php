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
class Notifications
{
    public static function mo_networksecurity_email_notifications_form()
    {
        ?>
        <div class="mo_boot_col-sm-12">
        <h3 class="mo_boot_mt-3">Email Notifications <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></h3>
        <hr>
        <form name="mo_email_notifications" method="post">
            <input type="checkbox" class="check_email_notify_admin checkbox_style" value="1" name="check_email_notify_admin" onclick="enable_checkbox_notification()" disabled>
            Notify Administrator if IP address is blocked.<br><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-5">
                    <p>Enter the Administrator Email:</p>
                </div>
                <div class="mo_boot_col-sm-7">
                    <textarea id="email_id" name="admin_email_address" class="admin_email_address" style="border-radius:4px;resize: vertical;width: 77%;height: 55px;" placeholder="Enter an Admin Email address here" disabled></textarea>
                </div>
            </div><br>

            <input class="checkbox_style" type="checkbox" value="1" name="check_email_notify_user" disabled>Notify users for unusual activity with their account.<br><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                    <input type="submit" class="mo_boot_btn mo_boot_btn-primary" value="Save" disabled>
                </div>
            </div>
            <script>
                function enable_checkbox_notification() {
                    var email_notify = document.getElementsByClassName('check_email_notify_admin')[0];
                    var admin_email = document.getElementsByClassName('admin_email_address')[0];

                    admin_email.disabled = email_notify.checked !== true;
                }
            </script>
        </form>
        </div>
        <?php
    }
}