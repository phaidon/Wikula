<?php
/**
 * Wikula
 *
 * @copyright  (c) Wikula Development Team
 * @link       http://code.zikula.org/wikula/
 * @version    $Id: pntables.php 154 2010-04-20 14:33:18Z gilles $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * category    Zikula_3rdParty_Modules
 * @subpackage Wiki
 * @subpackage Wikula
 */

function wikula_pntables()
{
    $tables = array();


    // wiki pages table
    $tables['wikula_pages'] = DBUtil::getLimitedTablename('wikula_pages');

    $tables['wikula_pages_column'] = array(
        'id'        => 'id',
        'tag'       => 'tag',
        'time'      => 'time',
        'body'      => 'body',
        'owner'     => 'owner',
        'user'      => 'user',
        'latest'    => 'latest',
        'note'      => 'note',
        'handler'   => 'handler'
    );

    $tables['wikula_pages_column_def'] = array(
        'id'        => 'I      NOTNULL AUTO PRIMARY',
        'tag'       => 'C(75)  NOTNULL DEFAULT ""',
        'time'      => 'T      NOTNULL DEFAULT "0000-00-00 00:00:00"',
        'body'      => 'X2     NOTNULL',
        'owner'     => 'C(75)  NOTNULL DEFAULT ""',
        'user'      => 'C(75)  NOTNULL DEFAULT ""',
        'latest'    => 'C(1)   NOTNULL DEFAULT "N"',
        'note'      => 'C(100) NOTNULL DEFAULT ""',
        'handler'   => 'C(30)  NOTNULL DEFAULT "page"'
    );


    // Links table
    $tables['wikula_links'] = DBUtil::getLimitedTablename('wikula_links');

    $tables['wikula_links_column'] = array(
        'from_tag'  => 'from_tag',
        'to_tag'    => 'to_tag'
    );

    $tables['wikula_links_column_def'] = array(
        'from_tag'  => 'C(75) NOTNULL DEFAULT ""',
        'to_tag'    => 'C(75) NOTNULL DEFAULT ""'
    );


    // Referrers table
    $tables['wikula_referrers'] = DBUtil::getLimitedTablename('wikula_referrers');

    $tables['wikula_referrers_column'] = array(
        'page_tag'  => 'page_tag',
        'referrer'  => 'referrer',
        'time'      => 'time'
    );

    $tables['wikula_referrers_column_def'] = array(
        'page_tag'  => 'C(75)  NOTNULL DEFAULT ""',
        'referrer'  => 'C(150) NOTNULL DEFAULT ""',
        'time'      => 'T      NOTNULL DEFAULT "0000-00-00 00:00:00"'
    );


    // Legacy tables definition
    $tables['pnwikka_pages']     = DBUtil::getLimitedTablename('pnwikka_pages');
    $tables['pnwikka_links']     = DBUtil::getLimitedTablename('pnwikka_links');
    $tables['pnwikka_referrers'] = DBUtil::getLimitedTablename('pnwikka_referrers');

    //Used for Data import from a standalone installation of wikka.
    $tables['wikka_pages']     = 'wikka_pages';
    $tables['wikka_pages_column'] = array(
        'id'        => 'id',
        'tag'       => 'tag',
        'time'      => 'time',
        'body'      => 'body',
        'owner'     => 'owner',
        'user'      => 'user',
        'latest'    => 'latest',
        'note'      => 'note',
        'handler'   => 'handler'
    );
    $tables['wikka_links']     = 'wikka_links';
    $tables['wikka_links_column'] = array(
        'from_tag'  => 'from_tag',
        'to_tag'    => 'to_tag'
    );

    // Return table information
    return $tables;
}
