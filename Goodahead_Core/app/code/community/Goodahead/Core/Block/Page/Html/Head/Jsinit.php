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

class Goodahead_Core_Block_Page_Html_Head_Jsinit
    extends Mage_Core_Block_Template
{
    /**
     * Container for added url params via layout or observers
     *
     * @var array
     */
    protected $_urlParams = array();

    /**
     * Method for adding/changing url param for core js module
     *
     * @param $name string
     * @param $value string
     */
    public function addUrlParam($name, $value)
    {
        if (trim($name)) {
            $this->_urlParams[$name] = $value;
        }
    }

    /**
     * Adding js with custom params with merge support.
     *
     * @return $this|Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (!Mage::registry('goodahead_js_initialized')) {
            $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
            $paramStr = '';
            if (count($this->_urlParams) && !$shouldMergeJs) {
                $paramStr = '?' . http_build_query($this->_urlParams);
            }

            /* @var $headBlock Mage_Page_Block_Html_Head */
            $headBlock = $this->getLayout()->getBlock('head');
            if ($headBlock) {
                /* @var $jsUrlParts Zend_Uri_Http */
                $jsUrlParts = Zend_Uri::factory($this->getJsUrl());
                $headBlock->addJs('goodahead/goodahead.js' . $paramStr, 'jsPath="'
                    . $jsUrlParts->getPath() . '" id="goodahead-js-init"');
            }
            Mage::register('goodahead_js_initialized', true);
        }
        return $this;
    }

}
