<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: lastezcomments.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows the latest comments made in the Wiki
 *
 * @author Frank Chestnut
 * @todo check if works and re-template
 */
function wikula_actionapi_lastezcomments($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnModAvailable('EZComments')) {
        return __('EZComments unavailable');
    }

    if (!pnModIsHooked('EZComments', 'wikula')) {
        return __('Not hooked!', $dom);
    }
    $itemsperpage = pnModGetVar('wikula', 'itemsperpage');

    $items = pnModAPIFunc('EZComments', 'user', 'getall',
                          array('mod'          => 'wikula',
                                'itemsperpage' => $itemsperpage));

    if (!$items) {
        return __('No comments yet...', $dom);
    }

    for ($x = 0; $x < count($items); $x++) {
        //$fulldate = $items[$x]['date'];

        $arrdate = explode(' ', $items[$x]['date']);

        $titledate = $arrdate[0];

        $items[$x]['titledate'] = $titledate;

        if (strlen($items[$x]['comment']) > 150) {
            $short = substr($items[$x]['comment'], 0, 150);
            $items[$x]['comment'] = $short.'[....]';
        }
    }

    $render = pnRender::getInstance('wikula');

    $render->assign('items', $items);

    return $render->fetch('action/lastezcomments.tpl');
}
