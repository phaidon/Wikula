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

function smarty_modifier_htmlspecialchars_ent($text)
{
    // Fixing for now the other args
    $quote_style = ENT_COMPAT;
    $charset     = 'UTF-8';

    // define patterns
    $alpha          = '[a-z]+';               # character entity reference
    $numdec         = '#[0-9]+';              # numeric character reference (decimal)
    $numhex         = '#x[0-9a-f]+';          # numeric character reference (hexadecimal)
    $terminator     = ';|(?=($|[\n<]|&lt;))'; # semicolon; or end-of-string, newline or tag

    $entitystring   = $alpha.'|'.$numdec.'|'.$numhex;
    $escaped_entity = '&amp;('.$entitystring.')('.$terminator.')';

    // execute PHP built-in function, passing on optional parameters
    $output = htmlspecialchars($text, $quote_style, $charset);

    // "repair" escaped entities
    // modifiers: s = across lines, i = case-insensitive
    $output = preg_replace('/'.$escaped_entity.'/si', "&$1;", $output);

    // return output
    return $output;
}
