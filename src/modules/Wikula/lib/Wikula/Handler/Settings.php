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

class Wikula_Handler_Settings  extends Zikula_Form_AbstractHandler
{
    private $_subscription;

    function initialize(Zikula_Form_View $view)
    {
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) OR !UserUtil::isLoggedIn()) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        $this->_subscription = $this->entityManager->find('Wikula_Entity_Subscriptions', UserUtil::getVar('uid'));
        if ($this->_subscription) {
            $this->view->assign('subscribe', true);
        } else {
            $this->view->assign('subscribe', false);
            $this->_subscription = new Wikula_Entity_Subscriptions();
        }
        
        $this->view->caching = false;
        
        return true;
    }


    function handleCommand(Zikula_Form_View $view, &$args)
    {
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url('Wikula', 'user', 'settings' );
            return $view->redirect($url);
        }    
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();
        
        if($data['subscribe']) {                
            $values['uid'] = UserUtil::getVar('uid');;
             $this->_subscription->merge($values);
             $this->entityManager->persist($this->_subscription);
        } else {
            $this->entityManager->remove($this->_subscription);
        }

        $this->entityManager->flush();
        return true;
    }

}
