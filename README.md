Core
====

Core extension used in most Goodahead extensions. It provides some basic
functionality required by Goodahead extensions. Some of this functionality may
be used by extension developers.

## Features

### Easy CMS Page / CMS Block create/update feature

You can create ```cms.xml``` files inside your own extension ```etc``` dir with
CMS Page and CMS Block definitions.

#### Sample CMS Block definition
```xml
<config>
    <global>
        <goodahead>
            <core>
                <cms>
                    <block>
                        <block_name>
                            <version>0.0.1</version>
                            <store default="true"/>
                            <identifier>test</identifier>
                            <data>
                                <title><![CDATA[Default test block]]></title>
                                <content><![CDATA[This block is for default storeview]]></content>
                                <is_active>1</is_active>
                            </data>
                        </block_name>
                        <another_block>
                            <version>0.0.1</version>
                            <store>
                                <french />
                            </store>
                            <identifier>test</identifier>
                            <data>
                                <title><![CDATA[French Test block]]></title>
                                <is_active>1</is_active>
                                <content><![CDATA[Block with same identifier for french storeview]]></content>
                            </data>
                        </another_block>
                    </block>
                </cms>
            </core>
        </goodahead>
    </global>
</config>
```

#### Sample CMS Page definition

```xml
<config>
    <global>
        <goodahead>
            <core>
                <cms>
                    <page>
                        <page_name>
                            <version>0.0.1</version>
                            <store default="true"/>
                            <identifier>about-magento-demo-store-test</identifier>
                            <data>
                                <title><![CDATA[Sample page for default store]]></title>
                                <root_template>one_column</root_template>
                                <meta_keywords></meta_keywords>
                                <meta_description></meta_description>
                                <content_heading></content_heading>
                                <content><![CDATA[]]></content>
                                <is_active>1</is_active>
                                <sort_order>0</sort_order>
                                <layout_update_xml><![CDATA[]]></layout_update_xml>
                                <custom_theme></custom_theme>
                                <custom_root_template></custom_root_template>
                                <custom_layout_update_xml><![CDATA[]]></custom_layout_update_xml>
                                <custom_theme_from></custom_theme_from>
                                <custom_theme_to></custom_theme_to>
                            </data>
                        </page_name>
                        <another_page_name>
                            <version>0.0.1</version>
                            <store>
                                <german />
                            </store>
                            <identifier>test-cms-page-01</identifier>
                            <data>
                                <title><![CDATA[Anotehr sample page for german store]]></title>
                                <root_template>one_column</root_template>
                                <meta_keywords></meta_keywords>
                                <meta_description></meta_description>
                                <content_heading></content_heading>
                                <content><![CDATA[]]></content>
                                <is_active>1</is_active>
                                <sort_order>0</sort_order>
                                <layout_update_xml><![CDATA[]]></layout_update_xml>
                                <custom_theme></custom_theme>
                                <custom_root_template></custom_root_template>
                                <custom_layout_update_xml><![CDATA[]]></custom_layout_update_xml>
                                <custom_theme_from>now</custom_theme_from>
                                <custom_theme_to>m/d/yyyy</custom_theme_to>
                            </data>
                        </another_page_name>
                    </page>
                </cms>
            </core>
        </goodahead>
    </global>
</config>
```

By incrementing version number inside block definition you will indicate
that this particular block or page need to be updated. Any CMS Block or CMS Page
that was changed from magento admin panel will not be updated unless 
```force update mode``` is defined in update tracking table.

Currently there's no way to manage ```force update mode``` from admin panel. 
This functionality is planned for future extension releases.

Sample [```cms.xml```](Goodahead_Core/app/code/community/Goodahead/Core/etc/cms.xml)
is shipped together with this extension.

### Magento Version Helper functions

Goodaheda Core extension provides several helper functions to work with Magento
version/edition information. Following functions are available from
[```goodahead_core```](Goodahead_Core/app/code/community/Goodahead/Core/Helper/Data.php) helper:

* ```getMagentoEdition``` &mdash; returns current magento edition (Community /
Enterprise / Professional). Compatible with all magento versions starting from
1.4.1 (corresponding ```getEdition``` function in Mage class appeared only 
starting from 1.7+ Community edition and 1.12+ Enterprise edition).
* ```getMagentoCoreVersion``` &mdash; return corresponding Magento Core version
(Community Magento version number that correspond to current Magento version 
number) for Enterpise and Professional Magento editions. Not 100% accurate (yet)
for very old versions and for build numbers.

This functions available in static context as well.

### Setup Model with Compatibility functions for older Magento versions

Magento 1.4.1 introduce new library class ```Varien_Db_Ddl_Table``` and new
method ```createTable``` for ```Varien_Db_Adapter_Pdo_Mysql``` which allows
creating nice install / upgrade scripts using DDL abstraction layer.

Those methods were extended in 1.6 Magento version to provide some additional
functionality (like generating Index names, Constraint names, etc.).

Goodahead Core extension provide [```Goodahead_Core_Model_Resource_Setup```](Goodahead_Core/app/code/community/Goodahead/Core/Model/Resource/Setup.php) 
model which can be used as your setup model to support DDL install / upgrade 
scripts like this [install-1.0.0.php](Goodahead_Core/app/code/community/Goodahead/Core/sql/goodahead_core_setup/install-1.0.0.php) 
script.

Please note that some parts require additional verification (like shown in
[mysql4-install-1.0.0.php](Goodahead_Core/app/code/community/Goodahead/Core/sql/goodahead_core_setup/mysql4-install-1.0.0.php))
