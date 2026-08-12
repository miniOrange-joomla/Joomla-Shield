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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
class LicensePlans
{
	public static function moJoomshieldLicensePlans()
	{
		?>
			<div class="mo_boot_col-sm-12">
				<div class="mo_boot_row">
					<div class="mo_boot_col-sm-12 mo_boot_my-4 mo_boot_px-0 ">
						<div class="mo_js_pricing_wrapper">
							 <!-- Free -->
							<div class="mo_js_pricing_table">
								<div class="mo_js_pricing_plan_name"> <?php echo Text::_('COM_JOOMSHIELD_FREE'); ?> </div>
								<div class="mo_js_pricing_table_btn">
									<a href=""> <?php echo Text::_('COM_JOOMSHIELD_CURRENT_PLAN'); ?> </a>
								</div>

								<div class="mo_boot_my-4">
									<div class="mo_boot_d-flex mo_js_justify-content-between" onclick="toggleFeatureList('mo_free_feature_include')">
										<div ><span class="mo_oauth_square_check"><i class="fa-solid fa-square-check"></i></span></div>
										<div class="mo_js_pricing_table_feature_title"> <?php echo Text::_('COM_JOOMSHIELD_INCLUDED_FEATURES'); ?> </div>
										<div><span class="mo_js_pricing_table_feature_arrow"> <i class="fa-solid fa-chevron-down"></i> </span></div>
									</div>

									<ul id="mo_free_feature_include" class="mo_feature_list" style="display: none;">
										<li> <?php echo Text::_('COM_JOOMSHIELD_ADMIN_TOKEN'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_RESTRICT_FAKE_REGISTRATIONS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_LOOKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORD'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_LOGIN_TRANSACTIONS_REPORTS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_BROWSER_BLOCKING_MICROSOFT_EDGE'); ?> </li>
									</ul>
								</div>

								<div class="mo_boot_my-4">
									<div class="mo_boot_d-flex mo_js_justify-content-between" onclick="toggleFeatureList('mo_free_feature_exclude')">
										<div ><span class="mo_js_pricing_table_feature_square_xmark"><i class="fa-solid fa-square-xmark"></i></span></div>
										<div class="mo_js_pricing_table_feature_title"> <?php echo Text::_('COM_JOOMSHIELD_NOT_INCLUDED_FEATURES'); ?> </div>
										<div><span class="mo_js_pricing_table_feature_arrow"> <i class="fa-solid fa-chevron-down"></i> </span></div>
									</div>

									<ul id="mo_free_feature_exclude" class="mo_feature_list" style="display: none;">
										<li> <?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_RESTRICT_LOGIN_ATTEMPTS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_CUSTOM_TIME_PERIOD_FOR_BLOCKED_IPS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_WHITELIST_BLACKLIST_IP_ADDRESS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS_RANGE_BLOCKING'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_COUNTRY_BLOCKING'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_DATABASE_BACKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_SCHEDULED_AUTOMATIC_DATABASE_BACKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_BLOCKED_NOTIFICATION'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_UNUSUAL_ACCOUNT_ACTIVITY_NOTIFICATION_TO_USERS'); ?> </li>
									</ul>
								</div>
							</div>

							<!-- Premium Feature -->
							<div class="mo_js_pricing_table">
								<div class="mo_js_pricing_plan_name"> <?php echo Text::_('COM_JOOMSHIELD_PREMIUM'); ?> </div>
								<div class="mo_js_pricing_table_btn">
									<a href="https://www.miniorange.com/contact" target="_blank"> <?php echo Text::_('COM_JOOMSHIELD_UPGRADE_PLAN'); ?> </a>
								</div>

								<div class="mo_boot_my-4">
									<div class="mo_boot_d-flex mo_js_justify-content-between" onclick="toggleFeatureList('mo_premium_feature_include')">
										<div ><span class="mo_oauth_square_check"><i class="fa-solid fa-square-check"></i></span></div>
										<div class="mo_js_pricing_table_feature_title"> <?php echo Text::_('COM_JOOMSHIELD_INCLUDED_FEATURES'); ?> </div>
										<div><span class="mo_js_pricing_table_feature_arrow"> <i class="fa-solid fa-chevron-down"></i> </span></div>
									</div>

									<ul id="mo_premium_feature_include" class="mo_feature_list" style="display: none;">
										<li> <?php echo Text::_('COM_JOOMSHIELD_ADMIN_TOKEN'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_RESTRICT_FAKE_REGISTRATIONS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_LOOKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_ENFORCE_STRONG_PASSWORD'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_LOGIN_TRANSACTIONS_REPORTS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_BROWSER_BLOCKING'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_BRUTE_FORCE_PROTECTION'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_RESTRICT_LOGIN_ATTEMPTS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_CUSTOM_TIME_PERIOD_FOR_BLOCKED_IPS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_WHITELIST_BLACKLIST_IP_ADDRESS'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_ADDRESS_RANGE_BLOCKING'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_COUNTRY_BLOCKING'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_DATABASE_BACKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_SCHEDULED_AUTOMATIC_DATABASE_BACKUP'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_IP_BLOCKED_NOTIFICATION'); ?> </li>
										<li> <?php echo Text::_('COM_JOOMSHIELD_UNUSUAL_ACCOUNT_ACTIVITY_NOTIFICATION_TO_USERS'); ?> </li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

				<br>
				<div class="mo_boot_col-sm-12 mo_boot_px-0">
					<p> <i class="fa-solid fa-envelope"></i> <strong> <?php echo Text::_('COM_JOOMSHIELD_NEED_HELP'); ?> </strong> <?php echo Text::_('COM_JOOMSHIELD_CONTACT_US_AT_JOOMLASUPPORT_FOR_ANY_QUESTIONS_ABOUT_THIS_POLICY'); ?> </p>
				</div>
			</div>
		<?php
	}
}
