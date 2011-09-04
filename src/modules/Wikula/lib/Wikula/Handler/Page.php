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

class Wikula_Handler_Page  extends Zikula_Form_AbstractHandler
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
        
        $specialPages = ModUtil::apiFunc($this->name, 'SpecialPage', 'listpages');
        if( array_key_exists($this->_tag, $specialPages)) {
            return $this->redirect(ModUtil::url('Wikula', 'user', 'main', array('tag' => $this->_tag)));
        }
        
        $page = ModUtil::apiFunc('Wikula', 'user', 'LoadPage', array(
            'tag'  => $this->_tag,
        ));
        if($page) {
            $page['note'] = '';
        } else {
            $page = array(
                'tag'  => $this->_tag,
                'note' => $this->__('Initial Insert')
            );
        }
     
        // build the output 
        $this->view->assign($page);
        
        
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        // cancel
        //--------------------------
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url(
                'Wikula',
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
        // strip CRLF line endings down to LF to achieve consistency ... plus it saves database space.
        $data['body'] = str_replace("\r\n", "\n", $data['body']);
        
        
        
        // preview
        //--------------------------
        if ($args['commandName'] == 'preview'){
            $view->assign("preview", $data['body']);
            return true;
        }
        
        
        
        // store
        //--------------------------
        // check for overwriting
        $previousid = ModUtil::apiFunc('Wikula', 'user', 'PageExists', array('tag' => $this->_tag));
        
        
        if ($previousid && $previousid != $data['id']) {
            return LogUtil::registerError(
                $this->__('OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br />Please copy your changes and re-edit this page.')
            );
        }
        $store = ModUtil::apiFunc('Wikula', 'user', 'SavePage', array(
            'tag'      => $this->_tag,
            'body'     => $data['body'],
            'note'     => $data['note'],
            'tracking' => true
        ));
        
        $url = ModUtil::url('Wikula', 'user', 'main', array('tag' => $this->_tag));
        return $view->redirect($url);            

    }

}
