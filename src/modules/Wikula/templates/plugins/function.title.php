<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @subpackage Wikula
 */

function smarty_function_title($params, &$smarty)
{
    
    $dom = ZLanguage::getModuleDomain('Wikula');
    $tag    = $params['tag'];
    $action = FormUtil::getPassedValue('func');
    $name   = ModUtil::getName();
    $info   = ModUtil::getInfoFromName($name);
        
    // hyphen2space
    $nicetag = str_replace('_', ' ', $tag);
    
    $output = array();
    $title  = array();
    $output[] = '<a href="'.ModUtil::url($name, 'user', 'main').'">'.$info['displayname'].'</a>';
    $title[]  = $info['displayname'];
    $title[]  = $nicetag;

    if($action == 'main') {
        $output[] = $nicetag;
    } else {
        $output[] = '<a href="'.ModUtil::url($name, 'user', 'main', array('tag' => $tag)).'">'.$nicetag.'</a>';
        if ( $action == 'history' ) {
            $output[] = __('history', $dom);
            $title[]  = __('history', $dom);
        } else if($action == 'edit') {
            $output[] = __('edit', $dom);
            $title[]  = __('edit', $dom);
        } else if($action == 'backlinks') {
            $output[] = __('backlinks', $dom);
            $title[]  = __('backlinks', $dom);
        }
    }
    
    PageUtil::setVar('title', implode($title, '::'));
    return '<h2>'.implode($output, ' &#187; ').'</h2>';
}
