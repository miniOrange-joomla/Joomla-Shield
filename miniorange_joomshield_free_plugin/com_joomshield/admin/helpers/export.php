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

class Mo_Site_Protection extends BasicEnum
{
	const EMERGENCY_OFFLINE = 'emergency_offline';
	const OFFLINE_WHITELIST_IPS = 'offline_whitelist_ips';
	const ADMIN_HTTP_AUTH = 'admin_http_auth';
	const ADMIN_HTTP_USER = 'admin_http_user';
	const ADMIN_HTTP_WHITELIST_IPS = 'admin_http_whitelist_ips';
	const FEATURE_LOCK_ENABLED = 'feature_lock_enabled';
	const FEATURE_LOCK_ITEMS = 'feature_lock_items';
	const SERVER_RULES_APACHE = 'server_rules_apache';
	const STALE_URL_REWRITE = 'link_migration_live';
	const STALE_URL_HOSTS = 'link_migration_old_hosts';
}
