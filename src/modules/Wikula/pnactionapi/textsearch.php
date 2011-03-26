<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: textsearch.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Text Search
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['phrase'] phrase to search
 */
function wikula_actionapi_TextSearch()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $phrase = FormUtil::getPassedValue('phrase');

    // Defaults
    $result   = array();
    $notfound = false;
    $oneword  = false;

    // Process the query
    if (!empty($phrase)) {
        $phrase = trim($phrase);

        $result = pnModAPIFunc('wikula', 'user', 'FullTextSearch',
                               array('phrase' => $phrase));

        if (empty($result)) {
            $notfound = true;
        }

        // check if searched phrase exists as tag
        // only do this check if searched phrase is only one word and if there is no space in it
        if (strpos($phrase, ' ') !== false)  {
            $oneword  = true;
        }
    }

    // create the output
    $render = pnRender::getInstance('wikula', false);

    $render->assign('phrase',                 $phrase);
    $render->assign('results',                $result);
    $render->assign('resultcount',            count($result));
    $render->assign('notfound',               $notfound);
    $render->assign('oneword',                $oneword);
    $render->assign('TextSearchExpandedTag',  __('TextSearchExpanded', $dom));

    return $render->fetch('action/textsearch.tpl');
}
