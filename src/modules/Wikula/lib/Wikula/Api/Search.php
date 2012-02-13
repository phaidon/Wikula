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
 * Search api class
 */
class Wikula_Api_Search extends Zikula_AbstractApi
{
    /**
     * Return Search plugin info
     *
     * @return array An array containing information for the searc API.
     */
    public function info()
    {
        return array(
            'title' => 'Wikula',
            'functions' => array('Wikula' => 'search')
        );
    }

    /**
     * Render the search form component for Users.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * boolean 'active' Indicates that the Users module is an active part of the search(?).
     * 
     * @param array $args All parameters passed to this function.
     *
     * @return string The rendered template for the Users search component.
     */
    public function options($args)
    {
        if (SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ)) {
            $renderer = Zikula_View::getInstance('Wikula');
            $active = (isset($args['active'])&&isset($args['active']['Wikula']))||(!isset($args['active']));
            $renderer->assign('active',$active);
            return $renderer->fetch('search/options.tpl');
        }

        return '';
    }

    /**
     * Perform a search.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * ? $args['q'] ?.
     * ? $args[?]   ?.
     * 
     * @param array $args All parameters passed to this function.
     *
     * @return bool True on success or null result, false on error.
     */
    public function search($args)
    {
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead');

        $search = $args['q'];
        
        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadPages', array('q' => $search));
       
       
        $sessionId = session_id();
        
        foreach ($pages as $page) {
            $hook = new Zikula_FilterHook(
                'wikula.filter_hooks.body.filter', 
                $page['body']
            );
            $text = ServiceUtil::getManager()->getService('zikula.hookmanager')
                                            ->notify($hook)->getData(); 
            
            // mark search phrase and truncate it
            preg_match("/(.{0,120}$search.{0,120})/is", $text, $matches);
            $text = $matches[0];
            
            
            $item = array(
                'title'   => $page['tag'],
                'text'    => $text,
                'extra'   => $page['tag'],
                'created' => $page['time']->format('Y-m-d'),
                'module'  => $this->name,
                'session' => $sessionId
            );
            $insertResult = DBUtil::insertObject($item, 'search_result');
            if (!$insertResult) {
                return LogUtil::registerError($this->__('Error! Could not load any articles.'));
            }
        }

        return true;
    }


    /**
     * Do last minute access checking and assign URL to items.
     *
     * Access checking is ignored since access check has
     * already been done. But we do add a URL to the found user.
     *
     * Parameters passed in the $args array:
     * -------------------------------------
     * array $args['datarow'] ?.
     * 
     * @param array $args The search results.
     *
     * @return bool True.
     */
    public function search_check($args)
    {
        $datarow = &$args['datarow'];
        $tag = $datarow['extra'];
        $datarow['url'] = ModUtil::url($this->name, 'user', 'main', array('tag' => $tag));

        return true;
    }

}

