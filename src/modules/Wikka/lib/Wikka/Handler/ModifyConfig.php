<?php

/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/LGPLv3 (or at your option, any later version).
 * @package Wikka
 * @link http://code.zikula.org/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wikka_Handler_ModifyConfig extends Zikula_Form_AbstractHandler
{

    function initialize(Zikula_Form_View $view)
    {
        $view->caching = false;
        
        $view->assign($this->getVars());

        $editors = array(
            0 => array(
                    'value' => 'wikka100',
                    'text'  => 'WikkaEdit v.1.0.0'
                 ),
            1 => array(
                    'value' => 'wikka132',
                    'text'  => 'WikkaEdit v.1.3.2'
                 ),
            2 =>  array(
                    'value' => 'wikiedit',
                    'text'  => 'WikiEdit (old version)'
                 )
        );
        $view->assign('editors', $editors);
        $view->assign('editor', $this->getVar('editor', 'wikka100'));
        $view->assign('showIndex', $this->getVar('showIndex', false));
        
        
        $syntaxHighlighters = array(
            array('value' => 'none',              'text' => $this->__('None')),
            array('value' => 'prettify',          'text' => 'Google-Code-Prettify'),
            array('value' => 'geshi',             'text' => 'GeSHi'),
            array('value' => 'syntaxhighlighter', 'text' => 'SyntaxHighlighter'),
        );
        $view->assign('syntaxHighlighters', $syntaxHighlighters);
        
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url($this->name, 'admin', 'modifyconfig');
            return $view->redirect($url);
        }

        // Security check
        if (!SecurityUtil::checkPermission('Wikka::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }

        $ok = $view->isValid();
        $data = $view->getValues();

        $this->setVars($data);


        LogUtil::registerStatus($this->__('Done! Configuration has been updated'));

        return true;
    }
}