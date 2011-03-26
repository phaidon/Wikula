<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: countpages.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print the total number of pages in this wiki
 *
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param bool $args['nolink'] (optional) flag to return the count instead a Link (default = false)
 * @param bool $args['nopublic'] (optional) flag to discard the Public wiki pages (default = false)
 */
function wikula_actionapi_countpages($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $nolink   = (isset($args['nolink']) && $args['nolink']) ? true : false;
    $nopublic = (isset($args['nopublic']) && $args['nopublic']) ? true : false;

    $pntable = &pnDBGetTables();
    $columns = &$pntable['wikula_pages_column'];

    $where = $columns['latest'].' = \'Y\'';

    if ($nopublic) {
        $where .= ' AND '.$columns['owner'].' != "(Public)"';
    }

    $count = (int)DBUtil::selectObjectCount('wikula_pages', $where);

    if ($nolink) {
        return $count;
    } else {
        return '<a href="'.pnModUrl('wikula', 'user', 'main', array('tag' => __('PageIndex', $dom))).'" title="'.__('Page index', $dom).'">'.$count.'</a>';
    }
}
