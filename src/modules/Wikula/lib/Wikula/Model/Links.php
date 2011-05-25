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

class Wikula_Model_Links extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('wikula_links');
        $this->hasColumn('from_tag', 'string', 75, array(
            'primary' => true,
            'notnull' => true,
            'default' => ""
        ));
        $this->hasColumn('to_tag',   'string',  75, array(
            'primary' => true,
            'notnull' => true,
            'default' => ""
        ));
       

    }


}