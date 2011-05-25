<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: wikkaversion.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print current Wikula version
 * 
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_wikkaversion()
{
    $modinfo = ModUtil::getInfoFromName('Wikula');
    return $modinfo['version'];
}
