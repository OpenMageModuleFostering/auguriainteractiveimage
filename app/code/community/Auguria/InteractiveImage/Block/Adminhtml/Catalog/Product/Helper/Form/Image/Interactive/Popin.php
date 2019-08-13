<?php
/**
* @category   Auguria
* @package    Auguria_InteractiveImage
* @author     Auguria
* @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
*/

class Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive_Popin extends Mage_Core_Block_Template
{
    /**
     * Get edition interface parameters
     *
     * @var null|array
     */
    protected $_params = null;

    /**
     * Initialise parameters
     *
     * @param array $params
     * @return Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive_Popin
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get parameters
     *
     * @return null|array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Get image properties
     *
     * @return array
     */
    public function getImage()
    {
        return array('url' => $this->_params['image_url'], 'size' => getimagesize($this->_params['image_url']));
    }

}