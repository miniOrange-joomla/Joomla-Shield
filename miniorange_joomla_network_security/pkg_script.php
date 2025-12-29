<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of miniorange_web_security_lite_plugin.
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class pkg_MINIORANGEJOOMLANETWORKSECURITYInstallerScript
{
    /**
     * This method is called after a component is installed.
     * @param  \stdClass $parent - Parent object calling this method.
     * @return void
     */
    public function install($parent) 
    {
    }

    /**
     * This method is called after a component is uninstalled.
     * @param  \stdClass $parent - Parent object calling this method.
     * @return void
     */
    public function uninstall($parent) 
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
    }

    /**
     * This method is called after a component is updated.
     * @param  \stdClass $parent - Parent object calling object.
     * @return void
     */
    public function update($parent) 
    {
        //echo '<p>' . JText::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     * @return void
     */
    public function preflight($type, $parent) 
    {
        //echo '<p>' . JText::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
    }

    /**
     * Runs right after any installation action is performed on the component.
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     * @return void
     */
    function postflight($type, $parent) 
    {
       if ($type == 'uninstall') {
        return true;
        }
       $this->showInstallMessage('');
    }

    protected function showInstallMessage($messages=array()) {
        ?>
        <style>
            .mo-row{
                width: 100%;
                display: block;
                margin-bottom: 2%;
            }
            .mo-row:after{
                clear: both;
                display: block;
                content: "";
            }
            .mo-button-style, .mo-button-style:hover {
                color: #FFF;
                background-color: #226a8b !important;
            }
        </style>
        <p>Plugin package for miniOrange <strong>Web Security Lite</strong> plugin in Joomla.</p>
        <ul><h3>Steps to use the miniOrange Web Security Lite.</h3>
            <li>Click on <strong>Components</strong></li>
            <li>Click on <strong>miniOrange - Web Security Lite</strong> and select <strong>Login Security</strong> tab</li>
            <li>You can start configuring</li>
        </ul>
    	<div class="mo-row">
            <a class="btn btn-secondary mo-button-style" href="index.php?option=com_joomla_networksecurity&tab=login_security">Start Using miniOrange Web Security Lite plugin</a>
            <a class="btn btn-secondary mo-button-style" href="https://plugins.miniorange.com/joomla-network-security" target="_blank">Read the miniOrange documents</a>
		    <a class="btn btn-secondary mo-button-style" href="https://www.miniorange.com/contact" target="_blank">Get Support!</a>
        </div>
        <?php
    }
}