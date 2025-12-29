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

class DB_Backup
{
    public static function mo_networksecurity_db_backup_form()
    {
        $db_sync_interval = '';
        ?>
            
        <div class="mo_boot_col-sm-12">
        <h3 class="mo_boot_mt-3">Database Backup <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></h3>
        <hr>
        <div>Backup your Joomla database easily.</div><br>
        <form name="mo_take_db_backup" method="post">
                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong>Host Name: </strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_host_name" type="text" placeholder="Enter your host name:" disabled/>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong>Database Username: </strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_username" type="text" placeholder="Enter your Database username:" disabled/>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong>Database Password: </strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_password" type="password" placeholder="Enter your Database password:" disabled/>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                        <strong>Database Name: </strong>
                    </div>
                    <div class="mo_boot_col-sm-5">
                        <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_name" type="text" placeholder="Enter your Database name:" disabled/>
                    </div>
                </div><br>

                <div class="mo_boot_row">
                    <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                        <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Backup" disabled>
                    </div>
                </div>
        </form><br><br>
        <h3>Automatic / Scheduled Database Backup <sup><a href='index.php?option=com_joomla_networksecurity&view=licensing'><strong>Premium Feature</strong></a></sup></h3><hr>

        <form name="mo_take_db_backup" method="post">

            <input type="checkbox" class="mo_enable_automatic_bckp checkbox_style" value="true" name="mo_enable_automatic_backup" disabled>
            Enable the checkbox if you want to take a scheduled backup, otherwise you can take a backup manually.<br><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                    <strong>Host Name: </strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_host_name" type="text" placeholder="Enter your host name:" disabled/>
                </div>
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                    <strong>Database Username: </strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_username" type="text" placeholder="Enter your Database username:" disabled/>
                </div>
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                    <strong>Database Password: </strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_password" type="password" placeholder="Enter your Database password:" disabled/>
                </div>
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                    <strong>Database Name: </strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <input class="form-control mo_security_textfield mo_boot_form-control" name="mo_db_name" type="text" placeholder="Enter your Database name:" disabled/>
                </div>
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-3 mo_boot_offset-sm-2">
                    <strong>Select how often you want to take the Database backup: </strong>
                </div>
                <div class="mo_boot_col-sm-5">
                    <select class="mo_db_backup_dropdown" name="sync_interval">
                        <option value="hourly" <?php if ($db_sync_interval == "hourly") echo "selected"; ?>>hourly</option>
                        <option value="daily" <?php if ($db_sync_interval == "daily") echo "selected"; ?>>daily</option>
                        <option value="weekly" <?php if ($db_sync_interval == "weekly") echo "selected"; ?>>weekly</option>
                        <option value="monthly" <?php if ($db_sync_interval == "monthly") echo "selected"; ?>>monthly</option>
                    </select>
                </div>
            </div><br>

            <div class="mo_boot_row">
                <div class="mo_boot_col-sm-7 mo_boot_offset-sm-5">
                    <input type="submit" name="submit" class="mo_boot_btn mo_boot_btn-primary" value="Backup" disabled>
                </div>
            </div><br>
        </form>
        </div>
        <?php
    }
}