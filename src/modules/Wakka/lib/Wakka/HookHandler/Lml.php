<?php

/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wakka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wakka_HookHandler_Lml extends Zikula_Hook_AbstractHandler
{
    /**
     * Zikula_View instance
     *
     * @var Zikula_View
     */
    private $view;

    /**
     * Post constructor hook.
     *
     * @return void
     */
    public function setup()
    {
        $this->view = Zikula_View::getInstance("Wakka");
        $this->name = 'Wakka';
    }

    /**
     * Display hook for view.
     *
     * Subject is the object being viewed that we're attaching to.
     * args[id] is the id of the object.
     * args[caller] the module who notified of this event.
     *
     * @param Zikula_Hook $hook
     *
     * @return void
     */
    public function ui_view(Zikula_DisplayHook $hook)
    {
        $modname = $hook->getCaller();
        $textfieldname = $hook->getId();
        
        if(empty($textfieldname)) {
           $textfieldname = 'textfield';
        } 

        $this->view->assign('textfieldname', $textfieldname)
                   ->assign('baseurl',       System::getBaseUrl());
        
        $version = ModUtil::getVar('Wakka', 'editor', 'wakka100');
        
        if($version == 'wikiedit') {
            $this->view->assign('maAvailable', ModUtil::available('MediaAttach'));
        }
  
        $response = new Zikula_Response_DisplayHook('provider_area.ui.wakka.lml', $this->view, $version.'.tpl');
        $hook->setResponse($response);
    }

    /**
     * Filter hook for view
     *
     * Subject is the object being viewed that we're attaching to.
     * args[id] is the id of the object.
     * args[caller] the module who notified of this event.
     *
     * @param Zikula_Hook $hook
     *
     * @return void
     */
    public static function filter(Zikula_FilterHook $hook)
    {
        $text = $hook->getData();
        $text = ModUtil::apiFunc('Wakka', 'transform', 'transform', array(
            'text'   => $text,
            'modname' => $hook->getCaller())
        );        
        $hook->setData($text);
    }



}