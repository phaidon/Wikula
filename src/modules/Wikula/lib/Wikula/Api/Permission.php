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
 * Permissions api class
 */
class Wikula_Api_Permission extends Zikula_AbstractApi 
{
    /**
     * Check if a user has the right to read a wiki page
     *
     * @param string $tag Tag of the wiki page.
     * 
     * @return boolean
     * 
     * @throws Zikula_Exception_Forbidden If the current user does not have adequate permissions to perform this function.
     */
    public function canRead($tag = null)
    {
        $instance = $this->getInstance($tag);
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_READ)
        ); 
        return true;
    }
    
    /**
     * Check if a user has the right to edit a wiki page
     *
     * @param string $tag Tag of the wiki page.
     * 
     * @return boolean
     */
    public function canEdit($tag = null)
    {
        $instance = $this->getInstance($tag);
        return SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_COMMENT);
    }
    
    /**
     * Check if a user has the right to moderate a wiki page
     *
     * @param string $tag Tag of the wiki page.
     * 
     * @return boolean
     */
    public function canModerate($tag = null)
    {
        $instance = $this->getInstance($tag);
        return SecurityUtil::checkPermission('Wikula::', $instance, ACCESS_EDIT);
    }
    
    /**
     * Get the instance of a wiki page
     *
     * @param string $tag Tag of the wiki page.
     * 
     * @return string
     */
    private function getInstance($tag)
    {
        if ($this->getVar('single_page_permissions', false) && !is_null($tag)) {
            $instance = 'page::'.$tag;
        } else {
            $instance = '::';
        }
        return $instance;
    }
    
}
