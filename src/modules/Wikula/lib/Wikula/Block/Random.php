<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

class Wikula_Block_Random extends Zikula_Controller_AbstractBlock
{
    /**
     * Initialise block
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('Wikula:randomblock:', 'Block title::');
    }

    /**
     * Get information on block
     * 
     * @return       array       The block information
     */
    public function info()
    {
        return array('text_type'       => $this->__('random'),
                     'text_type_long'  => $this->__('Wikula Random Page'),
                     'module'          => 'Wikula',
                     'allow_multiple'  => true,
                     'form_content'    => false,
                     'form_refresh'    => false,
                     'admin_tableless' => true,
                     'show_preview'    => true);
    }

    /**
     * Display block
     * 
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the rendered bock
     */
    public function display($blockinfo)
    {
        if (!SecurityUtil::checkPermission('Wikula:randomblock', $blockinfo['title'].'::', ACCESS_READ)) {
            return false;
        }

        // Get variables from content block
        $vars = BlockUtil::varsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['chars'])) {
            $vars['chars'] = 120;
        }

        // Check if the wikula module is available. 
        if (!ModUtil::available('Wikula')) {
            return false;
        }

        // Add stylesheet and language
        PageUtil::AddVar('stylesheet', ThemeUtil::getModuleStylesheet('Wikula'));

        // get random article
        $pages = ModUtil::apiFunc($this->name, 'user', 'LoadAllPages');
        $id = rand(0,count($pages)-1);
        $page = $pages[$id];        

        if (empty($page)) {
            return false;
        }
        
        if (SecurityUtil::checkPermission('Wikula::', 'page::'.$page['tag'], ACCESS_COMMENT) ) {
            $canedit = true;
        } else {
            $canedit = false;
        }

        SessionUtil::setVar('wikula_previous', $page['tag']);
        $this->view->setCaching(false);
        $this->view->assign($page);
        $this->view->assign('canedit',  $canedit);

        $content = $this->view->fetch('block/random.tpl');

        // Populate block info and pass to theme
        $blockinfo['content'] = $content;
        return BlockUtil::themeBlock($blockinfo);
    }

    /**
     * Modify block settings
     * 
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the bock form
     */
    public function modify($blockinfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['chars'])) {
            $vars['chars'] = 120;
        }

        // Create output object

        // assign the approriate values
            $this->view->assign('chars', $vars['chars']);

        // Return the output that has been generated by this function
            return $this->view->fetch('block/random_modify.tpl');
    }

    /**
     * Update block settings
     * 
     * @param        array       $blockinfo     a blockinfo structure
     * @return       $blockinfo  the modified blockinfo structure
     */
    public function update($blockinfo)
    {
        // Get current content
        $vars = BlockUtil::varsFromContent($blockinfo['content']);

            // alter the corresponding variable
        $vars['chars'] = FormUtil::getPassedValue('chars');

            // write back the new contents
        $blockinfo['content'] = BlockUtil::varsToContent($vars);

        // clear the block cache
        $this->view->clear_cache('block/random.tpl');

        return $blockinfo;
    }
}