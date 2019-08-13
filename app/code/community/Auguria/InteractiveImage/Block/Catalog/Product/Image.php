<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_InteractiveImage_Block_Catalog_Product_Image extends Mage_Core_Block_Template
{
    protected $_imageUrl;
    protected $_imageSize;
    protected $_areas;
    protected $_products;
    protected $_productsBySku;
    protected $_positions;

    /**
     * Get image url
     *
     * @return string
     */
    public function getInteractiveImage()
    {
        if(!$this->_imageUrl){
            $values = Mage::helper('core')->jsonDecode($this->getProduct()->getData('auguria_interactiveimage_image'));
            // image
            if(!empty($values['image_path'])){
                $this->_imageUrl = Mage::getSingleton('catalog/product_media_config')->getBaseMediaUrl() . '/' . $values['image_path'];

                $sizes = getimagesize($this->_imageUrl);
                $this->_imageSize = array('width' => $sizes[0], 'height' => $sizes[1]);
            }

            // areas
            if(!empty($values['areas'])){
                $this->_areas = Mage::helper('core')->jsonDecode($values['areas']);
            }
        }

        return $this->_imageUrl;
    }

    /**
     * Get image size
     *
     * @return null|array
     */
    public function getInteractiveImageSize()
    {
        return $this->_imageSize;
    }

    /**
     * Get areas
     *
     * @return array
     */
    public function getAreas()
    {
        return $this->_areas;
    }

    /**
     * Retrieve current product model
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * Get area coords
     *
     * @param array $area
     * @return array
     */
    public function getCoords($area)
    {
        $coords = array();

        if($area['type'] == 'rect'){
            $x2 = $area['size']['x'] + $area['size']['width'];
            $y2 = $area['size']['y'] + $area['size']['height'];
            $coords['area'] = $area['size']['x'] . ',' . $area['size']['y'] . ',' . $x2 . ',' . $y2;
            $coords['tooltip'] = array('top' => -($this->_imageSize['height'] - $area['size']['y']), 'left' => $x2 + 10);
        }

        return $coords;
    }

    /**
     * Get product associated with areas
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProducts()
    {
        if(!$this->_products){
            $ids = array();

            $product = Mage::getSingleton('catalog/product');
            if($this->_areas){
                foreach ($this->_areas as $_area){
                    $ids[] = $product->getIdBySku($_area['sku']);
                    $this->_positions[$_area['sku']] = $_area['position'];
                }
            }

            $this->_products = Mage::getResourceModel('catalog/product_collection')
                                    ->addAttributeToSelect(array('name', 'sku'))
                                    ->addFieldToFilter('entity_id', array('in' => $ids))
                                    ->addFinalPrice()
                                    ->addStoreFilter(Mage::app()->getStore());

            // get easily product by without reload them
            foreach ($this->_products as $_product){
              $this->_productsBySku[$_product->getSku()] = $_product;
            }
        }

        return $this->_products;
    }

    /**
     * Get product by sku
     *
     * @param string $sku
     * @return Mage_Catalog_Model_Product
     */
    public function getProductBySku($sku)
    {
        return is_array($this->_productsBySku) && array_key_exists($sku, $this->_productsBySku) ? $this->_productsBySku[$sku] : null;
    }

    /**
     * Get product position by sku
     *
     * @param string $sku
     */
    public function getPositionBySku($sku)
    {
        return $this->_positions[$sku];
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param string $sku
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($sku, $additional = array())
    {
        if ($this->getRequest()->getParam('wishlist_next')){
            $additional['wishlist_next'] = 1;
        }

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        // no product load
        $product = Mage::getModel('catalog/product');
        $id = Mage::getModel('catalog/product')->getIdBySku($sku);
        $product->setId($id);

        return $this->helper('checkout/cart')->getAddUrl($product, $additional);
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @return string
     */
    public function getMassAddToCartUrl()
    {
        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);

        return Mage::getUrl('image/checkout_cart/add', $additional);
    }
}