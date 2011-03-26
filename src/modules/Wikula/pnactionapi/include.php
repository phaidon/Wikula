<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: include.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Includes another Wiki page
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_include($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (isset($args['page']) && !empty($args['page'])) {
        $tag = $args['page'];
    } else {
        return;
    }

    $current = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));
    if ($current == $tag) {
        return __('Circular reference detected', $dom);
    }

    $page = pnModAPIFunc('wikula', 'user', 'LoadPage', array('tag' => $tag));
    if (!$page) {
        return __('Page missing', $dom);
    }

    $render = pnRender::getInstance('wikula', false);
    $render->assign('page',  $page);

    return $render->fetch('action/include.tpl');
}
