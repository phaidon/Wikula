<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
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
 */
function smarty_modifier_teaser($text)
{
    $text = strip_tags($text);
    if (strlen($text) > 100) {
        $text = preg_replace('/\s+?(\S+)?$/', '', substr($text, 0, 100)).'...';
    }
    return $text;
}
