<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Special page api class
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
    
    /**
     * Check if a page is a special page
     *
     * @param string $tag Tag of page.
     *
     * @return boolean
     */
    public function isSpecialPage($tag) {
        return array_key_exists($tag, $this->listpages());
    }
     
    /**
     * List all special pages
     *
     * @return array
     */
    public function listpages()
    {
        return array(
            $this->__('Page_index')          => array(
                'action' => 'pageindex',
                'description' =>  $this->__('index of the available pages on the wiki')
            ),
            $this->__('My_pages')            => array(
                'action' => 'mypages',
                'description' => $this->__('list of pages that you own on this wiki')
            ),
            $this->__('Recent_changes')      => array(
                'action' => 'recentchanges',
                'description' => $this->__('check which pages that were changed recently')
            ),
            $this->__('High_scores')         => array(
                'action' => 'highscores',
                'description' => $this->__('check who had contributed more to the wiki')
            ),
            $this->__('My_changes')          => array(
                'action' => 'mychanges',
                'description' => $this->__('list of changes that you have done')
            ),
            $this->__('Search')             => array(
                'action' => 'search',
                'description' => $this->__('search something of your interest in the wiki')
            ),
            $this->__('Wanted_pages')        => array(
                'action' => 'wantedpages',
                'description' => $this->__('check out the pages pending for creation')
            ),
            $this->__('Orphaned_pages')      => array(
                'action' => 'orphanedpages',
                'description' => $this->__('list of orphaned pages')
            ),
            $this->__('Special_pages')      => array(
                'action' => 'specialpages',
                'description' => $this->__('list of special pages')
            ),
        );
    }
    

    /**
     * It returns the specialpages page 
     *
     * @return string HTML string containing the rendered template.
     */
    public function specialpages()
    {
        
        $specialpages = $this->listpages();
        unset($specialpages[$this->__('Special_pages')]);
        $this->view->assign('specialpages',   $specialpages);
        return $this->view->fetch('action/specialpages.tpl');
        
    }
    
    /**
     * It returns the pageindex page 
     *
     * @param array $args Arguments.
     * 
     * @return string HTML string containing the rendered template.
     */
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
        
        
        $this->view->assign('pages', $pages);

        return $this->view->fetch('action/pageindex.tpl');
    }
    
    /**
     * It returns the mypages page 
     *
     * @return string HTML string containing the rendered template.
     */
    public function mypages()
    {
        if (!UserUtil::isLoggedIn()) {
            return __("You are not logged in, the list of pages you own couldn't be retrieved.");
        }

        $uname = UserUtil::getVar('uname');

        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPagesOwnedByUser',
                              array('uname' => $uname));

        if (!$pages) {
            return $this->__('No pages found!');
        }


        return $this->view->assign('pages',  $pages['pages'])
                          ->assign('pagecount', count($pages['pages']))
                          ->assign('count',     $pages['count'])
                          ->assign('total',     $pages['total'])
                          ->fetch('action/mypages.tpl', $uname.$pages['count']);
    }
    
    /**
     * It returns the RecentChanges page 
     *
     * @return string HTML string containing the rendered template.
     */
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
        foreach ($pages as $page) {            
            $day = $page['time']->format('Y-m-d');
            if ($day != $curday) {
                $dateformatted = $page['time']->format('D, d M Y');
                $curday = $day;
            }

            $page['timeformatted'] = $page['time']->format('H:i T');

            if ($page['user'] == System::getVar('anonymous')) {
                $page['user'] .= ' ('.__('anonymous user').')'; // anonymous user
            }

            $pagelist[$dateformatted][] = $page;
        }
        unset($pages);


        $this->view->assign('pagelist', $pagelist);
        return $this->view->fetch('action/recentchanges.tpl');
    }
    
    /**
     * It returns the RecentChanges page 
     *
     * @return string HTML string containing the rendered template.
     */
    function highscores()
    {
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('p.user, count(p.id) as number')
           ->from('Wikula_Entity_Pages', 'p')
           ->groupBy('p.user')
           ->orderBy('number', 'DESC')
           ;
        $query = $qb->getQuery();
        $revisions = $query->getArrayResult();
        
        
        $qb = $em->createQueryBuilder();
        $qb->select('p.user, count(p.id) as number')
           ->where('p.latest = :latest')
           ->setParameter('latest', 'Y')
           ->from('Wikula_Entity_Pages', 'p')
           ->groupBy('p.user')
           ->orderBy('number', 'DESC')
           ;
        $query = $qb->getQuery();
        $pages = $query->getArrayResult();
                
        $items = array();
        $total['revisions'] = 0;
        $total['pages']     = 0;
        $i = 1;

        foreach ($revisions as $revision) {
            $total['revisions'] += $revision['number'];
            $user = $revision['user'];
            $items[$user]['revisions'] = $revision['number'];
            $items[$user]['i'] = $i;
            $i++;
        }
        foreach ($pages as $page) {
            $total['pages'] += $page['number'];
            $user = $page['user'];
            $items[$user]['pages'] =  $page['number'];
        }
        

        return $this->view->assign('total', $total)
                          ->assign('items',  $items)
                          ->fetch('action/highscores.tpl');
    }

    /**
     * It returns the mychanges page 
     *
     * @return string HTML string containing the rendered template.
     */
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
                    //$day day header
                    $day  = $page['time']->format('Y-m-d');
                    if ($day != $curDay) {
                        $curDay = $day;
                    }
                    $page['timeformatted'] = $page['time']->format('H:i: T');

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

    
    /**
     * It redirecst the textsearch to the search page
     *
     * @return string HTML string containing the rendered template.
     */
    public function textsearch() {
        return $this->search();
    }
    
    /**
     * It returns the search page 
     *
     * @return string HTML string containing the rendered template.
     */
    public function search()
    {
        $phrase         = FormUtil::getPassedValue('phrase');
        $fulltextsearch = FormUtil::getPassedValue('fulltextsearch', false);

        $phrase = str_replace($this->__('containing...'), '', $phrase);
        
        // Defaults
        $result   = array();
        $notfound = false;
        $oneword  = false;

        // Process the query
        if (!empty($phrase)) {
            $phrase = trim($phrase);

            $result = ModUtil::apiFunc(
                          $this->name,
                          'user',
                          'Search',
                          array(
                              'phrase'         => $phrase,
                              'fulltextsearch' => $fulltextsearch
                          )
                      );

            if (empty($result)) {
                $notfound = true;
            }

            // check if searched phrase exists as tag
            // only do this check if searched phrase is only one word and if there is no space in it
            if (strpos($phrase, ' ') !== false) {
                $oneword  = true;
            }
        }
        
        // create the output

        return $this->view->assign('phrase',         $phrase)
                          ->assign('results',        $result)
                          ->assign('resultcount',    count($result))
                          ->assign('notfound',       $notfound)
                          ->assign('oneword',        $oneword)
                          ->assign('fulltextsearch', $fulltextsearch)
                          ->fetch('action/search.tpl');
    }   

    /**
     * It returns the TextSearchExpanded page 
     *
     * @return statement
     */
    function TextSearchExpanded()
    {
        return $this->search();
    }
    
    /**
     * It returns the wantedpages page 
     *
     * @param array $args Arguments.
     * 
     * @return string HTML string containing the rendered template.
     */
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
    
    /**
     * It returns the OrphanedPages page 
     *
     * @return string HTML string containing the rendered template.
     */
    function OrphanedPages()
    {
        SessionUtil::setVar('linktracking', 0);

        $items = ModUtil::apiFunc($this->name, 'user', 'LoadOrphanedPages');
        return $this->view->assign('items', $items)
                          ->fetch('action/orphanedpages.tpl');
    }
    
    /**
     * Returns the content of a special page
     *
     * @param array $args Arguments.
     * 
     * @return string HTML string containing the rendered template.
     */
    public function get($args)
    {
        $action = $args['action'];	  	
        if ($action == 'allcategories') {	  	
            $categories =  ModUtil::apiFunc($this->name, 'page', 'category', array(
                'compact' => 1,
                'notitle' => 1
            ));

            return $this->view->assign('categories', $categories)  	
                               ->fetch('action/allcategories.tpl');
        }
        return call_user_func(array($this, $action), $args);

    }
    
    
    
}
