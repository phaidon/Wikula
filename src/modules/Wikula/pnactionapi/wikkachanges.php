<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: wikkachanges.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print current Wikula information
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_wikkachanges()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    return pnModAPIFunc('wikula', 'user', 'wakka',
                        array('text' => __('=====Wikka Release Notes=====
This site is running Wikula -the Wiki module for Zikula- version **{{wikkaversion}}**.
The release notes are described on the [[http://code.zikula.org/wikula development website]].
', $dom)));
}
