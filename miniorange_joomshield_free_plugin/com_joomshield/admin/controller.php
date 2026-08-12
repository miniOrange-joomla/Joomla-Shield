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

use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * Default admin controller. Must be JoomshieldController: BaseController::getInstance('joomshield')
 * builds ucfirst('joomshield') . 'Controller'.
 *
 * @since 1.6
 */
class JoomshieldController extends BaseController
{
	public function __construct($config = [], $factory = null, $app = null, $input = null)
	{
		$config['default_view'] = $config['default_view'] ?? 'accountsetup';

		parent::__construct($config, $factory, $app, $input);
	}
}
