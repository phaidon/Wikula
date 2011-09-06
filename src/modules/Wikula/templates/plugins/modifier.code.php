<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Plain text language file for Wikka highlighting (or unknown language).
 */
function smarty_modifier_code($text)
{
    return '<pre>'.htmlspecialchars($text, ENT_QUOTES).'</pre>';
}
