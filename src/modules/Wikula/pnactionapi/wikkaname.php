<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: wikkaname.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Returns the name of the module in a link
 * 
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_wikkaname()
{
    $modinfo = ModUtil::getInfoFromName('Wikula');
    return '<a href="'.ModUtil::url('Wikula').'" title="'.$modinfo['displayname'].'">'.$modinfo['displayname'].'</a>';
}
