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

class ComnpayPaymentModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        $this->display_column_left = false;
        parent::initContent();
        $context = $this->context;
        $cart = $context->cart;
        $customer = new Customer((int)$cart->id_customer);
        $addressUser = new Address((int)$cart->id_address_invoice);
        $extension = new Comnpay();
        if (!$extension->isAvailable()) {
            Tools::redirect('index.php?controller=order');
        }
        if ((Tools::getvalue('typeTr') == 1) && (Configuration::get('COMNPAY_GATEWAY_P3F')=="on")) {
            $P3F= "P3F";
            $context->cookie->__set("typeTr", $P3F);
        } else {
            $P3F = "D";
            $context->cookie->__set("typeTr", $P3F);
        }

        if (!empty($addressUser->phone)) {
            $phoneUser = $addressUser->phone;
        } else {
            $phoneUser = $addressUser->phone_mobile;
        }
        // On prépare le formulaire qui enverra l'utilisateur sur la passerelle de paiement
        $comnpay = array();
        $comnpay['montant'] = number_format($cart->getOrderTotal(), 2, '.', '');
        $comnpay['idTPE'] = Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER');
        $comnpay['idTransaction'] = time()."-".(int)$cart->id;
        $comnpay['devise'] = $context->currency->iso_code;
        $comnpay['lang'] = Language::getIsoById($this->context->language->id);
        $comnpay['nom_produit'] = "";
        $comnpay['source'] = $_SERVER['SERVER_NAME'];
        $getsUrlRetour = $this->context->link->getModuleLink(
            'comnpay',
            'retour',
            array('customer'=>$customer->secure_key, 'id_cart'=>(int)$cart->id)
        );
        $comnpay['urlRetourOK'] = $getsUrlRetour;
        $comnpay['urlRetourNOK'] = $getsUrlRetour;
        $urlIPN = (Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http');
        $urlIPN .= '://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->module->name.'/ipn.php';
        $comnpay['urlIPN'] = $urlIPN;
        $comnpay['extension'] = "prestashop-".$this->module->name."-".$this->module->version;
        $comnpay['data'] = $P3F;
        $comnpay['typeTr'] = $P3F;
        $porteurDatas = array(
            'adresse' => $addressUser->address1,
            'codePostal' => $addressUser->postcode,
            'email' => $customer->email,
            'nom' => $customer->lastname,
            'pays' => $addressUser->country,
            'prenom' => $customer->firstname,
            'qualite' => 'Particulier',
            'telephone' => $phoneUser,
            'ville' => $addressUser->city,
            'refPorteur' => $customer->secure_key
        );
        $porteurJsonEncode = Tools::jsonEncode($porteurDatas);
        $comnpay['porteur'] = base64_encode($porteurJsonEncode);
        $comnpay['codeTemplate'] = Configuration::get('COMNPAY_CODE_TEMPLATE');
        $comnpay['key'] = Configuration::get('COMNPAY_GATEWAY_SECRET_KEY');

        // Encoding

        $comnpayWithKey = base64_encode(implode("|", $comnpay));
        unset($comnpay['key']);
        $comnpay['sec'] = hash("sha512", $comnpayWithKey);

        if (Configuration::get('COMNPAY_GATEWAY_CONFIG')=="HOMOLOGATION") {
            $form_action = Configuration::get('COMNPAY_GATEWAY_HOMOLOGATION');
        } else {
            $form_action = Configuration::get('COMNPAY_GATEWAY_PRODUCTION');
        }

        // Datas Form
        $arraySmarty = array(
            'isCartEmpty' => ((int)$cart->getOrderTotal() == 0) ? true:false,
            'form_action' => $form_action
        );
        foreach ($comnpay as $key => $value) {
            $arraySmarty[$key] = $value;
        }
        $this->context->smarty->assign($arraySmarty);
        $this->setTemplate('module:comnpay/views/templates/front/comnpay.tpl');
    }
}
