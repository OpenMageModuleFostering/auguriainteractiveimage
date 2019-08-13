<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_InteractiveImage_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Get the full image path by attribute path saved
     *
     * @param string $attributeImagePath
     * @return string
     */
    public function getFullImagePath($attributeImagePath)
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . 'product' . DS . $attributeImagePath;
    }

    /**
     * Get the full image url by attribute path saved
     *
     * @param string $attributeImagePath
     * @return string
     */
    public function getFullImageUrl($attributeImagePath)
    {
        return Mage::getBaseUrl('media') . 'catalog/product/' . $attributeImagePath;
    }

}