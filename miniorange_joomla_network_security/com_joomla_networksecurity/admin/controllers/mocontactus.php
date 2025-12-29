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

class Joomla_networksecurityControllerMoContactUS extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'mocontactus';
        parent::__construct();
    }

    function _contactUs()
    {
        $post = JFactory::getApplication()->input->post->getArray();
        if (empty(trim($post['query_email']))) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Please submit your query with email.', 'error');
            return;
        } else if (empty(trim($post['query']))) {
            $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Empty query could not be submitted. Please try again.', 'error');
            return;
        } else {
            $query = $post['query'];
            $email = $post['query_email'];
            $phone = $post['query_phone'];

            $contact_us = new Joomla_networksecurityCustomer();
            $submited = json_decode($contact_us->submit_contact_us($email, $phone, $query), true);
            if (json_last_error() == JSON_ERROR_NONE) {
                if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                    $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', $submited['message'], 'error');
                } else {
                    if ($submited == false) {
                        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Your query could not be submitted. Please try again.', 'error');
                    } else {
                        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=account_setup', 'Thanks for getting in touch! We shall get back to you shortly.');
                    }
                }
            }
        }
    }
}