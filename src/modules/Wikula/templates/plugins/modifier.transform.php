<?php

/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Wikula
 */

function smarty_modifier_transform($text)
{
    if( ModUtil::available('LuMicuLa') ) {
        return ModUtil::apiFunc('LuMicuLa', 'user', 'transform', array(
            'text'    => $text,
            'modname' => 'Wikula')
        );
    } else {
        return $text;
    }
}
