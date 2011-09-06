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
     * initialise block
     * 
     * @author       The PostNuke Development Team
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Wikula:search:', 'Block title::');
    }

    /**
     * get information on block
     * 
     * @author       The PostNuke Development Team
     * @return       array       The block information
     */
    public function info()
    {
        return array(
            'text_type'      => 'search',
            'module'         => 'Wikula',
            'text_type_long' => 'Search box',
            'allow_multiple' => true,
            'form_content'   => false,
            'form_refresh'   => false,
            'show_preview'   => true
        );
    }

    /**
     * display block
     * 
     * @TODO: OPTIMIZE!
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the rendered bock
     */
    public function display($blockinfo)
    {
        if (!SecurityUtil::checkPermission('Wikula:searchblock', $blockinfo['title'].'::', ACCESS_READ)) {
            return;
        }

        // Get variables from content block
        $vars = BlockUtil::varsFromContent($blockinfo['content']);


        // Check if the wikula module is available. 
        if (!ModUtil::available('Wikula')) {
            return false;
        }



        $render = pnRender::getInstance('Wikula');

        $content = $render->fetch('block/search.tpl');

        // Populate block info and pass to theme
        $blockinfo['content'] = $content;
        return BlockUtil::themesideblock($blockinfo);
    }


}