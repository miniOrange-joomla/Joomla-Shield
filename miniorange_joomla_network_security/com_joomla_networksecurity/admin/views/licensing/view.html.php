<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_joomla_networksecurity
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorlds View
 *
 * @since  0.0.1
 */
class Joomla_NetworksecurityViewLicensing extends JViewLegacy
{
	/**
	 * Display the Hello World view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state = $this->get('State');

        $errors = $this->get('Errors');
		// Check for errors.
		if (is_array($errors) &&  count($errors))
		{
			JFactory::getApplication()->enqueueMessage(500, implode('<br />', $errors));
			return false;
		}
		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();
       
		// Display the template
		parent::display($tpl);
	}
	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.0
	 */
	protected function addToolbar()
	{
        JToolBarHelper::title(JText::_('mini<span><strong style="color:orange;">O</strong>range Web Security Lite</span>'), 'mo_jnsp_logo mo_jnsp_icon');
	}
}