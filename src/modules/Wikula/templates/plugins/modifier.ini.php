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
 * INI language file for Wikka highlighting (configuration file).
 */
function smarty_modifier_php($text, $method='diff')
{
    $text = htmlspecialchars($text, ENT_QUOTES);

    $text = preg_replace('/([=,\|]+)/m',
                         '<span style="color:#4400DD">\\1</span>',
                         $text);
    $text = preg_replace('/^([;#].+)$/m',
                         '<span style="color:#226622">\\1</span>',
                         $text);
    $text = preg_replace('/([^\d\w#;:>])([;#].+)$/m',
                         '<span style="color:#226622">\\2</span>',
                         $text);
    $text = preg_replace('/^(\[.*\])/m',
                         '<strong style="color:#AA0000; background:#EEE0CC">\\1</strong>',
                         $text);

    return '<pre>'.$text.'</pre>';
}
