<?php
/**
 * This file is part of Goodahead_Core extension
 *
 * This extension is supplied with every Goodahead extension and provide common
 * features, used by Goodahead extensions.
 *
 * Copyright (C) 2015 Goodahead Ltd. (http://www.goodahead.com)
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
 * @copyright  Copyright (c) 2015 Goodahead Ltd. (http://www.goodahead.com)
 * @license    http://www.gnu.org/licenses/lgpl-3.0-standalone.html
 */


class Goodahead_Core_Model_Email_Template
    extends Mage_Core_Model_Email_Template
{
    /**
     * Load default email template from locale translate
     *
     * @param string $templateId
     * @param string $locale
     */
    public function loadDefault($templateId, $locale=null)
    {
        Mage::dispatchEvent('goodahead_core_email_template_loadDefault_before', array('object' => $this, 'template_id' => $templateId));
        $result = parent::loadDefault($templateId, $locale);
        Mage::dispatchEvent('goodahead_core_email_template_loadDefault_after', array('object' => $this, 'template_id' => $templateId));
        return $result;
    }
    
    /**
     * Send mail to recipient
     *
     * @param   array|string       $email        E-mail(s)
     * @param   array|string|null  $name         receiver name(s)
     * @param   array              $variables    template variables
     * @return  boolean
     **/
    public function send($email, $name = null, array $variables = array())
    {
        Mage::dispatchEvent('goodahead_core_email_template_send_before', array('object' => $this, 'email' => $email, 'name' => $name, 'variables' => $variables));
        $result = parent::send($email, $name, $variables);
        Mage::dispatchEvent('goodahead_core_email_template_send_after', array('object' => $this, 'email' => $email, 'name' => $name, 'variables' => $variables));
        return $result;
    }
    
    /**
     * Send transactional email to recipient
     *
     * @param   int $templateId
     * @param   string|array $sender sender information, can be declared as part of config path
     * @param   string $email recipient email
     * @param   string $name recipient name
     * @param   array $vars variables which can be used in template
     * @param   int|null $storeId
     *
     * @throws Mage_Core_Exception
     *
     * @return  Mage_Core_Model_Email_Template
     */
    public function sendTransactional($templateId, $sender, $email, $name, $vars=array(), $storeId=null)
    {
        Mage::dispatchEvent('goodahead_core_email_template_sendTransactional_before', array('object' => $this, 
                                                                                            'template_id' => $templateId, 
                                                                                            'sender' => $sender, 
                                                                                            'email' => $email, 
                                                                                            'name' => $name, 
                                                                                            'variables' => $vars, 
                                                                                            'stoer_id' => $storeId)
                                                                                        );
        
        $result = parent::sendTransactional($templateId, $sender, $email, $name, $vars, $storeId);
        
        Mage::dispatchEvent('goodahead_core_email_template_sendTransactional_after', array('object' => $this, 
                                                                                           'template_id' => $templateId, 
                                                                                           'sender' => $sender, 
                                                                                           'email' => $email, 
                                                                                           'name' => $name, 
                                                                                           'variables' => $vars, 
                                                                                           'stoer_id' => $storeId)
                                                                                        );
        return $result;
    }
}