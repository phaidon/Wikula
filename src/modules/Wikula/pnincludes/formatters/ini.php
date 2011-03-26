<?php
/**
 * INI language file for Wikka highlighting (configuration file).
 */

$code = htmlspecialchars($code, ENT_QUOTES);

$code = preg_replace('/([=,\|]+)/m','<span style="color:#4400DD">\\1</span>',$code);
$code = preg_replace('/^([;#].+)$/m','<span style="color:#226622">\\1</span>',$code);
$code = preg_replace('/([^\d\w#;:>])([;#].+)$/m','<span style="color:#226622">\\2</span>',$code);
$code = preg_replace('/^(\[.*\])/m','<strong style="color:#AA0000;background:#EEE0CC">\\1</strong>',$code);
print '<pre>'.$code.'</pre>';
