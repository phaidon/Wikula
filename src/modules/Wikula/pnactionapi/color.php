<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: color.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Color selected text
 * 
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['text'] text to "colorize"
 * @param integer $args['c'] (optional) name of the color
 * @param integer $args['hex'] (optional) hex code of the color if name not defined
 */
function wikula_actionapi_color($args)
{
    if (!isset($args['text'])) {
        return;
    }

    $colorcode = 'inherit';
    if (isset($args['c'])) {
        $colorcode = $args['c'];
    } elseif (isset($args['hex'])) {
        $colorcode = $args['hex'];
    }

    return '<span style="color:'.$colorcode.';">'.$args['text'].'</span>';
}
