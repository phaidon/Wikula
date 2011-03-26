<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: table.php 107 2009-02-22 08:51:33Z mateo $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

/**
 * Create a HTML Table
 * 
 * @author Mateo Tibaquirá
 * @author Frank Chestnut
 * @author Wikka Dev Team
 * @param string $args['cells'] cells for the table
 * @param string $args['headers'] (optional) headers to use
 * @param string $args['style'] (optional) inline styles of the table
 * @param string $args['class'] (optional) CSS Class
 * @param string $args['columns'] (optional) number of columns, default = 1
 * @param string $args['cellpadding'] (optional) padding between cells
 * @param string $args['cellspacing'] (optional) spacing between cells
 * @param string $args['border'] (optional) border width
 * @param string $args['delimiter'] (optional) delimiter used in the cells and headers, default = ';'
 */
function wikula_actionapi_table($args)
{
    $cr = "\n";

    // Init
    $delimiter    = ';';
    $cells        = '';
    $headers      = '';
    $empty_cell   = '###';
    $columns      = 1;
    $class        = '';
    $style        = '';
    $cellpadding  = '';
    $cellspacing  = '';
    $border       = '';
    $row          = 1;

    // Input arguments
    if (isset($args['delimiter']) && !empty($args['delimiter'])) {
        $delimiter = $args['delimiter'];
    }
    if (isset($args['cells']) && !empty($args['cells'])) {
        $cells = explode($delimiter, $args['cells']);
    }
    if (isset($args['headers']) && !empty($args['headers'])) {
        $headers = explode($delimiter, $args['headers']);
    }
    if (isset($args['style']) && !empty($args['style'])) {
        $style = ' style="'.$args['style'].'"';
    }
    if (isset($args['class']) && !empty($args['class'])) {
        $class = ' class="'.$args['class'].'"';
    }
    if (isset($args['columns']) && !empty($args['columns'])) {
        $columns = $args['columns'];
    }
    if (isset($args['cellpadding']) && !empty($args['cellpadding'])) {
        $cellpadding = ' cellpadding="'.$args['cellpadding'].'"';
    }
    if (isset($args['cellspacing']) && !empty($args['cellspacing'])) {
        $cellspacing = ' cellspacing="'.$args['cellspacing'].'"';
    }
    if (isset($args['border']) && !empty($args['border'])) {
        $border = ' border="'.$args['border'].'"';
    }

    // initialize the output
    $output = '<table class="data" '.$class.$cellpadding.$cellspacing.$border.$style.'>'.$cr;

    if (is_array($headers)) {
        $output .= '  <thead>'.$cr.'<tr>'.$cr;
        foreach ($headers as $header) {
            $output .= '  <th>'.$header.'</th>'.$cr;
        }
        $output .= '</tr>'.$cr.'</thead>'.$cr.'<tbody>'.$cr;
    }

    foreach ($cells as $cell_item)
    {
        // begin the row if needed
        if ($row == 1) {
            $output .= '   <tr>'.$cr;
        }
        // empty cell check
        if ($cell_item == $empty_cell) {
            $cell_item = '&nbsp;';
        }
        // build the cell
        $output .= '       <td>'.$cell_item.'</td>'.$cr;
        // increment and check the columns number
        $row++;
        if ($row > $columns) {
            $output .= '   </tr>'.$cr;
            $row = 1;
        }
    }

    if (is_array($headers)) {
        $output .= '</tbody>'.$cr;
    }
    $output .= '</table>';

    return DataUtil::formatForDisplayHTML($output);
}
