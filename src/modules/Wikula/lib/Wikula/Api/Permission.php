<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Permissions api class
 *
 * @package Wikula
 */
class Wikula_Api_Permission extends Zikula_AbstractApi 
{

    /**
     * Check if a user has the right to read a wiki page
     *
     * @param string tag of the wiki page
     */
    public function canRead($tag = null)
    {
        $instance = $this->getInstance($tag);
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_READ)
        );  
    }
    
    /**
     * Check if a user has the right to edit a wiki page
     *
     * @param string tag of the wiki page
     */
    public function canEdit($tag = null)
    {
        $instance = $this->getInstance($tag);
        return SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_COMMENT);
    }
    
    /**
     * Check if a user has the right to moderate a wiki page
     *
     * @param string tag of the wiki page
     */
    public function canModerate($tag = null)
    {
        $instance = $this->getInstance($tag);
        return SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_EDIT);
    }
    
    /**
     * Get the instance of a wiki page
     *
     * @param string tag of the wiki page
     */
    private function getInstance($tag)
    {
        if($this->getVar('single_page_permissions', false) and !is_null($tag)) {
            $instance = 'page::'.$tag;
        } else {
            $instance = '::';
        }
        return $instance;
    }
    
}
