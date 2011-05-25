<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function wikula_actionapi_search()
{
    $dom = ZLanguage::getModuleDomain('Wikula');
    $phrase = FormUtil::getPassedValue('phrase');

    // Defaults
    $result   = array();
    $notfound = false;
    $oneword  = false;

    // Process the query
    if (!empty($phrase)) {
        $phrase = trim($phrase);

        $result = ModUtil::apiFunc('wikula', 'user', 'FullTextSearch',
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
    
?>