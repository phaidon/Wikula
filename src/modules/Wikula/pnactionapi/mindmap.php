<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: mindmap.php 127 2009-09-09 04:56:18Z drak $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Print a mindmap
 *
 * @author Frank Chestnut
 * @author Wikka Dev Team
 */
function wikula_actionapi_mindmap($args)
{
    $dom = ZLanguage::getModuleDomain('wikula');
    if (!isset($args['url']) || !empty($args['url'])) {
        return __('Invalid MindMap action syntax.<br />Proper usage: {{mindmap http://domain.com/MapName/mindmap.mm}} or {{mindmap url="http://domain.com/MapName/mindmap.mm"}}', $dom);
    }

    $height = (isset($args['height']) && !empty($args['height'])) ? $args['height'] : '550';

    $mindmap_url_fullscreen = 'modules/Wikula/pnincludes/freemind/fullscreen.php?url='.$args['url'];

    $output =
        '<script type="text/javascript" language="JavaScript">
         /* <![CDATA[ */
             if(!navigator.javaEnabled()) {
                 document.write(\'Please install <a href="http://www.java.com/">Java Runtime Environment</a> on your computer.\');
             }

         function popup(mylink, windowname)
         {
             if (!window.focus) return true;
             var href;
             if (typeof(mylink) == "string") {
                href=mylink;
             } else {
                href=mylink.href;
             }
             window.open(href, windowname, ",type=fullWindow,fullscreen,scrollbars=yes");
             return false;
         }
         /* ]]> */
         </script>

         <applet code="freemind.main.FreeMindApplet.class" archive="modules/Wikula/pnincludes/freemind/freemindbrowser.jar" width="100%" height="$height">
          <param name="type" value="application/x-java-applet;version=1.4" />
          <param name="scriptable" value="false" />
          <param name="modes" value="freemind.modes.browsemode.BrowseMode" />
          <param name="browsemode_initial_map" value="'.$args['url'].'" />
          <param name="initial_mode" value="Browse" />
          <param name="selection_method" value="selection_method_direct" />
         </applet>
         <br />
         <span class="floatr">
           '.__f('<a href="%1$s">Download this mind map</a> :: Use <a href="http://freemind.sourceforge.net/">Freemind</a> to edit it :: <a href="%2$s" onclick="return popup(this, \'fullmindmap\')">Open fullscreen</a>', array($args['url'], $mindmap_url_fullscreen)).'
         </span>
         <div style="clear: both;"></div>';

    return $output;
}
