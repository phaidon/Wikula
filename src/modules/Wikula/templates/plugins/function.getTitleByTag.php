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

function smarty_function_getTitleByTag($params, &$smarty)
{
    if (!isset($params['body'])) {
        return;
    }
    $title = '';

    if (preg_match("`(=){3,5}([^=\n]+)(=){3,5}`", $params['body'], $title)) {
        $formatting_tags = array('**', '//', '__', '##', "''", '++', '#%', '@@', '""');
        $title = str_replace($formatting_tags, '', $title[2]);
    }

    $value =  !empty($title) ? $title : (isset($params['tag']) ? $params['tag'] : '');

    if (isset($params['assign'])) {  
        $smarty->assign($params['assign'], $value);
    } else {
        return $value;
    }
}
