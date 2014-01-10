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

class Goodahead_Core_Model_Observer
{
    public function cmsPageSaveAfter($observer)
    {
        try {
            $object = $observer->getEvent()->getDataObject();
            $updateObject = Mage::getModel('goodahead_core/cms_update');
            $updateObject->loadByObject($object);
            if ($updateObject->getId()) {
                $updateObject->setHasLocalChanges(1);
                $updateObject->save();
            }
        } catch (Exception $e) {}
    }

    public function cmsBlockSaveAfter($observer)
    {
        $object = $observer->getEvent()->getDataObject();

        if ($object instanceof Mage_Cms_Model_Block) {
            try {
                $updateObject = Mage::getModel('goodahead_core/cms_update');
                $updateObject->loadByObject($object);
                if ($updateObject->getId()) {
                    $updateObject->setHasLocalChanges(1);
                    $updateObject->save();
                }
            } catch (Exception $e) {}
        }
    }
}