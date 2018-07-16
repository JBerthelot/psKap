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

if (!defined('_PS_VERSION_')) {
    exit;
}


class ComnpayInstall
{

    /**
        * Set configuration table
    */

    public function updateConfiguration()
    {
        Configuration::updateValue('COMNPAY_GATEWAY_CONFIG', 'HOMOLOGATION');
        Configuration::updateValue('COMNPAY_GATEWAY_HOMOLOGATION', 'https://secure-homologation.comnpay.com');
        Configuration::updateValue('COMNPAY_GATEWAY_PRODUCTION', 'https://secure.comnpay.com');
        Configuration::updateValue('COMNPAY_GATEWAY_P3F', 'off');
        Configuration::updateValue('COMNPAY_GATEWAY_PORT', 60000);
        Configuration::updateValue('COMNPAY_GATEWAY_PATH_D', '/rest/payment/find');
        Configuration::updateValue('COMNPAY_GATEWAY_PATH_P3F', '/rest/pnfFile/find');
        Configuration::updateValue('COMNPAY_GATEWAY_PATH_VALIDATE', '/rest/customer/validate');
        Configuration::updateValue('COMNPAY_TRIGGER_P3F', 50);
        Configuration::updateValue('COMNPAY_CODE_TEMPLATE', '');
        // Add eMail template
            // Sources
        $ComNpay_Mail_html_default = _PS_MODULE_DIR_.'/'.$this->name.'/views/mails/fr/comnpay.html';
        $ComNpay_Mail_txt_default = _PS_MODULE_DIR_.'/'.$this->name.'/views/mails/fr/comnpay.txt';
        $ComNpay_Mail_html_Service_default = _PS_MODULE_DIR_.'/'.$this->name.'/views/mails/fr/comnpay_service.html';
        $ComNpay_Mail_txt_Service_default = _PS_MODULE_DIR_.'/'.$this->name.'/views/mails/fr/comnpay_service.txt';
            // New Files
        $ComNpay_Mail_html = _PS_BASE_URL_.'/mails/fr/comnpay.html';
        $ComNpay_Mail_txt = _PS_BASE_URL_.'/mails/fr/comnpay.txt';
        $ComNpay_Mail_html_Service = _PS_BASE_URL_.'/mails/fr/comnpay_service.html';
        $ComNpay_Mail_txt_Service = _PS_BASE_URL_.'/mails/fr/comnpay_service.txt';
            // Copies
        @copy($ComNpay_Mail_html_default, $ComNpay_Mail_html);
        @copy($ComNpay_Mail_txt_default, $ComNpay_Mail_txt);
        @copy($ComNpay_Mail_html_Service_default, $ComNpay_Mail_html_Service);
        @copy($ComNpay_Mail_txt_Service_default, $ComNpay_Mail_txt_Service);
    }

    /**
        * Delete Comnpay configuration
    */

    public function deleteConfiguration()
    {
        Configuration::deleteByName('COMNPAY_GATEWAY_CONFIG');
        Configuration::deleteByName('COMNPAY_GATEWAY_HOMOLOGATION');
        Configuration::deleteByName('COMNPAY_GATEWAY_PRODUCTION');
        Configuration::deleteByName('COMNPAY_GATEWAY_TPE_NUMBER');
        Configuration::deleteByName('COMNPAY_GATEWAY_SECRET_KEY');
        Configuration::deleteByName('COMNPAY_GATEWAY_PORT');
        Configuration::deleteByName('COMNPAY_GATEWAY_PATH_D');
        Configuration::deleteByName('COMNPAY_GATEWAY_PATH_P3F');
        Configuration::deleteByName('COMNPAY_GATEWAY_PATH_VALIDATE');
        Configuration::deleteByName('COMNPAY_GATEWAY_P3F');
        Configuration::deleteByName('COMNPAY_TRIGGER_P3F');
        Configuration::deleteByName('COMNPAY_CODE_TEMPLATE');
        // Remove eMail template
            // Files
        $ComNpay_Mail_html = _PS_BASE_URL_.'/mails/fr/comnpay.html';
        $ComNpay_Mail_txt = _PS_BASE_URL_.'/mails/fr/comnpay.txt';
        $ComNpay_Mail_html_Service = _PS_BASE_URL_.'/mails/fr/comnpay_service.html';
        $ComNpay_Mail_txt_Service = _PS_BASE_URL_.'/mails/fr/comnpay_service.txt';
            // Removes
        @unlink($ComNpay_Mail_html);
        @unlink($ComNpay_Mail_txt);
        @unlink($ComNpay_Mail_html_Service);
        @unlink($ComNpay_Mail_txt_Service);
    }

    /**
        * Create a new order state
    */

    public function createOrderState()
    {
        $this->pendingD();
        $this->acceptedD();
        $this->pendingP3f();
        $this->acceptedP3f();
    }

    public function pendingD()
    {
        if (!Configuration::get('COMNPAY_OS_PENDING')) {
            $orderState = new OrderState();
            $orderState->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $orderState->name[$language['id_lang']] = 'En attente du paiement ComNpay';
                } else {
                    $orderState->name[$language['id_lang']] = 'Pending payment from ComnPay';
                }
            }

            $orderState->send_email = false;
            $orderState->color = '#ffc702';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;

            if ($orderState->add()) {
                $source = dirname(__FILE__).'/views/img/logo.gif';
                $destination = dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('COMNPAY_OS_PENDING', (int)$orderState->id);
        }
    }


    public function acceptedD()
    {
        if (!Configuration::get('COMNPAY_OS_ACCEPTED')) {
            $orderState = new OrderState();
            $orderState->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $orderState->name[$language['id_lang']] = 'Paiement ComNpay accepté';
                } else {
                    $orderState->name[$language['id_lang']] = 'Accepted payment from ComnPay';
                }
            }

            $orderState->send_email = true;
            $orderState->color = '#96CA2D';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = true;
            $orderState->invoice = true;
            $orderState->paid = true;

            if ($orderState->add()) {
                $source = dirname(__FILE__).'/views/img/logo.gif';
                $destination = dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('COMNPAY_OS_ACCEPTED', (int)$orderState->id);
        }
    }


    public function pendingP3f()
    {
        if (!Configuration::get('COMNPAY_OS_PENDING_P3F')) {
            $orderState = new OrderState();
            $orderState->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $orderState->name[$language['id_lang']] = 'En attente du paiement ComNpay en 3 fois';
                } else {
                    $orderState->name[$language['id_lang']] = 'Pending payment in 3 times from ComnPay';
                }
            }

            $orderState->send_email = false;
            $orderState->color = '#ffc702';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = false;
            $orderState->invoice = false;

            if ($orderState->add()) {
                $source = dirname(__FILE__).'/views/img/logo.gif';
                $destination = dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('COMNPAY_OS_PENDING_P3F', (int)$orderState->id);
        }
    }

    public function acceptedP3f()
    {
        if (!Configuration::get('COMNPAY_OS_ACCEPTED_P3F')) {
            $orderState = new OrderState();
            $orderState->name = array();
            foreach (Language::getLanguages() as $language) {
                if (Tools::strtolower($language['iso_code']) == 'fr') {
                    $orderState->name[$language['id_lang']] = 'Paiement ComNpay en 3 fois accepté';
                } else {
                    $orderState->name[$language['id_lang']] = 'Payment in 3 times from ComnPay accepted';
                }
            }
            $orderState->send_email = true;
            $orderState->color = '#96CA2D';
            $orderState->hidden = false;
            $orderState->delivery = false;
            $orderState->logable = true;
            $orderState->invoice = true;
            $orderState->paid = true;

            if ($orderState->add()) {
                $source = dirname(__FILE__).'/views/img/logo.gif';
                $destination = dirname(__FILE__).'/../../img/os/'.(int)$orderState->id.'.gif';
                copy($source, $destination);
            }
            Configuration::updateValue('COMNPAY_OS_ACCEPTED_P3F', (int)$orderState->id);
        }
    }
}
