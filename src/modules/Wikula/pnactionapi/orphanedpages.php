<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: orphanedpages.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print Orphaned Pages
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_OrphanedPages($args)
{
    SessionUtil::setVar('linktracking', 0);

    $items = pnModAPIFunc('wikula', 'user', 'LoadOrphanedPages');

    $render = pnRender::getInstance('wikula', false);
    $render->assign('items', $items);
    return $render->fetch('action/orphanedpages.tpl');
}
