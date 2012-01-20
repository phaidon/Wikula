<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * This function sets he title of a special page.
 */
function smarty_function_specialPageTitle($params, &$smarty)
{
    unset($smarty);
    
    $dom = ZLanguage::getModuleDomain('Wikula');
    $tag    = $params['tag'];
    $name   = ModUtil::getName();
    $info   = ModUtil::getInfoFromName($name);
        
    // hyphen2space
    $nicetag = str_replace('_', ' ', $tag);
    
    $output = array();
    $title  = array();
    $output[] = '<a href="'.ModUtil::url($name, 'user', 'main').'">'.$info['displayname'].'</a>';
    $title[]  = $info['displayname'];
    $title[]  = __('Special pages', $dom);

    if($tag == __('Special_pages', $dom) ) {
        $output[] = __('Special pages', $dom);
    } else {
        $output[] = '<a href="'.
                    ModUtil::url($name, 'user', 'main', array('tag' => __('Special_pages', $dom) ) ).
                    '">'.
                    __('Special pages', $dom).
                    '</a>';
        $output[] = $nicetag;
        $title[]  = $nicetag;
    }
    
    PageUtil::setVar('title', implode($title, '::'));
    return DataUtil::formatForDisplayHTML('<h2>'.implode($output, ' &#187; ').'</h2>');
}
