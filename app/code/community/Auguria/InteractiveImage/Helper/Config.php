<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */

class Auguria_InteractiveImage_Helper_Config extends Mage_Core_Helper_Abstract {

    /* PATH TO CONFIGURATION */
    const XML_PATH_INTERACTIVE_IMAGE_USE_AJAX    = 'auguria_interactiveimage/general/use_ajax';

    /**
     * Check if ajax must be used
     *
     * @return bool
     */
    public function useAjax()
    {
        return Mage::helper('core')->isModuleEnabled('Auguria_CartAjax') && Mage::getStoreConfigFlag(self::XML_PATH_INTERACTIVE_IMAGE_USE_AJAX);
    }

}