<?php
   /**
    * @package     Joomla.Administrator
    * @subpackage  com_miniorange_twofa
    *
    * @license     GNU General Public License version 2 or later; see LICENSE.txt
    */
   defined('_JEXEC') or die('Restricted access');
   JHtml::_('jquery.framework',false);
   JHtml::_('stylesheet', JUri::base() .'components/com_joomla_networksecurity/assets/css/miniorange_netsecurity.css');
   JHtml::_('stylesheet', JURI::base() .'components/com_joomla_networksecurity/assets/css/miniorange_boot.css');
   JHtml::_('script', JUri::base() . 'components/com_joomla_networksecurity/assets/js/utility.js');
   JHtml::_('stylesheet','https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
   $mfa_active_tab="license";

?>

<div id="account" class="mo_boot_container-fluid" style="background: white;">
    <div class="mo_boot_row">
        <div class="mo_boot_col-sm-12">
            <?php licensingtab(); ?>
        </div>
    </div>
</div>
<?php
   function licensingtab()
    {
        ?>
        <div class="mo_boot_row" style="background: #ddecff;border-radius: 5px;box-shadow: 0px 0px 10px black;">
            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_row mo_boot_mt-2">
                    <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                        <h2 style="display: inline-block;color: black;">PLANS AND FEATURES</h2>
                        <a href="<?php echo JURI::base().'index.php?option=com_joomla_networksecurity&tab=account_setup';?>" style="float: right;" class="mo_boot_btn mo_boot_btn-primary mo_boot_px-5">BACK TO PLUGIN CONFIGURATION</a>
                        <hr>
                    </div>
                </div>
                <div class="mo_boot_row mo_boot_mt-4 mo_boot_text-center">
                    <div class="" style="width: 48%;">
                        <div class="mo_boot_row mo_boot_m-1" style="border: 3px solid #3d618f;background: white;border-radius: 10px;">
                            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                <h2>Lite</h2><hr><br>

                                <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;">$0</span><br><br><br><br>
                                <a class="mo_boot_btn mo_boot_btn-primary">You are on this plan</a><br><br>
                            </div>
                            <div class="mo_boot_col-sm-12">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2" style="border-top: 1px solid black;">
                                        <span>Admin Token</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Restrict fake Registrations</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>IP Lookup</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        Enforce Strong password
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        Login transactions Reports
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Only Microsoft Edge</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="" style="width: 48%;margin-left: 4%;" >
                        <div class="mo_boot_row mo_boot_m-1" style="border: 3px solid #3d618f;background: white;border-radius: 10px;">
                            <div class="mo_boot_col-sm-12 mo_boot_mt-4">
                                <h2>Pro</h2><hr><br>

                                <span id="plus_total_price" style="font-weight: bolder;font-size: xx-large;">$49</span><br><br><br><br>
                                <button class="mo_boot_btn mo_boot_btn-primary" onclick="window.open('https://portal.miniorange.com/initializepayment?requestOrigin=joomla_web_security_premium_plan')">Upgrade Now</button><br><br>
                            </div>
                            <div class="mo_boot_col-sm-12">
                                <div class="mo_boot_row">
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2" style="border-top: 1px solid black;">
                                        <span>Admin Token</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Restrict fake Registrations</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>IP Lookup</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Enforce Strong password</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Login transactions Reports</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Browser Blocking</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Brute Force Protection</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Restrict Login Attempts</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Custom time period for blocked IPs</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Whitelist/Blacklist IP Address</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>IP Address Range Blocking</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Country Blocking</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Database Backup</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Scheduled/Automatic Database Backup</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>IP Blocked Notification</span>
                                    </div>
                                    <div class="mo_boot_col-sm-12 mo_boot_py-2">
                                        <span>Unusual Account Activity Notification to users</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mo_boot_col-sm-12 mo_boot_my-2">
                <div class="mo_boot_my-4" style="border:3px solid #3d618f;background:white; border-radius:10px;" id="upgrade-steps">
                    <div style="padding-top: 1px;">
                        <h2 class="mo_boot_mt-3" style="text-align:center">HOW TO UPGRADE TO LICENSED VERSION</h2>
                    </div><hr>
                    <section>
                        <div class="mo_boot_row">
                            <div class=" mo_boot_col-sm-6 mo_jnsp_works-step mo_boot_mt-1" style="padding-left: 40px">
                                <div style="padding-top:1%"><strong>1</strong></div>
                                <p>Click on <strong><em>Upgrade Now</em></strong> button to upgrade, and you will be redirected to register with miniOrange page.
                                    After registration click on <strong><em>Contact Us</em></strong> and you will be redirected to miniOrange login console.</p>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_jnsp_works-step mo_boot_ml-5 mo_boot_mt-1">
                                <div style="padding-top:1%"><strong>4</strong></div>
                                <p>You can download the license version plugin from the <strong><em>View License > Releases and Downloads</em></strong> section on the miniOrange console.</p>
                            </div>
                        </div>

                        <div class=" mo_boot_row">
                            <div class="mo_boot_col-sm-6 mo_jnsp_works-step" style="padding-left: 40px">
                                <div style="padding-top:1%"><strong>2</strong></div>
                                <p>Enter your miniOrange account credentials. You can create one for free
                                    <a href="<?php echo JURI::base()?>index.php?option=com_joomla_networksecurity&tab=account_setup"><strong><em>here</strong></em></a>
                                    if you don't have. Once you have successfully logged in, you will be redirected towards the payment page.</p>
                            </div>
                            <div class="mo_boot_col-sm-6 mo_jnsp_works-step mo_boot_ml-5">
                                <div style="padding-top:1%"><strong>5</strong></div>
                                <p>From the Joomla admin dashboard, uninstall the free plugin currently installed.</p>
                            </div>
                        </div>

                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-6 mo_jnsp_works-step mo_boot_mb-1" style="padding-left: 40px">
                                <div style="padding-top:1%"><strong>3</strong></div>
                                <p>Enter your card details and proceed for payment. On successful payment completion, the license version plugin will be available to download.</p>
                            </div>
                            <div class=" mo_boot_col-sm-6 mo_jnsp_works-step mo_boot_ml-5 mo_boot_mb-1">
                                <div style="padding-top:1%"><strong>6</strong></div>
                                <p>Now install the downloaded license version plugin. After installing the license version plugin,
                                    login using the account which you have used for the purchase of license version plugin.</p>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="mo_boot_my-4"  id="payment-method"  style="border:3px solid #3d618f;background: white; border-radius:10px;" >
                    <h2 class="mo_boot_mt-3" style="text-align:center">ACCEPTED PAYMENT METHODS</h2><hr>
                    <section style="height: 350px;" >
                        <br>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-4">
                                <div class="mo_plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <em style="font-size:30px;" class="fa fa-cc-amex" aria-hidden="true"></em>
                                        <em style="font-size:30px;" class="fa fa-cc-visa" aria-hidden="true"></em>
                                        <em style="font-size:30px;" class="fa fa-cc-mastercard" aria-hidden="true"></em>
                                    </div>
                                    <div>
                                        If the payment is made through Credit Card/International Debit Card, the license will be created automatically once the payment is completed.
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-4">
                                <div class="mo_plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <img class="payment-images" src="<?php echo JUri::base();?>/components/com_joomla_networksecurity/assets/images/paypal.png" alt="" style="width:50px;height:50px;">
                                    </div>
                                    <div>
                                        Use the following PayPal ID <em><strong>info@xecurify.com</strong></em> for making the payment via PayPal.<br><br>
                                    </div>
                                </div>
                            </div>
                            <div class="mo_boot_col-sm-4">
                                <div class="mo_plan-box">
                                    <div style="background-color:white; border-radius:10px; ">
                                        <img class="payment-images card-image" src="" alt="">
                                        <em style="font-size:30px;" class="fa fa-university" aria-hidden="true"><span style="font-size: 20px;font-weight:500;">&nbsp;&nbsp;Bank Transfer</span></em>
                                    </div>
                                    <div>
                                        If you want to use bank transfer for the payment then contact us at <strong><a href="mailto:joomlasupport@xecurify.com?">joomlasupport@xecurify.com</a></strong> so that we can provide you the bank details.
                                    </div>
                                </div>
                            </div>
                        </div><br>
                        <div class="mo_boot_row">
                            <div class="mo_boot_col-sm-12 mo_boot_text-center">
                                <p>
                                    <strong>Note:</strong> Once you have paid through PayPal/Net Banking, please inform us so that we can confirm and update your license.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="mo_boot_col-sm-12">
                <div class="mo_boot_my-4" style="border: 3px solid #3d618f;background: white;border-radius: 10px;" >
                    <h2 class="mo_boot_mt-3 mo_boot_text-center">RETURN POLICY</h2><hr>
                    <div class="mo_boot_px-3">
                        <p>At miniOrange, we want our customers to be 100% satisfied with their purchases.
                            In case the licensed plugin you purchased, is not working as advertised,
                            you can report the issue with our Joomla support team within the first 10 days of the purchase.
                            After reporting the issue, our team will try to resolve those issues within the given timeline as stated by the team,
                            and if the issue does not get resolved within the given time period, the whole amount will be refunded.
                        </p>
                        <p>
                            <strong>Note that this policy does not cover the following cases:</strong>
                            <ul>
                                <li>1. Change in mind or change in requirements after purchase.</li>
                                <li>2. Infrastructure issues do not allow the functionality to work.</li>
                            </ul>
                            If you have any doubts or queries regarding the licensing plans or return policy, you can email us at <a href="mailto:joomlasupport@xecurify.com">joomlasupport@xecurify.com</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?php
}
?>