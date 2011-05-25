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

class Wikula_Model_Pages extends Doctrine_Record
{
    /**
     * Set table definition.
     *
     * @return void
     */
    public function setTableDefinition()
    {
        $this->setTableName('wikula_pages');
        $this->hasColumn('id',      'integer', 16, array(
            'unique'  => true,
            'primary' => true,
            'notnull' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('tag',     'string',  75, array(
            'notnull' => true,
            'default' => ""
        ));
        $this->hasColumn('time',    'timestamp', array(
            'notnull' => true,
            'default' => '0000-00-00 00:00:00'
        ));
        $this->hasColumn('body',    'clob', array(
            'notnull' => true
        ));
        $this->hasColumn('owner',   'string',  75, array(
            'notnull' => true,
            'default' => ''
        ));       
        $this->hasColumn('user',    'string',  75, array(
            'notnull' => true,
            'default' => ''
        ));
        $this->hasColumn('latest',  'string',  1, array(
            'notnull' => true,
            'default' => 'N'
        ));
        $this->hasColumn('note',    'string',  100, array(
            'notnull' => true,
            'default' => ''
        ));
        $this->hasColumn('handler', 'string',  30, array(
            'notnull' => true,
            'default' => 'page'
        ));   
   
    }
    
    public function setUp() {
        $this->hasMany('Wikula_Model_Links', array(
            'local' => 'tag',
            'foreign' => 'to_tag',
            'onDelete' => 'CASCADE')
        );
    }

}