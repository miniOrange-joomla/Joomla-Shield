<?php
// @package    miniOrange
// @subpackage Plugins
// @license    GNU/GPLv3
// @copyright  Copyright 2015 miniOrange. All Rights Reserved.
// No direct access
defined('_JEXEC') or die;

/**
 * Class Miniorange_samlController
 *
 * @since  1.6
 */
class Joomla_networksecurityController extends JControllerLegacy
{
    /**
     * Method to display a view.
     *
     * @param boolean $cachable If true, the view output will be cached
     * @param mixed $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return   JController This object to support chaining.
     *
     * @since    1.5
     */
    protected $default_view = 'accountsetup';
}