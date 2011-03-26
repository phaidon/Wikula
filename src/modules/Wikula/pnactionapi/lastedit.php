<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: lastedit.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Prints short infos on last edit
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param integer $args['show'] possible values:
 *     0: show user only
 *     1: show user and notes
 *     2: show user, notes, date
 *     3: show user, notes, date and quickdiff link
 * @todo implement the DIFFLINK
 */
function wikula_actionapi_lastedit($args)
{
    $tag    = FormUtil::getPassedValue('tag', pnModGetVar('wikula', 'root_page'));
    $show   = (isset($args['show']) && is_numeric($args['show'])) ? $args['show'] : 1;

    // initialize the output array
    $output = array(
        'tag'  => $tag,
        'show' => $show
    );

    $output['page'] = pnModAPIFunc('wikula', 'user', 'LoadPage',
                                   array('tag' => $tag));

    if (!$output['page']) {
        return;
    }

    if ($show >= 2) {
        list($day, $time) = explode(' ', $output['page']['time']);
        $output['dateformatted'] = date(__('D, d M Y', $dom), strtotime($day));
        $output['timeformatted'] = date(__('H:i T', $dom), strtotime($output['page']['time']));
    }

    $render = pnRender::getInstance('wikula', false);
    $render->assign('action_le', $output);
    return $render->fetch('action/lastedit.tpl');
}
