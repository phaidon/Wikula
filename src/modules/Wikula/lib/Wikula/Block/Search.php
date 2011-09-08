<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

class Wikula_Block_Search extends Zikula_Controller_AbstractBlock
{

    /**
     * Initialise block
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Wikula:search:', 'Block title::');
    }

    /**
     * Get information on block
     * 
     * @return       array       The block information
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
     * Display block
     * 
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the rendered bock
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