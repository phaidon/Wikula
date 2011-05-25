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
    $dom = ZLanguage::getModuleDomain('Wikula');
    $nolink   = (isset($args['nolink']) && $args['nolink']) ? true : false;
    $nopublic = (isset($args['nopublic']) && $args['nopublic']) ? true : false;

    $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
    $q->where('latest = ?', array('Y'));

    
    if ($nopublic) {
        $q->addWhere('owner != ?', array('(Public)'));
    }

    $result = $q->execute();

    $count = $result->count();

    if ($nolink) {
        return $count;
    } else {
        return '<a href="'
            .ModUtil::url('Wikula', 'user', 'main', array('tag' => __('PageIndex', $dom)))
            .'" title="'.__('Page index', $dom).'">'.$count.'</a>';
    }
}
