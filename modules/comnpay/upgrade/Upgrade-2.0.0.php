<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is create by Afone
 * For the installation of the software in your application
 * You accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 *  @author    Afone
 *  @copyright 2015-2017 ComNpay
 *  @license   comnpay.com
 */

include(dirname(__FILE__)."/../comnpay_install.php");

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_2_0_0()
{
    Configuration::updateValue('COMNPAY_GATEWAY_PATH_VALIDATE', '/rest/customer/validate');
}
