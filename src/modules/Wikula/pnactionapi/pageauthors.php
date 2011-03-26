<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pageauthors.php 117 2009-02-22 12:27:44Z quan $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print the Page Index
 * 
 * @author Florian Schie�l
 * @author Mateo Tibaquir�
 * @param string $args['letter'] (optional) letter to index
 */
function wikula_actionapi_pageauthors($args)
{
    $tag  = (isset($args['tag'])) ? $args['tag'] : FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));
    $page = (isset($args['page'])) ? $args['page'] : null;

    if (empty($page)) {
        $page = pnModAPIFunc('wikula', 'user', 'LoadPage', array('tag'  => $tag));
    }

    if ($page['owner'] == '(Public)') {
        return '';
    }

    // Check if this view is cached
    $render = pnRender::getInstance('wikula');
    $render->cacheid = $tag.'|'.pnUserGetVar('uid').'|'.$numitems;
    if ($render->is_cached('action/pageauthors.tpl')) {
       return $render->fetch('action/pageauthors.tpl');
    }

    // If not, build it

    // must check for exlusion rules
    // check for spaces before the comma
    $excludes = explode(',', pnModGetVar('wikula', 'excludefromhistory', ''));
    if (!empty($excludes)) {
        foreach ($excludes as $exclusion) {
            if (strtolower(trim($exclusion)) == strtolower($tag)) {
                return '';
            }
        }
    }
    $pages = pnModAPIFunc('wikula', 'user', 'LoadRevisions',
                          array('tag' => $tag,
                                'numitems' => 5));
    // check if we need to load the oldes revision
    if ($numitems == count($pages)) {
        // not sure if all are here, so get the oldest
        $oldest = pnModAPIFunc('wikula', 'user', 'LoadRevisions',
                               array('tag' => $tag,
                                     'getoldest' => true));
    } else {
        $oldest = $pages[(count($pages)-1)];
    }
    // get the initial autor for the page
    $first_writer = array (
        'uname' => $oldest['user'],
        'uid'   => pnUserGetIDFromName($oldest['user']),
        'time'  => $oldest['time']
    );

    // clean history to avoid the double, trippe etc. listing of the same user in the infobox
    $lastuser = '';
    $history  = array();
    foreach ($pages as $p) {
        if ($p['user'] != $lastuser) {
            $history[] = array (
                'uname' => $p['user'],
                'uid'   => pnUserGetIDFromName($p['user']),
                'time'  => $p['time']
            );
        }
        $lastuser = $p['user'];
    }
    unset($pages);

    // assign to template
    $render->assign('first_writer', $first_writer);
    $render->assign('history',      $history);

    return $render->fetch('action/pageauthors.tpl');
}
