<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: rss.php 152 2010-04-20 14:16:09Z yokav $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print a RSS Feed.
 * This action uses SimplePie available through the Feeds module.
 *
 * Usage:
 * {{rss http://domain.com/feed.xml}} or {{rss url="http://domain.com/feed.xml" cachetime="30"}}
 *
 * @author Mateo Tibaquirá
 * @author Wikka Dev Team
 * @param string $args['url'] URL of the feed
 * @param string $args['name'] (optional) Name of the feed
 * @param string $args['showimage'] (optional) Flag to show or not the image of the feed
 * @param string $args['openinnewwindow'] (optional) Flag to open the feed links in a new window
 * @param string $args['cachetime'] (optional) cache time interval
 * @return feed output
 */
function wikula_actionapi_rss($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!isset($args['url']) && !empty($args['url'])) {
        return LogUtil::registerArgsError();
    }

    // If the Feeds module is not available return the error
    if (!pnModAvailable('Feeds')) {
        return LogUtil::registerError(__('Sorry! The <strong>Feeds</strong> module is not available.', $dom));
    }

    // Defaults
    $name = (isset($args['name']) && !empty($args['name'])) ? $args['name'] : '';
    $show = (isset($args['showimage']) && $args['showimage']) ? 1 : 0;
    $new  = (isset($args['openinnewwindow']) && $args['openinnewwindow']) ? 1 : 0;


    // Check out if the feed is cached
    $render = pnRender::getInstance('wikula');
    $render->cache_id = md5($args['url']);
    if ($render->is_cached('action/rss.tpl')) {
       return $render->fetch('action/rss.tpl');
    }

    $rssfeed = array();

    // fetch the feed
    $feed = ModUtil::apiFunc('Feeds', 'user', 'getfeed',
                         array('furl' => $args['url']));

    // assign the output variables
    $render->assign('feed',       $feed);
    $render->assign('feedname',   $name);
    $render->assign('feedimage',  $show);
    $render->assign('feedurl',    $args['url']);
    $render->assign('feednewwin', $new);

    // return the action output
    return $render->fetch('action/rss.tpl');
}
