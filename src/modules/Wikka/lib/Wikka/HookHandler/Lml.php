<?php
/**
 * Copyright Wikula Team 2011
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Wikka
 * @link https://github.com/phaidon/Wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

/**
 * Provides module hook handler.
 * 
 */
class Wikka_HookHandler_Lml extends Zikula_Hook_AbstractHandler
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
        $this->view = Zikula_View::getInstance("Wikka");
        $this->name = 'Wikka';
    }

    /**
     * Display hook for view.
     *
     * Subject is the object being viewed that we're attaching to.
     * args[id] is the id of the object.
     * args[caller] the module who notified of this event.
     *
     * @param Zikula_DisplayHook $hook Zikula display hook.
     *
     * @return void
     */
    public function ui_view(Zikula_DisplayHook $hook)
    {
        $textfieldname = $hook->getId();
        
        if (empty($textfieldname)) {
            $textfieldname = 'body';
        }
        
        $this->view->assign('textfieldname', $textfieldname)
                   ->assign('baseurl',       System::getBaseUrl());
        
        
        //if ($version == 'wikiedit') {
        //    $this->view->assign('maAvailable', ModUtil::available('MediaAttach'));
        //}
  
        $response = new Zikula_Response_DisplayHook('provider_area.ui.wikka.lml', $this->view, 'wikkaedit.tpl');
        $hook->setResponse($response);
    }

    /**
     * Filter hook for view
     *
     * Subject is the object being viewed that we're attaching to.
     * args[id] is the id of the object.
     * args[caller] the module who notified of this event.
     *
     * @param Zikula_FilterHook $hook Zikula filter hook.
     *
     * @return void
     */
    public static function filter(Zikula_FilterHook $hook)
    {
        $text = $hook->getData();        
        
        
        if ($hook->getCaller() == 'WikulaSaver') {
            $data = array();
            $data['links']      = self::getPageLinks($text);
            $data['categories'] = self::getPageCategories($text);
            $text = $data;
        } else {
            $text = ModUtil::apiFunc('Wikka', 'transform', 'transform', array(
                'text'   => $text,
                'modname' => $hook->getCaller())
            );
        }
        $hook->setData($text);
    }
    
    
    /**
     * This function extracts links from a given text
     *
     * @param string $text Text to check for links.
     *
     * @return array
     */
    private function getPageLinks($text)
    {
        $links = array();
        $pagelinks = array();
        preg_match_all("/\[\[(.*?)\]\]/", $text, $links);
        $links = $links[1];
        foreach ($links as $link) {
            $link = explode(' ', $link);
            // check if link is a hyperlink
            if (strstr($link[0], '://' ) || strstr($link[0], '@' ) ) {
                continue;
            }
            $pagelinks[] = $link[0];                 
        }
        return array_unique($pagelinks);
    }
    
    /**
     * This function extracts categories from a given text
     *
     * @param string $text Text to check for categories.
     *
     * @return array
     */
    private function getPageCategories($text)
    {
        $categories = array();        
        preg_match_all("/\n\[\[Category(.*?)\]\]/", $text, $categories);
        $categories = $categories[1];
        $categories2 = array();        
        preg_match_all("/\nCategory([a-zA-Z0-9]*+)/", $text, $categories2);
        $categories2 = $categories2[1];
        $categories = array_merge($categories, $categories2);
        
        foreach ($categories as $key => $value) {
            $value = explode(' ', $value);
            $value = $value[0];
            $categories[$key] = $value;
        }
        return array_unique($categories);
    }

}