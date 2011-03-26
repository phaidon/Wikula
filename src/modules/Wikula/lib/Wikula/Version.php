<?php
/**
 * Wikula
 *
 * @copyright (c) Wikula Development Team
 * @link      http://code.zikula.org/wikula/
 * @version   $Id: pnversion.php 157 2010-04-20 21:35:31Z yokav $
 * @license   GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 */

class Wikula_Version extends Zikula_Version
{
    public function getMetaData()
    {
        $meta['name']           = 'Wikula';
        $meta['displayname']    = __('Wikula');
        $meta['oldnames']       = array('pnWikka', 'wikula');
        $meta['description']    = __('The Wikula module provides a wiki to your website.');
        $meta['url']            = __('wikula');
        $meta['version']        = '2.0.0';
        $meta['credits']        = 'docs/credits.txt';
        $meta['help']           = 'docs/install.txt';
        $meta['changelog']      = 'docs/changelog.txt';
        $meta['license']        = 'docs/license.txt';

        $meta['official']       = false;

        $meta['author']         = 'Fabian Würtz, Frank Chestnut, Chris Hildebrandt, Florian Schießl, Mateo Tibaquirá, Gilles Pilloud,';
        $meta['contact']        = 'http://code.zikula.org/wikula';

        $meta['securityschema'] = array('wikula::' => '::',
                                              'wikula::' => 'page::Page Tag');
        return $meta;
    }
}
