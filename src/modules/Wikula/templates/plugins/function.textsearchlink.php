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

function smarty_function_textsearchlink($params, &$smarty)
{
    return ModUtil::url('Wikula', 'user', 'main', array(
        'tag' => __('Search')
    ));
}
