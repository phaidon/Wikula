<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: backlinks.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows all backlinks for this page
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['tag'] tag of the page to check backlinks to
 */
function wikula_actionapi_backlinks($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $tag = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));

    if (empty($tag)) {
        return __f('Missing argument [%s]', 'tag');
    }

    $pages = pnModAPIFunc('wikula', 'user', 'LoadPagesLinkingTo',
                          array('tag' => $tag));

    if (!$pages) {
        return false;
    }

    $render = pnRender::getInstance('wikula');

    $render->assign('pages', $pages);

    return $render->fetch('action/backlinks.tpl', md5(serialize($pages)));
}
