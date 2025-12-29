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
include_once 'BasicEnum.php';
class mo_login_security extends BasicEnum {
    const id = "id";
    const enable_custom_admin_login = "enable_custom_admin_login";
    const access_lgn_urlky = "access_lgn_urlky";
    const after_adm_failure_response = "after_adm_failure_response";
    const custom_failure_destination = "custom_failure_destination";
    const custom_message_after_fail = "custom_message_after_fail";
    const enforce_strong_password_login = "enforce_strong_password_login";
}

class mo_register_security extends BasicEnum{
    const block_fake_emails = "block_fake_emails";
    const mo_email_domains = "mo_email_domains";
    const enforce_strong_password_register = "enforce_strong_password_register";
}

class mo_advance_blocking extends BasicEnum{
    const mo_enable_browser_blocking = "mo_enable_browser_blocking";
    const mo_medge_blocking = "mo_medge_blocking";
}