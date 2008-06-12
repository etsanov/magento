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
 * @category   Mage
 * @package    Mage_Giftcert
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Giftcert_Model_Entity_Quote_Address_Attribute_Backend_Giftcert
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $gift = Mage::getResourceModel('sales/giftcert')->getGiftcertByCode($address->getGiftcertCode());
        if ($gift) {
            $address->setGiftcertAmount(min($address->getGrandTotal(), $gift['balance_amount']));
        } else {
            $address->setGiftcertAmount(0);
        }
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getGiftcertAmount());
        
        return $this;
    }

}