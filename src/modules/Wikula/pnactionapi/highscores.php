<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: highscores.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Shows a highscore list
 * 
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_highscores()
{
    $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
    $q->select('user, count(*) as count');
    $q->groupBy('user');
    $q->orderBy('count desc');
    $items = $q->execute();
    $items = $items->toArray();
    
    $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
    $total = $q->execute();
    $total = $total->count();

    $render = pnRender::getInstance('Wikula');
    $render->assign('total', $total);
    $render->assign('items',  $items);
    return $render->fetch('action/highscores.tpl');
}
