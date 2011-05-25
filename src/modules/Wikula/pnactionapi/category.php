<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: category.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Category file
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @todo rework this a little
 * @param string $args['page'] tag of the Category page
 * @param integer $args['col'] (optional) number of columns (default=1)
 * @param integer $args['full'] (optional) flag to have a full width table in the non compact version (default=0)
 * @param integer $args['compact'] (optional) flag to show the compact version (default=0)
 * @param integer $args['notitle'] (optional) flag to not show the title (default=0)
 */
function wikula_actionapi_category($args)
{
    $dom = ZLanguage::getModuleDomain('Wikula');
    // Defaults
    $tag     = (isset($args['page']) && !empty($args['page'])) ? $args['page'] : FormUtil::getPassedValue('tag', ModUtil::getVar('wikula', 'root_page'));
    $col     = (isset($args['col']) && !empty($args['col'])) ? $args['col'] : 1;
    $full    = (isset($args['full']) && !empty($args['full'])) ? 1 : 0;
    $compact = (isset($args['compact']) && !empty($args['compact'])) ? 1 : 0;
    $notitle = (isset($args['notitle']) && !empty($args['notitle'])) ? 1 : 0;

    
    // if page is empty
    if (empty($tag) or $tag = __('AllCategories', $dom)) {
        // CategoryCategory page as default
        $tag = __('CategoryCategory', $dom);
    }
    

    $pages = ModUtil::apiFunc('Wikula', 'user', 'FullCategoryTextSearch',
                          array('phrase' => $tag));
    
    


    if (!$pages) {
        return false;
    }

    // Delete the not authorized pages or the page itself
    foreach ($pages as $key => $page) {
        if ($page['page_tag'] == $tag || !SecurityUtil::checkPermission('Wikula::', 'page::'.$page['page_tag'], ACCESS_READ)) {
            unset($pages[$key]);
        }
    }

    $total = count($pages);
    if ($col >= $total) {
        $col = $total;
    }
    $int = floor(($total / $col));
    $endcell = $col - ($total - ($int * $col));

    // build the output
    $render = pnRender::getInstance('Wikula');

    $render->assign('action_cc', array('pages'   => $pages,
                                       'tag'     => $tag,
                                       'col'     => $col,
                                       'full'    => $full,
                                       'compact' => $compact,
                                       'notitle' => $notitle,
                                       'total'   => $total,
                                       'endcell' => $endcell));

    return $render->fetch('action/categorycategory.tpl');
}
