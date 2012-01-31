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
 * Access to (non-administrative) ajax-initiated actions for the Wikula module.
 * 
 * @package Wikula
 */
class Wikula_Controller_Ajax extends Zikula_AbstractController
{
    
    /**
     * Performs a user search based on the user name fragment entered so far.
     *
     * Available Request Parameters:
     * - fragment (string) A partial user name entered by the user.
     *
     * @return string Zikula_Response_Ajax_Plain with list of users matching the criteria.
     */
    public function search()
    {
        $view = Zikula_View::getInstance('Wikula');
        $phrase = FormUtil::getPassedValue('phrase'); 
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadPages',
                                   array('s' => $phrase));
        $view->assign('phrase', $phrase);
        $view->assign('pages', $pages);
        $output = $view->fetch('ajax/search.tpl');

        return new Zikula_Response_Ajax_Plain($output);
    }
    
    
}
