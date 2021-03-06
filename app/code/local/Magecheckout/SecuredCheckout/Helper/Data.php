<?php

/**
 * Magecheckout
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magecheckout.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magecheckout.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magecheckout
 * @package     Magecheckout_SecuredCheckout
 * @copyright   Copyright (c) 2014 Magecheckout (http://www.magecheckout.com/)
 * @license     http://www.magecheckout.com/license-agreement.html
 */
class Magecheckout_SecuredCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{

    const BLOCK_NUMBER_STORAGE_KEY = 'mc-osc-block-number';
    protected $currentNumber = '';

    public function isCustomerMustBeLogged()
    {
        $helper = Mage::helper('checkout');
        if (method_exists($helper, 'isCustomerMustBeLogged')) {
            return $helper->isCustomerMustBeLogged();
        }

        return false;
    }

    public function getGrandTotal($quote)
    {
        $grandTotal = $quote->getGrandTotal();

        return Mage::app()->getStore()->getCurrentCurrency()->format($grandTotal, array(), false);
    }
    /*
     *Customer attribute
     *
    */
    /**
     * Return available customer attribute form as select options
     *
     * @throws Mage_Core_Exception
     */
    public function getAttributeFormOptions()
    {
        Mage::throwException(Mage::helper('securedcheckout')->__('Use helper with defined EAV entity'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws Mage_Core_Exception
     */
    protected function _getEntityTypeCode()
    {
        Mage::throwException(Mage::helper('securedcheckout')->__('Use helper with defined EAV entity'));
    }

    public function getAttributeFrontendLabel($attribute_code, $entity_type = 1)
    {
        return Mage::getSingleton('eav/entity_attribute')->loadByCode($entity_type, $attribute_code)->getFrontendLabel();
    }

    /**
     * @param        $extensionName
     * @param        $extVersion
     * @param string $operator
     * @return bool|mixed
     */
    public function checkExtensionVersion($extensionName, $extVersion, $operator = '>=')
    {
        if ($this->isExtensionInstalled($extensionName)
            && ($version = Mage::getConfig()->getModuleConfig($extensionName)->version)
        ) {
            return version_compare($version, $extVersion, $operator);
        }

        return false;
    }

    /**
     * Removes empty values from the array given
     *
     * @param mixed $data Array to inspect or data to be placed in new array as first value
     * @return array Array processed
     */
    public static function noEmptyValues($data)
    {
        $result = array();
        if (is_array($data)) {
            foreach ($data as $a) {
                if ($a) {
                    $result[] = $a;
                }
            }
        } else {
            $result = $data ? array() : array($data);
        }

        return $result;
    }

    public function getNumbering($increment = true)
    {
        $configHelper = Mage::helper('securedcheckout/config');
        if (!$configHelper->isShowNumbering()) {
            return false;
        }
        if (!$this->currentNumber) {
            $this->currentNumber = 0;
        }
        if ($increment) {
            $this->currentNumber++;
        }

        return $this->currentNumber;
    }

    public function formatPrice($value)
    {
        return Mage::app()->getStore()->formatPrice($value);
    }

    public function getWishlistCollection(Mage_Wishlist_Model_Wishlist $wishlist)
    {
        $collection = Mage::getResourceModel('wishlist/product_collection')
            ->setStoreId($wishlist->getStore()->getId())
            ->addWishlistFilter($wishlist)
            ->addWishListSortOrder();

        return $collection;
    }
}