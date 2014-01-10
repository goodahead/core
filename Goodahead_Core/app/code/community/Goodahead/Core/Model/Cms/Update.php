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


class Goodahead_Core_Model_Cms_Update extends Mage_Core_Model_Abstract
{

    const CMS_PAGE      = 'page';
    const CMS_BLOCK     = 'block';

    protected $_eventPrefix = 'goodahead_core_cms_update';

    protected function _construct()
    {
        $this->_init('goodahead_core/cms_update');
        parent::_construct();
    }

    public function loadByObject(Mage_Core_Model_Abstract $cmsObject)
    {
        if ($cmsObject instanceof Mage_Cms_Model_Page) {
            $itemType = self::CMS_PAGE;
        } elseif ($cmsObject instanceof Mage_Cms_Model_Block) {
            $itemType = self::CMS_BLOCK;
        } else {
            Mage::throwException('Unsupported CMS object type');
        }

        $this->setData('item_type', $itemType);
        $this->_beforeLoad($cmsObject->getId(), 'cms_item_id');
        $this->_getResource()->load($this, $cmsObject->getId(), 'cms_item_id');
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

}