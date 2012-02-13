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
 * This class provides a handler to modify the user module settings.
 */
class Wikula_Handler_Settings  extends Zikula_Form_AbstractHandler
{
    /**
     * subscription
     *
     * User uid
     *
     * @var integer
     */
    private $_subscription;

    /**
     * Setup form.
     *
     * @param Zikula_Form_View $view Current Zikula_Form_View instance.
     *
     * @return boolean
     * 
     * @throws Zikula_Exception_Forbidden If the current user does not have adequate permissions to perform this function.
     */
    function initialize(Zikula_Form_View $view)
    {
        // Permission check
        if (!SecurityUtil::checkPermission('Wikula::', '::', ACCESS_READ) || !UserUtil::isLoggedIn()) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        $this->_subscription = $this->entityManager->find('Wikula_Entity_Subscriptions', UserUtil::getVar('uid'));
        if ($this->_subscription) {
            $view->assign('subscribe', true);
        } else {
            $view->assign('subscribe', false);
            $this->_subscription = new Wikula_Entity_Subscriptions();
        }
        
        $view->caching = false;
        
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
        if ($args['commandName'] == 'cancel') {
            $url = ModUtil::url('Wikula', 'user', 'settings' );
            return $view->redirect($url);
        }    
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        
        $data = $view->getValues();
        
        if ($data['subscribe']) {                
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
