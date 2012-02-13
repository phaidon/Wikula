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
 * This function sorts a list by letter.
 *
 * @param array $params Plugin parameters.
 * @param mixed $smarty Smarty.
 * 
 * @return string
 */
function smarty_function_letterList($params, $smarty)
{
    $pagelist = array();
    
    foreach ($params['pages'] as $page) {
        $tag = $page['tag'];

        $firstChar = strtoupper(substr($tag, 0, 1));
        if (!preg_match('/[A-Za-z]/', $firstChar)) {
            $firstChar = '#';
        }

        $pagelist[$firstChar][] = $tag;

        
    }
    
    $headerletters = array_keys($pagelist);
    
    return $smarty->assign('pagelist', $pagelist)
                  ->assign('headerletters', $headerletters)
                  ->fetch('plugins/letterList.tpl');
}
