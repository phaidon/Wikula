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
        if (SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN)) {
            $links[] = array(
                'url' => ModUtil::url('Wikula', 'admin', 'main'),
                'text' => __('Main'),
                'class' => 'z-icon-es-home'
            );
            $links[] = array(
                'url' => ModUtil::url('Wikula', 'admin', 'pages'),
                'text' =>  __('Pages'),
                'class' => 'z-icon-es-view'
            );
            $links[] = array(
                'url' => ModUtil::url('Wikula', 'admin', 'modifyconfig'),
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
     *
     * @return unknown
     */
    public function PageIndex()
    {
        
        $pages = ModUtil::apiFunc('Wikula', 'user', 'LoadAllPages');

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

        $render = pnRender::getInstance('Wikula', false);

        $render->assign('currentpage',   $currentpage);
        $render->assign('headerletters', $headerletters);
        $render->assign('pagelist',      $pagelist);
        $render->assign('username',      $cached_username);
        $render->assign('userownspages', $user_owns_pages);

        return $render->fetch('wikula_admin_pages.tpl', $tag.$letter);
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
            $sort == 'backlinks') {
            $sortby = 'id';
        }

        if (!isset($sort) || empty($sort)) {
            $sortby  = 'time';
        } else {
            $sortby  = $sort;
        }

        if (isset($col) and !array_key_exists($sortby, $col)) {
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
        if (isset($q) && !empty($q)) {
            $qy->addWhere('tag LIKE ?', array('%'.$q.'%'));
        }

        $pages = $qy->execute();
        $pages = $pages->toArray();
        


        foreach ($pages as $pageID => $pageTab) {
            $pages[$pageID]['revisions'] = ModUtil::apiFunc($this->name, 'admin', 'CountRevisions', array('tag' => $pageTab['tag']));
            $pages[$pageID]['backlinks'] = ModUtil::apiFunc($this->name, 'user',  'CountBackLinks', $pageTab['tag']);
            if( ModUtil::available('EZComments')) {
                $commentsCount = ModUtil::apiFunc('EZComments', 'user', 'countitems', array(
                    'mod' => $this->name,
                    'objectid' => $pageTab['tag']
                ));
            } else {
                $commentsCount = 0;
            }
            $pages[$pageID]['comments'] = $commentsCount;
        }


        if ($sort == 'revisions' ||
            $sort == 'comments'  ||
            $sort == 'backlinks') {
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

        if (empty($pages) or !is_array($pages)) {
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
