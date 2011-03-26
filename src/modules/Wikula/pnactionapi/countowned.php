<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: countowned.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print number of pages owned by the current user
 *
 * @author Mateo Tibaquirá 
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['uname'] username to get his/her owned pages (default = current user)
 */
function wikula_actionapi_countowned($args)
{
    $uname = (isset($args['uname']) && !empty($args['uname'])) ? $args['uname'] : pnUserGetVar('uname');

    $pntable = &pnDBGetTables();
    $columns = &$pntable['wikula_pages_column'];
    
    $where = $columns['owner'].' = \''.DataUtil::formatForStore($uname).'\' AND '.
             $columns['latest'].' = \'Y\'';

    return DBUtil::selectObjectCount('wikula_pages', $where);
}
