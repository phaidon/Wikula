<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: textsearchexpanded.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Expanded Text Search
 *
 * @author Mateo Tibaquir�
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['phrase'] phrase to search
 */
function wikula_actionapi_TextSearchExpanded()
{
    $dom = ZLanguage::getModuleDomain('wikula');
    $phrase = FormUtil::getPassedValue('phrase');

    // Defaults
    $result   = array();
    $notfound = false;

    // Process the query
    if (!empty($phrase)) {
        $phrase = trim($phrase);

        $result = ModUtil::apiFunc('wikula', 'user', 'FullTextSearch',
                               array('phrase' => $phrase));

        if (empty($result)) {
            $notfound = true;
        } else {
            $search = str_replace('"', '', $phrase);
            $search = preg_quote($search, '/');
            foreach ($result as $i => $item) {
                preg_match("/(.{0,120}$search.{0,120})/is", $item['page_body'], $matchString);

                $text = ModUtil::apiFunc('Wikula', 'user', 'htmlspecialchars_ent',
                                     array('text' => isset($matchString[0]) ? $matchString[0] : ''));

                $result[$i]['matchtext'] = preg_replace("/($search)/i",
                                                        "<span style=\"color:green;\"><b>$1</b></span>",
                                                        $text,
                                                        -1);

            }
        }
    }

    // create the output
    $render = pnRender::getInstance('wikula', false);

    $render->assign('phrase',                 $phrase);
    $render->assign('results',                $result);
    $render->assign('resultcount',            count($result));
    $render->assign('notfound',               $notfound);
    $render->assign('TextSearchExpandedTag',  __('TextSearchExpanded', $dom));

    return $render->fetch('action/textsearchexpanded.tpl');
}
