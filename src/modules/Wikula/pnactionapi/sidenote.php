<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: sidenote.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows a note/tip/warning
 * 
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Tormod Haugen
 * @author Jason Huebel
 * @param string $args['text'] Content for the note
 * @param string $args['type'] (optional) tip/note/warning, default = note
 * @param string $args['side'] (optional) left/right/none, default = left
 * @param string $args['title'] (optional) optional title
 * @param string $args['width'] (optional) default '200px'
 */
function wikula_actionapi_sidenote($args)
{
    if (!isset($args['text']) || empty($args['text'])) {
        return false;
    }

    $types = array('tip', 'note', 'warning');
    $sides = array('left', 'right', 'none');

    $type  = (isset($args['type']) && in_array($args['type'], $types)) ? $args['type'] : 'note';
    $side  = (isset($args['side']) && in_array($args['side'], $sides)) ? $args['side'] : 'left';
    $title = (isset($args['title'])) ? $args['title'] : '';
    $width = (isset($args['width'])) ? $args['width'] : '200px';

    $render = pnRender::getInstance('wikula', false);
    //$render->force_compile = true;

    $render->assign('title', $title);
    $render->assign('text',  $args['text']);
    $render->assign('width', $width);
    $render->assign('type',  $type);
    $render->assign('side',  $side);

    return $render->fetch('action/sidenote.tpl');
}
