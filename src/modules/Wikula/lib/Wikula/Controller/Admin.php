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


class Wikula_Controller_Admin extends Zikula_AbstractController
{


    public function main()
    {
        
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN),
            LogUtil::getErrorMsgPermission()
        );

        $pagecount = ModUtil::apiFunc($this->name, 'user', 'CountAllPages');
        $owners    = ModUtil::apiFunc($this->name, 'admin', 'GetOwners');

        $this->view->assign('pagecount', $pagecount);
        $this->view->assign($owners);

        return $this->view->fetch('admin/main.tpl');
    }

    public function pages()
    {
        
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN),
            LogUtil::getErrorMsgPermission()
        );

        $q            = FormUtil::getPassedValue('q');
        $sort         = FormUtil::getPassedValue('sort');
        $order        = FormUtil::getPassedValue('order');
        $startnum     = FormUtil::getPassedValue('startnum');
        $itemsperpage = FormUtil::getPassedValue('itemsperpage');

        if (empty($itemsperpage) || !is_numeric($itemsperpage)) {
            $itemsperpage = $this->getVar('itemsperpage');
        }
        if (empty($startnum) || !is_numeric($startnum)) {
            $startnum = 1;
        }

        $items = ModUtil::apiFunc($this->name, 'admin', 'getall', array(
            'sort'     => $sort,
            'order'    => $order,
            'startnum' => $startnum,
            'numitems' => $itemsperpage,
            'q'        => $q
        ));

        $total = ModUtil::apiFunc($this->name, 'user', 'CountAllPages');


        $this->view->assign('sort',         $sort);
        $this->view->assign('order',        $order);
        $this->view->assign('itemcount',    count($items));
        $this->view->assign('total',        $total);
        $this->view->assign('items',        $items);
        $this->view->assign('startnum',     $startnum);
        $this->view->assign('itemsperpage', $itemsperpage);
        $this->view->assign('pageroptions', array(5, 10, 20, 30, 40, 50, 100, 200, 300, 400, 500));

        $this->view->assign('pager', array('numitems'     => $total,
                                       'itemsperpage' => $itemsperpage));

        return $this->view->fetch('admin/pageadmin.tpl', $order . $sort . $startnum);
    }

    public function modifyconfig()
    {
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('admin/modifyconfig.tpl', new Wikula_Handler_ModifyConfig());
    }

    public function updateconfig()
    {
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN),
            LogUtil::getErrorMsgPermission()
        );

        $root_page    = FormUtil::getPassedValue('root_page');
        $savewarning  = FormUtil::getPassedValue('savewarning');
        $hidehistory  = FormUtil::getPassedValue('hidehistory');
        $itemsperpage = FormUtil::getPassedValue('itemsperpage');
        $hideeditbar  = FormUtil::getPassedValue('hideeditbar');
        $logreferers  = FormUtil::getPassedValue('logreferers');
        $excludefromhistory = FormUtil::getPassedValue('excludefromhistory');

        // Initialize the modvars array
        $modvars = array();

        $modvars['excludefromhistory'] = $excludefromhistory;

        if (empty($hideeditbar)) {
            $hideeditbar = false;
        }
        $modvars['hideeditbar'] = (bool)$hideeditbar;

        if (empty($savewarning)) {
            $savewarning = false;
        }
        $modvars['savewarning'] = (bool)$savewarning;

        if (empty($hidehistory)) {
            $hidehistory = false;
        }
        $modvars['hidehistory'] = (bool)$hidehistory;

        if (empty($logreferers)) {
            $logreferers = false;
        }
        $modvars['logreferers'] = (bool)$logreferers;

        if (empty($itemsperpage) || !is_numeric($itemsperpage) || (int)$itemsperpage < 1) {
            $itemsperpage = 25;
        }
        $modvars['itemsperpage'] = (int)$itemsperpage;

        if (!empty($root_page)) {
            $modvars['root_page'] = $root_page;
        }

        $this->setVars( $modvars);

        $render = pnRender::getInstance($this->name);
        $this->view->clear_cache();

        LogUtil::registerStatus(__('Done! Module configuration updated.'));

        // TODO Hook
        //pnModCallHooks('module', 'updateconfig', $this->name, array('module' => $this->name));

        return $this->redirect(ModUtil::url($this->name, 'admin', 'modifyconfig'));
    }

    public function ClearReferrers()
    {

        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN),
            LogUtil::getErrorMsgPermission()
        );

        $tag    = FormUtil::getPassedValue('tag');
        $global = FormUtil::getPassedValue('global');

        $result = ModUtil::apiFunc($this->name, 'admin', 'ClearReferrers',
                               array('tag'    => $tag,
                                     'global' => $global));

        return pnRedirect(ModUtil::url($this->name, 'user', 'referrers', array('tag' => $tag)));
    }

    public function delete()
    {
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_DELETE),
            LogUtil::getErrorMsgPermission()
        );
        

        $tag    = FormUtil::getPassedValue('tag');
        $submit = FormUtil::getPassedValue('submit');

        if (!empty($submit)) {

            $revids = FormUtil::getPassedValue('revids');

            if (empty($revids)) {
                return LogUtil::registerArgsError(ModUtil::url($this->name, 'admin', 'pages'));
            }

            $ids = array_keys($revids);

            foreach($ids as $id) {
                $revisions[] = ModUtil::apiFunc($this->name, 'user', 'LoadPagebyId', array('id' => $id));
                //echo $page['tag'] . ' - '. $page['time'] . ' - '. $page['note'] . '<br />';
            }

        } else {
            $revisions = ModUtil::apiFunc($this->name, 'user', 'LoadRevisions', array('tag' => $tag));
        }


        $this->view->assign($this->getVars());
        $this->view->assign('tag',       $tag);
        $this->view->assign('revisions', $revisions);
        $this->view->assign('submit',    $submit);

        return $this->view->fetch('admin/deletepages.tpl');
    }

    public function confirmdeletepage()
    {

        $revids = FormUtil::getPassedValue('revids');
        $tag    = FormUtil::getPassedValue('tag');

        if (empty($revids) || empty($tag)) {
            return LogUtil::registerArgsError(ModUtil::url($this->name, 'admin', 'pages'));
        }

        $revisions = array_keys($revids);

        foreach ($revisions as $revision) {
            $action = ModUtil::apiFunc($this->name, 'admin', 'deletepageid', array('id' => $revision));
            if ($action === false) {
                return pnRedirect(ModUtil::url($this->name, 'admin', 'pages'));
            }
        }

        // Set the latest
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadRevisions', array('tag' => $tag));

        if ($pages) {
            $setlatest = ModUtil::apiFunc($this->name, 'admin', 'setlatest', array('pages' => $pages));
        }

        LogUtil::registerStatus('Pages deleted');

        return $this->redirect(ModUtil::url($this->name, 'admin', 'pages'));
    /*
        echo '<pre>';
        print_r($revids);
        echo '</pre>';
        //exit;
    */
        //revisions to delete
    /*
        echo '<pre>';
        print_r($revisions);
        echo '</pre>';
        //exit;

        echo 'Please confirm you want to delete these revisions (The most recent revision left will be set as "Latest")<br />';
        echo 'If there is no revision left, the page will be completly deleted.<br /><br />';
        foreach($revisions as $revision) {
            $page = ModUtil::apiFunc($this->name, 'user', 'LoadPagebyId', array('id' => $revision));
            echo $page['tag'] . ' - '. $page['time'] . ' - '. $page['note'] . '<br />';
        }

        exit;
    */
    }
}