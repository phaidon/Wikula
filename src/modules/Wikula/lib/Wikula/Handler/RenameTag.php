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

class Wikula_Handler_RenameTag  extends Zikula_Form_AbstractHandler
{
    /**
     * tag.
     *
     * When set this handler is in edit mode.
     *
     * @var string
     */
    private $_tag;
    
    function initialize(Zikula_Form_View $view)
    {
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_EDIT) ) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        $this->_tag = FormUtil::getPassedValue('tag', null, "GET", FILTER_SANITIZE_STRING);
        
        
        // redirect if tag contains spaces
        if (strpos($this->_tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $this->_tag),
            );
            $redirecturl = ModUtil::url($this->name, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }
        
        // redirect if tag is a special page
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if( array_key_exists($this->_tag, $specialPages)) {
            return $view->redirect(ModUtil::url($this->name, 'user', 'main', array('tag' => $this->_tag)));
        }
        
        
        // check if page exists
        if (!ModUtil::apiFunc($this->name, 'user', 'PageExists', array('tag' => $this->_tag))) {
            return LogUtil::registerError(__("The page you requested doesn't exists"), null, ModUtil::url($this->name, 'user', 'show'));
        }
        
        
        // build the output 
        $this->view->assign('tag',  $this->_tag);
 
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {

        
        // cancel
        //--------------------------
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url(
                $this->name,
                'user',
                'main',
                array('tag' => $this->_tag) 
            );
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        $data = $view->getValues();
        extract($data); //$to, $edit, $note
        

        // Validate the choosen pagename
        if (!ModUtil::apiFunc($this->name, 'user', 'isValidPagename', array('tag' => $to))) {
            return LogUtil::registerError($this->__('That page name is not valid'));
        }
        
        // check if the page already exists
        if (ModUtil::apiFunc($this->name, 'user', 'PageExists', array('tag' => $to))) {
            return LogUtil::registerError($this->__('This page does already exist'));
        }

        // check if has access to create it
        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$this->_tag, ACCESS_EDIT)) {
            return LogUtil::registerError($this->__('You do not have the authorization to edit this page!'));
        }


        // set all other revisions to old
        $q = Doctrine_Query::create()
            ->update('Wikula_Model_Pages t')
            ->set('tag', '?', array($to))
            ->where('tag = ?', array($this->_tag));
        $q->execute();
        

        LogUtil::registerStatus(__('Rename successfully'));
        return $view->redirect(ModUtil::url($this->name, 'user', 'show', array('tag' => $to)));

    }
}
