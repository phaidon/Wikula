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
 * This class provides a handler to modify the module settings.
 */
class Wikula_Handler_ModifyConfig  extends Zikula_Form_AbstractHandler
{

    /**
     * Setup form.
     *
     * @param Zikula_Form_View $view Current Zikula_Form_View instance.
     *
     * @return boolean
     */
    function initialize(Zikula_Form_View $view)
    {
        
        $view->caching = false;
        $view->assign($this->getVars());
        
        
        // get editors
        $editor = 'none';
        $editorHook = HookUtil::getBindingsFor('subscriber.wikula.ui_hooks.editor');
        foreach ($editorHook as $value) {
            if ($value['areaname'] == 'provider.lumicula.ui_hooks.lml') {
                $editor = 'LuMicuLa';
                break;
            } else if ($value['areaname'] == 'provider.wikka.ui_hooks.lml') {
                $editor = 'Wikka';
                break;
            }
        }
        $editors = array();
        $editors[] = array(
            'text' => 'None',
            'value' => 'none'
        );
        if (ModUtil::available('LuMicuLa')) {
            $editors[] = array(
                'text' => 'LuMicuLa',
                'value' => 'LuMicuLa'
            );
        }
        if (ModUtil::available('Wikka')) {
            $editors[] = array(
                'text' => 'Wikka',
                'value' => 'Wikka'
            );
        }
        $view->assign('editor',  $editor);
        $view->assign('editors', $editors);
        
        
        // get discussion modules
        $discussionModule = 'none';
        $discussionHook = HookUtil::getBindingsFor('subscriber.wikula.ui_hooks.discuss');
        foreach ($discussionHook as $value) {
            if ($value['areaname'] == 'provider.ezcomments.ui_hooks.comments') {
                $discussionModule = 'EZComments';
                break;
            }
        }
        $discussionModules = array();
        $discussionModules[] = array(
            'text' => 'None',
            'value' => 'none'
        );
        if (ModUtil::available('EZComments')) {
            $discussionModules[] = array(
                'text' => 'EZComments',
                'value' => 'EZComments'
            );
        }
        $view->assign('discussionModule',  $discussionModule);
        $view->assign('discussionModules', $discussionModules);
        
        
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
        $url = ModUtil::url('Wikula', 'admin', 'modifyconfig' );
        
        if ($args['commandName'] == 'cancel') {
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();

        // set editor
        $hookManager = ServiceUtil::getService('zikula.hookmanager');        
        switch ($data['editor']) {
            case 'none':
                if (ModUtil::available('Wikka')) {
                    $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                }
                if (ModUtil::available('LuMicuLa')) {
                    $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                }
                break;
            case 'Wikka':
                $hookManager->bindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                if (ModUtil::available('LuMicuLa')) {
                    $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                }
                break;
            case 'LuMicuLa':
                $hookManager->bindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                if (ModUtil::available('Wikka')) {
                    $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                }
                break;
        }
        unset($data['editor']);

        // set discussion module
        switch ($data['discussionModule']) {
            case 'none':
                if (ModUtil::available('EZComments')) {
                    $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.discuss', 'provider.ezcomments.ui_hooks.comments');
                }
                $this->setVar('discussion_is_available', false);
                break;
            case 'EZComments':
                $hookManager->bindSubscriber(  'subscriber.wikula.ui_hooks.discuss', 'provider.ezcomments.ui_hooks.comments');
                $this->setVar('discussion_is_available', true);
                break;
        }
        unset($data['discussionModule']);
        
        
        $this->setVars($data);


        return $view->redirect($url);
    }

}