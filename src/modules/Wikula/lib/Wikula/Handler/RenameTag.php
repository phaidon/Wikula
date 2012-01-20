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
 * This class provides a handler to rename wiki pages.
 * 
 * @package Wikula
 */
class Wikula_Handler_RenameTag  extends Zikula_Form_AbstractHandler
{
    /**
     * tag.
     *
     * When set this handler is in edit mode.
     *
     * @var string
     */
    private $_tag;
    
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
        $this->_tag = FormUtil::getPassedValue('tag', null, "GET", FILTER_SANITIZE_STRING);
        
        // Permission check
        if (!ModUtil::apiFunc($modname, 'Permission', 'canModerate', $this->_tag)) {
            throw new Zikula_Exception_Forbidden(LogUtil::getErrorMsgPermission());
        }
        
        
        
        // redirect if tag contains spaces
        if (strpos($this->_tag, ' ') !== false) {
            $arguments = array(
                'tag'  => str_replace(' ', '_', $this->_tag),
            );
            $redirecturl = ModUtil::url($modname, 'user', 'show', $arguments);
            System::redirect($redirecturl);
        }
        
        // redirect if tag is a special page
        $specialPages = ModUtil::apiFunc($modname, 'SpecialPage', 'listpages');
        if( array_key_exists($this->_tag, $specialPages)) {
            return $view->redirect(ModUtil::url($modname, 'user', 'main', array('tag' => $this->_tag)));
        }
        
        
        // check if page exists
        if (!ModUtil::apiFunc($modname, 'user', 'PageExists', array('tag' => $this->_tag))) {
            return LogUtil::registerError(__("The page you requested doesn't exists"), null, ModUtil::url($modname, 'user', 'show'));
        }
        
        
        // build the output 
        $view->assign('tag', $this->_tag);
        $view->assign('to',  $this->_tag);
 
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
                'main',
                array('tag' => $this->_tag) 
            );
            return $view->redirect($url);
        }
        
        
        // check for valid form
        if (!$view->isValid()) {
            return false;
        }
        $data = $view->getValues();
        

        // Validate the choosen pagename
        if (!ModUtil::apiFunc($modname, 'user', 'isValidPagename', array('tag' => $data['to']))) {
            return LogUtil::registerError($this->__('That page name is not valid'));
        }
        
        // check if the page already exists
        if (ModUtil::apiFunc($modname, 'user', 'PageExists', array('tag' => $data['to']))) {
            return LogUtil::registerError($this->__('This page does already exist'));
        }

        // check if has access to create it
        if (!SecurityUtil::checkPermission('Wikula::', 'page::'.$this->_tag, ACCESS_EDIT)) {
            return LogUtil::registerError($this->__('You do not have the authorization to edit this page!'));
        }

       
        // rename page
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Pages', 'p')
           ->where('p.tag = :tag')
           ->setParameter('tag', $this->_tag)
           ->set('p.tag', "'".$data['to']."'");
        $query = $qb->getQuery();
        $query->execute();
        
        
        // rename links
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Links2', 'l')
           ->where('l.from_tag = :from_tag')
           ->setParameter('from_tag', $this->_tag)
           ->set('l.from_tag', "'".$data['to']."'");
        $query = $qb->getQuery();
        $query->execute();
        
        
        // rename categories
        $em = ServiceUtil::getService('doctrine.entitymanager');
        $qb = $em->createQueryBuilder();
        $qb->update('Wikula_Entity_Categories', 'c')
           ->where('c.tag = :tag')
           ->setParameter('tag', $this->_tag)
           ->set('c.tag', "'".$data['to']."'");
        $query = $qb->getQuery();
        $query->execute();
                

        LogUtil::registerStatus($this->__('Rename successfully'));
        $url = ModUtil::url($modname, 'user', 'show', array('tag' => $data['to']));
        return $view->redirect($url);

    }
}
