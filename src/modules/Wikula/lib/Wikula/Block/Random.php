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
     * initialise block
     * 
     * @author       The PostNuke Development Team
     */
    public function init()
    {
        // Security
        SecurityUtil::registerPermissionSchema('wikula:randomblock:', 'Block title::');
    }

    /**
     * get information on block
     * 
     * @author       The PostNuke Development Team
     * @return       array       The block information
     */
    public function info()
    {
        return array('text_type'      => 'random',
                     'module'         => 'wikula',
                     'text_type_long' => 'Show random wikula page',
                     'allow_multiple' => true,
                     'form_content'   => false,
                     'form_refresh'   => false,
                     'show_preview'   => true);
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
        if (!SecurityUtil::checkPermission('wikula:randomblock', $blockinfo['title'].'::', ACCESS_READ)) {
            return;
        }

        // Get variables from content block
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['chars'])) {
            $vars['chars'] = 120;
        }

        // Check if the wikula module is available. 
        if (!pnModAvailable('wikula')) return false;

            // Add stylesheet and language
            PageUtil::AddVar('stylesheet', ThemeUtil::getModuleStylesheet('wikula'));

            // get random article
            $pages = ModUtil::apiFunc('wikula', 'user', 'LoadAllPages');
            $id = rand(1,(count($pages)+1))-1;
            $page = $pages[$id];

            extract($page);

        if (SecurityUtil::checkPermission('wikula::', 'page::'.$tag, ACCESS_COMMENT) || $tag == __('SandBox', $dom)) {
            $canedit = true;
        } else {
            $canedit = false;
        }

        $render = pnRender::getInstance('wikula');
        if ($method != 'show') {
            $render->caching = false;
        }
        SessionUtil::setVar('wikula_previous', $tag);

        $render->assign('latest',   1);
        $render->assign('tag',      $tag);
        $render->assign('islogged', $islogged);
        $render->assign('canedit',  $canedit);
        $render->assign('body',     $page['body']);
        $render->assign('time',     $page['time']);
        $render->assign('user',     $page['user']);
        $render->assign('owner',    $page['owner']);
        $render->assign('hooks',    pnModCallHooks('item', 'display', $tag,
                                                   pnModURL('wikula', 'user', 'main',
                                                            array('tag' => $tag))));

        $content = $render->fetch('block/random.tpl', md5($page['id'].$page['time']));

        // Populate block info and pass to theme
        $blockinfo['content'] = $content;
        return themesideblock($blockinfo);
    }

    /**
     * modify block settings
     * 
     * @author       The PostNuke Development Team
     * @param        array       $blockinfo     a blockinfo structure
     * @return       output      the bock form
     */
    public function modify($blockinfo)
    {
        // Get current content
        $vars = pnBlockVarsFromContent($blockinfo['content']);

        // Defaults
        if (empty($vars['chars'])) {
            $vars['chars'] = 120;
        }

        // Create output object
            // As Admin output changes often, we do not want caching.
        $render = pnRender::getInstance('wikula', false);

        // assign the approriate values
            $render->assign('chars', $vars['chars']);

        // Return the output that has been generated by this function
            return $render->fetch('block/random_modify.tpl');
    }

    /**
     * update block settings
     * 
     * @param        array       $blockinfo     a blockinfo structure
     * @return       $blockinfo  the modified blockinfo structure
     */
    public function update($blockinfo)
    {
        // Get current content
        $vars = pnBlockVarsFromContent($blockinfo['content']);

            // alter the corresponding variable
        $vars['chars'] = FormUtil::getPassedValue('chars');

            // write back the new contents
        $blockinfo['content'] = pnBlockVarsToContent($vars);

        // clear the block cache
        $render = pnRender::getInstance('wikula');
        $render->clear_cache('block/random.tpl');

        return $blockinfo;
    }
}