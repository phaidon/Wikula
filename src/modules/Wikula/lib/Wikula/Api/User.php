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


require_once 'modules/Wikula/lib/Wikula/Common.php';

class Wikula_Api_User extends Zikula_AbstractApi
{


    /**
    * Validate a PageName
    */
    public function isValidPagename($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        return preg_match(VALID_PAGENAME_PATTERN, $args['tag']);
    }

    /**
    * Check access to edit a page
    */
    public function isAllowedToEdit($args)
    {
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        $access = SecurityUtil::checkPermission('Wikula::', 'page::'.$args['tag'], ACCESS_COMMENT) ||
                $args['tag'] == __('SandBox');

        return $access;
    }

    /**
    * Load a wiki page with the given tag
    *
    * @param string $args['tag'] tag of the wiki page to get
    * @param string $args['time'] (optional) update time, latest page if not defined
    * @return array wiki page data
    */
    public function LoadPage($args)
    {
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$args['tag'], ACCESS_READ)) {
            return LogUtil::registerError(__('You do not have the authorization to read this page!'), 403);
        }


        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');

        if (isset($args['time']) && !empty($args['time'])) {
            $q->where('time = ?', array($args['time']));

        } else {
            $q->where('latest = ?', array('Y'));
        }

        $q->addWhere('tag = ?', array($args['tag']));

        // return the page or false if failed
        $result = $q->execute();
        $result = $result->toArray();
        if(count($result) == 0) {
            return false;
        }
        return $result[0];

    }


    /**
    * Check if a page exist
    *
    * @param $args['tag'] tag of the page to check
    * @return id of the page, false if doesn't exists
    */
    public function PageExists($args)
    {
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if(array_key_exists($args['tag'], $specialPages)) {
            return true;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $q->where('latest = ?', array('y'));
        $q->addWhere('tag = ?', array($args['tag']));
        $result = $q->execute();
        if(!$result) {
            return false;
        }
        $result->toArray();
        return $result[0]['id'];

    }

    public function LoadPagebyId($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($id) || !is_numeric($id)) {
            return LogUtil::registerArgsError();
        }

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', "page:$id:", ACCESS_READ),
            LogUtil::getErrorMsgPermission()
        );

        $page = Doctrine_Core::getTable('Wikula_Model_Pages')->find($id);


        if ($page === false) {
            return LogUtil::registerError(__('Error! Getting the this page by id failed.'));
        }

        return $page;

    }

    /**
    * Get the Revisions of a Wiki Page
    *
    * @param string $args['tag']
    * @param string $args['startnum'] (optional) start offset
    * @param string $args['numitems'] (optional) number of items to fetch
    * @param string $args['orderby'] (optional) sort by fieldname
    * @param string $args['orderdir'] (optional) sort direction (ASC or DESC)
    * @param string $args['loadbody'] (optional) flag to include the body in the results
    * @param string $args['getoldest'] (optional) flag to fetch the oldes revision only
    * @return array of revisions
    */
    public function LoadRevisions($args)
    {
        extract($args);
        unset($args);
        
        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ),
            LogUtil::getErrorMsgPermission()
        );

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');


        // Defaults
        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        } 
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }

        $q->where('tag = ?', array($tag));

        // build the order by
        if (!isset($orderby)) {
            $orderBy = 'time';
        } else {
            $orderBy = $orderby;
        }
        if (!isset($orderdir) || !in_array(strtoupper($orderdir), array('ASC', 'DESC'))) {
            $orderBy .= ' DESC';
        } else {
            $orderBy .= ' '.strtoupper($orderdir);
        }
        $q->orderBy($orderBy);

        // define the permission filter to apply
        // TODO permissions 
        /*$permFilter = array(array('realm'           => 0,
                                'component_left'  => 'wikula',
                                'instance_left'   => 'page',
                                'instance_right'  => 'tag',
                                'level'           => ACCESS_READ));*/



        // exclude fields if needed
        /*if (!isset($args['loadbody']) || (isset($args['loadbody']) && !$args['loadbody'])) {
            unset($columnArray['body']);
        }*/

        // check if we want to get the latest only
        if (isset($getoldest) && $getoldest) {
            $orderBy = $time.' DESC';
            $numitems = 1;
        }

        // getn the revisions from the db
        $q->offset($startnum-1);
        if($numitems-1 > 0) {
            $q->limit($numitems-1);
        }
        $result = $q->execute();
        $result = $result->toArray();

        // return the results
        if (isset($result[0]) && isset($getoldest) && $getoldest) {
            return $result[0];
        }
        return $result;
    }

    public function LoadPagesLinkingTo($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ),
            LogUtil::getErrorMsgPermission()
        );

        $q = Doctrine_Query::create()->from('Wikula_Model_Links t');
        $q->where('to_tag = ?', array($tag));
        $q->orderBy('from_tag');
        $links = $q->execute();
        $links = $links->toKeyValueArray('from_tag', 'from_tag');
        if( array_key_exists($tag, $links) ) {
            unset($links['$tag']);
        }

        if ($links === false) {
            return LogUtil::registerError(__('Error! Getting the links for this page failed.'));
        }

        return $links;

    }

    

    public function CountBackLinks($args = array())
    {
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', 'page::'.$args['tag'], ACCESS_READ),
            LogUtil::getErrorMsgPermission()
        );


        $q = Doctrine_Query::create()->from('Wikula_Model_Links t');
        $q->where('to_tag = ?', array($args['tag']));
        $links = $q->execute();
        $links = $links->toKeyValueArray('from_tag', 'from_tag');



        if ($links === false) {
            return LogUtil::registerError(__('Error! Count the links for this page failed.'));
        }

        return count($links);

    }

    /**
    * Load the recently changed pages
    *
    * @param unknown_type $args
    * @return unknown
    * @todo only loads the latest = Y... param and RecentChanges show show all eh?
    */
    public function LoadRecentlyChanged($args)
    {
        
        extract($args);
        unset($args);




        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');

        $q->where('latest = ?', array('Y'));
        $q->orderBy('time DESC');

        if (isset($startnum) and is_numeric($startnum)) {
            $q->offset($startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems)) {
            $q->limit($numitems);
        }
        


        // return the page or false if failed
        $revisions = $q->execute();
        $revisions = $revisions->toArray();

        
        if ($revisions === false) {
            return LogUtil::registerError(__('Error! Getting revisions failed.'));
        }

        
        if(!empty($formated) and $formated) {
            $curday = '';
            $pagelist = array();
            foreach ($revisions as $page)
            {
                list($day, $time) = explode(' ', $page['time']);
                if ($day != $curday) {
                    $dateformatted = date(__('D, d M Y'), strtotime($day));
                    $curday = $day;
                }

                if ($page['user'] == System::getVar('anonymous')) {
                    $page['user'] .= ' ('.$this->__('anonymous user').')'; // anonymous user
                }

                $pagelist[$dateformatted][] = $page;
            }
            return $pagelist;
        }


        return $revisions;

    }

    //function LoadAllPages($args)
    public function LoadAllPages($args)
    {
        
        extract($args);
        unset($args);

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $q->offset($startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $q->limit($numitems);
        }
        $q->where('latest = ?', array('Y'));
        $q->orderBy('tag');
        $pages = $q->execute();
        $pages = $pages->toArray();

        if ($pages === false) {
            return LogUtil::registerError(__('Error! Getting all pages failed.'));
        }

        return $pages;

    }

    //function LoadAllPagesEditedByUser($args)
    public function LoadAllPagesEditedByUser($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($uname)) {
            return false;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        if (isset($startnum) and is_numeric($startnum) and $startnum > 1) {
            $q->offset($startnum-1);
        }
        if (isset($numitems) and is_numeric($numitems) and $numitems > 0) {
            $q->limit($numitems);
        }
        $q->where('user = ?', array($uname));


        if (!isset($all) || (isset($all) && !$all)) {
            $q->addWhere('latest = ?', array('Y'));
        }
        if (isset($alpha) && $alpha == 1) {
            $q->orderBy('tag ASC, time DESC');
        } else {
            $q->orderBy('time DESC, tag ASC');
        }

        $pages = $q->execute();
        $pages = $pages->toArray();
        

        if ($pages === false) {
            return LogUtil::registerError(__('Error! Getting all pages by user failed.'));
        }

        return $pages;

    }

    /**
    * Count the total number of active pages in the Wiki
    *
    * @return integer total wiki pages
    */
    public function CountAllPages()
    {     
        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $q->where('latest = ?', array('Y'));
        $result = $q->execute();
        return $result->count();
    }

    /**
    * Load all the pages owned by a specific User
    *
    * @param string  $args['uname'] username to search
    * @param integer $args['startnum'] (optional) start point
    * @param integer $args['numitems'] (optional) number of items to fetch
    * @param integer $args['justcount'] (optional) flag to perform just a page count and not return the wiki pages
    */
    public function LoadAllPagesOwnedByUser($args)
    {
        extract($args);
        unset($args);

        if (!isset($uname) || empty($uname)) {
            return LogUtil::registerArgsError();
        }

        // Defaults
        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }
        if (!isset($justcount) || !is_bool($justcount)) {
            $justcount = false;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');

        $q->orderBy('time DESC');


        // build the order by
        if (!isset($orderby)) {
            $orderby = 'tag';
        }
        if (!isset($orderdir) || !in_array(strtoupper($orderdir), array('ASC', 'DESC'))) {
            $orderby .= ' ASC';
        } else {
            $orderby .= ' '.strtoupper($orderdir);
        }

        // define the permission filter to apply
        // even when doesn't make sense in the user's owned pages
        /*$permFilter = array(array('realm'           => 0,
                                'component_left'  => 'wikula',
                                'instance_left'   => 'page',
                                'instance_right'  => 'tag',
                                'level'           => ACCESS_READ));*/

        
        if ($justcount) {
            $result = $q->execute();
            $count = $q->count();
        } else {
            $q->where('latest = ? and owner = ?', array('Y', $uname) );
            $result = $q->execute();
            $pages = $result->toArray();
        }

        // build the result array
        $result = array();

        if ($justcount) {
            $result['count'] = $count;
        } else {
            $result['pages'] = $pages;
            $result['count'] = count($pages);
        }
        $result['total'] =  ModUtil::apiFunc($this->name, 'user', 'CountAllPages');

        return $result;
    }

    public function FullTextSearch($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($phrase)) {
            return LogUtil::registerArgsError();
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');




        $phrase = DataUtil::formatForStore($phrase);

        $boolean = '';
       
            $boolean = ' IN BOOLEAN MODE';
  

        $q->where('latest = ?', array('Y'));
        $q->orderBy('time DESC');

 
        $q->addWhere("tag LIKE ? or body LIKE ?", array('%'.$phrase.'%','%'.$phrase.'%'));  //IN BOOLEAN MODE)");

        $result = $q->execute();

        $result = $result->toArray();

        $pages = array();



        foreach ($result as $value) {

           extract($value);

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ))  {
                $pages[] = array(
                    'page_id'         => $id,
                    'page_tag'        => $tag,
                    'page_time'       => $time,
                    'page_body'       => $body,
                    'page_owner'      => $owner,
                    'page_user'       => $user,
                    'page_handler'    => $handler
                );
            }

        }
        //unset($pages[0]);
        return $pages;

    }

    public function LoadWantedPages($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }

        $q = Doctrine_Query::create()
            ->from('Wikula_Model_Pages t')
            ->leftJoin('t.Wikula_Model_Links s');

        $q->where('tag IS NULL');
        $q->groupBy('s.to_tag, s.from_tag');
        $q->orderBy('s.from_tag ASC, s.to_tag ASC');
        $result = $q->execute();
        $result = $result->toArray();
        return;


        $sql = 'SELECT DISTINCT '.$linkcol['from_tag'].', '
                                .$linkcol['to_tag'].', '
                                .'COUNT('.$linkcol['to_tag'].') as count '


                .' ORDER BY count DESC, ';

        //$result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);
        $result =& $dbconn->Execute($sql);

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('Getting matches for this page failed!').' - '.$dbconn->ErrorMsg());
        }

        if ($result->EOF) {
            return LogUtil::registerError(__('No items found.'));
        }
        $pages = array();

        for (; !$result->EOF; $result->MoveNext()) {

            list($from_tag, $to_tag, $count) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$to_tag, ACCESS_READ))  {
            if ($from_tag != 'WantedPages') {
                $pages[] = array('from_tag' => $from_tag,
                                'to_tag'   => $to_tag,
                                'count'    => $count);
            }
            }

        }

        $result->Close();

        return $pages;

    }
    
    
    
    public function LoadOrphanedPages($args)
    {

        
        extract($args);
        unset($args);

        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }

        $dbconn  =& pnDBGetConn(true);
        $table =& DBUtil::getTables();

        $pagetbl = &$table['wikula_pages'];
        $pagecol = &$table['wikula_pages_column'];
        $linktbl = &$table['wikula_links'];
        $linkcol = &$table['wikula_links_column'];

        $sql = 'SELECT DISTINCT '.$pagecol['tag']
                .' FROM '.$pagetbl
                .' LEFT JOIN '.$linktbl.' ON '.$pagecol['tag'].' = '.$linkcol['to_tag']
                .' WHERE '.$linkcol['to_tag'].' IS NULL '
                .' ORDER BY '.$pagecol['tag'];

        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('Getting matches for this page failed!').' - '.$dbconn->ErrorMsg());
        }

        $pages = array();

        for (; !$result->EOF; $result->MoveNext()) {

            list($tag) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ))  {
                $pages[] = array('tag' => $tag);
            }

        }

        $result->Close();

        return $pages;

    }

    public function IsOrphanedPage($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }

        $dbconn  =& pnDBGetConn(true);
        $table =& DBUtil::getTables();

        $pagetbl = &$table['wikula_pages'];
        $pagecol = &$table['wikula_pages_column'];
        $linktbl = &$table['wikula_links'];
        $linkcol = &$table['wikula_links_colum'];

        $sql = 'SELECT DISTINCT '.$pagecol['tag']
            .' FROM '.$pagetbl
            .' LEFT JOIN '.$linktbl.' ON '.$pagecol['tag'].' = '.$linkcol['to_tag']
            .' WHERE '.$linkcol['to_tag'].' IS NULL '
            .' AND '.$pagecol['comment_on'].' = "" '
            .' AND '.$pagecol['tag'].' = "'.DataUtil::formatForStore($tag).'" '
            .' ORDER BY '.$pagecol['tag'];

        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1);

        if ($dbconn->ErrorNo() != 0) {
            return LogUtil::registerError(__('Getting matches for this page failed!').' - '.$dbconn->ErrorMsg());
        }

        $pages = array();

        for (; !$result->EOF; $result->MoveNext()) {

            list($tag) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_READ))  {
                $pages[] = array('tag' => $tag);
            }

        }

        $result->Close();

        return $pages;

    }

    public function DeleteOrphanedPage($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_DELETE))  {
            return LogUtil::registerError(__('You do not have the right to delete this page!'), 403);
        }

        $table =& DBUtil::getTables();

        $pagecol   =& $table['wikula_pages_column'];
        $pagewhere = 'WHERE '.$pagecol['tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_pages', $pagewhere)) {
            return LogUtil::registerError(__('Error! Deleting this page failed.'));
        }

        $linkcol   =& $table['wikula_links_colum'];
        $linkwhere = 'WHERE '.$linkcol['from_tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_links', $linkwhere)) {
            return LogUtil::registerError(__('Error! Deleting links for this page failed.'));
        }

        $aclscol   =& $table['wikula_acls_column'];
        $aclswhere = 'WHERE '.$aclscol['page_tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_acls', $aclswhere)) {
            return LogUtil::registerError(__('Error! Deleting ACLS for this page failed.'));
        }

        $refscol   =& $table['wikula_referrers_colum'];
        $refswhere = 'WHERE '.$refscol['page_tag'].' = "'.DataUtil::formatForStore($tag).'"';
        if (!DBUtil::deleteWhere('wikula_referrers', $refswhere)) {
            return LogUtil::registerError(__('Error! Deleting referers for this page failed.'));
        }

        return LogUtil::registerStatus(__('Done! Page deleted with success.'));
    }

    public function SavePage($args)
    {
        
        extract($args);
        unset($args);

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', 'page::'.$tag, ACCESS_COMMENT),
            LogUtil::getErrorMsgPermission()
        );

        if (!isset($tag)) {
            return LogUtil::registerArgsError();
        }

        $user = UserUtil::getVar('uname');

        // Check if page is new
        $oldpage =  ModUtil::apiFunc($this->name, 'user', 'LoadPage', array('tag' => $tag));

        // only save if new body differs from old body
        if ($oldpage && $oldpage['body'] == $body) {
            return LogUtil::registerError(__('The content of the page to save is the same of the current revision. New page not saved.'));
        }

        if (!$oldpage) {
            $owner = $user;
        } else {
            $owner = $oldpage['owner'];
        }

        // set all other revisions to old
        $q = Doctrine_Query::create()
            ->update('Wikula_Model_Pages t')
            ->set('latest', '?', 'N')
            ->where('latest = ? and tag = ?', array('Y', $tag));
        $q->execute();

        if ($res === false) {
            return LogUtil::registerError(__('Setting last revision failed!'));
        }

        // add new revision
        $newrev = array(
            'tag'    => DataUtil::formatForStore($tag),
            'body'   => $body,
            'note'   => DataUtil::formatForStore($note),
            'time'   => DateUtil::getDatetime(),
            'owner'  => $owner,
            'user'   => $user,
            'latest' => 'Y'
        );

        $res = new Wikula_Model_Pages();
        $res->merge($newrev);
        $res->save();

        if ($res === false) {
            return LogUtil::registerError(__('Saving revision failed!'));
        }
    /*
        if ((isset($args['tracking']) && $args['tracking'] ) || SessionUtil::getVar('tracking')) {
            SessionUtil::setVar('wikula_previous', $tag);
        }
    */

         ModUtil::apiFunc($this->name, 'user', 'WriteLinkTable', array('tag' => $tag));

        // TODO: Wikka Ping feature here

        if (!$oldpage) {
            LogUtil::registerStatus(__('New page created!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewPage', $tag);
        } else {
            LogUtil::registerStatus(__('New revision saved!'));
             ModUtil::apiFunc($this->name, 'user', 'NotificateNewRevsion', $tag);
        }

        return true;
    }


    public function NotificateNewPage($tag)
    {

        if (empty($tag) or !$this->getVar('subscription') ) {
            return false;
        }

        $view = Zikula_View::getInstance($this->name, false);
        $view->assign('baseUrl', System::getBaseUrl());
        $view->assign('tag', $tag);
        $view->assign('uname', UserUtil::getVar('uname') );
        $message = $view->fetch('notification/newPage.tpl');
        $subject = $this->__('Wiki update');


        $uids = Doctrine_Core::getTable('Wikula_Model_Subscriptions')->findAll();
        $uids = $uids->toKeyValueArray('uid', 'uid');
        foreach($uids as $uid) {
            $toaddress = UserUtil::getVar('email', $uid);
            ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                'toaddress' => $toaddress,
                'subject'   => $subject,
                'body'      => $message,
                'html'      => true
            ));
        }
        return true;

        
    }


    public function NotificateNewRevsion($tag)
    {

        if (empty($tag) or !$this->getVar('subscription') ) {
            return false;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $q->where('latest = ? and tag = ? and user = ?', array('N', $tag, UserUtil::getVar('uname')));
        $q->orderBy('time desc');
        $q->limit(1);
        $lastEdit = $q->execute();
        $lastEdit = $lastEdit->toArray();

        $notification = false;
        if(count($lastEdit) == 0 ) {
            $notification = true;
        } else {
            $lastEdit = $lastEdit[0]['time'];
            $timeDiff = DateUtil::getDatetimeDiff($lastEdit, DateUtil::getDatetime());
            $minutesSinceLastEdit = 0;
            if(array_key_exists('m', $timeDiff) ) {
                $minutesSinceLastEdit = $timeDiff['m'];
            }
            if(array_key_exists('h', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['h'] * 60;

            }
            if(array_key_exists('d', $timeDiff) ) {
                $minutesSinceLastEdit = $minutesSinceLastEdit + $timeDiff['d'] * 60 * 24;
            }
            if($minutesSinceLastEdit > 20) {
                $notification = true;
            }
        }

        

        if($notification) {
            $view = Zikula_View::getInstance($this->name, false);
            $view->assign('baseUrl', System::getBaseUrl());
            $view->assign('tag', $tag);
            $view->assign('uname', UserUtil::getVar('uname') );
            $message = $view->fetch('notification/newRevision.tpl');
            $subject = $this->__('Wiki update');

            $uids = Doctrine_Core::getTable('Wikula_Model_Subscriptions')->findAll();
            $uids = $uids->toKeyValueArray('uid', 'uid');
            foreach($uids as $uid) {
                $toaddress = UserUtil::getVar('email', $uid);
                ModUtil::apiFunc('Mailer', 'user', 'sendmessage', array(
                    'toaddress' => $toaddress,
                    'subject'   => $subject,
                    'body'      => $message,
                    'html'      => true
                ));
            }
        }
    }


    // Disabled function for now...
    public function PurgePages($args)
    {
        

        extract($args);
        unset($args);

        $days = $this->getVar('pages_purge_time');

        if ($days) {
            // Get all pages with an interval greater than pages_purge_time
            // $pages = All page blabla
            // TODO
        }

        return true;

    }

    // INTERWIKI STUFF
    public function ReadInterWikiConfig()
    {
        
        static $interwiki = array();

        if (!empty($interwiki)) {
            return $interwiki;
        }

        if (file_exists('modules/Wikula/pnincludes/interwiki.conf') && $lines = file('modules/Wikula/pnincludes/interwiki.conf')) {
            foreach($lines as $line) {
                if ($line = trim($line)) {
                    list($wikiName, $wikiUrl) = explode(' ', trim($line));

                    $interwiki[strtoupper($wikiName)] = $wikiUrl;
                }
            }
        } else {
            $interwiki['WIKULA'] = 'http://code.zikula.org/wikula/wiki/';
            $interwiki['ZIKULA'] = 'http://community.zikula.org/index.php?module=Wikula&tag=';
        }

        return $interwiki;

    }

    public function AddInterWiki($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($wikiname) || !isset($wikiurl)) {
            return LogUtil::registerError(__('Adding InterWiki failed due to missing arguments!'));
        }

        $interwiki = unserialize($this->getVar('interwiki'));

        if (!is_array($interwiki)) {
            $interwiki = array();
            $interwiki[strtoupper($wikiname)] = $wikiurl;

        } elseif (!isset($interwiki[strtoupper($wikiname)])) {
            $interwiki[strtoupper($wikiname)] = $wikiurl;
        }

        if (!$this->setVar('interwiki', serialize($interwiki))) {
            return LogUtil::registerError(__('Adding interwiki failed!'));
        }

        return LogUtil::registerStatus(__('Interwiki added with success!'));
    }

    public function GetInterWikiUrl($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($name) || !isset($tag)) {
            return LogUtil::registerError(__('Error! Invalid arguments.'));
        }

        $interwiki =  ModUtil::apiFunc($this->name, 'user', 'ReadInterWikiConfig');

        if (!$interwiki || !is_array($interwiki)) {
            return 'http://'.$tag;
        }

        if (isset($interwiki[strtoupper($name)])) {
            return $interwiki[strtoupper($name)].$tag;
        }

        return 'http://'.$tag; //avoid xss by putting http:// in front of JavaScript:()

    }

    /**
    * Build a wiki link code
    * @todo needs rework
    * @todo can we index all the Links and check if exists in the DB once?
    */
    public function Link($args)
    {
        
        if (!isset($args['tag'])) {
            return false;
        }

        if (!isset($args['text']) || empty($args['text'])) {
            // No text, we fill the page with at least its tag
            $args['text'] = $args['tag'];
        }
        if (!isset($args['title'])) {
            // No text, we fill the page with at least its tag
            $args['title'] = $args['tag'];
        }

        // is this an interwiki link?
        if (preg_match('/^([A-Z][A-Z,a-z]+)[:]([A-Z,a-z,0-9]*)$/s', $args['tag'], $matches)) {

            $link =  ModUtil::apiFunc($this->name, 'user', 'GetInterWikiUrl',
                                array('name' => $matches[1],
                                    'tag'  => isset($matches[2]) ? $matches[2] : ''));

            $textlink = (isset($matches[2]) && !empty($matches[2])) ? $matches[2] : $matches[1];

            return '<a class="ext" href="'.$link.'" title="'.$matches[1].' - '.$matches[2].'">'.$textlink.'</a><span class="exttail">&#8734;</span>';

        } else if (preg_match('/[^[:alnum:]]/', $args['tag'])) {

            // is this a full link? i.e., does it contain non alpha-numeric characters?
            // Note : [:alnum:] is equivalent [0-9A-Za-z]
            //        [^[:alnum:]] means : some caracters other than [0-9A-Za-z]
            // For example : "www.address.com", "mailto:address@domain.com", "http://www.address.com"

            // check for email addresses
            if (preg_match('/^.+\@.+$/', $args['tag'])) {
                // Building spam safe email link and text
                if ($args['text'] == $args['tag']) {
                    $args['text'] = htmlspecialchars(str_replace(array('@', '.'), array(' [at] ', ' [dot] '), $args['text']));
                }
                $mailto = '&#109;&#97;&#105;&#108;&#116;&#111;&#58;';
                $address = htmlspecialchars($args['tag']);
                $address_encode = '';
                for ($x=0; $x < strlen($address); $x++) {
                    if (preg_match('!\w!',$address[$x])) {
                        $address_encode .= '%' . bin2hex($address[$x]);
                    } else {
                        $address_encode .= $address[$x];
                    }
                }
                $args['tag'] = $mailto . $address_encode;

            } else if (!preg_match('/:\/\//', $args['tag'])) {
                // check for protocol-less URLs
                $args['tag'] = 'http://'.$args['tag'];  // Very important for xss (avoid javascript:() hacking)
            }

            if ($args['text'] != $args['tag'] && preg_match('/.(gif|jpeg|png|jpg)$/i', $args['tag'])) {
                return '<img src="'.DataUtil::formatForDisplay($args['tag']).'" alt="'.DataUtil::formatForDisplay($args['text']).'" />';
            }

        } else {
            // it's a Wiki link!
            $pageid =  ModUtil::apiFunc($this->name, 'user', 'PageExists',
                                array('tag' => $args['tag']));

            $linktable = SessionUtil::getVar('linktable');
            if (is_array(unserialize($linktable))) {
                $linktable = unserialize($linktable);
            }
            $linktable[] = $args['tag']; //$args['page']['tag'];
            SessionUtil::setVar('linktable', serialize($linktable));

            if (!empty($pageid)) {
                //$pnurl = urlencode( ModUtil::url('wikula', 'user', 'main', array('tag' => $args['tag'])));
                //$text = DataUtil::formatForDisplay($args['text']);
                return '<a href="'. ModUtil::url('wikula', 'user', 'main', array('tag' => DataUtil::formatForDisplay(urlencode($args['tag'])))).'" title="'.$args['text'].'">'.$args['text'].'</a>';
            } else {
                return '<span class="missingpage">'.$args['text'].'</span><a href="'. ModUtil::url('wikula', 'user', 'edit', array('tag' => urlencode($args['tag']))).'" title="'.DataUtil::formatForDisplay($args['tag']).'">?</a>';
            }
        }

        // Non Wiki external link ?
        $external_link_tail = '<span class="exttail">&#8734;</span>';
        return !empty($args['tag']) ? '<a title="'.$args['text'].'" href="'.$args['tag'].'">'.$args['text'].'</a>'.$external_link_tail : $args['text']; //// ?????
    }

    public function TrackLinkTo($tag)
    {
        $linktable = SessionUtil::getVar('linktable');

        if (!$linktable || !is_array($linktable)) {
            $linktable = array();
        }

        $count = count($linktable);
        $linktable[$count] = $tag;

        SessionUtil::setVar('linktable', $linktable);
    }
    
    

    public function GetLinkTable()
    {
        return unserialize(SessionUtil::getVar('linktable'));
    }

    public function ClearLinkTable()
    {
        return SessionUtil::delVar('linktable');
    }

    public function StartLinkTracking()
    {
        SessionUtil::setVar('linktracking', 1);
    }

    public function StopLinkTracking()
    {
        SessionUtil::setVar('linktracking', 0);
    }

    public function WriteLinkTable($args)
    {


        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        if (empty($args['tag'])) {
            return true;
        }

        // delete old link table
        $links = Doctrine_Core::getTable('Wikula_Model_Links')->findBy('from_tag', $args['tag']);
        $links->delete();

        $linktable =  ModUtil::apiFunc($this->name, 'user', 'GetLinkTable');

        if (is_array($linktable)) {
            $from_tag = DataUtil::formatForStore($args['tag']);
            $written  = array();

            foreach ($linktable as $to_tag) {
                $lower_to_tag = strtolower($to_tag);

                if (!isset($written[$lower_to_tag])) {
                    $record = array(
                        'from_tag' => $from_tag,
                        'to_tag'   => DataUtil::formatForStore($to_tag)
                    );

                    $link = new Wikula_Model_Links();
                    $link->merge($record);
                    $link->save();

                    /*if ($record === false) {
                        return LogUtil::registerError(__('Inserting link failed!'));
                    }*/

                    $written[$lower_to_tag] = 1;
                }
            }
        }

        return true;
    }

    /**
    * Log Referrers
    *
    * Store external referrer into table wikka_referrers. The referrer's host is
    * checked against the blacklist table and it will be ignored
    * if it's present at this table.
    *
    * @param string $args['tag']
    * @paran string $args['referrer']
    */
    public function LogReferer($args)
    {
        
        if (!isset($args['tag']) || empty($args['tag'])) {
            return LogUtil::registerError(__('Error! Invalid arguments.'));
        }

        if (empty($args['referrer'])) {
            $args['referrer'] = trim(pnServerGetVar('HTTP_REFERER', ''));
        }

        $tag      = $args['tag'];
        $referrer = $args['referrer'];
        unset($args);

        if ($referrer && !preg_match('/^'.preg_quote(pnGetBaseUrl(), '/').'/', $referrer)) {

            $writeref = true;

            if (pnModAvailable('Referers')) {
                $refex = System::getVar('httprefexcluded');
                if (!empty($refex)) {
                    $refexclusion = explode(' ', $refex);
                    $count = count($refexclusion);
                    $eregicondition = '((';
                    for ($i = 0; $i < $count; $i++) {
                        if ($i != $count-1) {
                            $eregicondition .= $refexclusion[$i] . ')|(';
                        } else {
                            $eregicondition .= $refexclusion[$i] . '))';
                        }
                    }

                    if (eregi($eregicondition, $referrer)) {
                        $writeref = false;
                    }
                }
            }

            if ($writeref == true) {

                $record = array(
                    'page_tag' => DataUtil::formatForStore($tag),
                    'referrer' => DataUtil::formatForStore($referrer),
                    'time'     => DateUtil::getDatetime()
                );

                $res = DBUtil::insertObject($record, 'wikula_referrers');

                if ($res === false) {
                    return LogUtil::registerError(__('Inserting Referer failed!'));
                }
            }

        }

        return true;

    }

    //function LoadReferrers($args)
    public function LoadReferrers($args)
    {
        
        extract($args);
        unset($args);

        $query = Doctrine_Query::create()->from('Wikula_Model_Referrers t');


        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }


        if ($global != 1) {
            $query->where('page_tag = ?', array($tag));
        }

  

        /*if ($submit) {
            if (!empty($q)) {
                if ($qo == 1) {
                    $where .= 'MATCH('.$col['referrer'].') ';
                } else {
                    $where .= 'NOT MATCH('.$col['referrer'].') ';
                }
                $where .= 'AGAINST("'.DataUtil::formatForStore($q).'"'.$boolean.') ';  //IN BOOLEAN MODE'
            }

            if ($h > 0) {
                $having = ' HAVING ';

                $having .= '  num ';
                if ($ho == 0) {
                    $having .= ' <= ';
                } else {
                    $having .= ' >= ';
                }
                $having .= '"'.DataUtil::formatForStore($h).'"';
            }
        }*/
        $protocol_host = 'SUBSTRING_INDEX('.$col['referrer'].',"/",3)';
        $start_host    = 'LOCATE("//",'.$col['referrer'].')+2';
        //SUBSTRING('.$protocol_host.' FROM ('.$start_host.')),
        $query->select('page_tag, referrer');

                /*.' COUNT(SUBSTRING('.$protocol_host.' FROM ('.$start_host.'))) AS num, '
                .$col['time']
                .' FROM '.$tbl
                .$where
                .' GROUP BY '.$col['referrer']
                .$having
                .' ORDER BY num DESC, '.$col['time'].' DESC';*/


        $result = $query->execute();
        $result = $result->toArray();

        if ($result === false) {
            return LogUtil::registerError(__('Error! Get page failed.'));
        }

        $items = array();

        /*for (; !$result->EOF; $result->MoveNext()) {

            list($page_tag, $referrer, $num, $time) = $result->fields;

            if (SecurityUtil::checkPermission('Wikula::', 'page::'.$page_tag, ACCESS_READ)) {
                $items[] = array('page_tag' => $page_tag,
                                'referrer' => $referrer,
                                'num'      => $num,
                                'time'     => $time);
            }

        }*/

        if ($sites == 1) {
            $referrer_sites = array();
            for ($a = 0; $a < count($items); $a++) {
                $temp_parse_url = parse_url($items[$a]['referrer']);
                $temp_parse_url = ($temp_parse_url['host'] != '') ? strtolower(preg_replace('/^www\./Ui', '', $temp_parse_url['host'])) : 'unknown';

                if (isset($referrer_sites[$temp_parse_url])) {
                    $referrer_sites[$temp_parse_url] += $items[$a]['num'];
                } else {
                    $referrer_sites[$temp_parse_url] = $items[$a]['num'];
                }
            }

            array_multisort($referrer_sites, SORT_DESC, SORT_NUMERIC);
            reset($referrer_sites);
            $items = array();
            while(list($referrer, $num) = each($referrer_sites)) {
                $items[] = array('referrer' => $referrer,
                                'num'      => $num);
            }
        }

        return $items;

    }

    public function CountReferers($args = array())
    {
        
        if (!isset($args['tag'])) {
            return LogUtil::registerArgsError();
        }

        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$args['tag'], ACCESS_READ)) {
            return LogUtil::registerError(__('You do not have the authorization to read this page!'), 403);
        }

        $table     =& DBUtil::getTables();
        $col         =& $table['wikula_referrers_column'];

        $countColumn = $col['referrer'];
        $where       = 'WHERE '.$col['page_tag'].' = "'.DataUtil::formatForStore($args['tag']).'"';
        $links = DBUtil::selectObjectCount('wikula_referrers', $where, $countColumn);

        if ($links === false) {
            return LogUtil::registerError(__('Error! Getting the links for this page failed.'));
        }

        return $links;

    }

    public function ReturnSafeHTML($args)
    {
        extract($args);
        unset($args);

        if (!isset($html)) {
            return LogUtil::registerArgsError();
        }
    /*
        require_once('pnincludes/safehtml/classes/safehtml.php');

        // Instantiate the handler
        $safehtml =& new safehtml();

        $filtered_output = $safehtml->parse($html);

        return $filtered_output; */
        return $html;

    }


    public function GeSHi_Highlight($args)
    {
        extract($args);
        unset($args);

        if (!isset($sourcecode) || !isset($language)) {
            return LogUtil::registerArgsError();
        }

        if (empty($start)) $start = 0;

        // create GeSHi object
        if (!class_exists('GeSHi')) {
            include_once('modules/Wikula/pnincludes/geshi/geshi.php');
        }

        // create object by reference
        $geshi = new GeSHi($sourcecode, $language, 'modules/Wikula/pnincludes/geshi/geshi');

        $geshi->enable_classes();               // use classes for highlighting (must be first after creating object)
        $geshi->set_overall_class('code');      // enables using a single stylesheet for multiple code fragments

        // configure user-defined behavior
        $geshi->set_header_type(GESHI_HEADER_DIV); // set default
        $geshi_header = $this->getVar('geshi_header');
        if (!empty($geshi_header)) {
            if ('pre' == $geshi_header) {
                $geshi->set_header_type(GESHI_HEADER_PRE);
            }
        }

        // set default
        $geshi->enable_line_numbers(GESHI_NO_LINE_NUMBERS);
        // line number > 0 _enables_ numbering
        if ($start > 0) {
            // effect only if enabled in configuration
            $geshi_line_numbers = $this->getVar('geshi_line_numbers', '1');
            if (!empty($geshi_line_numbers)) {
                if ('1' == $geshi_line_numbers) {
                    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
                } elseif ('2' == $geshi_line_numbers) {
                    $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS);
                }
                if ($start > 1) {
                    $geshi->start_line_numbers_at($start);
                }
            }
        }

        // GeSHi override (default is 8)
        $geshi_tab_width = $this->getVar('geshi_tab_width', 4);
        if ($geshi_tab_width) {
            $geshi->set_tab_width($geshi_tab_width);
        }

        // parse and return highlighted code
        // comments added to make GeSHi-highlighted block visible in code JW/20070220
        return '<!--start GeSHi-->'."\n".$geshi->parse_code()."\n".'<!--end GeSHi-->'."\n";
    }

    public function Action($args)
    {
        
        if (!isset($args['action'])) {
            return LogUtil::registerError(__('Action argument missing!'));
        }

        $action = trim($args['action']);
        unset($args['action']);

        $vars   = array();
        // only search for parameters if there is a space
        if (strpos($action, ' ') !== false) {
            // treat everything after the first whitespace as parameter
            preg_match('/^([A-Za-z0-9]*)\s+(.*)$/', $action, $matches);

            // extract $action and $vars_temp ("raw" attributes)
            $action    = isset($matches[1]) ? $matches[1] : '';
            $vars_temp = isset($matches[2]) ? $matches[2] : '';

            if (!empty($action)) {
                // match all attributes (key and value)
                preg_match_all('/([A-Za-z0-9]*)="(.*)"/U', $vars_temp, $matches);

                // prepare an array for extract() to work with (in $this->IncludeBuffered())
                if (is_array($matches)) {
                    for ($a = 0; $a < count($matches[0]); $a++) {
                        $vars[$matches[1][$a]] = $matches[2][$a];
                    }
                }
                //$vars['wikka_vars'] = trim($vars_temp); // <<< add the buffered parameter-string to the array
            } else {
                return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>'; // <<< the pattern ([A-Za-z0-9])\s+ didn't match!
            }
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $action)) {
            return '<span class="error"><em>'.__f('Unknown action %s; the action name must not contain special characters', DataUtil::formatForDisplay($action)).'.</em></span>';
        }

        $vars = array_merge($args, $vars);


        // return the Action result
        return  ModUtil::apiFunc($this->name, 'action', strtolower($action), $vars);
    }

    public function FullCategoryTextSearch($args)
    {
        
        extract($args);
        unset($args);

        if (!isset($phrase)) {
            return false;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $q->where('latest = ?', array('Y'));
        $q->addWhere('body LIKE ?', array('%[['.$phrase.'%'));
        $q->orderBy('tag');
        $result = $q->execute();
        $result = $result->toArray();

        if ($result === false) {
            return LogUtil::registerError(__('Error! Happened during element fetching.'));
        }

        $pages = array();
        foreach ($result as $id => $page) {
            $pages[$id]['page_id']  = $page['id'];
            $pages[$id]['page_tag'] = $page['tag'];
        }

        return $pages;
    }


        
}

