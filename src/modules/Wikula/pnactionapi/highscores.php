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
 * @todo convert to DBUtil and marshallObjects
 * @param string $args['full'] (optional) flag to enable a full width table to show the results (default=false)
 */
function wikula_actionapi_highscores($args)
{
    $full = (isset($args['full']) && $args['full']) ? true : false;

    $dbconn  =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $ptbl = &$pntable['wikula_pages'];
    $pcol = &$pntable['wikula_pages_column'];
    $utbl = &$pntable['users'];
    $ucol = &$pntable['users_column'];

    $sql = 'SELECT  count(*) as ncount, '
                   .$ucol['uname']
         .' FROM  '.$utbl.' AS tbl, '.$ptbl.' AS a'
         .' WHERE tbl.'.$ucol['uname'].' = a.'.$pcol['owner'].' '
         .' AND   a.'.$pcol['latest'].' = \'Y\' '
         .' GROUP BY tbl.'.$ucol['uname']
         .' ORDER BY ncount DESC, '
                   .'tbl.'.$ucol['uname'].' ASC';

    $result =& $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return $dbconn->ErrorMsg();
    }

    $total = pnModAPIFunc('wikula', 'user', 'Action',
                          array('action'   => 'countpages',
                                'nolink'   => true,
                                'nopublic' => true));

    // fill the output variables
    $i = 1;
    $items = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($ncount, $uname) = $result->fields;

        $items[] = array('position' => $i,
                         'uname'    => $uname,
                         'count'    => $ncount,
                         'percent'  => round(($ncount/$total)*100, 2));

        $i++;
    }
    $result->Close();

    $render = pnRender::getInstance('wikula'/*, false*/);
    $render->assign('hsfull', $full);
    $render->assign('items',  $items);
    return $render->fetch('action/highscores.tpl');
}
