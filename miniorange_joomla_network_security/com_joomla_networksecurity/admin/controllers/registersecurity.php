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

class Joomla_networksecurityControllerRegisterSecurity extends JControllerForm
{
    function __construct()
    {
        $this->view_list = 'registersecurity';
        parent::__construct();
    }

    function saveRegisterSecuritySettings()
    {
        $post = JFactory::getApplication()->input->post->getArray();

        if (isset($post['mo_block_fake_registration'])){
            $block_fake_emails = $post['block_fake_emails'] ?? 0;
            $block_email_domains = $post['mo_email_domains'] ?? '';

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('block_fake_emails') . ' = ' . $db->quote($block_fake_emails),
                $db->quoteName('mo_email_domains') . ' = ' . $db->quote($block_email_domains),
            );
            $msg="Block Registration from fake users Configuration has been saved successfully.";
        }
        else{
            $enforce_strong_pass = $post['enforce_strong_password_register'] ?? 0;

            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $fields = array(
                $db->quoteName('enforce_strong_password_register') . ' = ' . $db->quote($enforce_strong_pass),
            );
            $msg="Enforce Strong Password Configuration has been saved successfully.";
        }

        // Conditions for which records should be updated.
        $conditions = array(
            $db->quoteName('id') . ' = 1'
        );

        $query->update($db->quoteName('#__miniorange_jnsp_registersecurity_setup'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();

        $this->setRedirect('index.php?option=com_joomla_networksecurity&tab=register_security', $msg);
    }
}