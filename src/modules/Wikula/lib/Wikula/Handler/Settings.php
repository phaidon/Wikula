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

class Wikula_Handler_Settings  extends Zikula_Form_AbstractHandler
{

    function initialize(Zikula_Form_View $view)
    {
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) OR !UserUtil::isLoggedIn()) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        $subscription = Doctrine_Core::getTable('Wikula_Model_Subscriptions')->find(UserUtil::getVar('uid'));
        if ($subscription) {
            $subscribe = true;
        } else {
            $subscribe = false;
        }
        $this->view->assign('subscribe', $subscribe);
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
        
        $uid = UserUtil::getVar('uid');
        $subscription = Doctrine_Core::getTable('Wikula_Model_Subscriptions')->find($uid);        
        if($data['subscribe']) {
            if(!$subscription) {
                $values['uid'] = $uid;
                $sub = new Wikula_Model_Subscriptions();
                $sub->merge($values);
                $sub->save();
            }
        } else {
            if($subscription) {
                $subscription->delete();
            }
        }

        return true;
    }

}
