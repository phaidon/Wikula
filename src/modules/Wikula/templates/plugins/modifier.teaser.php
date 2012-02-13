<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * This function modifies a perma link.
 *
 * @param string $text Text.
 * @param string $search Phrase to highlight.
 * 
 * @return string
 */
function smarty_modifier_teaser($text, $search)
{
    $text = strip_tags($text);
    $text = DataUtil::formatForDisplay($text);
    $contextSize = 100;
    return StringUtil::highlightWords($text, $search, $contextSize);
}
