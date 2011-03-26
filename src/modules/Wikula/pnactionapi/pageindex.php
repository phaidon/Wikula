<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pageindex.php 164 2010-04-22 13:05:06Z yokav $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print the Page Index
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['letter'] (optional) letter to index
 */
function wikula_actionapi_pageindex($args)
{
    print_r('hallo');
    
    $dom = ZLanguage::getModuleDomain('wikula');
    $letter      = (isset($args['letter'])) ? $args['letter'] : FormUtil::getPassedValue('letter');
    $username    = (UserUtil::isLoggedIn()) ? UserUtil::getVar('uname') : '';
    $currentpage = FormUtil::getPassedValue('tag', __('PageIndex', $dom));

    // Check if we are in Wikula edit mode, and reset to the default PageIndex page
    if (ModUtil::getName() == 'Wikula' && FormUtil::getPassedValue('func') == 'edit') {
        $currentpage = __('PageIndex', $dom);
    }

    // Check if this view is cached
    $render = pnRender::getInstance('Wikula');
    $render->cacheid = $username.$currentpage.$letter;
    if ($render->is_cached('action/pageindex.tpl')) {
       return $render->fetch('action/pageindex.tpl');
    }

    // If not, build it
    $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadAllPages');

    if (!$pages) {
        return __('No pages found!', $dom);
    }

    $currentChar       = '';
    $user_owns_pages   = false;
    $headerletters     = array();
    $pagelist          = array();

    foreach ($pages as $page) {
        $value = '';
        if (preg_match("`(=){3,5}([^=\n]+)(=){3,5}`", $page['body'], $value)) {
            $formatting_tags = array('**', '//', '__', '##', "''", '++', '#%', '@@', '""');
            $value = str_replace($formatting_tags, '', $value[2]);
        } else {
            $value = $page['tag'];
        }

        $firstChar = strtoupper(substr($value, 0, 1));
        if (!preg_match('/[A-Za-z]/', $firstChar)) {
            $firstChar = '#';
        }

        if ($firstChar != $currentChar) {
            $headerletters[] = $firstChar;
            $currentChar     = $firstChar;
        }

        if (empty($letter) || $firstChar == $letter) {
            $pagelist[$firstChar][] = $page;

            if ($username == $page['owner']) {
                $user_owns_pages = true;
            }
        }

    }

    $headerletters = array_unique($headerletters);
    sort($headerletters);
    ksort($pagelist);

    $render->assign('currentpage',   $currentpage);
    $render->assign('headerletters', $headerletters);
    $render->assign('pagelist',      $pagelist);
    $render->assign('username',      $username);
    $render->assign('userownspages', $user_owns_pages);

    return $render->fetch('action/pageindex.tpl');
}
