<?php
/**
 * Copyright Wikula Team 2012
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikula
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * This class provides a handler to modify the engine settings.
 */
class Wikula_Handler_Engine  extends Zikula_Form_AbstractHandler
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
        
       
        // get active engine
        $engine = 'none';
        $engineHook = HookUtil::getBindingsFor('subscriber.wikula.filter_hooks.body');
        foreach ($engineHook as $value) {
            if ($value['areaname'] == 'provider.lumicula.filter_hooks.lml') {
                $engine = 'LuMicuLa';
                break;
            } else if ($value['areaname'] == 'provider.wikka.filter_hooks.lml') {
                $engine = 'Wikka';
                break;
            }
        }
        
        // get available engines
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
        $url = ModUtil::url('Wikula', 'admin', 'engine' );
        
        if ($args['commandName'] == 'cancel') {
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();
        
        // set engine
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

        return $view->redirect($url);
    }

}
