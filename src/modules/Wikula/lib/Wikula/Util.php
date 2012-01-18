<?php
/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 * @package Wikula
 * @license GNU/GPLv2 (or at your option, any later version).
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides some common functions.
 * 
 * @package Wikula
 */
class Wikula_Util extends Zikula_AbstractVersion
{
    
    /**
     * Wikula Default Module Settings
     * 
     * @return array An associated array with key=>value pairs of the default module settings
     */
    public static function getDefaultVars()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        
        $defaults = array(
            'root_page'               => __('HomePage', $dom),
            //'savewarning'           => (bool)$wikulainit['savewarning'],
            //'excludefromhistory'    => $wikulainit['root_page'],
            'modulestylesheet'        => 'style.css',
            'hideeditbar'             => false,
            'hidehistory'             => 20,
            'itemsperpage'            => 25,
            'langinstall'             => ZLanguage::getLanguageCode(),
            'double_doublequote_html' => 'safe',
            'geshi_tab_width'         => 4,
            'geshi_header'            => '',
            'geshi_line_numbers'      => '1',
            'grabcode_button'         => true,
            'subscription'            => false,
            'mandatorycomment'        => false,
            'single_page_permissions' => false
        );
    
        return $defaults;
    }

}
