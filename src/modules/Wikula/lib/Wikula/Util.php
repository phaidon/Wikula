<?php

class Wikula_Util
{
    /**
     * Wikula Default Module Settings
     * @author Fabian Wuertz
     * @return array An associated array with key=>value pairs of the default module settings
     */
    public static function getDefaultVars()
    {
        $dom = ZLanguage::getModuleDomain('Wikula');
        
        $defaults = array(
            'root_page'               => __('HomePage', $dom),
            //'savewarning'             => (bool)$wikulainit['savewarning'],
            //'excludefromhistory'      => $wikulainit['root_page'],
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
        

} // end class def