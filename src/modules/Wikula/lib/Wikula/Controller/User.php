<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link http://code.zikula.org/wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */


class Wikula_Controller_User extends Zikula_AbstractController
{

    function __autoload($class_name) {
        require_once 'modules/Wikula/lib/Wikula/Common.php';
    }

    /**
     * Main function
     * 
     * Displays a wiki page
     *
     * @param string $args['tag'] Tag of the wiki page to show
     * @TODO Improve the authors box grouping the same users contribs
     * @TODO Do not show the Last edit in the authorsbox if it's the same creation
     * @return unknown
     */
    
    public function main($args)
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
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        
        if( array_key_exists($tag, $specialPages)) {
            $content = ModUtil::apiFunc($this->name, 'SpecialPage', 'get', $specialPages[$tag]);
            return $this->view->assign('content', $content)
                              ->assign('tag',     $tag)
                              ->fetch('user/specialPage.tpl');
        }

        //if ($modvars['logreferers']) {
        //    ModUtil::apiFunc('Wikula', 'user', 'LogReferer', array('tag' => $tag));
        //}


        // Get the page
        $page = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array(
            'tag'  => $tag,
            'time' => $time)
        );
       
        
        // Validate invalid petition
        if (!$page && !empty($time)) {
            LogUtil::registerError(__("The page you requested doesn't exists"), null, ModUtil::url('Wikula'));
        }

        // Get the latest version
        if (empty($time)) {
            $latest = $page;
        } else {
            $latest = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array('tag' => $tag));
        }

        // Check if this tag doesn't exists
        if (!$page && !$latest) {
            LogUtil::registerStatus(__('The page does not exist yet! do you want to create it?').'<br />'.__('Feel free to participate and be the first who creates content for this page!'));
            System::redirect(ModUtil::url('Wikula', 'user', 'edit', array('tag' => $tag)));
        }

        // Resetting session access and previous
        SessionUtil::delVar('wikula_access');
        SessionUtil::setVar('wikula_previous', $tag);

        
        // TODO: check if this can be migrated to an action
        // we'll get later revisions too because we want to display the history and the last editors next to the page

        // assign the modvars
        //$this->view->assign('modvars', $modvars);

        $this->view->assign('tag',      $tag);
        $this->view->assign('time',     $time);
        $this->view->assign('showpage', $page);

        return $this->view->fetch('user/show.tpl', md5($page['id'].$page['time']));
    }

    /**
     * Edit method
     */
    public function edit()
    {
        $form = FormUtil::newForm('Wikula', $this);
        return $form->execute('user/edit.tpl', new Wikula_Handler_Page());

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
        //$time = FormUtil::getPassedValue('time');

        if (empty($tag)) {
            return LogUtil::registerError(__f('Missing argument [%s]', 'tag'),
                                          null,
                                          ModUtil::url('Wikula', 'user', 'main'));
        }

        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRevisions', array(
            'tag' => $tag)
        );

        if (!$pages) {
            return LogUtil::registerError(
                __f('No %s found.', 'Rev'),
                null,
                ModUtil::url('Wikula', 'user', 'main')
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
        
        if (!SecurityUtil::checkPermission('wikula::', 'xml::recentchanges', ACCESS_OVERVIEW)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRecentlyChanged');

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
        
        if (!SecurityUtil::checkPermission('wikula::', 'xml::revisions', ACCESS_OVERVIEW)) {
            return LogUtil::registerError(__('Sorry! No authorization to access this module.'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        $tag = FormUtil::getPassedValue('tag');

        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRevisions', array('tag' => $tag));

        if (!$pages) {
            return LogUtil::registerError(__('Error during element fetching !'));
        }

        
        $this->view->force_compile = true;

        $this->view->assign('tag',   $tag);
        $this->view->assign('pages', $pages);

        return $this->view->fetch('xml/revisions.tpl');
    }

    public function RecentChangesMindMap()
    {

        
        $cr = "\n";

        $xml  = '<map version="0.7.1">'.$cr
        .'  <node text="'.__('Recent Changes').'">'.$cr
        .'    <node text="Date" position="right">'.$cr;

        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadRecentlyChanged', array());

        $users  = array();
        $curday = '';
        $max = $this->getVar( 'itemsperpage');

        $c = 0;
        foreach ($pages as $page) {
            $c++;
            if (($c <= $max) || !$max) {
                $pageuser = $page['user'];
                $pagetag  = $page['tag'];

                // day header
                list($day, $time) = explode(' ', $page['time']);
                if ($day != $curday) {
                    $dateformatted = date(__('D, d M Y'), strtotime($day));
                    if ($curday) {
                        $xml .= '      </node>'.$cr;
                    }
                    $xml .= '      <node text="'.$dateformatted.'">'.$cr;
                    $curday = $day;
                }

                $pagelink      = ModUtil::url('Wikula', 'user', 'main',    array('tag' => DataUtil::formatForDisplay($pagetag)));
                $revlink       = ModUtil::url('Wikula', 'user', 'History', array('tag' => DataUtil::formatForDisplay($pagetag)));
                $xml          .= '        <node text="'.$pagetag.'" folded="true">'.$cr;
                $timeformatted = date('H:i T', strtotime($page['time']));
                $xml          .= '          <node link="'.$pagelink.'" text="'.$this->__('Revision time: ').$timeformatted.'"/>'.$cr;
                if ($page['note']) {
                    $xml .= '          <node text="'.$pageuser.': '.DataUtil::formatForDisplay($page['note']).'"/>'.$cr;
                } else {
                    $xml .= '          <node text="'.$this->__('Author: ').$pageuser.'"/>'.$cr;
                }

                $xml .= '          <node link="'.$revlink.'" text="'.$this->__('View History').'"/>'.$cr.'</node>'.$cr;

                if (is_array($users[$pageuser])) {
                    $u_count = count($users[$pageuser]);
                    $users[$pageuser][$u_count] = $pagetag;
                } else {
                    $users[$pageuser][0] = $pageuser;
                    $users[$pageuser][1] = $pagetag;
                }
            }
        }

        $xml .= '    </node>'.$cr.'  </node>'.$cr
        .'  <node text="'.$this->__('Author').'" position="left">'.$cr;
        foreach ($users as $user) {
            $start_loop = true;
            foreach ($user as $user_page) {
                if (!$start_loop) {
                    $xml .= '    <node link="'.ModUtil::url('Wikula','user','main', array('tag' => DataUtil::formatForDisplay($user_page))).'" text="'.$user_page.'"/>'.$cr;
                } else {
                    $xml .= '    <node text="'.$user_page.'">'.$cr;
                    $start_loop = false;
                }
            }
            $xml .= '  </node>'.$cr;
        }

        $xml .= '</node>'.$cr.'</node>'.$cr.'</map>'.$cr;

        echo $xml;

        return true;
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
        
        $linkedtag = FormUtil::getPassedValue('tag');

        if (empty($linkedtag)) {
            return LogUtil::registerError(__f('Missing argument [%s]', 'tag'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        // Get the variables
        $page  = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array('tag' => $linkedtag));
        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadPagesLinkingTo', array('tag' => $linkedtag));

        
        $this->view->assign('tag',       $linkedtag);
        $this->view->assign('backpage',  $page);
        $this->view->assign('pages',     $pages);

        return $this->view->fetch('user/backlinks.tpl');
    }

    /**
     * Clone the current page and save a copy of it as a new page
     */
    public function cloneTag()
    {
        
        $tag = FormUtil::getPassedValue('tag');
        
        if (empty($tag)) {
            return LogUtil::registerError(__f('Missing argument [%s]', 'tag'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        // Permission check
        if (!SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT)) {
            return LogUtil::registerError(__('You do not have the authorization to edit this page!'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        if (!ModUtil::apiFunc('Wikula', 'user', 'PageExists', array('tag' => $tag))) {
            return LogUtil::registerError(__("The page you requested doesn't exists"), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        // Default values
        $to   = $tag;
        $note = __f('Cloned from %s', $tag);
        $edit = false;

        $submit = FormUtil::getPassedValue('submit');

        if ($submit == $this->__('Cancel')) {
            $this->redirect(ModUtil::url('Wikula', 'user', 'main', array('tag' => $tag)));

        } elseif ($submit == $this->__('Submit')) {
            $to   = FormUtil::getPassedValue('to');
            $note = FormUtil::getPassedValue('note');
            $edit = (bool)FormUtil::getPassedValue('edit');

            // Validate the choosen pagename
            $validationerror = false;
            if (!ModUtil::apiFunc('Wikula', 'user', 'isValidPagename', array('tag' => $to))) {
                LogUtil::registerError(__('That page name is not valid'));
                $validationerror = true;
            }

            // check if the page already exists
            if (!$validationerror && ModUtil::apiFunc('Wikula', 'user', 'PageExists', array('tag' => $to))) {
                return LogUtil::registerError(__('This page does already exist'), null, ModUtil::url('Wikula', 'user', 'main'));
            }

            // check if has access to create it
            if (!$validationerror && !SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT)) {
                return LogUtil::registerError(__('You do not have the authorization to edit this page!'), null, ModUtil::url('Wikula', 'user', 'main'));
            }

            // if valid request
            if (!$validationerror) {
                // proceed to page cloning
                $page = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array('tag' => $tag));
                $newpage = array(
                    'tag'  => $to,
                    'body' => $page['body'],
                    'note' => $note
                );

                if (ModUtil::apiFunc('Wikula', 'user', 'SavePage', $newpage)) {
                    // redirect
                    if ($edit) {
                        $this->redirect(ModUtil::url('Wikula', 'user', 'edit', array('tag' => $to)));
                    } else {
                        LogUtil::registerStatus(__('Clone created successfully'));
                        $this->redirect(ModUtil::url('Wikula', 'user', 'main', array('tag' => $to)));
                    }
                }
            }
        }

        

        $this->view->assign('tag',  $tag);
        $this->view->assign('to',   $to);
        $this->view->assign('note', $note);
        $this->view->assign('edit', $edit);

        return $this->view->fetch('user/clone.tpl');
    }

    /**
     * Display the Referrers to a page
     */
    public function Referrers()
    {
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ)
        );
        
        if (!UserUtil::isLoggedIn()) {
            return LogUtil::registerError(__('You must be logged in to be able to view referrers - Anti botspam'), null, ModUtil::url('Wikula', 'user', 'main'));
        }

        $tag    = FormUtil::getPassedValue('tag');
        $global = FormUtil::getPassedValue('global');
        $sites  = FormUtil::getPassedValue('sites');
        $q      = FormUtil::getPassedValue('q');
        $qo     = FormUtil::getPassedValue('qo');
        $h      = FormUtil::getPassedValue('h');
        $ho     = FormUtil::getPassedValue('ho');
        $days   = FormUtil::getPassedValue('days');
        $submit = FormUtil::getPassedValue('submit');

        if (empty($sites) || !is_numeric($sites)) {
            $sites = 0;
        }
        if (empty($global) || !is_numeric($global)) {
            $global = 0;
        }

        if (empty($h) || !is_numeric($h)) {
            $h = 1;
        }
        if (!empty($ho) || !is_numeric($ho)) {
            $ho = 1;
        }
        if (!empty($days) || !is_numeric($days)) {
            $days = 1;
        }

        if (!empty($submit)) {
            $submit = true;
        }

        $referrers = ModUtil::apiFunc('Wikula', 'user', 'LoadReferrers', array(
            'tag'    => $tag,
            'global' => $global,
            'sites'  => $sites,
            'q'      => $q,
            'qo'     => $qo,
            'h'      => $h,
            'ho'     => $ho,
            'days'   => $days,
            'submit' => $submit)
        );

        if (!$referrers) {
            //return 'No Referrers';
        }

        // load the wiki page for output purposes
        $page = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array('tag' => $tag));

        

        $this->view->assign('tag',       $tag);
        $this->view->assign('page',      $page);
        $this->view->assign('q',         $q);
        $this->view->assign('h',         $h);
        $this->view->assign('sites',     $sites);
        $this->view->assign('global',    $global);
        $this->view->assign('referrers', $referrers);
        $this->view->assign('total',     count($referrers));

        return $this->view->fetch('user/referrers.tpl');
    }
    
    public function grabcode()
    {
        $code = FormUtil::getPassedValue('code');
        return $this->nl2br(htmlentities(br2nl(urldecode($code))));

    }

    public function br2nl($string){
        $return=preg_replace('`<br[[:space:]]*/?'.'[[:space:]]*>`',chr(13),$string);
        return $return;
    }
    
    public function settings()
    {
        $form = FormUtil::newForm('Wikula', $this);
        return $form->execute('user/settings.tpl', new Wikula_Handler_Settings());
    }

    
    
}