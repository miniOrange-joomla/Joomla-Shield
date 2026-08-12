<?php

/**
 * @package    Joomla.Administrator
 * @subpackage com_joomshield
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

defined('_JEXEC') or die;
require_once 'BasicEnum.php';

class Mo_Login_Security extends BasicEnum
{
	const ID = 'id';
	const ENABLE_CUSTOM_ADMIN_LOGIN = 'enable_custom_admin_login';
	const ACCESS_LGN_URLKY = 'access_lgn_urlky';
	const AFTER_ADM_FAILURE_RESPONSE = 'after_adm_failure_response';
	const CUSTOM_FAILURE_DESTINATION = 'custom_failure_destination';
	const CUSTOM_MESSAGE_AFTER_FAIL = 'custom_message_after_fail';
	const ENFORCE_STRONG_PASSWORD_LOGIN = 'enforce_strong_password_login';
}

class Mo_Register_Security extends BasicEnum
{
	const BLOCK_FAKE_EMAILS = 'block_fake_emails';
	const MO_EMAIL_DOMAINS = 'mo_email_domains';
	const ENFORCE_STRONG_PASSWORD_REGISTER = 'enforce_strong_password_register';
}

class Mo_Advance_Blocking extends BasicEnum
{
	const MO_ENABLE_BROWSER_BLOCKING = 'mo_enable_browser_blocking';
	const MO_MEDGE_BLOCKING = 'mo_medge_blocking';
}
