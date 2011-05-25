<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: function.textsearchlink.php 163 2010-04-22 12:49:04Z yokav $
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
