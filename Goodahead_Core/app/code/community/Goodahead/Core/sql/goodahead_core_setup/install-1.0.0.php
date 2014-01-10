<?php
/**
 * This file is part of Goodahead_Core extension
 *
 * This extension is supplied with every Goodahead extension and provide common
 * features, used by Goodahead extensions.
 *
 * Copyright (C) 2013 Goodahead Ltd. (http://www.goodahead.com)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * and GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Goodahead
 * @package    Goodahead_Core
 * @copyright  Copyright (c) 2013 Goodahead Ltd. (http://www.goodahead.com)
 * @license    http://www.gnu.org/licenses/lgpl-3.0-standalone.html
 */

/**
 * This file will be used only by Magento version 1.6.x Community and higher
 * or 1.11.x Enterprise and higher.
 *
 * Due to presence of same mysql4-xxx file, this file will be used only as
 * fallback when connection/model (in configuration) is different from mysql4
 *
 * This file is required for newer versions of Magento which may use different
 * DB connection model (Oracle, etc.)
 */

/** @var $installer Goodahead_Core_Model_Resource_Setup */
$installer = $this;

/** @var $table Varien_Db_Ddl_Table */
$table = $installer->getConnection()
    ->newTable($installer->getTable('goodahead_core/cms_update'))
    ->addColumn(
        'item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Update Node ID')
    ->addColumn(
        'code',
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
        array(
            'nullable'  => false,
        ),
        'Update Node Code')
    ->addColumn(
        'item_type',
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
        array(
            'nullable'  => false,
        ),
        'Item type (block, page, etc.)')
    ->addColumn(
        'cms_item_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => true,
        ),
        'ID of updatable object')
    ->addColumn(
        'version',
        Varien_Db_Ddl_Table::TYPE_VARCHAR, 255,
        array(
            'nullable'  => false,
        ),
        'Current item version')
    ->addColumn(
        'has_local_changes',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'If referenced item was updated via standard admin interface')
    ->addColumn(
        'force_update',
        Varien_Db_Ddl_Table::TYPE_SMALLINT,
        null,
        array(
            'unsigned'  => true,
            'nullable'  => false,
        ),
        'Ignore local changes and update if version differs')
    ->addIndex(
        $installer->getIdxName(
            'goodahead_core/cms_update',
            array('code', 'item_type'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('code', 'item_type'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'goodahead_core/cms_update',
            array('cms_item_id')),
        array('cms_item_id'))
    ->setComment('CMS Block/Page update resource table');

$installer->getConnection()->createTable($table);
