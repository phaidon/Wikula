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

class Wikula_Handler_Delete  extends Zikula_Form_AbstractHandler
{
    /**
     * tag.
     *
     * When set this handler is in edit mode.
     *
     * @var string
     */
    private $tag;
    
    function initialize(Zikula_Form_View $view)
    {
        $this->tag = FormUtil::getPassedValue('tag', null, "GET", FILTER_SANITIZE_STRING);   
        
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_DELETE) ) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
             
        $revisions = ModUtil::apiFunc( $this->name, 'user', 'LoadRevisions0', $this->tag);
        
        
        // build the output 
        $this->view->assign($this->getVars());
        $this->view->assign('tag',       $this->tag);
        $this->view->assign('deleteAll', true);
        $this->view->assign('revisions', $revisions);
        
        
        
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        // cancel
        //--------------------------
        $url = ModUtil::url( $this->name, 'admin', 'pages');
        if ($args['commandName'] == 'cancel') {
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();

        $itemsToRemove = array();
        $itemsToRemoveCount = 0;
        foreach ($data['revisionids'] as $id => $value) {
            if($value) {
                $itemsToRemove[] = $id;
                $itemsToRemoveCount++;                
            }
        }
        
        if($itemsToRemoveCount == 0) {
            return $view->redirect($url);
        }
        
        if( $itemsToRemoveCount == count($data['revisionids']) ) {
            ModUtil::apiFunc($this->name, 'admin', 'deletepage', $this->tag);
            LogUtil::registerStatus( $this->__('Page deleted') );
            return $view->redirect($url);
        }
        
        foreach ($itemsToRemove as $id) {
            ModUtil::apiFunc($this->name, 'admin', 'deletepageid', array('id' => $id));
        }
        


        ModUtil::apiFunc($this->name, 'admin', 'setlatest', $this->tag);

        LogUtil::registerStatus($this->__('Revisions deleted'));

        return $view->redirect($url);   
        

    }

}
