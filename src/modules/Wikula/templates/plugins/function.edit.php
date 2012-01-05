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

function smarty_function_edit($params, &$smarty)
{
    if( ModUtil::apiFunc('Wikula', 'user', 'isAllowedToEdit', array('tag' => $params['tag'])) ) {
        return $smarty->fetch('plugin/edit.tpl');
    }
}
