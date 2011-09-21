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


class Wikula_Controller_User extends Zikula_AbstractController
{

    function __autoload($class_name) {
        require_once 'modules/Wikula/lib/Wikula/Common.php';
    }

    
    public function main($args)
    {
        return $this->show($args);
    }
    
    /**
     * Show function
     * 
     * Displays a wiki page
     *
     * @param string $args['tag'] Tag of the wiki page to show
     * @TODO Improve the authors box grouping the same users contribs
     * @TODO Do not show the Last edit in the authorsbox if it's the same creation
     * @return unknown
     */
    
    public function show($args)
    {

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ)
        );
        

        // Get input parameters
        $tag  = isset($args['tag']) ? $args['tag'] : FormUtil::getPassedValue('tag');
        $time = isset($args['time']) ? $args['time'] : FormUtil::getPassedValue('time');
        $raw  = isset($args['raw']) ? $args['raw'] : FormUtil::getPassedValue('raw');
        unset($args);

        if(empty($tag)) {
            $tag = $this->getVar('root_page');
        }

        
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
            System::redirect($redirecturl);
        }
        
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        
        if( array_key_exists($tag, $specialPages)) {
            $content = ModUtil::apiFunc($this->name, 'SpecialPage', 'get', $specialPages[$tag]);
            return $this->view->assign('content', $content)
                              ->assign('tag',     $tag)
                              ->fetch('user/specialPage.tpl');
        }


        // Get the page
        $page = ModUtil::apiFunc($this->name, 'user', 'LoadPage', array(
            'tag'  => $tag,
            'time' => $time)
        );
       
        
        // Validate invalid petition
        if (!$page && !empty($time)) {
            LogUtil::registerError(__("The page you requested doesn't exists"), null, ModUtil::url($this->name));
        }

        // Get the latest version
        if (empty($time)) {
            $latest = $page;
        } else {
            $latest = ModUtil::apiFunc($this->name, 'user', 'LoadPage', array('tag' => $tag));
        }

        // Check if this tag doesn't exists
        if (!$page && !$latest) {
            LogUtil::registerStatus(__('The page does not exist yet! do you want to create it? Feel free to participate and be the first who creates content for this page!'));
            System::redirect(ModUtil::url($this->name, 'user', 'edit', array('tag' => $tag)));
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
     * Edit method
     */
    public function edit()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/edit.tpl', new Wikula_Handler_EditTag());
    }

    /**
     * Rename method
     */
    public function renameTag()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/rename.tpl', new Wikula_Handler_RenameTag());
    }
    
    /**
     * Show the history of the Wiki Page
     *
     * @param string $args['tag'] tag of the page
     * @TODO Implement the time parameter?
     * @TODO Add a paginator?
     * @TODO Improve this view with JavaScript sliders
     * @return unknown
     */
    public function history($args)
    {
         // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ)
        );
        
        $tag  = FormUtil::getPassedValue('tag');
        
        // redirect if tag contains spaces
        if (strpos($tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $tag),
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }

        if (empty($tag)) {
            return LogUtil::registerError(
                __f('Missing argument [%s]', 'tag'),
                null,
                ModUtil::url($this->name, 'user', 'main')
            );
        }
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if( array_key_exists($tag, $specialPages)) {
            return System::redirect(ModUtil::url($this->name, 'user', 'main', array('tag' => $tag)));
        }

        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadRevisions', array(
            'tag' => $tag)
        );

        if (!$pages) {
            return LogUtil::registerError(
                __f('No %s found.', 'Rev'),
                null,
                ModUtil::url($this->name, 'user', 'main')
            );
        }

        $objects  = array();
        $previous = array();
        foreach ($pages as $page) {

            if (empty($previous)) {
                // We filter the first one as we don't want to check it
                $previous = $page;
                continue;
            }

            $bodylast = explode("\n", $previous['body']);

            $bodynext = explode("\n", $page['body']);

            $added   = array_diff($bodylast, $bodynext);
            $deleted = array_diff($bodynext, $bodylast);

            if ($added) {
                $newcontent = implode("\n", $added)/*."\n"*/;
            } else {
                $newcontent = '';
                $added = false;
            }

            if ($deleted) {
                $oldcontent = implode("\n", $deleted)/*."\n"*/;
            } else {
                $oldcontent = '';
                $deleted = false;
            }

            $objects[] = array(
                'pageAtime'    => $previous['time'],
                'pageBtime'    => $page['time'],
                'pageAtimeurl' => urlencode($previous['time']),
                'pageBtimeurl' => urlencode($page['time']),
                'EditedByUser' => $previous['user'],
                'note'         => $previous['note'],
                'newcontent'   => $newcontent,
                'oldcontent'   => $oldcontent,
                'added'        => $added,
                'deleted'      => $deleted
            );

            $previous = $page;
        }

        

        $this->view->assign('tag',     $tag);
        $this->view->assign('objects', $objects);
        $this->view->assign('oldest',  $page);

        return $this->view->fetch('user/history.tpl');
    }

    /**
     * XML output of the recent changes of the specified page
     *
     * @return xml maincontent for the RSS theme
     */
    public function RecentChangesXML()
    {
        
        if (!SecurityUtil::checkPermission('Wikula::', 'xml::recentchanges', ACCESS_READ)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), null, ModUtil::url($this->name, 'user', 'main'));
        }

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
        
        if (!SecurityUtil::checkPermission('Wikula::', 'xml::revisions', ACCESS_READ)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), null, ModUtil::url($this->name, 'user', 'main'));
        }

        $tag = FormUtil::getPassedValue('tag');

        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadRevisions', array('tag' => $tag));

        if (!$pages) {
            return LogUtil::registerError(__('Error during element fetching !'));
        }

        
        $this->view->force_compile = true;

        $this->view->assign('tag',   $tag);
        $this->view->assign('pages', $pages);

        return $this->view->fetch('xml/revisions.tpl');
    }

    /**
     * Display a list of internal pages linking to the current page
     */
    public function backlinks()
    {
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ)
        );
        
        $tag = FormUtil::getPassedValue('tag');
        if (empty($tag)) {
            return LogUtil::registerError(__f('Missing argument [%s]', 'tag'), null, ModUtil::url($this->name, 'user', 'main'));
        }
        
        
        if (strpos($tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $tag)
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }

        // Get the variables
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadPagesLinkingTo', $tag);

        
        $this->view->assign('tag',   $tag);
        $this->view->assign('pages', $pages);

        return $this->view->fetch('user/backlinks.tpl');
    }

    /**
     * Clone the current page and save a copy of it as a new page
     */
    public function cloneTag()
    {
        // clone is not possible as function name.

        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/clone.tpl', new Wikula_Handler_CloneTag());

        
    }
    

    
    public function settings()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('user/settings.tpl', new Wikula_Handler_Settings());
    }

    
    
}