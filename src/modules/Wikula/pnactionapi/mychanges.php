<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function wikula_actionapi_mychanges()
{
    $dom = ZLanguage::getModuleDomain('Wikula');
    if (!UserUtil::isLoggedIn()) {
        return __("You are not logged in, the list of pages you've edited couldn't be retrieved.", $dom);
    }

    $tag   = FormUtil::getPassedValue('tag', ModUtil::getVar('Wikula', 'root_page'));
    $uname = (isset($args['uname']) && !empty($args['uname'])) ? $args['uname'] : UserUtil::getVar('uname');
    $alpha = (isset($args['alpha']) && is_numeric($args['alpha']) && $args['alpha']) ? true : (bool)FormUtil::getPassedValue('alpha');

    // initialize the output parameter
    $output = array(
        'tag'   => $tag,
        'alpha' => $alpha,
        'uname' => $uname
    );

    // TODO: distinct parameter needed when alpha=1?
    $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadAllPagesEditedByUser', array(
        'alpha' => $alpha,
        'uname' => $uname
    ));

    $my_edits_count = 0;
    $pagelist = array();

    if ($pages) {
        if ($alpha) {
            $curChar  = '';
            $last_tag = '';
            foreach ($pages as $page) {
                if ($last_tag != $page['tag']) {
                    $last_tag = $page['tag'];
                    $firstChar = strtoupper(substr($page['tag'], 0, 1));
                    if (!preg_match('/[A-Z,a-z]/', $firstChar)) {
                        $firstChar = '#';
                    }
                    if ($firstChar != $curChar) {
                        $curChar = $firstChar;
                    }
                }

                $page['timeformatted'] = date(__('D, d M Y', $dom), strtotime($page['time']));

                $pagelist[$firstChar][] = $page;

                $my_edits_count++;
            }
        } else {
            $curDay = '';
            foreach ($pages as $page) {
                // day header
                list($day, $time) = explode(' ', $page['time']);
                if ($day != $curDay) {
                    $curDay = $day;
                }

                $page['timeformatted'] = date(__('H:i T', $dom), strtotime($time));

                $pagelist[$day][] = $page;

                $my_edits_count++;
            }
        }
    }
    unset($pages);

    $output['editcount'] = $my_edits_count;
    $output['pagelist']  = $pagelist;

    $render = pnRender::getInstance('Wikula', false);
    $render->assign($output);
    return $render->fetch('action/mychanges.tpl', $uname.$alpha);
}
    
?>