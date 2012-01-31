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
 * This functions sets the title of a wiki page.
 */
function smarty_function_wikulaPageTitle($params, &$smarty)
{
    unset($smarty);
    
    $dom    = ZLanguage::getModuleDomain('Wikula');
    $tag    = $params['tag'];
    $action = FormUtil::getPassedValue('func');
    $info   = ModUtil::getInfoFromName('Wikula');
        
    // hyphen2space
    $nicetag = str_replace('_', ' ', $tag);
    
    $output = array();
    $title  = array();
    $output[] = '<a href="'.ModUtil::url('Wikula', 'user', 'main').'">'.$info['displayname'].'</a>';
    $title[]  = $info['displayname'];
    $title[]  = $nicetag;

    if($action == 'main') {
        $output[] = $nicetag;
    } else {
        $output[] = '<a href="'.ModUtil::url('Wikula', 'user', 'main', array('tag' => $tag)).'">'.$nicetag.'</a>';
        if ( $action == 'history' ) {
            $output[] = __('history', $dom);
            $title[]  = __('history', $dom);
        } else if($action == 'edit') {
            $output[] = __('edit', $dom);
            $title[]  = __('edit', $dom);
        } else if($action == 'backlinks') {
            $output[] = __('backlinks', $dom);
            $title[]  = __('backlinks', $dom);
        }
    }
    
    PageUtil::setVar('title', implode($title, '::'));
    return DataUtil::formatForDisplayHTML('<h2>'.implode($output, ' &#187; ').'</h2>');
}
