<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
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
