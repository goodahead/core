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

class Goodahead_Core_Model_Resource_Setup
    extends Mage_Core_Model_Resource_Setup
{
    /**
     * XML CMS blocks definition file
     */
    const XML_CMS_BLOCK_SETUP_FILE       = 'cms.xml';

    /**
     * XML path to CMS update nodes
     */
    const XML_PATH_CMS              = 'global/goodahead/core/cms';

    /**
     * Apply data updates to the system after upgrading.
     *
     * @param string $fromVersion
     * @return Mage_Core_Model_Resource_Setup
     */
    public function applyDataUpdates()
    {
        parent::applyDataUpdates();

        $cmsConfig = new Varien_Simplexml_Config;
        $cmsConfig->loadString('<?xml version="1.0"?><config><global><goodahead><core><cms><block></block><page></page></cms></core></goodahead></global></config>');
        Mage::getConfig()->loadModulesConfiguration(
            self::XML_CMS_BLOCK_SETUP_FILE, $cmsConfig);

        $updatesCollection = Mage::getModel('goodahead_core/cms_update')->getCollection();

        $updatesApplied = array();

        foreach ($updatesCollection as $update) {
            $updatesApplied[$update->getItemType()][$update->getCode()] = $update;
        }

        foreach (array(
            Goodahead_Core_Model_Cms_Update::CMS_BLOCK,
            Goodahead_Core_Model_Cms_Update::CMS_PAGE,
        ) as $section) {
            $updateItems = $cmsConfig->getNode(self::XML_PATH_CMS . '/' . $section);
            if (isset($updateItems)) {
                /** @var $item Varien_Simplexml_Element */
                foreach ($updateItems->children() as $item) {
                    $currentVersion = (string)$item->version;
                    $updateItem = Mage::getModel('cms/' . $section);
                    $storeIds = false;
                    // Check store ID for block
                    if ($storeNode = $item->descend('store')) {
                        if ((bool)$storeNode->getAttribute('default')) {
                            $storeIds = array(Mage_Core_Model_App::ADMIN_STORE_ID);
                        } else {
                            try {
                                if (count($storeNode->children())) {
                                    $storeIds = array();
                                    foreach ($storeNode->children() as $childStore) {
                                        $storeIds[] = Mage::app()->getStore((string)$childStore->getName())->getId();
                                    }
                                } else {
                                    $storeIds = array(Mage::app()->getStore((string)$storeNode)->getId());
                                }
                            } catch (Exception $e) {
                                Mage::log(
                                    sprintf('Store %s not found, skipping update node', (string)$storeNode),
                                    Zend_Log::WARN,
                                    'goodahead_cms_update.log');
                                continue;
                            }
                        }
                    }

                    if (isset($updatesApplied[$section][$item->getName()])) {
                        $update = $updatesApplied[$section][$item->getName()];
                        // Ignore node if changes were already applied
                        if (version_compare($currentVersion, $update->getVersion(), '<=')) {
                            continue;
                        }
                        // Skip update if node was changed locally and no force_update flag was set
                        if ($update->getHasLocalChanges() && !$update->getForceUpdate()) {
                            continue;
                        }
                        $updateItem->load($update->getCmsItemId());
                    } else {
                        $update = Mage::getModel('goodahead_core/cms_update');
                        $update->setData('code', $item->getName());
                        $update->setData('item_type', $section);
                        if ($storeIds !== false && is_array($storeIds) && count($storeIds) == 1) {
                            $updateItem->setStoreId(reset($storeIds));
                        }
                        if (isset($item->identifier)) {
                            $updateItem->load((string)$item->identifier);
                            if ($updateItem->getId()) {
                                if (count(array_diff($updateItem->getStoreId(), $storeIds))) {
                                    $updateItem = Mage::getModel('cms/' . $section);
                                }
                            }
                            if (!$updateItem->getId()) {
                                $updateItem->setIdentifier((string)$item->identifier);
                            }
                        }
                        $updateItem->setStoreId($storeIds);
                        $updateItem->setStores($storeIds);
                    }

                    // All checks passed. Apply data updates
                    try {
                        foreach ($item->descend('data')->children() as $dataNode) {
                            $updateItem->setData($dataNode->getName(), (string)$dataNode);
                        }
                        $updateItem->save();

                        $update->setVersion($currentVersion);
                        $update->setCmsItemId($updateItem->getId());
                        $update->setHasLocalChanges(0);
                        $update->setForceUpdate(0);
                        $update->save();
                    } catch (Exception $e) {
                        Mage::log(
                            sprintf('CMS %s update apply failed with message %s', $section, $e->getMessage()),
                            Zend_Log::WARN,
                            'goodahead_cms_update.log');
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Redeclared for backwards compatibility with Magento versions prior to
     * 1.6.x Community and 1.11.x Enterprise
     *
     * @param string       $tableName
     * @param array|string $fields
     * @param string       $indexType
     *
     * @return mixed|string
     */
    public function getIdxName($tableName, $fields, $indexType = '')
    {
        if (method_exists(get_parent_class(__CLASS__), 'getIdxName')) {
            return parent::getIdxName($tableName, $fields, $indexType);
        } else {
            return Goodahead_Core_Model_Resource_Setup_Compatibility::getIndexName(
                $this->getTable($tableName),
                $fields,
                $indexType);
        }
    }

    /**
     * Redeclared for backwards compatibility with Magento versions prior to
     * 1.6.x Community and 1.11.x Enterprise
     *
     * @param string $priTableName
     * @param string $priColumnName
     * @param string $refTableName
     * @param string $refColumnName
     *
     * @return mixed|string
     */
    public function getFkName($priTableName, $priColumnName, $refTableName, $refColumnName)
    {
        if (method_exists(get_parent_class(__CLASS__), 'getFkName')) {
            return parent::getFkName($priTableName, $priColumnName, $refTableName, $refColumnName);
        } else {
            return Goodahead_Core_Model_Resource_Setup_Compatibility::getIndexName(
                $this->getTable($priTableName),
                $priColumnName,
                $this->getTable($refTableName),
                $refColumnName);
        }
    }

    public function createTable(Varien_Db_Ddl_Table $table)
    {
        Goodahead_Core_Model_Resource_Setup_Compatibility::createTable(
            $this, $table
        );
    }

}