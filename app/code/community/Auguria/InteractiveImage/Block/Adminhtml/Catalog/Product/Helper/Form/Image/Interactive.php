<?php
/**
* @category   Auguria
* @package    Auguria_InteractiveImage
* @author     Auguria
* @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
*/

class Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Image
{
    /**
     * Override getValue method
     *
     * @return string : path of the image
     */
    public function getValue()
    {
        $array = $this->getJsonValue();

        return $array['image_path'];
    }

    /**
     * Get json value
     *
     * @param bool $asArray
     * @return mixed|array|string
     */
    public function getJsonValue($asArray = true)
    {
        $string = $this->getData('value');

        if($asArray){
            return Mage::helper('core')->jsonDecode($string);
        }

        return $string;
    }

    /**
     * Get areas value
     *
     * @param bool $asArray
     * @return mixed|array|string
     */
    public function getAreas($asArray = true)
    {
        $attributeValue = $this->getJsonValue();
        $areas = is_array($attributeValue) && array_key_exists('areas', $attributeValue) ? $attributeValue['areas'] : array();

        if($asArray){
            return $areas;
        }else{
            return Mage::helper('core')->jsonEncode($areas);
        }
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $areas = $this->getAreas(false); // json encoded
        $imageUrl = $this->_getUrl();

        $params = $this->getJsonValue(false); // json encoded
        $parameters = $params ? $params : '\'\'';

        $html .= '
            <div class="clear"></div>
            <input type="hidden" id="' . $this->getName() . '_areas" name="product['. $this->getName() . '][areas]" value="' . $areas . '"/>
            <script type="text/javascript">var interactiveImage = new InteractiveImage(\'' . $imageUrl . '\', ' . $parameters . ');</script>
            <button type="button" onclick="interactiveImage.display();return false;">' . Mage::helper('auguria_interactiveimage')->__('Define areas') . '</button>
        ';

        return $html;
    }
}