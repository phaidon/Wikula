<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * This class provides a handler to edit wiki pages.
 * 
 * @package Wikula
 */
class Wikula_Handler_EditTag  extends Zikula_Form_AbstractHandler
{
    /**
     * tag.
     *
     * When set this handler is in edit mode.
     *
     * @var string
     */
    private $tag = null;
    
    
    
    /**
     * Setup form.
     *
     * @param Zikula_Form_View $view Current Zikula_Form_View instance.
     *
     * @return boolean
     */
    function initialize(Zikula_Form_View $view)
    {
        $modname = 'Wikula';
        $this->tag = FormUtil::getPassedValue('tag', null, "GET", FILTER_SANITIZE_STRING);
     
        
        // Permission check
        if (!ModUtil::apiFunc($modname, 'Permission', 'canEdit', $this->tag)) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        
        // redirect if tag contains spaces
        if (strpos($this->tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $this->tag),
            );
            $redirecturl = ModUtil::url($modname, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }
        
        $specialPages = ModUtil::apiFunc($modname, 'SpecialPage', 'listpages');
        if( array_key_exists($this->tag, $specialPages)) {
            return $view->redirect(ModUtil::url($modname, 'user', 'main', array('tag' => $this->tag)));
        }
        
        $page = ModUtil::apiFunc($modname, 'user', 'LoadPage', array(
            'tag'  => $this->tag,
        ));
        if($page) {
            $page['note'] = '';
        } else {
            $page = array(
                'note' => $this->__('Initial Insert')
            );
            $view->assign('tag', $this->tag);
        }
     
        // build the output 
        $view->assign($page);
        $view->assign('mandatorycomment', $this->getVar('mandatorycomment', false));
        $showeditnote = $this->getVar('showeditnote', false);
        $view->assign('showeditnote', $showeditnote);
        if($showeditnote) {
            $view->assign('editnote', $this->getVar('editnote', ''));
        }
        
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
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url(
                $modname,
                'user',
                'show',
                array('tag' => $this->tag) 
            );
            return $view->redirect($url);
        } else if ($args['commandName'] == 'clone') {
                $url = ModUtil::url(
                $modname,
                'user',
                'cloneTag',
                array('tag' => $this->tag) 
            );
            return $view->redirect($url);
        } else if ($args['commandName'] == 'rename') {
                $url = ModUtil::url(
                $modname,
                'user',
                'renameTag',
                array('tag' => $this->tag) 
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
        $previousid = ModUtil::apiFunc($modname, 'user', 'PageExists', $this->tag);
        
        
        if ($previousid && $previousid != $data['id']) {
            return LogUtil::registerError(
                $this->__('OVERWRITE ALERT: This page was modified by someone else while you were editing it.<br />Please copy your changes and re-edit this page.')
            );
        }
        unset($data['id']);
        $store = ModUtil::apiFunc($modname, 'user', 'SavePage', array(
            'tag'      => $this->tag,
            'body'     => $data['body'],
            'note'     => $data['note'],
            'tracking' => true
        ));
        
        $url = ModUtil::url($modname, 'user', 'main', array('tag' => $this->tag));
        return $view->redirect($url);            

    }

}
