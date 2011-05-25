<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: recentchanges.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print Recent Changes
 *
 * @author Mateo Tibaquirá
 * @author Frank Chesnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_RecentChanges()
{
    $dom = ZLanguage::getModuleDomain('Wikula');
    $max   = (int)ModUtil::getVar('Wikula', 'itemsperpage', 50);
    $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRecentlyChanged',
                          array('numitems' => $max));

    if (!$pages) {
        return __('There are no recent changes', $dom);
    }

    $curday = '';
    $pagelist = array();
    foreach ($pages as $page)
    {
        list($day, $time) = explode(' ', $page['time']);
        if ($day != $curday) {
            $dateformatted = date(__('D, d M Y', $dom), strtotime($day));
            $curday = $day;
        }

        $page['timeformatted'] = date(__('H:i T', $dom), strtotime($page['time']));

        if ($page['user'] == System::getVar('anonymous')) {
            $page['user'] .= ' ('.__('anonymous user', $dom).')'; // anonymous user
        }

        $pagelist[$dateformatted][] = $page;
    }
    unset($pages);

    $render = pnRender::getInstance('Wikula', false);
    $render->assign('pagelist', $pagelist);
    return $render->fetch('action/recentchanges.tpl');
}
