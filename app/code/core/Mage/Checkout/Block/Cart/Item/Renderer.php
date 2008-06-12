<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shopping cart item render block
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Item_Renderer extends Mage_Core_Block_Template
{
    protected $_item;

    /**
     * Set item for render
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Checkout_Block_Cart_Item_Renderer
     */
    public function setItem(Mage_Sales_Model_Quote_Item $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get item product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail');
    }

    /**
     * Get url to item product
     *
     * @return string
     */
    public function getProductUrl()
    {
        return $this->getProduct()->getProductUrl();
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    /**
     * Get product customize options
     *
     * @return array || false
     */
    public function getProductOptions()
    {
        $options = false;
        if ($optionIds = $this->getItem()->getOptionByCode('option_ids')) {
            $optionIds = explode(',', $optionIds->getValue());
            $options = array();
            foreach ($optionIds as $optionId) {
                if ($optionId) {
                    if ($option = $this->getProduct()->getOptionById($optionId)) {
                        $optionValue = '';
                        $optionGroup = $option->getGroupByType($option->getType());
                        if ($optionGroup == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
                            $optionValue = $option->getValueById(
                                $this->getItem()->getOptionByCode('option_'.$optionId)->getValue())->getTitle();
                        } else {
                            $optionValue =  $this->getItem()->getOptionByCode('option_'.$optionId)->getValue();
                        }
                        $options[] = array(
                            'label' => $option->getTitle(),
                            'value' => $optionValue
                        );
                    }
                }
            }
        }
        return $options;
    }

    /**
     * Get item delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl(
            'checkout/cart/delete',
            array(
                'id'=>$this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl()
            )
        );
    }

    /**
     * Get quote item qty
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->getItem()->getQty()*1;
    }

    /**
     * Check item is in stock
     *
     * @return bool
     */
    public function getIsInStock()
    {
        if ($this->getItem()->getProduct()->isSaleable()) {
            if ($this->getItem()->getProduct()->getQty()>=$this->getItem()->getQty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve item messages
     * Return array with keys
     *
     * type     => type of a message
     * text     => the message text
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        if ($this->getItem()->getMessage()) {
            $messages[] = array(
                'text'  => $this->getItem()->getMessage(),
                'type'  => $this->getItem()->getHasError() ? 'error' : 'notice'
            );
        }
        return $messages;
    }
}