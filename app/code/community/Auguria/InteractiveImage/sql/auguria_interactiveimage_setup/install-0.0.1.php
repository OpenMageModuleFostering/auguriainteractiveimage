<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

/* @var $this Mage_Catalog_Model_Resource_Setup */

// Create new attribute group ( render attribute element correctly )
$entityTypeId = $this->getEntityTypeId('catalog_product');

// add group for each existing attribute set
$attributeSets = $this->_conn->fetchAll('select * from '.$this->getTable('eav/attribute_set').' where entity_type_id=?', $entityTypeId);
foreach ($attributeSets as $attributeSet) {
    $setId = $attributeSet['attribute_set_id'];
    $this->addAttributeGroup($entityTypeId, $setId, 'Interactive image');
}

// auguria_matrix_active : define if the matrix can be displayed on the product view page
$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'auguria_interactiveimage_image', array(
        'group'             => 'Interactive image',
        'type'              => 'text',
        'backend'           => 'auguria_interactiveimage/catalog_product_attribute_backend_image',
        'frontend'          => '',
        'label'             => 'Interactive image',
        'input'             => 'image',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'input_renderer'    => 'auguria_interactiveimage/adminhtml_catalog_product_helper_form_image_interactive',
        'visible_on_front'  => false,
        'used_in_product_listing' => true
));
