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
 * A search block.
 */
class Wikula_Block_Search extends Zikula_Controller_AbstractBlock
{
    /**
     * Initialise block.
     *
     * @return void
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Wikula:search:', 'Block title::');
    }

    /**
     * Get information on block.
     *
     * @return array The block information
     */
    public function info()
    {
        return array(
            'text_type'       => $this->__('search'),
            'text_type_long'  => $this->__('Wikula Search Box'),
            'module'          => 'Wikula',
            'allow_multiple'  => true,
            'form_content'    => false,
            'form_refresh'    => false,
            'admin_tableless' => true,
            'show_preview'    => true
        );
    }

    /**
     * Display block.
     *
     * @param array $blockinfo A blockinfo structure.
     *
     * @return string|void The rendered block.
     */
    public function display($blockinfo)
    {
        if (!SecurityUtil::checkPermission('Wikula:searchblock', $blockinfo['title'].'::', ACCESS_READ)) {
            return false;
        }

        // Get variables from content block
        $vars = BlockUtil::varsFromContent($blockinfo['content']);


        // Check if the wikula module is available. 
        if (!ModUtil::available('Wikula')) {
            return false;
        }

        $content = $this->view->fetch('block/search.tpl');

        // Populate block info and pass to theme
        $blockinfo['content'] = $content;
        return BlockUtil::themeBlock($blockinfo);
    }

}
