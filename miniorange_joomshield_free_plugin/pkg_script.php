<?php
/**
 * Script file of miniorange_joomshield_plugin.
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class Pkg_MiniorangeJoomShieldInstallerScript
{
	/**
	 * This method is called after a component is installed.
	 *
	 * @param  \stdClass $parent - Parent object calling this method.
	 * @return void
	 */
	public function install($parent)
	{

	}

	/**
	 * This method is called after a component is uninstalled.
	 *
	 * @param  \stdClass $parent - Parent object calling this method.
	 * @return void
	 */
	public function uninstall($parent)
	{
		// Echo '<p>' . Text::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * This method is called after a component is updated.
	 *
	 * @param  \stdClass $parent - Parent object calling object.
	 * @return void
	 */
	public function update($parent)
	{
		// Echo '<p>' . Text::sprintf('COM_HELLOWORLD_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
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
		// Echo '<p>' . Text::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Runs right after any installation action is performed on the component.
	 *
	 * @param  string    $type   - Type of PostFlight action. Possible values are:
	 *                           - * install
	 *                           - * update
	 *                           - * discover_install
	 * @param  \stdClass $parent - Parent object calling object.
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		if ($type == 'uninstall')
		{
			return true;
		}

		$this->showInstallMessage('');

		$helperPath = JPATH_ADMINISTRATOR . '/components/com_joomshield/helpers/mo_networksecurity_customer_setup.php';

		if (file_exists($helperPath))
		{
			include_once $helperPath;

			if (class_exists('Joomla_NetworksecurityCustomer') && method_exists('Joomla_NetworksecurityCustomer', 'installationMessage'))
			{
				Joomla_NetworksecurityCustomer::installationMessage();
			}
		}
		else
		{
			return;
		}
	}

	protected function showInstallMessage($messages=array())
	{
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
			.mo-button-style {
				background-color: #007DB0;
				color: #ffffff !important;
				text-decoration: none !important;
				border: 1px solid #007DB0;
				padding: 8px 16px;
				border-radius: 4px;
				font-size: 14px;
				cursor: pointer;
				display: inline-block;
				text-decoration: none;
			}

			.mo-button-style:hover,
			.mo-button-style:focus {
				background-color: #00597D;
				border-color: #00597D;
				color: #ffffff;
				text-decoration: none;
			}
		</style>
		<p>Plugin package for miniOrange <strong>JoomShield</strong> plugin in Joomla.</p>
		<ul><h3>Steps to use the miniOrange JoomShield.</h3>
			<li>Click on <strong>Components</strong></li>
			<li>Click on <strong>miniOrange - JoomShield</strong> and select <strong>Login Security</strong> tab</li>
			<li>You can start configuring</li>
		</ul>
		<div class="mo-row">
			<a class=" mo-button-style " href="index.php?option=com_joomshield&tab=login_security">Start Using miniOrange JoomShield plugin</a>
			<a class=" mo-button-style " href="https://plugins.miniorange.com/joomla-web-security" target="_blank">Read the miniOrange documents</a>
			<a class=" mo-button-style " href="https://www.miniorange.com/contact" target="_blank">Get Support!</a>
		</div>
		<?php
	}
}
