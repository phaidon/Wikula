<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: license.php 147 2010-04-20 09:16:54Z gilles $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Display the full text of a license
 * if its define is available in pnlang/$lang/actions/license.php 
 * 
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Dario Taraborelli
 * @param $args['type'] (optional) license to be displayed
 *                      default available ones are:
 *                      'GPL' :   GNU General Public License (default)
 *                      'LGPL':  GNU Lesser General Public License
 *                      'GFDL':  GNU Free Documentation License
 * @return string full text of the specified license
 */
function wikula_actionapi_license($args)
{

    // check if the passed type is available
    // if not or if it's not set, default the GPL
    $type = (isset($args['type']) && defined("{$args['type']}_FULL_TEXT")) ? $args['type'] : 'GPL';

    $render = pnRender::getInstance('wikula');
    $render->assign('license', "{$type}_FULL_TEXT");
    return $render->fetch('action/license.tpl', $type);
}
