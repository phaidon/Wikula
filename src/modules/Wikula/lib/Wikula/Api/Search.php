<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Search api class
 *
 * @package Wikula
 */
class Wikula_Api_Search extends Zikula_AbstractApi
{

    /**
     * Search plugin info
     */
    public function info()
    {
        return array(
            'title' => 'Wikula',
            'functions' => array('Wikula' => 'search')
        );
    }

    /**
     * Search form component
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
     * Search plugin main function
     */
    public function search($args)
    {
        // Permission check
         ModUtil::apiFunc($this->name, 'Permission', 'canRead');

        $search = $args['q'];
        
       $pages = ModUtil::apiFunc('Wikula', 'user', 'FullTextSearch', array('phrase' => $search));
       
       
        $sessionId = session_id();
        
        foreach ($pages as $page)
        {
            
            preg_match("/(.{0,120}$search.{0,120})/is", $page['page_body'], $matches);
            $text = $matches[0];
            
            if( ModUtil::available('LuMicuLa') ) {
                $text =  ModUtil::apiFunc('LuMicuLa', 'user', 'transform', array(
                    'text'   => $text)
                );
            }
            
            $item = array(
                'title'   => $page['page_tag'],
                'text'    => $text,
                'extra'   => $page['page_tag'],
                'created' => $page['page_time'],
                'module'  => $this->name,
                'session' => $sessionId
            );
            /*$insertResult = DBUtil::insertObject($item, 'search_result');
            if (!$insertResult) {
                return LogUtil::registerError($this->__('Error! Could not load any articles.'));
            }*/
        }

        return true;
    }


    /**
     * Do last minute access checking and assign URL to items
     *
     * Access checking is ignored since access check has
     * already been done. But we do add a URL to the found user
     */
    public function search_check($args)
    {
        $datarow = &$args['datarow'];
        $tag = $datarow['extra'];
        $datarow['url'] = ModUtil::url($this->name, 'user', 'main', array('tag' => $tag));

        return true;
    }

}

