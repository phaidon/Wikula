<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function wikula_actionapi_mypages()
{
    $dom = ZLanguage::getModuleDomain('wikula');
        if (!UserUtil::isLoggedIn()) {
            return __("You are not logged in, the list of pages you own couldn't be retrieved.", $dom);
        }

        $uname = UserUtil::getVar('uname');

        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadAllPagesOwnedByUser',
                              array('uname' => $uname));

        if (!$pages) {
            return __('No pages found!', $dom);
        }

        $render = pnRender::getInstance('Wikula', false);

        $curChar = '';
        $pagelist = array();

        foreach ($pages['pages'] as $page) {
            $firstChar = strtoupper(substr($page['tag'], 0, 1));
            if (!preg_match('/[A-Z,a-z]/', $firstChar)) {
                $firstChar = '#';
            }
            if ($firstChar != $curChar) {
                $curChar = $firstChar;
            }
            $pagelist[$firstChar][] = $page;
        }
        unset($pages['pages']);

        $render->assign('pagelist',  $pagelist);
        $render->assign('pagecount', count($pagelist));
        $render->assign('count',     $pages['count']);
        $render->assign('total',     $pages['total']);

        return $render->fetch('action/mypages.tpl', $uname.$pages['count']);
}
    
?>