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
class Magecheckout_SecuredCheckout_Block_Checkout_Review_Coupon extends Magecheckout_SecuredCheckout_Block_Container
{
    protected $_helper;


    /**
     * enable gift message or not
     *
     */
    public function canShow()
    {
        return $this->getHelperConfig()->isCoupon();
    }

    /**
     * @return mixed
     */
    public function getShowApplyButton()
    {
        return $this->getHelperConfig()->isApplyCouponButton();
    }

}