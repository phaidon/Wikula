<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link http://code.zikula.org/wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wikula_Api_Account extends Zikula_AbstractApi 
{

    /**
    * Return an array of items to show in the your account panel
    *
    * @return   array   array of items, or false on failure
    */
    public function getall($args)
    {
        $items = array();
        if (
            UserUtil::isLoggedIn() and
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) and 
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
