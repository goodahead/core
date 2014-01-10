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

class Goodahead_Core_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Available Magento editions. Mage::EDITION_XXX constants are not used
     * here due to unavailability of such constants in Magento versions
     * prior to 1.7.x Community and 1.12.x Enterprise
     */
    const MAGENTO_EDITION_CE = 'Community';
    const MAGENTO_EDITION_EE = 'Enterprise';
    const MAGENTO_EDITION_PE = 'Professional';

    /**
     * Magent
     */
    const MAGENTO_EE_TO_CE_VERSION_DELTA = 5;

    /**
     * Current Magento edition
     *
     * @var string
     */
    static protected $_magentoEdition;

    /**
     * Current Magento edition
     *
     * @var string
     */
    static protected  $_magentoCoreVersion;


    /**
     * Return current Magento edition
     *
     * @return string
     */
    public static function getMagentoEdition()
    {
        if (!isset(self::$_magentoEdition)) {
            if (method_exists('Mage', 'getEdition')) {
                self::$_magentoEdition = Mage::getEdition();
            } else {
                if (Mage::getConfig()->getModuleConfig('Enterprise_Checkout')) {
                    self::$_magentoEdition = self::MAGENTO_EDITION_EE;
                } elseif (Mage::getConfig()->getModuleConfig('Enterprise_Enterprise')) {
                    self::$_magentoEdition = self::MAGENTO_EDITION_PE;
                } else {
                    self::$_magentoEdition = self::MAGENTO_EDITION_CE;
                }
            }
        }
        return self::$_magentoEdition;
    }

    /**
     * Return equivalent Magento Community version for current Magento version
     * (mostly for Enterprise editions).
     *
     * This not guaranteed to be 100% accurate and not guaranteed to work for
     * Enterprise versions prior to 1.9.x
     *
     * @return string
     */
    public static function getMagentoCoreVersion()
    {
        if (!isset(self::$_magentoCoreVersion)) {
            switch (self::getMagentoEdition()) {
                case self::MAGENTO_EDITION_PE:
                case self::MAGENTO_EDITION_EE:
                    $i = Mage::getVersionInfo();
                    // Line up to community version
                    $i['minor'] -= self::MAGENTO_EE_TO_CE_VERSION_DELTA;
                    self::$_magentoCoreVersion =
                        trim("{$i['major']}.{$i['minor']}.{$i['revision']}"
                                . ($i['patch'] != '' ? ".{$i['patch']}" : "")
                                . "-{$i['stability']}{$i['number']}", '.-');
                    break;
                default:
                    self::$_magentoCoreVersion = Mage::getVersion();
            }
        }
        return self::$_magentoCoreVersion;
    }

    public static function getConstValue($constName, $fallback = null)
    {
        try {
            if (defined($constName)) {
                return constant($constName);
            }
        } catch (Exception $e) {}
        return $fallback;
    }

}