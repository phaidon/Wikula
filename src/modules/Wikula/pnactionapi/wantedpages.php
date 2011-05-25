<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: wantedpages.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows all links to inexistant pages
 *
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['linkingto'] (optional) tag of the page
 */
function wikula_actionapi_wantedpages($args)
{
    $dom = ZLanguage::getModuleDomain('Wikula');
    // default
    $linkingto = (isset($args['linkingto']) && !empty($args['linkingto'])) ? $args['linkingto'] : '';

    // reset the output items
    $items  = array();

    if (!empty($linkingto)) {
        $items = ModUtil::apiFunc('Wikula', 'user', 'LoadPagesLinkingTo',
                              array('tag' => $linkingto));

        if (!$items) {
            return __f('No pages linking to %s', $linkingto);
        }

    } else {
        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadWantedPages');

        if (!$pages) {
            return __('No wanted pages', $dom);
        }

        // Need permission check
        foreach ($pages as $page) {
            if ($page['to_tag'] != 'MissingPage') {
                $items[] = $page;
            }
        }
        unset($pages);
    }

    $render = pnRender::getInstance('Wikula');
    $render->assign('items', $items);
    $render->assign('linkingto', $linkingto);
    return $render->fetch('action/wantedpages.tpl');
}
