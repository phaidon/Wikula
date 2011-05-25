<?php

/**
 * Copyright Wikula Team 2011
 *
 * This work is contributed to the Zikula Foundation under one or more
 * Contributor Agreements and licensed to You under the following license:
 *
 * @license GNU/GPLv3 (or at your option, any later version).
 * @package Piwik
 * @link http://code.zikula.org/wikula
 *
 * Please see the NOTICE file distributed with this source code for further
 * information regarding copyright and licensing.
 */

class Wikula_Model_Referrers extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('wikula_referrers');
        $this->hasColumn('page_tag', 'string', 75, array(
            'primary' => true,
            'notnull' => true,
            'default' => ""
        ));
        $this->hasColumn('referrer', 'string',  150, array(
            'primary' => true,
            'notnull' => true,
            'default' => ""
        ));
        $this->hasColumn('time',   'timestamp',  75, array(
            'notnull' => true,
            'default' => "0000-00-00 00:00:00"
        ));

   
    }
   


}