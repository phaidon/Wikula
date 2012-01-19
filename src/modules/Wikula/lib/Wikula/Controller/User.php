<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Access to (non-administrative) user-initiated actions for the Wikula module.
 * 
 * @package Wikula
 */
class Wikula_Controller_User extends Zikula_AbstractController
{
    /**
     * autoload
     * 
     * Loads common values at the beginning
     *
     */
    function __autoload($class_name) {
        require_once 'modules/Wikula/lib/Wikula/Common.php';
    }

    /**
     * main
     * 
     * This function is a forward to the show function. 
     *
     */    
    public function main($args)
    {
        return $this->show($args);
    }
    
    /**
     * show
     * 
     * Displays a wiki page
     *
     * @param string $args['tag'] Tag of the wiki page to show
     * @return smarty output
     */
    public function show($args)
    {   
        
        // Get input parameters
        $tag  = isset($args['tag']) ? $args['tag'] : FormUtil::getPassedValue('tag');
        $time = isset($args['time']) ? $args['time'] : FormUtil::getPassedValue('time');
        $raw  = isset($args['raw']) ? $args['raw'] : FormUtil::getPassedValue('raw');
        unset($args);
        
        if(empty($tag)) {
            $tag = $this->getVar('root_page');
        }

        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);        
        
        
        if (empty($time)) {
            $time = null;
        }
        
        // redirect if tag contains spaces
        if (strpos($tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $tag),
                'time' => $time,
                'raw'  => $raw
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            return System::redirect($redirecturl);
        }
        
        
        // check if it is category page
        if($tag == $this->__('Categories')) {
            $redirecturl = ModUtil::url($this->name, 'category', 'showAll');
            return System::redirect($redirecturl);
        }     
        if( substr($tag, 0, 8) == 'Category') {
            $args = array( 'category' => substr($tag, 8) );
            $redirecturl = ModUtil::url( $this->name, 'category', 'show', $args);
            return System::redirect($redirecturl);
        }
        
        
        // check if it is category page
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if( array_key_exists($tag, $specialPages)) {
            $content = ModUtil::apiFunc($this->name, 'SpecialPage', 'get', $specialPages[$tag]);
            return $this->view->assign('content', $content)
                              ->assign('tag',     $tag)
                              ->assign('name',    str_replace('_', ' ', $tag))
                              ->fetch('user/specialPage.tpl');
        }
        
        
        // Get the page
        $page = ModUtil::apiFunc($this->name, 'user', 'LoadPage', array(
            'tag'  => $tag,
            'time' => $time)
        );
       
        
        // Validate invalid petition
        if (!$page && !empty($time)) {
            return LogUtil::registerError($this->__("The page you requested doesn't exists"), null, ModUtil::url($this->name));
        }

        // Get the latest version
        /*if (empty($time)) {
            $latest = $page;
        } else {
            $latest = ModUtil::apiFunc($this->name, 'user', 'LoadPage', array('tag' => $tag));
        }*/

        // Check if this tag doesn't exists
        if (!$page ) {//&& !$latest) {
            LogUtil::registerStatus(__('The page does not exist yet! do you want to create it? Feel free to participate and be the first who creates content for this page!'));
            return System::redirect(ModUtil::url($this->name, 'user', 'edit', array('tag' => $tag)));
        }

        // Resetting session access and previous
        SessionUtil::delVar('wikula_access');
        SessionUtil::setVar('wikula_previous', $tag);
        
        
        // TODO: check if this can be migrated to an action
        // we'll get later revisions too because we want to display the history and the last editors next to the page

        $this->view->assign('tag',      $tag);
        $this->view->assign('time',     $time);
        $this->view->assign('showpage', $page);
        
        $datetime = $page['time']->format('Y-m-d H:i:s');
        return $this->view->fetch('user/show.tpl', md5($page['id'].$datetime));
    }
    

    /**
     * edit
     * 
     * This function edits a wiki page.
     *     
     * @param string $_POST['tag'] name of the wiki page
     * @return smarty output
     */
    public function edit()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/edit.tpl', new Wikula_Handler_EditTag());
    }

    /**
     * rename
     * 
     * This function renames a wiki page
     *
     * @return smarty output
     */
    public function renameTag()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/rename.tpl', new Wikula_Handler_RenameTag());
    }
    
    /**
     * history
     * 
     * This function shows the history of a wiki page
     *
     * @param string $args['tag'] tag of the page
     * @TODO Implement the time parameter?
     * @TODO Add a paginator?
     * @TODO Improve this view with JavaScript sliders
     * @return smarty output
     */
    public function history()
    {
        // Security check will be done by LoadRevisions()
        
        $tag = FormUtil::getPassedValue('tag');        
        ModUtil::apiFunc($this->name, 'User', 'CheckTag', $tag);
        
        if( ModUtil::apiFunc($this->name, 'SpecialPage', 'isSpecialPage', $tag) ) {
            return System::redirect( ModUtil::url($this->name, 'user', 'main', array('tag' => $tag)) );
        }

        $revisions = ModUtil::apiFunc($this->name, 'user', 'LoadRevisions', array(
            'tag' => $tag)
        );

        $this->view->assign('tag',     $tag);
        $this->view->assign('objects', $revisions['objects']);
        $this->view->assign('oldest',  $revisions['oldest']);
        return $this->view->fetch('user/history.tpl');
    }

    /**
     * XML output of the recent changes of the specified page
     *
     * @return xml maincontent for the RSS theme
     */
    public function RecentChangesXML()
    {
        
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead');

        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadRecentlyChanged');

        if (!$pages) {
            return LogUtil::registerError(__('Error during element fetching !'));
        }

        
        $this->view->force_compile = true;

        $this->view->assign('pages', $pages);

        
        return $this->view->fetch('xml/recentchanges.tpl');
    }

    /**
     * XML output of the revisions for the specified page
     *
     * @return xml maincontent for the RSS theme
     */
    public function RevisionsXML()
    {

        $tag = FormUtil::getPassedValue('tag'); 
        // Permission check
        ModUtil::apiFunc($this->name, 'Permission', 'canRead', $tag);

        $pages = ModUtil::apiFunc(
            $this->name,
            'user',
            'LoadRevisions0',
            $tag
        );
                
        
        $this->view->force_compile = true;
        $this->view->assign('tag',   $tag);
        $this->view->assign('pages', $pages);
        return $this->view->fetch('xml/revisions.tpl');
    }

    /**
     * backlings
     * 
     * This function displays a list of internal pages linking to the current 
     * page.
     * 
     * @param string $_POST['tag'] name of the wiki page
     * @return smarty output
     */
    public function backlinks()
    {
        // Security check will be done by LoadPagesLinkingTo()
        
        $tag = FormUtil::getPassedValue('tag');        
        ModUtil::apiFunc($this->name, 'User', 'CheckTag', $tag);
        
        if( ModUtil::apiFunc($this->name, 'SpecialPage', 'isSpecialPage', $tag) ) {
            return System::redirect( ModUtil::url($this->name, 'user', 'main', array('tag' => $tag)) );
        }

        // Get the variables
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadPagesLinkingTo', $tag);
                
        $this->view->assign('tag',   $tag);
        $this->view->assign('pages', $pages);
        return $this->view->fetch('user/backlinks.tpl');
    }

    /**
     * clone tag
     * 
     * This function clones a wiki page and save a copy of it as a new page.
     *      
     * @param string $_POST['tag'] name of the wiki page
     * @return smarty output
     */
    public function cloneTag()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/clone.tpl', new Wikula_Handler_CloneTag());
    }
    
    /**
     * settings
     * 
     * This functions shows the users settings.
     *      
     * @return smarty output
     */    
    public function settings()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/settings.tpl', new Wikula_Handler_Settings());
    }
    

    
    /**
     * Displays the wiki pages of a given category.
     *
     * @param string $args['category'] Name of the category.
     * @return smarty output
     */
    public function category($args)
    {   
        // Get input parameters
        $category  = isset($args['category']) ? $args['category'] : FormUtil::getPassedValue('category');

        if (empty($category)) {
            return LogUtil::registerError($this->__('No category specified!'));
        }
        
        // get pages of a category
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('c.tag')
           ->from('Wikula_Entity_Categories', 'c')
           ->where('c.category = :category')
           ->setParameter('category', $category)
           ->orderBy('c.tag');
        $pages = $qb->getQuery()->getArrayResult();

        return $this->view->assign('category', $category)
                          ->assign('pages', $pages)
                          ->fetch('user/category.tpl');
    }
    
    
    
    /**
     * Displays a list of all categories
     *
     * @return smarty output
     */
    public function categories()
    {   

        // get a list of all categoriess
        $em = $this->getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->select('c.category')
           ->from('Wikula_Entity_Categories', 'c')
           ->groupBy('c.category')
           ->orderBy('c.category');
        $categories = $qb->getQuery()->getArrayResult();
 
        return $this->view->assign('categories', $categories)
                          ->fetch('user/categories.tpl');
    }
    
    
    
}
