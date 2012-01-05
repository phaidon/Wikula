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

class Wakka_Handler_ModifyConfig extends Zikula_Form_AbstractHandler
{

    function initialize(Zikula_Form_View $view)
    {
        $view->caching = false;
        
        $view->assign($this->getVars());

        $editors = array(
            0 => array(
                    'value' => 'wakka100',
                    'text'  => 'WakkaEdit v.1.0.0'
                 ),
            1 => array(
                    'value' => 'wakka132',
                    'text'  => 'WakkaEdit v.1.3.2'
                 ),
            2 =>  array(
                    'value' => 'wikiedit',
                    'text'  => 'WikiEdit (old version)'
                 )
        );
        $view->assign('editors', $editors);
        $view->assign('editor', $this->getVar('editor', 'wakka100'));

        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url($this->name, 'admin', 'modifyconfig');
            return $view->redirect($url);
        }

        // Security check
        if (!SecurityUtil::checkPermission('Wakka::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $ok = $view->isValid();
        $data = $view->getValues();

        $this->setVars($data);


        LogUtil::registerStatus($this->__('Done! Configuration has been updated'));

        return true;
    }
}