<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: mypages.php 137 2010-03-02 13:42:20Z gilles $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print pages onwed by current user
 *
 * @author Frank Chestnut
 * @author Carlo Zottmann
 */
function wikula_actionapi_mypages()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnUserLoggedIn()) {
        return __("You are not logged in, the list of pages you own couldn't be retrieved.", $dom);
    }

    $uname = pnUserGetVar('uname');

    $pages = pnModAPIFunc('wikula', 'user', 'LoadAllPagesOwnedByUser',
                          array('uname' => $uname));

    if (!$pages) {
        return __('No pages found!', $dom);
    }

    $render = pnRender::getInstance('wikula', false);

    $curChar = '';
    $pagelist = array();

    foreach ($pages['pages'] as $page) {
        $firstChar = strtoupper(substr($page['tag'], 0, 1));
        if (!preg_match('/[A-Z,a-z]/', $firstChar)) {
            $firstChar = '#';
        }
        if ($firstChar != $curChar) {
            $curChar = $firstChar;
        }
        $pagelist[$firstChar][] = $page;
    }
    unset($pages['pages']);

    $render->assign('pagelist',  $pagelist);
    $render->assign('pagecount', count($pagelist));
    $render->assign('count',     $pages['count']);
    $render->assign('total',     $pages['total']);

    return $render->fetch('action/mypages.tpl', $uname.$pages['count']);
}
