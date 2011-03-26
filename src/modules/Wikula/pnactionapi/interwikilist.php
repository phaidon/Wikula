<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: interwikilist.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Prints InterWiki links
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_interwikilist()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!file_exists('modules/Wikula/pnincludes/interwiki.conf')) {
        return __('File interwiki.conf not found!', $dom);
    }

    $file = implode('', file('modules/Wikula/pnincludes/interwiki.conf', 1));

    return pnModAPIFunc('wikula', 'user', 'wakka',
                        array('text' => '%%'.$file.'%%'));
}
