<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: mychanges.php 137 2010-03-02 13:42:20Z gilles $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print changes made by X user
 *
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Carlo Zottman
 * @todo clean the output array to not polute
 */
function wikula_actionapi_mychanges($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!pnUserLoggedIn()) {
        return __("You are not logged in, the list of pages you've edited couldn't be retrieved.", $dom);
    }

    $tag   = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));
    $uname = (isset($args['uname']) && !empty($args['uname'])) ? $args['uname'] : pnUserGetVar('uname');
    $alpha = (isset($args['alpha']) && is_numeric($args['alpha']) && $args['alpha']) ? true : (bool)FormUtil::getPassedValue('alpha');

    // initialize the output parameter
    $output = array(
        'tag'   => $tag,
        'alpha' => $alpha,
        'uname' => $uname
    );

    // TODO: distinct parameter needed when alpha=1?
    $pages = pnModAPIFunc('wikula', 'user', 'LoadAllPagesEditedByUser',
                          array('alpha' => $alpha,
                                'uname' => $uname));

    $my_edits_count = 0;
    $pagelist = array();

    if ($pages) {
        if ($alpha) {
            $curChar  = '';
            $last_tag = '';
            foreach ($pages as $page) {
                if ($last_tag != $page['tag']) {
                    $last_tag = $page['tag'];
                    $firstChar = strtoupper(substr($page['tag'], 0, 1));
                    if (!preg_match('/[A-Z,a-z]/', $firstChar)) {
                        $firstChar = '#';
                    }
                    if ($firstChar != $curChar) {
                        $curChar = $firstChar;
                    }
                }

                $page['timeformatted'] = date(__('D, d M Y', $dom), strtotime($page['time']));

                $pagelist[$firstChar][] = $page;

                $my_edits_count++;
            }
        } else {
            $curDay = '';
            foreach ($pages as $page) {
                // day header
                list($day, $time) = explode(' ', $page['time']);
                if ($day != $curDay) {
                    $curDay = $day;
                }

                $page['timeformatted'] = date(__('H:i T', $dom), strtotime($time));

                $pagelist[$day][] = $page;

                $my_edits_count++;
            }
        }
    }
    unset($pages);

    $output['editcount'] = $my_edits_count;
    $output['pagelist']  = $pagelist;

    $render = pnRender::getInstance('wikula', false);
    $render->assign($output);
    return $render->fetch('action/mychanges.tpl', $uname.$alpha);
}
