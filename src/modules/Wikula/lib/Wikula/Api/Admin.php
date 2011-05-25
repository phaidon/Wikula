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

class Wikula_Api_Admin extends Zikula_AbstractApi
{
    /**
     * get available admin panel links
     *
     * @author Mateo Tibaquirá
     * @return array array of admin links
     */
    public function getlinks()
    {
        
        $links = array();
        if (SecurityUtil::checkPermission('wikula::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => ModUtil::url('wikula', 'admin', 'main'),
                'text' => __('Main'),
                'class' => 'z-icon-es-home'
            );
            $links[] = array(
                'url' => ModUtil::url('wikula', 'admin', 'pages'),
                'text' =>  __('Pages'),
                'class' => 'z-icon-es-view'
            );
            $links[] = array(
                'url' => ModUtil::url('wikula', 'admin', 'modifyconfig'),
                'text' => __('Settings'),
                'class' => 'z-icon-es-config'
            );
        }
        return $links;
    }

    public function GetOwners()
    {
        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $result = $q->execute();
        $result = $result->toKeyValueArray('owner', 'owner');

        if ($result === false) {
            return LogUtil::registerError(__('Error! Get owners failed.'));
        }

        $items['owners']      = $result;
        $items['ownerscount'] = sizeof($items['owners']);

        return $items;
    }

    /**
     * @todo REWORK! not possible to have outoutin an API func!
     *
     * @return unknown
     */
    public function PageIndex()
    {
        
        $pages = ModUtil::apiFunc('wikula', 'user', 'LoadAllPages');

        $requested_letter = FormUtil::getPassedValue('letter');
        $currentpage      = FormUtil::getPassedValue('tag');

        if (UserUtil::isLoggedIn()) {
            $cached_username = pnUserGetVar('uname');
        } else {
            $cached_username = '';
        }

        $current_character = '';
        $character_changed = false;
        $user_owns_pages   = false;
        $pagelist          = array();
        $headerletters     = array();

        if ($pages) {
            foreach($pages as $page) {

                $page_owner = $page['owner'];

                $firstChar = strtoupper(substr($page['tag'],0,1)); //echo $firstChar;
                if (!preg_match('/[A-Za-z]/', $firstChar)) {
                    $firstChar = '#';
                }

                if ($firstChar != $current_character) {
                    $headerletters[] = $firstChar;
                    $current_character = $firstChar;
                    $character_changed = true;
                }
                if ($requested_letter == '' || $firstChar == $requested_letter) {
                    if ($character_changed) {
                        $character_changed = false;
                    }

                    $pagelist[$firstChar][] = $page;

                    if ($cached_username == $page_owner) {
                        $user_owns_pages = true;
                    }
                }

            }
        }

        $render = pnRender::getInstance('wikula', false);

        $render->assign('currentpage',   $currentpage);
        $render->assign('headerletters', $headerletters);
        $render->assign('pagelist',      $pagelist);
        $render->assign('username',      $cached_username);
        $render->assign('userownspages', $user_owns_pages);

        return $render->fetch('wikula_admin_pages.tpl', $tag.$letter);
    }

    public function ClearReferrers($args)
    {
        
        $tag    = '';

        if (isset($args['tag'])) {
            $tag = $args['tag'];
        }


        if (isset($args['global']) && $args['global'] == 1) {
            // TODO delete all
            $where = '';
        } else {
            $referrer = Doctrine_Core::getTable('Wikula_Model_Referrers')->findBy('page_tag', $tag);
            $referrer->delete();
        }

        return true;
    }

    public function getall($args)
    {
        
        extract($args);
        unset($args);

        $qy = Doctrine_Query::create()->from('Wikula_Model_Pages t');

        
        if (!isset($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }
        if (!isset($numitems) || !is_numeric($numitems)) {
            $numitems = -1;
        }
        
        $qy->offset($startnum-1);
        $qy->limit($numitems);


        if ($sort == 'revisions' ||
            $sort == 'comments'  ||
            $sort == 'backlinks' ||
            $sort == 'referrers') {
            $sortby = 'id';
        }

        if (!isset($sort) || empty($sort)) {
            $sortby  = 'time';
        } else {
            $sortby  = $sort;
        }

        if (!array_key_exists($sortby, $col)) {
            $sortby  = 'time';
        }
        if ($order <> 'ASC' && $order <> 'DESC') {
            if (empty($sortby)) {
                $order = 'ASC';
            } else {
                $order = 'DESC';
            }
        }
        
        $qy->orderBy($sortby.' '.$order);

        $qy->where('latest = ?', array('Y'));

        $search  = '';
        $boolean = '';
        print_r($q);
        if (isset($q) && !empty($q)) {
            $qy->addWhere('tag LIKE ?', array('%'.$q.'%'));
        }


        
        // TODO permission
        /*$permission = array();
        $permission[] = array('realm' => 0,
                              'component_left'   => 'wikula',
                              'component_middle' => '',
                              'component_right'  => '',
                              'instance_left'    => '',
                              'instance_middle'  => '',
                              'instance_right'   => 'tag',
                              'level'            => ACCESS_READ);
         * 
         */
        $pages = $qy->execute();
        $pages = $pages->toArray();
        

        $logref  = $this->getVar('logreferers');
        //$ezhook  = false;
        // TODO Hook
        /*if (pnModAvailable('EZComments') && pnModIsHooked('EZComments', 'wikula')) {
            $ezhook = true;
        }*/

        foreach ($pages as $pageID => $pageTab) {
            $pages[$pageID]['revisions'] = ModUtil::apiFunc('Wikula', 'admin', 'CountRevisions', array('tag' => $pageTab['tag']));
            //$pages[$pageID]['comments']  = (($ezhook == true) ? ModUtil::apiFunc('EZComments', 'user',  'countitems', array('mod' => 'wikula', 'objectid' => $pageTab['tag'])) : 0);
            $pages[$pageID]['backlinks'] = ModUtil::apiFunc('Wikula', 'user', 'CountBackLinks', array('tag' => $pageTab['tag']));
            $pages[$pageID]['referrers'] = (($logref == 1) ? ModUtil::apiFunc('Wikula', 'user', 'CountReferers', array('tag' => $pageTab['tag'])) : 0);
        }


        if ($sort == 'revisions' ||
            $sort == 'comments'  ||
            $sort == 'backlinks' ||
            $sort == 'referrers') {
            $sortAarr = array();
            foreach($pages as $res) {
                $sortAarr[] = $res[$sort];
            }
            array_multisort($sortAarr, (($order == 'ASC') ? SORT_ASC : SORT_DESC), SORT_NUMERIC, $pages);
        }

        return $pages;
    }

    public function CountRevisions($args = array())
    {
        if (!isset($args['tag']) || empty($args['tag'])) {
            return false;
        }

        $q = Doctrine_Query::create()->from('Wikula_Model_Pages t');
        $q->where('tag = ?', array($args['tag']));
        $result = $q->execute();
        $pagecount = $result->count();

        if ($pagecount === false) {
            return LogUtil::registerError(__('Error! Count the revisions for this page failed.'));
        }

        return $pagecount;
    }

    public function deletepageid($args)
    {
        $id  = $args['id'];

        if (empty($id) || !is_numeric($id)) {
            return false;
        }
              
        $page = Doctrine_Core::getTable('Wikula_Model_Pages')->find($id);
        $page->delete();
        
        return true;
    }

    public function setlatest($args)
    {
        
        $pages = $args['pages'];

        if (empty($pages)) {
            return false;
        }

        $count   = 1;

        foreach ($pages as $page) {
            if ($count == 1) {
                $value = 'Y';
            } else {
                $value = 'N';
            }

            $updates['latest'] = DataUtil::formatForStore($value);

            
            $page = Doctrine_Core::getTable('Wikula_Model_Pages')->find($page['id']); 
            $page->merge($updates);
            $page->save();
            

            $count++;
        }

        return true;
        
       
        
    }
}
