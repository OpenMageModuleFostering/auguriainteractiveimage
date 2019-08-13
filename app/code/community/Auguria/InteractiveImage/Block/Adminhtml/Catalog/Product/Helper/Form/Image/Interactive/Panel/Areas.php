<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive_Panel_Areas extends Mage_Core_Block_Template
{
    protected $_areas;

    protected $_areaRenders = array();

    public function __construct()
    {
        parent::__construct();
        $this->addAreaRenderer(
                'default',
                'auguria_interactiveimage/adminhtml_catalog_product_helper_form_image_interactive_panel_area_default',
                'auguria/interactiveimage/catalog/product/helper/form/image/interactive/panel/area/default.phtml'
        );
    }

    /**
     * Retrieve area
     *
     * @return array
     */
    public function getAreas()
    {
        return $this->_areas;
    }

    /**
     * Set area
     *
     * @param array $area
     * @return Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive_Panel_Areas
     */
    public function setAreas(array $areas = null)
    {
        $this->_areas = $areas;
        return $this;
    }

    /**
     * Add area renderer to renderers array
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return Auguria_InteractiveImage_Block_Adminhtml_Catalog_Product_Helper_Form_Image_Interactive_Panel_Areas
     */
    public function addAreaRenderer($type, $block, $template)
    {
        $this->_areaRenders[$type] = array(
                'block' => $block,
                'template' => $template,
                'renderer' => null
        );
        return $this;
    }

    /**
     * Get area render by given type
     *
     * @param string $type
     * @return array
     */
    public function getAreaRender($type)
    {
        if (isset($this->_areaRenders[$type])) {
            return $this->_areaRenders[$type];
        }

        return $this->_areaRenders['default'];
    }

    /**
     * Get option html block
     *
     * @param array $area
     */
    public function getAreaHtml(array $area)
    {
        $renderer = $this->_areaRenders[$area['render']];

        if (is_null($renderer['renderer'])) {
            $renderer['renderer'] = $this->getLayout()->createBlock($renderer['block'])
            ->setTemplate($renderer['template']);
        }

        return $renderer['renderer']
            ->setInteractiveArea($area) // "area" reserved name
            ->toHtml();
    }
}