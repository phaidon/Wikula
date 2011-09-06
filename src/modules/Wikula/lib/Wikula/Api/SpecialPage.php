<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wikula_Api_SpecialPage extends Zikula_AbstractApi
{
        /**
     * Instance of Zikula_View.
     *
     * @var Zikula_View
     */
    protected $view;

    /**
     * Initialize.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->setView();
    }

    /**
     * Set view property.
     *
     * @param Zikula_View $view Default null means new Render instance for this module name.
     *
     * @return Zikula_AbstractController
     */
    protected function setView(Zikula_View $view = null)
    {
        if (is_null($view)) {
            $view = Zikula_View::getInstance($this->getName());
        }

        $this->view = $view;
        return $this;
    }
    
    public function listpages()
    {
        return array(
            $this->__('PageIndex')          => array(
                'action' => 'pageindex',
                'title'  => $this->__('Page index')
            ),
            $this->__('MyPages')            => array(
                'action' => 'mypages',
                'title'  => $this->__('My pages')
            ),
            $this->__('OwnedPages')         => array(
                'action' => 'ownedpages',
                'title'  => $this->__('Owned pages')
            ),
            $this->__('RecentChanges')      => array(
                'action' => 'recentchanges',
                'title'  => $this->__('Recent changes')
            ),
            $this->__('HighScores')         => array(
                'action' => 'highscores',
                'title'  => $this->__('High scores')
            ),
            $this->__('MyChanges')          => array(
                'action' => 'mychanges',
                'title'  => $this->__('My changes')
            ),
            $this->__('Search')             => array(
                'action' => 'search',
                'title'  => $this->__('Search')
            ),
            $this->__('TextSearchExpanded') => array(
                'action' => 'textsearchexpanded',
                'title'  => $this->__('TextSearchExpanded')
            ),
            $this->__('WantedPages')        => array(
                'action' => 'wantedpages',
                'title'  => $this->__('Wanted pages')
            ),
            $this->__('OrphanedPages')      => array(
                'action' => 'orphanedpages',
                'title'  => $this->__('Orphaned pages')
            ),
            $this->__('Help')               => array(
                'action' => 'help',
                'title'  => $this->__('Help')
            ),
            $this->__('AllCategories')      => array(
                'action' => 'allcategories',
                'title'  => $this->__('All categories')
            ),
        );
    }
    
    public function get($args)
    {
        $action = $args['action'];
        if($action == 'help') {
            return $this->view->fetch('user/help.tpl');
        } else if ($action == 'allcategories') {
            $categories =  ModUtil::apiFunc($this->name, 'page', 'category', array(
                'compact' => 1,
                'notitle' => 1
            ));
            return $this->view->assign('categories', $categories)
                            ->fetch('action/allcategories.tpl');
        }
        $body  = '<h2>'.$args['title'].'</h2><br />';
        $body .= call_user_func(array($this, $action), $args);
        return $body;
    }

    

    public function category($args)
    {
        $tag     = (isset($args['page']) && !empty($args['page'])) ? $args['page'] : FormUtil::getPassedValue('tag', ModUtil::getVar('Wikula', 'root_page'));
        $col     = (isset($args['col']) && !empty($args['col'])) ? $args['col'] : 1;
        $full    = (isset($args['full']) && !empty($args['full'])) ? 1 : 0;
        $compact = (isset($args['compact']) && !empty($args['compact'])) ? 1 : 0;
        $notitle = (isset($args['notitle']) && !empty($args['notitle'])) ? 1 : 0;


        // if page is empty
        if (empty($tag) or $tag = $this->__('AllCategories')) {
            // CategoryCategory page as default
            $tag = $this->__('CategoryCategory');
        }


        $pages = ModUtil::apiFunc($this->name, 'user', 'FullCategoryTextSearch',
                              array('phrase' => $tag));


        if (!$pages) {
            return false;
        }

        // Delete the not authorized pages or the page itself
        foreach ($pages as $key => $page) {
            if ($page['page_tag'] == $tag || !SecurityUtil::checkPermission('Wikula::', 'page::'.$page['page_tag'], ACCESS_READ)) {
                unset($pages[$key]);
            }
        }

        $total = count($pages);
        if ($col >= $total) {
            $col = $total;
        }
        $int = floor(($total / $col));
        $endcell = $col - ($total - ($int * $col));


        $assign = array(
            'pages'   => $pages,
            'tag'     => $tag,
            'col'     => $col,
            'full'    => $full,
            'compact' => $compact,
            'notitle' => $notitle,
            'total'   => $total,
            'endcell' => $endcell
        );
        return $this->view->assign('action_cc', $assign)
                          ->fetch('action/categorycategory.tpl');

    }
    
    
    public function pageindex($args)
    {
        $letter      = (isset($args['letter'])) ? $args['letter'] : FormUtil::getPassedValue('letter');
        $username    = (UserUtil::isLoggedIn()) ? UserUtil::getVar('uname') : '';
        $currentpage = FormUtil::getPassedValue('tag', __('PageIndex'));

        // Check if we are in Wikula edit mode, and reset to the default PageIndex page
        if (ModUtil::getName() == 'Wikula' && FormUtil::getPassedValue('func') == 'edit') {
            $currentpage = $this->__('PageIndex');
        }

        // Check if this view is cached
        $this->view->cacheid = $username.$currentpage.$letter;
        if ($this->view->is_cached('action/pageindex.tpl')) {
           return $this->view->fetch('action/pageindex.tpl');
        }

        // If not, build it
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPages');

        if (!$pages) {
            return __('No pages found!');
        }

        $currentChar       = '';
        $user_owns_pages   = false;
        $headerletters     = array();
        $pagelist          = array();

        foreach ($pages as $page) {
            $value = '';
            if (preg_match("`(=){3,5}([^=\n]+)(=){3,5}`", $page['body'], $value)) {
                $formatting_tags = array('**', '//', '__', '##', "''", '++', '#%', '@@', '""');
                $value = str_replace($formatting_tags, '', $value[2]);
            } else {
                $value = $page['tag'];
            }
            $page['title'] = $value;

            $firstChar = strtoupper(substr($value, 0, 1));
            if (!preg_match('/[A-Za-z]/', $firstChar)) {
                $firstChar = '#';
            }

            if ($firstChar != $currentChar) {
                $headerletters[] = $firstChar;
                $currentChar     = $firstChar;
            }

            if (empty($letter) || $firstChar == $letter) {
                $pagelist[$firstChar][] = $page;

                if ($username == $page['owner']) {
                    $user_owns_pages = true;
                }
            }

        }

         $specialPages = $this->listpages();
         foreach( $specialPages as $tag => $value) {

            $page = array(
                'tag'   => $tag,
                'owner' => __('(Public)'),
                'title' => $value['title']

            );

            $firstChar = strtoupper(substr($tag, 0, 1));
            if (!preg_match('/[A-Za-z]/', $firstChar)) {
                $firstChar = '#';
            }
            if ($firstChar != $currentChar) {
                $headerletters[] = $firstChar;
                $currentChar     = $firstChar;
            }

            $pagelist[$firstChar][] = $page;
         }

        $headerletters = array_unique($headerletters);
        sort($headerletters);

        ksort($pagelist);

        $this->view->assign('currentpage',   $currentpage);
        $this->view->assign('headerletters', $headerletters);
        $this->view->assign('pagelist',      $pagelist);
        $this->view->assign('username',      $username);
        $this->view->assign('userownspages', $user_owns_pages);

        return $this->view->fetch('action/pageindex.tpl');
    }
    
    public function mypages($args)
    {
        if (!UserUtil::isLoggedIn()) {
            return __("You are not logged in, the list of pages you own couldn't be retrieved.");
        }

        $uname = UserUtil::getVar('uname');

        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPagesOwnedByUser',
                              array('uname' => $uname));

        if (!$pages) {
            return __('No pages found!');
        }



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

        return $this->view->assign('pagelist',  $pagelist)
                          ->assign('pagecount', count($pagelist))
                          ->assign('count',     $pages['count'])
                          ->assign('total',     $pages['total'])
                          ->fetch('action/mypages.tpl', $uname.$pages['count']);
    }
    
    function ownedpages($args)
    {
        if (!UserUtil::isLoggedIn()) {
            return;
        }

        $result = ModUtil::apiFunc($this->name, 'user', 'LoadAllPagesOwnedByUser', array(
            'uname'     => UserUtil::getVar('uname'),
            'justcount' => true)
        );

        if (!$result) {
            return __('Error during element fetching !');
        }
        $percent = round(($result['count']/$result['total'])*100, 2);



        return $this->view->assign($result)
                          ->assign('percent', $percent)
                          ->fetch('action/ownedpages.tpl');
    }
    
    
    function RecentChanges()
    {
        $max   = (int)ModUtil::getVar('Wikula', 'itemsperpage', 50);
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadRecentlyChanged',
                              array('numitems' => $max));

        if (!$pages) {
            return __('There are no recent changes');
        }

        $curday = '';
        $pagelist = array();
        foreach ($pages as $page)
        {
            list($day, $time) = explode(' ', $page['time']);
            if ($day != $curday) {
                $dateformatted = date(__('D, d M Y'), strtotime($day));
                $curday = $day;
            }

            $page['timeformatted'] = date(__('H:i T'), strtotime($page['time']));

            if ($page['user'] == System::getVar('anonymous')) {
                $page['user'] .= ' ('.__('anonymous user').')'; // anonymous user
            }

            $pagelist[$dateformatted][] = $page;
        }
        unset($pages);


        $this->view->assign('pagelist', $pagelist);
        return $this->view->fetch('action/recentchanges.tpl');
    }
    
    
    function highscores()
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

        return $this->view->assign('total', $total)
                          ->assign('items',  $items)
                          ->fetch('action/highscores.tpl');
    }

    function mychanges()
    {
        if (!UserUtil::isLoggedIn()) {
            return __("You are not logged in, the list of pages you've edited couldn't be retrieved.");
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
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPagesEditedByUser', array(
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

                    $page['timeformatted'] = date(__('D, d M Y'), strtotime($page['time']));

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

                    $page['timeformatted'] = date(__('H:i T'), strtotime($time));

                    $pagelist[$day][] = $page;

                    $my_edits_count++;
                }
            }
        }
        unset($pages);

        $output['editcount'] = $my_edits_count;
        $output['pagelist']  = $pagelist;


        $this->view->assign($output);
        return $this->view->fetch('action/mychanges.tpl', $uname.$alpha);
    }

    
    function search()
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

            $result = ModUtil::apiFunc($this->name, 'user', 'FullTextSearch',
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


        $this->view->assign('phrase',                 $phrase);
        $this->view->assign('results',                $result);
        $this->view->assign('resultcount',            count($result));
        $this->view->assign('notfound',               $notfound);
        $this->view->assign('oneword',                $oneword);
        $this->view->assign('TextSearchExpandedTag',  $this->__('TextSearchExpanded'));

        return $this->view->fetch('action/textsearch.tpl');
    }   

    
    function TextSearchExpanded()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        $phrase = FormUtil::getPassedValue('phrase');

        // Defaults
        $result   = array();
        $notfound = false;

        // Process the query
        if (!empty($phrase)) {
            $phrase = trim($phrase);

            $result = ModUtil::apiFunc($this->name, 'user', 'FullTextSearch',
                                   array('phrase' => $phrase));

            if (empty($result)) {
                $notfound = true;
            } else {
                $search = str_replace('"', '', $phrase);
                $search = preg_quote($search, '/');
                foreach ($result as $i => $item) {
                    preg_match("/(.{0,120}$search.{0,120})/is", $item['page_body'], $matchString);

                    $text = ModUtil::apiFunc($this->name, 'user', 'htmlspecialchars_ent',
                                         array('text' => isset($matchString[0]) ? $matchString[0] : ''));

                    $result[$i]['matchtext'] = preg_replace("/($search)/i",
                                                            "<span style=\"color:green;\"><b>$1</b></span>",
                                                            $text,
                                                            -1);

                }
            }
        }

        // create the output


        $this->view->assign('phrase',                 $phrase);
        $this->view->assign('results',                $result);
        $this->view->assign('resultcount',            count($result));
        $this->view->assign('notfound',               $notfound);
        $this->view->assign('TextSearchExpandedTag',  $this->__('TextSearchExpanded'));

        return $this->view->fetch('action/textsearchexpanded.tpl');
    }

    function wantedpages($args)
    {
        // default
        $linkingto = (isset($args['linkingto']) && !empty($args['linkingto'])) ? $args['linkingto'] : '';

        // reset the output items
        $items  = array();

        if (!empty($linkingto)) {
            $items = ModUtil::apiFunc($this->name, 'user', 'LoadPagesLinkingTo',
                                  array('tag' => $linkingto));

            if (!$items) {
                return __f('No pages linking to %s', $linkingto);
            }

        } else {
            $pages = ModUtil::apiFunc($this->name, 'user', 'LoadWantedPages');
            if (!$pages) {
                return $this->__('No wanted pages');
            }
            // Need permission check
            foreach ($pages as $page) {
                if ($page['to_tag'] != 'MissingPage') {
                    $items[] = $page;
                }
            }
            unset($pages);
        }


        $this->view->assign('items', $items);
        $this->view->assign('linkingto', $linkingto);
        return $this->view->fetch('action/wantedpages.tpl');
    }
    
    function OrphanedPages($args)
    {
        SessionUtil::setVar('linktracking', 0);

        $items = ModUtil::apiFunc($this->name, 'user', 'LoadOrphanedPages');
        return $this->view->assign('items', $items)
                          ->fetch('action/orphanedpages.tpl');
    }
    
}
