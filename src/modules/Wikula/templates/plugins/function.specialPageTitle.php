<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       https://github.com/phaidon/Wikula/
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @subpackage Wikula
 */

function smarty_function_specialPageTitle($params, &$smarty)
{
    
    $dom = ZLanguage::getModuleDomain('Wikula');
    $tag    = $params['tag'];
    $name   = ModUtil::getName();
    $info   = ModUtil::getInfoFromName($name);
        
    // hyphen2space
    $nicetag = str_replace('_', ' ', $tag);
    
    $output = array();
    $title  = array();
    $output[] = '<a href="'.ModUtil::url($name, 'user', 'main').'">'.$info['displayname'].'</a>';
    $title[]  = $info['displayname'];
    $title[]  = __('Special pages', $dom);

    if($tag == __('Special_pages', $dom) ) {
        $output[] = __('Special pages', $dom);
    } else {
        $output[] = '<a href="'.
                    ModUtil::url($name, 'user', 'main', array('tag' => __('Special_pages', $dom) ) ).
                    '">'.
                    __('Special pages', $dom).
                    '</a>';
        $output[] = $nicetag;
        $title[]  = $nicetag;
    }
    
    PageUtil::setVar('title', implode($title, '::'));
    return '<h2>'.implode($output, ' &#187; ').'</h2>';   
}
