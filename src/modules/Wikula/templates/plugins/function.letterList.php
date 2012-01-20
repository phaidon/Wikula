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
 * This function sorts a list by letter.
 */
function smarty_function_letterList($params, &$smarty)
{
    $currentChar = '';
    $headerletters = array();
    $pagelist = array();
    
    
    foreach ($params['pages'] as $page) {
        $value = $page['tag'];
        $page['title'] = $value;

        $firstChar = strtoupper(substr($value, 0, 1));
        if (!preg_match('/[A-Za-z]/', $firstChar)) {
            $firstChar = '#';
        }

        if ($firstChar != $currentChar) {
            $headerletters[] = $firstChar;
            $currentChar     = $firstChar;
        }

        if (empty($letter) || $firstChar == $letter) {
            $pagelist[$firstChar][] = $page;

            if (array_key_exists('owner', $page) and System::getVar('uname') == $page['owner']) {
                $user_owns_pages = true;
            }
        }
    }

    return $smarty->assign('pagelist', $pagelist)
                  ->assign('headerletters', $headerletters)
                  ->fetch('plugins/letterList.tpl');
}
