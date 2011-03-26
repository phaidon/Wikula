<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: modifier.php.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * PHP language file for Wikka highlighting (uses PHP built-in highlighting).
 */
function smarty_modifier_php($text, $method='diff')
{
    if ($method == 'diff') {
        // save output buffer and restart with clean buffer
        $dummy = ob_get_clean();
        ob_start();
        // replace diff-tags to prevent highlighting these html-entities!
        $text = str_replace(array('&pound;&pound;', '&yen;&yen;'),
                            array('��', '��'),
                            $text);
    }
    highlight_string($text);
    if ($method == 'diff') {
        // get highlighting output
        $listing = ob_get_clean();
        ob_start();
        // render diff tags
        $listing = preg_replace('/��<\/font>/',
                                '</font>��',
                                $listing);
        $listing = preg_replace('/��(.*?)��/',
                                '<span class="additions">\\1</span>',
                                $listing);
        $listing = preg_replace('/��<\/font>/',
                                '</font>��',
                                $listing);
        $listing = preg_replace('/��(.*?)��/',
                                '<span class="deletions">\\1</span>',
                                $listing);

        // write original output and revised highlighting back to fresh buffer
        $test = $dummy.$listing;
    }

    return $text;
}
