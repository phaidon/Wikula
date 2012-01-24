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
 * This class provides a handler to modify the module settings.
 * 
 * @package Wikula
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
        foreach ($editorHook as  $value) {
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
        
        
        // get engines
        $engine = 'none';
        $engineHook = HookUtil::getBindingsFor('subscriber.wikula.filter_hooks.body');
        foreach ($engineHook as  $value) {
            if ($value['areaname'] == 'provider.lumicula.filter_hooks.lml') {
                $engine = 'LuMicuLa';
                break;
            } else if ($value['areaname'] == 'provider.wikka.filter_hooks.lml') {
                $engine = 'Wikka';
                break;
            }
        }
        $engines = array();
        $engines[] = array(
            'text' => 'None',
            'value' => 'none'
        );
        if (ModUtil::available('LuMicuLa')) {
            $engines[] = array(
                'text' => 'LuMicuLa',
                'value' => 'LuMicuLa'
            );
        }
        if (ModUtil::available('Wikka')) {
            $engines[] = array(
                'text' => 'Wikka',
                'value' => 'Wikka'
            );
        }
        $view->assign('engine',  $engine);
        $view->assign('engines', $engines);
        
        
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
                $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                break;
            case 'Wikka':
                $hookManager->bindSubscriber(  'subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                $hookManager->unbindSubscriber('subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                break;
            case 'LuMicuLa':
                $hookManager->bindSubscriber(   'subscriber.wikula.ui_hooks.editor', 'provider.lumicula.ui_hooks.lml');
                $hookManager->unbindSubscriber( 'subscriber.wikula.ui_hooks.editor', 'provider.wikka.ui_hooks.lml');
                break;
        }
        unset($data['editor']);

        
        // set editor
        $hookManager = ServiceUtil::getService('zikula.hookmanager');        
        switch ($data['engine']) {
            case 'none':
                $hookManager->unbindSubscriber('subscriber.wikula.filter_hooks.body', 'provider.wikka.filter_hooks.lml');
                $hookManager->unbindSubscriber('subscriber.wikula.filter_hooks.body', 'provider.lumicula.filter_hooks.lml');
                break;
            case 'Wikka':
                $hookManager->bindSubscriber(  'subscriber.wikula.filter_hooks.body', 'provider.wikka.filter_hooks.lml');
                $hookManager->unbindSubscriber('subscriber.wikula.filter_hooks.body', 'provider.lumicula.filter_hooks.lml');
                break;
            case 'LuMicuLa':
                $hookManager->bindSubscriber(   'subscriber.wikula.filter_hooks.body', 'provider.lumicula.filter_hooks.lml');
                $hookManager->unbindSubscriber( 'subscriber.wikula.filter_hooks.body', 'provider.wikka.filter_hooks.lml');
                break;
        }
        unset($data['engine']);
        
        
        $this->setVars($data);


        return $view->redirect($url);
    }

}
