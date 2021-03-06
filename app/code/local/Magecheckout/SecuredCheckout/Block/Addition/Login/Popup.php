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
class Magecheckout_SecuredCheckout_Block_Addition_Login_Popup extends Magecheckout_SecuredCheckout_Block_Addition_Login_Form
{
    /**
     * get login popup width
     *
     * @return null||number
     */
    public function getPopupWidth()
    {
        return $this->getHelperConfig()->getLoginPopupWidth();
    }

    /**
     * @return null||numberF
     */
    public function getLoginPopupHeight()
    {
        return $this->getHelperConfig()->getLoginPopupHeight();
    }
}