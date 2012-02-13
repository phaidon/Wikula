<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * The Account API provides links for modules on the "user account page"; this class provides them for the Users module. 
 */
class Wikula_Api_Account extends Zikula_AbstractApi 
{
    /**
     * Return an array of items to show in the your account panel
     *
     * @param mixed $args Not used.
     * 
     * @return array Indexed array of items.
     */
    public function getall($args)
    {
        $items = array();
        if (
            UserUtil::isLoggedIn() &&
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) &&∂ 
            $this->getVar('subscription', false)
        ) {
            // Create an array of links to return
            $items[] = array('url'     => ModUtil::url('Wikula', 'user', 'settings'),
                            'module'  => $this->name,
                            'set'     => '',
                            'title'   => 'Wiki settings',
                            'icon'    => 'admin.png');
        }
        return $items;
    }
}
