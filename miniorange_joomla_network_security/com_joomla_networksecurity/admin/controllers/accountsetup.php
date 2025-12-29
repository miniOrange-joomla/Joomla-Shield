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

defined('_JEXEC') or die('Restricted access');

class Joomla_networksecurityControllerAccountSetup extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'accountsetup';
        parent::__construct();
    }

    function registerCustomer()
    {
        $post = JFactory::getApplication()->input->post->getArray();

        $email = $post['email'] ?? '';
        $password = $post['password'] ?? '';
        $confirmPassword = $post['confirmPassword'] ?? '';

        if (empty($email) || empty($password) || empty($confirmPassword)) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'All the fields are required. Please enter valid entries.', 'error');
            return;
        } else if (strlen($password) < 6 || strlen($confirmPassword) < 6) {    //check password is of minimum length 6
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Password must be contians at least 12 characters in length.', 'error');
            return;
        } else {
            $email = strtolower($email);
            $phone = $post['phone'] ?? '';
        }

        if (strcmp($password, $confirmPassword) == 0) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            // Fields to update.
            $fields = array(
                $db->quoteName('email') . ' = ' . $db->quote($email),
                $db->quoteName('admin_phone') . ' = ' . $db->quote($phone),
                $db->quoteName('password') . ' = ' . $db->quote($password),

            );

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();

            $customer = new Joomla_networksecurityCustomer();
            $content = json_decode($customer->check_customer($email), true);
            if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') == 0) {
                $auth_type = 'EMAIL';
                $content = json_decode($customer->send_otp_token($auth_type, $email), true);
                if (strcasecmp($content['status'], 'SUCCESS') == 0) {

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('email_count') . ' = ' . $db->quote(1),
                        $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                        $db->quoteName('login_status') . ' = ' . $db->quote(0),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                    );
                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();

                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'A One Time Passcode has been sent to <b>' . $email . '</b>. Please enter the OTP below to verify your email. ');

                } else {

                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(0),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
                    );
                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();

                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'There was an error in sending email. Please click on Resend OTP to try again. ', 'error');

                }
            } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('login_status') . ' = ' . $db->quote(0),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
                );
                // Conditions for which records should be updated.
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();

                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $content['statusMessage'], 'error');

            } else {
                $content = $customer->get_customer_key($email, $password);
                $customerKey = json_decode($content, true);
                if (json_last_error() == JSON_ERROR_NONE) {

                    $customer_id = $customerKey['id'] ?? '';
                    $api_ket     = $customerKey['apiKey'] ?? '';
                    $token       = $customerKey['token'] ?? '';
                    $phone       = $customerKey['phone'] ?? '';

                    $this->save_customer_configurations($email, $customer_id, $api_ket, $token, $phone);
                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your account has been retrieved successfully.');
                } else {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(1),
                        $db->quoteName('registration_status') . ' = ' . $db->quote('')
                    );
                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();

                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'You already have an account with miniOrange. Please enter a valid password. ', 'error');

                }
            }
        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('login_status') . ' = ' . $db->quote(0)
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Password and Confirm password do not match.', 'error');
        }
    }

    function save_customer_configurations($email, $id, $apiKey, $token, $phone)
    {

        if (empty($phone)) {
            $phone = '';
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email') . ' = ' . $db->quote($email),
            $db->quoteName('customer_key') . ' = ' . $db->quote($id),
            $db->quoteName('api_key') . ' = ' . $db->quote($apiKey),
            $db->quoteName('customer_token') . ' = ' . $db->quote($token),
            $db->quoteName('admin_phone') . ' = ' . $db->quote($phone),
            $db->quoteName('login_status') . ' = ' . $db->quote(0),
            $db->quoteName('registration_status') . ' = ' . $db->quote('SUCCESS'),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    function validateOtp()
    {
        $otp_token = JFactory::getApplication()->input->post->getArray()["otp_token"];
        $otp_token = trim($otp_token);
        MoNetworkSecurityUtility::check_curl_installed();
        $customer_details = MonetworksecurityDB::getCustomerDetails();
        $admin_email = $customer_details['email'];

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('transaction_id');
        $query->from($db->quoteName('#__miniorange_networksecurity_customer'));
        $query->where($db->quoteName('id') . " = 1");
        $db->setQuery($query);
        $transaction_id = $db->loadResult();

        $customer = new Joomla_networksecurityCustomer();
        $content = json_decode($customer->validate_otp_token($transaction_id, $otp_token), true);

        if (strcasecmp($content['status'], 'SUCCESS') == 0) {

            $customer = new Joomla_networksecurityCustomer();
            $customerKey = json_decode($customer->create_customer(), true);
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $fields = array(
                $db->quoteName('email_count') . ' = ' . $db->quote(0),
                $db->quoteName('sms_count') . ' = ' . $db->quote(0)
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );
            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            if (strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {    //admin already exists in miniOrange
                $content = $customer->get_customer_key();
                $customerKey = json_decode($content, true);

                if (json_last_error() == JSON_ERROR_NONE) {

                    $customer_id = $customerKey['id'] ?? '';
                    $api_ket = $customerKey['apiKey'] ?? '';
                    $token = $customerKey['token'] ?? '';
                    $phone = $customerKey['phone'] ?? '';


                    $this->save_customer_configurations($admin_email, $customer_id, $api_ket, $token, $phone);
                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your account has been created successfully.');

                } else {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    $fields = array(
                        $db->quoteName('login_status') . ' = ' . $db->quote(1),
                        $db->quoteName('new_registration') . ' = ' . $db->quote(0),
                        $db->quoteName('password') . ' = ' . $db->quote(''),
                    );
                    // Conditions for which records should be updated.
                    $conditions = array(
                        $db->quoteName('id') . ' = 1'
                    );

                    $query->update($db->quoteName('#__miniorange_saml_customer_details'))->set($fields)->where($conditions);
                    $db->setQuery($query);
                    $db->execute();

                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'You already have an account with miniOrange. Please enter a valid password.', 'error');

                }
            } else if (strcasecmp($customerKey['status'], 'INVALID_EMAIL') == 0) {
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Invalid Email ID. Please use a valid Email ID to register .', 'error');
            } else if (strcasecmp($customerKey['status'], 'SUCCESS') == 0) {


                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('password') . ' = ' . $db->quote(''),
                );
                // Conditions for which records should be updated.
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();

                $customer_id = $customerKey['id'] ?? '';
                $api_ket = $customerKey['apiKey'] ?? '';
                $token = $customerKey['token'] ?? '';
                $phone = $customerKey['phone'] ?? '';

                //registration successful
                $this->save_customer_configurations($admin_email, $customer_id, $api_ket, $token, $phone);
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your account has been created successfully!');

            }
            //update_option('mo_saml_local_password', '');
        } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_VALIDATION_FAILURE')
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $content['statusMessage'], 'error');

        } else {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_VALIDATION_FAILURE')
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $result = $db->execute();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Invalid one time passcode(OTP). Please enter a valid OTP.', 'error');
        }
    }

    function removeAccount()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query = $db->getQuery(true);

        $fields = array(
            $db->quoteName('email') . ' = '.$db->quote(''),
            $db->quoteName('customer_key') . ' = '.$db->quote(''),
            $db->quoteName('api_key') . ' = '.$db->quote(''),
            $db->quoteName('customer_token') . ' = '.$db->quote(''),
            $db->quoteName('admin_phone') . ' = '.$db->quote(''),
            $db->quoteName('login_status') . ' = '.$db->quote(0),
            $db->quoteName('registration_status') .' = ' . $db->quote('SUCCESS'),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();

        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your account has been removed successfully.');
    }

    function resendOtp()
    {
        $customer = new Joomla_networksecurityCustomer();
        $auth_type = 'EMAIL';
        $customer_details = MonetworksecurityDB::getCustomerDetails();
        $admin_email = $customer_details['email'];

        $content = json_decode($customer->send_otp_token($auth_type, $admin_email), true);
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            $email_count = $customer_details['email_count'];
            $admin_email = $customer_details['email'];

            if ($email_count != '' && $email_count >= 1) {
                $email_count = $email_count + 1;

                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('email_count') . ' = ' . $db->quote($email_count),
                    $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                );
                // Conditions for which records should be updated.
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $result = $db->execute();

                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Another One Time Passcode has been sent to <b>' . $admin_email . '</b>. Please enter the OTP below to verify your email.');

            } else {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $fields = array(
                    $db->quoteName('email_count') . ' = ' . $db->quote(1),
                    $db->quoteName('transaction_id') . ' = ' . $db->quote($content['txId']),
                    $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_SUCCESS')
                );
                // Conditions for which records should be updated.
                $conditions = array(
                    $db->quoteName('id') . ' = 1'
                );

                $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'An OTP has been sent to <b>' . ($admin_email) . '</b>. Please enter the OTP below to verify your email.');

            }

        } else if (strcasecmp($content['status'], 'CURL_ERROR') == 0) {

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $content['statusMessage'], 'error');

        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('registration_status') . ' = ' . $db->quote('MO_OTP_DELIVERED_FAILURE')
            );
            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = 1'
            );

            $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
            $db->setQuery($query);
            $db->execute();
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'There was an error in sending email. Please click on Resend OTP to try again.', 'error');

        }
    }

    function customerLoginForm()
    {
        $db_table = '#__miniorange_networksecurity_customer';

        $db_coloums = array(
            'login_status' => true,
            'password' => '',
            'email_count' => 0,
            'sms_count' => 0,
        );

        NetworkSecurityUtilities::__genDBUpdate($db_table, $db_coloums);
        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup');
    }

    function forgotPassword(){
        $post = JFactory::getApplication()->input->post->getArray();
        $current_email = $post['current_admin_email'] ?? '';
        $admin_email = MonetworksecurityDB::getCustomerDetails();
        $admin_email = $admin_email['email'] ?? $current_email;


        if (empty($admin_email) || $admin_email==''){
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Please enter valid Email ID.','error');
            return;
        }


        $customer = new Joomla_networksecurityCustomer();
        $forgot_password_response = json_decode($customer->mo_saml_local_forgot_password($admin_email));
        if($forgot_password_response->status == 'SUCCESS'){
            $message = 'You password has been reset successfully. Please enter the new password sent to your registered mail here.';
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $message);
        }
    }

    function cancelform()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('email') . ' = ' . $db->quote(''),
            $db->quoteName('password') . ' = ' . $db->quote(''),
            $db->quoteName('customer_key') . ' = ' . $db->quote(''),
            $db->quoteName('admin_phone') . ' = ' . $db->quote(''),
            $db->quoteName('customer_token') . ' = ' . $db->quote(''),
            $db->quoteName('api_key') . ' = ' . $db->quote(''),
            $db->quoteName('registration_status') . ' = ' . $db->quote(''),
            $db->quoteName('login_status') . ' = ' . $db->quote(0),
            $db->quoteName('transaction_id') . ' = ' . $db->quote(''),
            $db->quoteName('email_count') . ' = ' . $db->quote(0),
            $db->quoteName('sms_count') . ' = ' . $db->quote(0),
        );
        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_networksecurity_customer'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup');

    }

    function verifyCustomer()
    {
        MoNetworkSecurityUtility::check_curl_installed();

        $post = JFactory::getApplication()->input->post->getArray();
        $email = $post['email'] ?? '';
        $password = $post['password'] ?? '';
        $customer = new Joomla_networksecurityCustomer();
        $content = $customer->get_customer_key($email, $password);
        $customerKey = json_decode($content, true);

        if (strcasecmp($customerKey['apiKey'], 'CURL_ERROR') == 0) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $customerKey['token'], 'error');
        } else if (json_last_error() == JSON_ERROR_NONE) {
            if (isset($customerKey['id']) && isset($customerKey['apiKey']) && !empty($customerKey['id']) && !empty($customerKey['apiKey'])) {

                $customer_id = $customerKey['id'] ?? '';
                $api_ket = $customerKey['apiKey'] ?? '';
                $token = $customerKey['token'] ?? '';
                $phone = $customerKey['phone'] ?? '';

                $this->save_customer_configurations($email, $customer_id, $api_ket, $token, $phone);
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your account has been retrieved successfully.');
            } else {
                $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'There was an error in fetching your details. Please try again.', 'error');
            }
        } else {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Invalid username or password. Please try again.', 'error');
        }
    }
}