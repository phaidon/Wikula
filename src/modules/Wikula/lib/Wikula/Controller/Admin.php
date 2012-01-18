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


require_once 'modules/Wikula/lib/Wikula/Common.php';


class Wikula_Controller_Admin extends Zikula_AbstractController
{


    public function main()
    {
        $url = ModUtil::url($this->name, 'admin', 'pages');
        return System::redirect($url);

    }
    
    
    
    public function stat() {
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


    public function delete()
    {   
        $form = FormUtil::newForm($this->name, $this);
        return $form->execute('admin/deletepages.tpl', new Wikula_Handler_Delete());
    }
    
    public function rebuildLinksAndCategoriesTables() {
        
        // Permission check
        $this->throwForbiddenUnless(
            SecurityUtil::checkPermission('Wikula::', '::', ACCESS_ADMIN),
            LogUtil::getErrorMsgPermission()
        );
        
        
        $oldlinks = $this->entityManager->getRepository('Wikula_Entity_Links2')->findAll();
        foreach($oldlinks as $oldlink) {
            $this->entityManager->remove($oldlink);
            $this->entityManager->flush();
        }
        
        $oldcategories = $this->entityManager->getRepository('Wikula_Entity_Categories')->findAll();
        foreach($oldcategories as $oldcategory) {
            $this->entityManager->remove($oldcategory);
            $this->entityManager->flush();
        }
        
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPages');
        foreach( $pages as $page ) {
            $hook = new Zikula_FilterHook(
                $eventname = 'wikula.filter_hooks.body.filter', 
                $content = $page['body']
            );
            $hook->setCaller('WikulaSaver');  
            $data = ServiceUtil::getManager()->getService('zikula.hookmanager')
                                            ->notify($hook)->getData(); 
            $pagelinks      = $data['links'];
            $pagecategories = $data['categories'];

            
            foreach($pagelinks as $pagelink) {
                $link = array(
                    'from_tag' => $page['tag'],
                    'to_tag'   => $pagelink
                );
                $d = new Wikula_Entity_Links2();
                $d->merge($link);
                $this->entityManager->persist($d);
                $this->entityManager->flush();
            }
            

            foreach($pagecategories as $pagecategory) {
                $category = array(
                    'tag'      => $page['tag'],
                    'category' => $pagecategory
                );
                $d = new Wikula_Entity_Categories();
                $d->merge($category);
                $this->entityManager->persist($d);
                $this->entityManager->flush();
            }
        }

        
        $redirecturl = ModUtil::url($this->name, 'admin', 'modifyconfig');
        return System::redirect($redirecturl);
        
    }
     
}