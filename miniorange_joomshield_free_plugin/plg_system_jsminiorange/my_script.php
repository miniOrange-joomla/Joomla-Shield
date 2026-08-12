<?php

/**
 * Script file of miniorange_joomshield_plugin.
 *
 * @author    miniOrange Security Software Pvt. Ltd.
 * @copyright Copyright (C) 2015 miniOrange (https://www.miniorange.com)
 * @license   GNU General Public License version 3; see LICENSE.txt
 * @contact   info@xecurify.com
 */

use Joomla\CMS\Factory;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class PlgSystemJsminiorangeInstallerScript
{
	/**
	 * This method is called after a plugin is installed.
	 *
	 * @param \stdClass $parent - Parent object calling this method.
	 *
	 * @return void
	 */
	public function install($parent)
	{
		$db  = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__extensions');
		$query->set($db->quoteName('enabled') . ' = 1');
		$query->where($db->quoteName('element') . ' = ' . $db->quote('jsminiorange'));
		$query->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * This method is called after a component is uninstalled.
	 *
	 * @param \stdClass $parent - Parent object calling this method.
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		// Echo '<p>' . Text::_('COM_HELLOWORLD_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * This method is called after a component is updated.
	 *
	 * @param \stdClass $parent - Parent object calling object.
	 *
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
	 * @param string    $type   - Type of PreFlight action. Possible values are:
	 *                          - * install - * update - * discover_install- * install - * update - * discover_install
	 *
	 * @param \stdClass $parent - Parent object calling object.
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		// Echo '<p>' . Text::_('COM_HELLOWORLD_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * Runs right after any installation action is performed on the component.
	 *
	 * @param string    $type   - Type of PostFlight action. Possible values are:
	 *                          - * install - * update - * discover_install- * install - * update - * discover_install
	 *
	 * @param \stdClass $parent - Parent object calling object.
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// Echo '<p>' . Text::_('COM_HELLOWORLD_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
