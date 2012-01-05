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
 * Email quoting file for Wikka highlighting.
 */
function smarty_modifier_email($text)
{
    $text = $this->htmlspecialchars_ent($text);
    $text = str_replace('&gt;', '>', $text);

    $text = preg_replace("/^([^\s\n>]*?(>{1}))([^>].*)$/m",
                         '<span style="color:#AA0000">\\1\\3</span>',
                         $text);
    $text = preg_replace("/^([^\s\n>]*?(>{2}))([^>].*)$/m",
                         '<span style="color:#0000AA">\\1\\3</span>',
                         $text);
    $text = preg_replace("/^([^\s\n>]*?(>{3}))([^>].*)$/m",
                         '<span style="color:#00AA00">\\1\\3</span>',
                         $text);
    $text = preg_replace("/^([^\s\n>]*?(>{4}))([^>].*)$/m",
                         '<span style="color:#AA0055">\\1\\3</span>',
                         $text);
    $text = preg_replace("/^([^\s\n>]*?(>{2})+>)([^>].*)$/m",
                         '<span style="color:#AAAAAA">\\1\\3</span>',
                         $text);
    $text = preg_replace("/^([^\s\n>]*?(>{2})+)([^>].*)$/m",
                         '<span style="color:#DDAA00">\\1\\3</span>',
                         $text);

    return '<pre>'.$text.'</pre>';
}
