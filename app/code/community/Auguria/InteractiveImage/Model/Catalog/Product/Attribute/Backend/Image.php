<?php
/**
 * @category   Auguria
 * @package    Auguria_Core
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_InteractiveImage_Model_Catalog_Product_Attribute_Backend_Image extends Auguria_Core_Model_Attribute_Backend_Image_Abstract
{
    /**
     * Default format/value for the attribute
     *
     * @var array
     */
    protected $_defaultValue = array('image_path' => '', 'areas' => array());

    /**
     * Save uploaded file
     *
     * @param Varien_Object $object
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value)) {
            if(!empty($value['delete'])){ // delete : save default value
                $object->setData($this->getAttribute()->getName(), Mage::helper('core')->jsonEncode($this->_defaultValue));
                $this->getAttribute()->getEntity()
                    ->saveAttribute($object, $this->getAttribute()->getName());
                return;
            }

            try {
                // upload image
                if(!isset($value['value']) || !file_exists($this->_getPathWithoutPrefix() . $value['value'])){
                    $uploader = new Mage_Core_Model_File_Uploader($this->getAttribute()->getName());
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($this->_getPath());

                    $value['image_path'] = $this->_getPrefix() . $result['file']; // update image path
                }else{
                    $value['image_path'] = $value['value'];
                }

                $object->setData($this->getAttribute()->getName(), Mage::helper('core')->jsonEncode($value));
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName()); // save attribute
            } catch (Exception $e) {
                if ($e->getCode() != Mage_Core_Model_File_Uploader::TMP_NAME_EMPTY) {
                    Mage::logException($e);
                }

                return;
            }
        }else{ // save default value
            $object->setData($this->getAttribute()->getName(), Mage::helper('core')->jsonEncode($this->_defaultValue));
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }
    }

    /**
     * Get path to save file
     *
     * @return string
     */
    protected function _getPath()
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . 'auguria' . DS . 'interactiveimage' . DS;
    }

    /**
     * Get prefix when save path file in attribute value
     *
     * @return string
     */
    protected function _getPrefix()
    {
        return 'auguria' . DS . 'interactiveimage' . DS;
    }

    /**
     * Get path without prefix
     *
     * @return string
     */
    protected function _getPathWithoutPrefix()
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS;
    }
}