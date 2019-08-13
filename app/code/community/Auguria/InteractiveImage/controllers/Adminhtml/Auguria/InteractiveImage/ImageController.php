<?php
/**
 * @category   Auguria
 * @package    Auguria_InteractiveImage
 * @author     Auguria
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3 (GPLv3)
 */
class Auguria_InteractiveImage_Adminhtml_Auguria_InteractiveImage_ImageController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Additional initialization
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Auguria_InteractiveImage');
    }

    /**
     * Display popin and canvas with image background and image dimensions
     */
    public function displayAction()
    {
        $response = array('error' => false, 'messages' => array(), 'content' => '');
        $params = Mage::helper('core')->jsonDecode($this->getRequest()->getParam('interactive_image', ''));

        $fullImagePath = Mage::helper('auguria_interactiveimage')->getFullImagePath($params['image_path']);
        $fullImageUrl = Mage::helper('auguria_interactiveimage')->getFullImageUrl($params['image_path']);

        try {
            if(file_exists($fullImagePath)){
                $this->loadLayout(); // init layout messages

                if(!is_array($params['areas'])){
                    $params['areas'] = Mage::helper('core')->jsonDecode($params['areas']);
                }

                $params['image_url'] = $fullImageUrl; // add full image path to params
                $response['content'] = $this->getLayout()->getBlock('interactive.image.popin')->setParams($params)->toHtml();
            }else{
                Mage::throwException($this->__('Image not found.'));
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $this->_getSession()->addException($e, $this->__('Error while loading edition interface.'));
        }

        if($response['error']){
            // if success messages called in template
            $response['messages'][Mage_Core_Model_Message::ERROR] = $this->_getSession()->getMessages(true)->getItemsByType(Mage_Core_Model_Message::ERROR); // clear messages
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Get Panel Tab html
     */
    public function createPanelTabAction()
    {
        $response = array('error' => false, 'messages' => array(), 'content' => '');
        $area = Mage::helper('core')->jsonDecode($this->getRequest()->getParam('area', ''));

        try {
            if($area){
                $this->loadLayout(); // init layout messages

                $response['content'] = $this->getLayout()->getBlock('interactive.image.panel.areas')->getAreaHtml($area);
            }else{
                Mage::throwException($this->__('Area does not exixsts.'));
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $this->_getSession()->addException($e, $this->__('Error while adding area.'));
        }

        if($response['error']){
            // if success messages called in template
            $response['messages'][Mage_Core_Model_Message::ERROR] = $this->_getSession()->getMessages(true)->getItemsByType(Mage_Core_Model_Message::ERROR); // clear messages
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    /**
     * Save form values
     */
    public function saveAction()
    {
        $response = array('error' => false, 'messages' => array(), 'content' => '');
        $areas = $this->getRequest()->getParam('areas', null);

        try {
            if($areas){
                $response['content'] = Mage::helper('core')->jsonEncode($areas);
            }else{
                Mage::throwException($this->__('Areas does not exixsts.'));
            }
        } catch (Exception $e) {
            $response['error'] = true;
            $this->_getSession()->addException($e, $this->__('Error while saving areas.'));
        }

        if($response['error']){
            // if success messages called in template
            $response['messages'][Mage_Core_Model_Message::ERROR] = $this->_getSession()->getMessages(true)->getItemsByType(Mage_Core_Model_Message::ERROR); // clear messages
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }
}