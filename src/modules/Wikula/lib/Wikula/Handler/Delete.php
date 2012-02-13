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
 * This class provides a handler to delete wiki pages.
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
    
    /**
     * Setup form.
     *
     * @param Zikula_Form_View $view Current Zikula_Form_View instance.
     *
     * @return boolean
     * 
     * @throws Zikula_Exception_Forbidden If the current user does not have adequate permissions to perform this function.
     */
    function initialize(Zikula_Form_View $view)
    {
        $modname = 'Wikula';
        $this->tag = FormUtil::getPassedValue('tag', null, "GET", FILTER_SANITIZE_STRING);   
        
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_DELETE) ) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
             
        $revisions = ModUtil::apiFunc( $modname, 'user', 'LoadRevisions0', $this->tag);
        
        
        // build the output 
        $view->assign($this->getVars());
        $view->assign('tag',       $this->tag);
        $view->assign('deleteAll', true);
        $view->assign('revisions', $revisions);
        
        
        return true;
    }

    /**
     * Handle form submission.
     *
     * @param Zikula_Form_View $view  Current Zikula_Form_View instance.
     * @param array            &$args Args.
     *
     * @return boolean
     */
    function handleCommand(Zikula_Form_View $view, &$args)
    {
        $modname = 'Wikula';
        
        // cancel
        //--------------------------
        $url = ModUtil::url( $modname, 'admin', 'pages');
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
            if ($value) {
                $itemsToRemove[] = $id;
                $itemsToRemoveCount++;                
            }
        }
        
        if ($itemsToRemoveCount == 0) {
            return $view->redirect($url);
        }
        
        if ($itemsToRemoveCount == count($data['revisionids']) ) {
            ModUtil::apiFunc($modname, 'admin', 'deletepage', $this->tag);
            LogUtil::registerStatus( $this->__('Page deleted') );
            return $view->redirect($url);
        }
        
        foreach ($itemsToRemove as $id) {
            ModUtil::apiFunc($modname, 'admin', 'deletepageid', array('id' => $id));
        }
        


        ModUtil::apiFunc($modname, 'admin', 'setlatest', $this->tag);

        LogUtil::registerStatus($this->__('Revisions deleted'));

        return $view->redirect($url);   
        

    }

}
