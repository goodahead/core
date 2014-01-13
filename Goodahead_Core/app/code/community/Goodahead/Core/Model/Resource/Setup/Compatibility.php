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
 * Class Goodahead_Core_Model_Resource_Setup_Compatibility
 *
 * This class is provided to solve Varien_Db_Ddl_Table and related classes
 * issues in Magento versions prior 1.6.x Community and 1.11.x Enterprise
 */
class Goodahead_Core_Model_Resource_Setup_Compatibility
{

    /**
     * Varien_Db_Adapter_Interface constants for backwards compatibility
     */
    const INDEX_TYPE_PRIMARY    = 'primary';
    const INDEX_TYPE_UNIQUE     = 'unique';
    const INDEX_TYPE_INDEX      = 'index';
    const INDEX_TYPE_FULLTEXT   = 'fulltext';

    /**
     * Varien_Db_Adapter_Pdo_Mysql constants for backwards compatibility
     */
    const LENGTH_TABLE_NAME     = 64;
    const LENGTH_INDEX_NAME     = 64;
    const LENGTH_FOREIGN_NAME   = 64;

    const TIMESTAMP_INIT_UPDATE = 'TIMESTAMP_INIT_UPDATE';
    const TIMESTAMP_INIT        = 'TIMESTAMP_INIT';
    const TIMESTAMP_UPDATE      = 'TIMESTAMP_UPDATE';

    /**
     * Varien_Db_Helper data for backwards compatibility
     * Dictionary for generate short name
     *
     * @var array
     */
    protected static $_translateMap = array(
        'address'       => 'addr',
        'admin'         => 'adm',
        'attribute'     => 'attr',
        'enterprise'    => 'ent',
        'catalog'       => 'cat',
        'category'      => 'ctgr',
        'customer'      => 'cstr',
        'notification'  => 'ntfc',
        'product'       => 'prd',
        'session'       => 'sess',
        'user'          => 'usr',
        'entity'        => 'entt',
        'datetime'      => 'dtime',
        'decimal'       => 'dec',
        'varchar'       => 'vchr',
        'index'         => 'idx',
        'compare'       => 'cmp',
        'bundle'        => 'bndl',
        'option'        => 'opt',
        'gallery'       => 'glr',
        'media'         => 'mda',
        'value'         => 'val',
        'link'          => 'lnk',
        'title'         => 'ttl',
        'super'         => 'spr',
        'label'         => 'lbl',
        'website'       => 'ws',
        'aggregat'      => 'aggr',
        'minimal'       => 'min',
        'inventory'     => 'inv',
        'status'        => 'sts',
        'agreement'     => 'agrt',
        'layout'        => 'lyt',
        'resource'      => 'res',
        'directory'     => 'dir',
        'downloadable'  => 'dl',
        'element'       => 'elm',
        'fieldset'      => 'fset',
        'checkout'      => 'chkt',
        'newsletter'    => 'nlttr',
        'shipping'      => 'shpp',
        'calculation'   => 'calc',
        'search'        => 'srch',
        'query'         => 'qr'
    );

    /**
     * Varien_Db_Adapter_Pdo_Mysql 1.6.x+ method for backwards compatibility
     *
     * Minus superfluous characters from hash.
     *
     * @param  $hash
     * @param  $prefix
     * @param  $maxCharacters
     * @return string
     */
    protected static function _minusSuperfluous($hash, $prefix, $maxCharacters)
    {
        $diff        = strlen($hash) + strlen($prefix) -  $maxCharacters;
        $superfluous = $diff / 2;
        $odd         = $diff % 2;
        $hash        = substr($hash, $superfluous, - ($superfluous + $odd));
        return $hash;
    }

    /**
     * Varien_Db_Adapter_Pdo_Mysql 1.6.x+ method for backwards compatibility
     *
     * Retrieve valid index name
     * Check index name length and allowed symbols
     *
     * @param string $tableName
     * @param string|array $fields  the columns list
     * @param string $indexType
     * @return string
     */
    public static function getIndexName($tableName, $fields, $indexType = '')
    {
        if (is_array($fields)) {
            $fields = implode('_', $fields);
        }

        switch (strtolower($indexType)) {
            case self::INDEX_TYPE_UNIQUE:
                $prefix = 'unq_';
                $shortPrefix = 'u_';
                break;
            case self::INDEX_TYPE_FULLTEXT:
                $prefix = 'fti_';
                $shortPrefix = 'f_';
                break;
            case self::INDEX_TYPE_INDEX:
            default:
                $prefix = 'idx_';
                $shortPrefix = 'i_';
        }

        $hash = $tableName . '_' . $fields;

        if (strlen($hash) + strlen($prefix) > self::LENGTH_INDEX_NAME) {
            $short = self::shortName($prefix . $hash);
            if (strlen($short) > self::LENGTH_INDEX_NAME) {
                $hash = md5($hash);
                if (strlen($hash) + strlen($shortPrefix) > self::LENGTH_INDEX_NAME) {
                    $hash = self::_minusSuperfluous($hash, $shortPrefix, self::LENGTH_INDEX_NAME);
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
        }

        return strtoupper($hash);
    }

    /**
     * Varien_Db_Adapter_Pdo_Mysql 1.6.x+ method for backwards compatibility
     *
     * Retrieve valid foreign key name
     * Check foreign key name length and allowed symbols
     *
     * @param string $priTableName
     * @param string $priColumnName
     * @param string $refTableName
     * @param string $refColumnName
     * @return string
     */
    public static function getForeignKeyName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        $prefix = 'fk_';
        $hash = sprintf('%s_%s_%s_%s', $priTableName, $priColumnName, $refTableName, $refColumnName);
        if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
            $short = self::shortName($prefix.$hash);
            if (strlen($short) > self::LENGTH_FOREIGN_NAME) {
                $hash = md5($hash);
                if (strlen($prefix.$hash) > self::LENGTH_FOREIGN_NAME) {
                    $hash = self::_minusSuperfluous($hash, $prefix, self::LENGTH_FOREIGN_NAME);
                } else {
                    $hash = $prefix . $hash;
                }
            } else {
                $hash = $short;
            }
        } else {
            $hash = $prefix . $hash;
        }

        return strtoupper($hash);
    }

    /**
     * Varien_Db_Helper 1.6.x+ method for backwards compatibility
     *
     * Convert name using dictionary
     *
     * @param string $name
     * @return string
     */
    public static function shortName($name)
    {
        return strtr($name, self::$_translateMap);
    }

    /**
     * Varien_Db_Helper 1.6.x+ method for backwards compatibility
     *
     * Add or replace translate to dictionary
     *
     * @param string $from
     * @param string $to
     */
    public static function addTranslate($from, $to)
    {
        self::$_translateMap[$from] = $to;
    }

    /**
     * Using this method you can eliminate inconsistancy between default values
     * for TIMESTAMP field in different magento versions
     *
     *  * in 1.4 -- 1.5 it was used as regular string / Zend_Db_Expr
     *  * in 1.6+ there are predefined values for this field and it defaulted
     *    to 0 in case if one of predefined values is used
     *
     * By applying this function to 'DEFAULT' argument for timestamp column and
     * using one of the predefined constants you can be sure that result you
     * receive will result in same column default value generation by
     * Varien_Db_Adapter_Pdo_* no matter what Magento version is used
     *
     * @param $value mixed Default value definition for column
     *
     * @return string|Zend_Db_Expr|mixed Default value definition for column adapted to current Magento version
     */
    public static function getTimestampColumnDefaultValue($value)
    {
        if (is_string($value)) {
            try {
                $constName = 'Varien_Db_Ddl_Table::' . $value;
                if (defined($constName)) {
                    return constant($constName);
                }
            } catch (Exception $e) {}
        }

        switch ($value) {
            case self::TIMESTAMP_INIT:
                return new Zend_Db_Expr('CURRENT_TIMESTAMP');
            case self::TIMESTAMP_UPDATE:
                return new Zend_Db_Expr('0 ON UPDATE CURRENT_TIMESTAMP');
            case self::TIMESTAMP_INIT_UPDATE:
                return new Zend_Db_Expr('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
            default:
                return $value;
        }
    }

}
