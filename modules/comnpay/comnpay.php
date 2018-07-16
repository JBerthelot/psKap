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

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Comnpay extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'comnpay';
        $this->tab = 'payments_gateways';
        $this->version = '2.1.6';
        $this->author = 'Afone';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
        $this->currencies = true;
        $this->currencies_mode = 'checkbox';
        $this->bootstrap = true;
        $this->module_key = '7daffdb3008980c4393c94e13e02fd29';
        
        parent::__construct();

        $this->displayName = 'ComNpay';
        $this->description = 'Accepte les paiements par CB avec ComNpay';

        $this->confirmUninstall = 'Êtes-vous sûr de vouloir désinstaller ?';
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        include_once(_PS_MODULE_DIR_.'/'.$this->name.'/comnpay_install.php');
        $comnpay_install = new ComnpayInstall();
        $comnpay_install->updateConfiguration();
        $comnpay_install->createOrderState();
        $this->createDatabaseTables();

        return parent::install() &&
                            $this->registerHook('paymentOptions') &&
                            $this->registerHook('paymentReturn') &&
                            $this->registerHook('displayPaymentEU') &&
                            $this->registerHook('displayHeader') &&
                            $this->registerHook('displayAdminOrder') &&
                            $this->registerHook('displayBackOfficeHeader');
    }

    public function createDatabaseTables()
    {
        try {
            $db = Db::getInstance();
            $db->execute(
                'CREATE TABLE IF NOT EXISTS `'
                ._DB_PREFIX_
                .'comnpay_transactiondata`
                (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `order_id` varchar(255),
                `transaction_id` varchar(255),
                `datetime` datetime,
                `type_tr` varchar(255),
                PRIMARY KEY (`id`)
                );'
            );
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function uninstall()
    {
        include_once(_PS_MODULE_DIR_.'/'.$this->name.'/comnpay_install.php');
        $comnpay_install = new ComnpayInstall();
        $comnpay_install->deleteConfiguration();

        if (!$this->unregisterHook('paymentOptions') ||
            !$this->unregisterHook('paymentReturn') ||
            !$this->registerHook('displayHeader') ||
            !$this->unregisterHook('displayBackOfficeHeader')
        ) {
            Logger::addLog('Comnpay module: unregisterHook failed', 4);
            return false;
        }

        if (!parent::uninstall()) {
            Logger::addLog('Comnpay module: uninstall failed', 4);
            return false;
        }

        return true;
    }

    /**
        * Affichage du mode de paiement Comnpay
    */
    public function hookPaymentOptions($params)
    {
        $order_total = (int) $params["cart"]->getordertotal(true);
        $seuil = (int) Configuration::get('COMNPAY_TRIGGER_P3F');
        if ($order_total >= $seuil) {
            $active = true;
        } else {
            $active = false;
        }
        if (!$this->isAvailable()) {
            return;
        }


        if ($this->context->language->iso_code == 'fr') {
            $imageNameD = "comnpay.png";
            $imageNameP3F = "comnpay_p3f.png";
        } else {
            $imageNameD = "comnpay_en.png";
            $imageNameP3F = "comnpay_p3f_en.png";
        }


        $this->context->smarty->assign(
            array(
                'imageNameD' => $imageNameD,
                'imageNameP3F' => $imageNameP3F,
                'path_img' => $this->_path,
                'p3f' => Configuration::get('COMNPAY_GATEWAY_P3F'),
                'p3f_seuil' => $active,
                'linkPayment' => $this->context->link->getModuleLink('comnpay', 'payment', array('typeTr'=>0)),
                'linkPayment3f' => $this->context->link->getModuleLink('comnpay', 'payment', array('typeTr'=>1)),
                'blockgrise' => Tools::getValue(
                    'COMNPAY_BLOCK_P3F_CONFIG',
                    Configuration::get('COMNPAY_BLOCK_P3F_CONFIG')
                ),
                'seuil' => $seuil
            )
        );

        $optionPaymentD = $this->optionPaymentD();
        $optionPaymentP3F = $this->optionPaymentP3F();
        $payment_options = array(
            $optionPaymentD
        );
        if ((Configuration::get('COMNPAY_GATEWAY_P3F') == "on") && ($active == true)) {
            array_push($payment_options, $optionPaymentP3F);
        }
        return $payment_options;
    }

    public function optionPaymentD()
    {
        $optionD = new PaymentOption();
        $optionD ->setCallToActionText($this->l('Pay by card'))
            ->setAction($this->context->link->getModuleLink($this->name, 'payment', array('typeTr'=>0)))
            ->setAdditionalInformation(
                $this->context->smarty->fetch('module:comnpay/views/templates/front/paymentD.tpl')
            );
        return $optionD;
    }
    public function optionPaymentP3F()
    {
        $optionP3F = new PaymentOption();
        $optionP3F->setCallToActionText($this->l('Pay by card in three times'))
            ->setAction($this->context->link->getModuleLink($this->name, 'payment', array('typeTr'=>1)))
            ->setAdditionalInformation(
                $this->context->smarty->fetch('module:comnpay/views/templates/front/paymentP3F.tpl')
            );
        return $optionP3F;
    }



    /**
        * Traitement du retour de l'utilisateur après le paiement
    */
    public function hookPaymentReturn($params)
    {
        if (!$this->isAvailable()) {
            return;
        }
        // Get informations
        $orderId = Tools::getValue('id_order');
        $order = new Order($orderId);

        if (($order->current_state == Configuration::get('COMNPAY_OS_PENDING'))||
            ($order->current_state == Configuration::get('COMNPAY_OS_PENDING_P3F'))
        ) {
            $template = 'pending.tpl';
        } elseif (($order->current_state == Configuration::get('COMNPAY_OS_ACCEPTED'))||
            ($order->current_state == Configuration::get('COMNPAY_OS_ACCEPTED_P3F'))
        ) {
            $template = 'authorised.tpl';
        } else {
            return;
        }
        return $this->display(__FILE__, 'views/templates/front/result/'.$template);
    }

    /**
        * Ajout d'une CSS personnalisée
    */
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->_path.'views/css/comnpay.css', 'all');
    }

    /**
        * Ajout d'une CSS personnalisée dans l'admin pour la page de configuration Comnpay
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminModules') {
            $this->context->controller->addJquery();
            $this->context->controller->addCSS($this->_path.'views/css/comnpay_back.css', 'all');
            $this->context->controller->addJS($this->_path.'views/js/validateConfiguration.js');
        }
    }

    /**
        * Affichage de l'échéancier P3F sur le panel admin de commande
    */
    public function hookdisplayAdminOrder($hook)
    {
        if (array_key_exists('id_order', $hook)) {
            $order_id = $hook['id_order'];
        }
        $db = Db::getInstance();
        try {
            $requestSqlTypeTr = 'SELECT `type_tr` FROM `'
                ._DB_PREFIX_
                .'comnpay_transactiondata` WHERE `order_id`='
                .(int)$order_id;
            $resultTypeTr = $db->getRow($requestSqlTypeTr);
        } catch (Exception $exception) {
            Logger::addLog("comnpay module: échec lors du chargement du type de transaction depuis la base de données
            ! Commande : ".(int)$order_id.$exception, 3);
        }

        if (Configuration::get('COMNPAY_GATEWAY_CONFIG')=="HOMOLOGATION") {
            $path = Configuration::get('COMNPAY_GATEWAY_HOMOLOGATION');
        } elseif (Configuration::get('COMNPAY_GATEWAY_CONFIG')=="PRODUCTION") {
            $path = Configuration::get('COMNPAY_GATEWAY_PRODUCTION');
        } else {
            Logger::addLog(
                "comnpay module: échec lors du chargement de la configuration du module !
                Commande : ".(int)$order_id,
                3
            );
        }

        if ($resultTypeTr['type_tr'] == "P3F") {
            $this->hookdisplayAdminOrderP3f($db, $order_id, $path);
            $file = "orderBlocP3f.tpl";
        } elseif ($resultTypeTr['type_tr'] == "D") {
            $this->hookdisplayAdminOrderD($db, $order_id, $path);
            $file = "orderBlocD.tpl";
        } else {
            return;
        }
        return $this->display(__FILE__, 'views/templates/front/'.$file);
    }

    public function hookdisplayAdminOrderP3f($db, $order_id, $path)
    {
        try {
            $requestSql = 'SELECT `transaction_id` FROM `'
                ._DB_PREFIX_
                .'comnpay_transactiondata` WHERE `order_id`='
                .(int)$order_id;
            $resultIdTransac = $db->getRow($requestSql);
        } catch (Exception $exception) {
            Logger::addLog("comnpay module: échec lors de la récuperation de l'id de transaction dans la base de
            données ! Commande : ".(int)$order_id, 3);
        }

        $numTpe = Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER');
        $secretKey = Configuration::get('COMNPAY_GATEWAY_SECRET_KEY');
        $data = "serialNumber=".$numTpe."&key=".$secretKey."&transactionRef=".$resultIdTransac['transaction_id'];
        $url = $path.":".Configuration::get('COMNPAY_GATEWAY_PORT').Configuration::get('COMNPAY_GATEWAY_PATH_P3F');
        $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'timeout' => 5,
                        'header' => "Content-Type: application/x-www-form-urlencoded",
                        'content' => $data
                    )
                );
        $transac = array();
        $context = stream_context_create($opts);
        $result = Tools::file_get_contents($url, false, $context);
        if ($result == false) {
            Logger::addLog("comnpay module: échec lors de l'appel au Web Service ! Commande : ".$order_id, 3);
        } else {
            $parsed_json = Tools::jsonDecode($result);

            for ($i = 0; $i <= 2; $i++) {
                $transac[$i] = array(
                    'transacId'  => $parsed_json->pnfFile[0]->pnfTransactions[$i]->pnfTransactionId,
                    'transacDate' => $parsed_json->pnfFile[0]->pnfTransactions[$i]->plannedDate,
                    'transacMontant' => $parsed_json->pnfFile[0]->pnfTransactions[$i]->amount,
                    'transacOK' => $parsed_json->pnfFile[0]->pnfTransactions[$i]->message
                );
            }
            for ($i=0; $i <= 2; $i++) {
                if ($transac[$i]['transacOK'] == "Paiement accepte") {
                    $transac[$i]['transacOK'] = "Paiement accepté";
                } else {
                    $transac[$i]['transacOK'] = "Paiement en attente";
                }
            }
            for ($i=0; $i <= 2; $i++) {
                $transac[$i]['transacMontant'] = ($transac[$i]['transacMontant']/100)." €";
            }
            for ($i=0; $i <= 2; $i++) {
                $transac[$i]['transacDate'] = (date_create($transac[$i]['transacDate'])->format('d-m-Y'));
            }
            $this->context->smarty->assign('transac', $transac);
        }
    }

    public function hookdisplayAdminOrderD($db, $order_id, $path)
    {
        try {
            $requestSql = 'SELECT `transaction_id` FROM `'
                ._DB_PREFIX_
                .'comnpay_transactiondata` WHERE `order_id`='
                .(int)$order_id;
            $resultIdTransac = $db->getRow($requestSql);
        } catch (Exception $exception) {
            Logger::addLog("comnpay module: échec lors de la récuperation de l'id de transaction dans la base de
             données ! Commande : ".(int)$order_id, 3);
        }

        $numTpe = Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER');
        $secretKey = Configuration::get('COMNPAY_GATEWAY_SECRET_KEY');
        $data = "serialNumber=".$numTpe."&key=".$secretKey."&transactionRef=".$resultIdTransac['transaction_id'];
        $url = $path.":".Configuration::get('COMNPAY_GATEWAY_PORT').Configuration::get('COMNPAY_GATEWAY_PATH_D');
        $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'timeout' => 5,
                        'header' => "Content-Type: application/x-www-form-urlencoded",
                        'content' => $data
                    )
                );
        $context = stream_context_create($opts);
        $result = Tools::file_get_contents($url, false, $context);
        if ($result == false) {
            Logger::addLog("comnpay module: échec lors de l'appel au Web Service ! Commande : ".$order_id, 3);
        } else {
            $parsed_json = Tools::jsonDecode($result);
            $transac = array(
                        'transacId' => $parsed_json->transaction[0]->transactionId,
                        'transacDate' => $parsed_json->transaction[0]->transactionDate,
                        'transacMontant' => $parsed_json->transaction[0]->amount,
                        'transacOK' => $parsed_json->transaction[0]->message
                    );
            $transac['transacDate'] = (date_create($transac['transacDate'])->format('d-m-Y'));
            $transac['transacMontant'] = ($transac['transacMontant']/100)." €";
            if ($transac['transacOK'] == "Paiement accepte") {
                $transac['transacOK'] = "Paiement accepté";
            } else {
                $transac['transacOK'] = "Paiement en attente";
            }
            $this->context->smarty->assign('transac', $transac);
        }
    }

    public function isAvailable()
    {
        if (!$this->active) {
            return false;
        }
        if ((Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER') != "") &&
            (Configuration::get('COMNPAY_GATEWAY_SECRET_KEY') != "")
        ) {
            return true;
        }
        return false;
    }

    /*
        * Secret Validation
    */
    public function validSec($values, $secret_key)
    {
        if (isset($values['sec']) && $values['sec'] != "") {
            $sec = $values['sec'];
            unset($values['sec']);
            return Tools::strtoupper(
                hash("sha512", base64_encode(implode("|", $values)."|".$secret_key))
            ) == Tools::strtoupper($sec);
        } else {
            return false;
        }
    }

    /**
        * Changer Id Order State
    */
    public function changeIdOrderState($transactionId, $stateId)
    {
        if ($transactionId == "") {
            return false;
        }
        $orderHistory = new OrderHistory();
        $orderHistory->id_order = $transactionId;
        $orderHistory->changeIdOrderState($stateId, $transactionId);
        $orderHistory->addWithemail();
        $orderHistory->save();
        return true;
    }

    /**
        * Administration
    */

    public function getContent()
    {
        if (!isset($this->_html) || empty($this->_html)) {
            $this->_html = '';
        }
        $msg_confirmation = '';
        $msg_confirmation_class = '';
        $display_msg_confirmation = '0';
        $msg_information = '';
        $msg_information_class = '';
        $display_msg_information = '0';
        $msg_souscription = '';
        $msg_souscription_class = '';
        $display_msg_souscription = '0';

        if (!empty(Tools::getValue('create_account_action'))) {
            $i_required = 0;
            $requiredFields = array(
                'lastname',
                'firstname',
                'email',
                'telephone',
                'raison',
                'url_website',
                'secteur',
                'siret',
                'code_postal',
                'ville',
                'adresse'
            );
            foreach ($requiredFields as $itemRequired) {
                if (!empty(Tools::getValue($itemRequired))) {
                    // Do nothing
                } else {
                    $i_required++;
                }
            }
            if ($i_required == 0) {
                $mailCustomer = Tools::getValue('email');
                if (preg_match("#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#", $mailCustomer)) {
                    $requiredAFP = 0;
                    if (Tools::getValue('client_afone') == 'Oui') {
                        if (Tools::getValue('compteAFP') == '') {
                            $requiredAFP = 1;
                        }
                    }
                    if ($requiredAFP == 0) {
                        // PARAM
                        $id_lang = $this->context->employee->id_lang;
                        $template = 'comnpay';
                        $template_ServiceClients = 'comnpay_service';
                        $subject = 'Confirmation de votre inscription à ComNpay';
                        $template_vars = array();
                        // Information sur le client
                        $template_vars['{compteAFP}'] = 'Client AFONE PAIEMENT : '.Tools::getValue('client_afone');
                        if (Tools::getValue('client_afone') == 'Oui') {
                            $template_vars['{compteAFP}'] = 'Numéro de compte : '.Tools::getValue('compteAFP');
                        } else {
                            $template_vars['{compteAFP}'] = '';
                        }
                        if (Tools::getValue('choixTransaction') == 'GRATUIT') {
                            $template_vars['{choix_transaction}'] = 'Une fois par semaine (gratuit)';
                        } else {
                            $template_vars['{choix_transaction}'] = 'J+2';
                        }
                        foreach ($requiredFields as $itemRequired) {
                            $key_template_vars = '{'.$itemRequired.'}';
                            $template_vars[$key_template_vars] = Tools::getValue($itemRequired);
                        }
                        $template_vars['{shop_name}'] = 'ComNpay';
                        $template_vars['{comnpay_logo}'] = _PS_BASE_URL_.'/modules/comnpay/views/img/logo.png';
                        $template_vars['{forme_juridique}'] = Tools::getValue('forme_juridique');
                        $to = $mailCustomer;
                        $to_name = Tools::getValue('firstname').' '.Tools::getValue('lastname');
                        $from = 'service-clients@afonepaiement.com';
                        $from_name = 'Service clients AFONE PAIEMENT';
                        $file_attachment = array();
                        $template_path = _PS_ROOT_DIR_. "/modules/comnpay/view/";
                            // FILES
                        $nameInputFiles = array('carte_identite_recto', 'carte_identite_verso', 'kbis', 'rib');
                        $nameInputFilesTemp = array();
                        foreach ($nameInputFiles as $itemInputFile) {
                            if ($_FILES[$itemInputFile]['error'] == 0) {
                                $keyFileTemp = rand(100, 100000000000);
                                $tab_info = pathinfo($_FILES[$itemInputFile]['name']);
                                $extension = Tools::strtolower($tab_info['extension']);
                                $tab_ext_autorisees = array('pdf', 'PDF', 'jpeg', 'JPEG', 'jpg', 'JPG', 'png', 'PNG');
                                if (in_array($extension, $tab_ext_autorisees)) {
                                    $chemin_fichier_a_renommer = $_FILES[$itemInputFile]['tmp_name'];
                                    $nom_fichier = $keyFileTemp.'_'.$itemInputFile.'.'.$extension;
                                    $nouveau_nom = dirname(__FILE__).'/'.$nom_fichier;
                                    array_push($nameInputFilesTemp, $nouveau_nom);
                                    if (@move_uploaded_file($chemin_fichier_a_renommer, $nouveau_nom)) {
                                        $getContent = Tools::file_get_contents($nouveau_nom);
                                        $name = $itemInputFile.'.'.$extension;
                                        $mime = $_FILES[$itemInputFile]["type"];
                                        array_push(
                                            $file_attachment,
                                            array(
                                                'content' => $getContent,
                                                'name' => $name,
                                                'mime' => $mime
                                            )
                                        );
                                    }
                                }
                            }
                        }

                        // mail client
                        Mail::Send(
                            $id_lang,
                            $template,
                            $subject,
                            $template_vars,
                            $to,
                            $to_name,
                            $from,
                            $from_name,
                            array(),
                            null,
                            $template_path,
                            false,
                            null,
                            null,
                            null
                        );
                        Mail::Send(
                            $id_lang,
                            $template_ServiceClients,
                            'Nouvelle souscription via Prestashop',
                            $template_vars,
                            'service-clients@afonepaiement.com',
                            'Service clients AFONE PAIEMENT',
                            'no_reply@comnpay.com',
                            'ComNpay',
                            $file_attachment,
                            null,
                            $template_path,
                            false,
                            null,
                            null,
                            null
                        );
                        Mail::Send(
                            $id_lang,
                            $template_ServiceClients,
                            'Nouvelle souscription via Prestashop',
                            $template_vars,
                            'prestashop@comnpay.com',
                            'ComNpay Prestashop',
                            'no_reply@comnpay.com',
                            'ComNpay',
                            $file_attachment,
                            null,
                            $template_path,
                            false,
                            null,
                            null,
                            null
                        );
                        $msg_souscription = "Votre demande de souscription a bien été envoyé.";
                        // Suppression des fichiers importés sur le serveur
                        foreach ($nameInputFilesTemp as $fileToDelete) {
                            @unlink($fileToDelete);
                        }
                    } else {
                        $msg_souscription = "Veuillez saisir votre numéro de compte AFONE PAIEMENT";
                        $msg_souscription_class = ' alert-error';
                    }
                } else {
                    $msg_souscription = "Votre adresse mail semble incorrecte";
                    $msg_souscription_class = ' alert-error';
                }
            } else {
                $msg_souscription = "Tous les champs sont obligatoires pour la souscription de votre compte.";
                $msg_souscription_class = ' alert-error';
            }
            $display_msg_souscription = '1';
        }

        if (!empty(Tools::getValue('COMNPAY_GATEWAY_CONFIG'))) {
            Configuration::updateValue('COMNPAY_GATEWAY_CONFIG', Tools::getValue('COMNPAY_GATEWAY_CONFIG'));
            Configuration::updateValue('COMNPAY_GATEWAY_HOMOLOGATION', Tools::getValue('COMNPAY_GATEWAY_HOMOLOGATION'));
            Configuration::updateValue('COMNPAY_GATEWAY_PRODUCTION', Tools::getValue('COMNPAY_GATEWAY_PRODUCTION'));
            Configuration::updateValue('COMNPAY_GATEWAY_TPE_NUMBER', Tools::getValue('COMNPAY_GATEWAY_TPE_NUMBER'));
            Configuration::updateValue('COMNPAY_GATEWAY_SECRET_KEY', Tools::getValue('COMNPAY_GATEWAY_SECRET_KEY'));
            Configuration::updateValue('COMNPAY_GATEWAY_P3F', Tools::getValue('COMNPAY_GATEWAY_P3F'));
            Configuration::updateValue('COMNPAY_CODE_TEMPLATE', Tools::getValue('COMNPAY_CODE_TEMPLATE'));

            /* on check le seuil P3F */
            if ((int)Tools::getValue('COMNPAY_TRIGGER_P3F') >= 50) {
                Configuration::updateValue('COMNPAY_TRIGGER_P3F', Tools::getValue('COMNPAY_TRIGGER_P3F'));
                $msg_confirmation = "Merci d'avoir choisi ComNpay. Configuration sauvegardée !";
                $display_msg_confirmation = '1';
            } else {
                if (Tools::getValue('COMNPAY_GATEWAY_P3F', Configuration::get('COMNPAY_GATEWAY_P3F')) == "on") {
                    $msg_confirmation = "Attention, le seuil d'autorisation du paiement en 3
                    fois est invalide (Minimum : 50€) . La configuration n'a pas été sauvegardée !";
                    $msg_confirmation_class = ' alert-error';
                    $display_msg_confirmation = '1';
                } else {
                    $msg_confirmation = "Merci d'avoir choisi ComNpay. Configuration sauvegardée !";
                    $display_msg_confirmation = '1';
                }
            }

            $checkConf = $this->checkConf(
                Tools::getValue('COMNPAY_GATEWAY_CONFIG'),
                Tools::getValue('COMNPAY_GATEWAY_TPE_NUMBER'),
                Tools::getValue('COMNPAY_GATEWAY_SECRET_KEY')
            );
            if ($checkConf == 1) {
                $msg_information = "Attention, votre numéro de TPE et/ou Secret Key sont incorrects.
                Votre boutique ne pourra pas accepter de paiements comNpay !";
                $msg_information_class = ' alert-error';
                $display_msg_information = '1';
            }
            if ($checkConf == 2) {
                $msg_information = "Impossible de vérifier votre numéro de TPE et Secret Key.
                Certaines fonctions peuvent être désactivée. Pour en savoir plus
                : https://docs.comnpay.com/api.html";
                $msg_information_class = ' alert-error';
                $display_msg_information = '1';
            }
        }

        if (!empty(Tools::getValue('COMNPAY_GATEWAY_CONFIG'))) {
            $activeTab_1 = ' active';
            $activeTab_2 = '';
            $activeTabList_1 = 'active';
            $activeTabList_2 = '';
            $tpeNumber = Tools::safeOutput(
                Tools::getValue('COMNPAY_GATEWAY_TPE_NUMBER', Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER'))
            );
            $secretKey = Tools::safeOutput(
                Tools::getValue('COMNPAY_GATEWAY_SECRET_KEY', Configuration::get('COMNPAY_GATEWAY_SECRET_KEY'))
            );

        } else {
            $tpeNumber = Tools::getValue(
                'COMNPAY_GATEWAY_TPE_NUMBER',
                Configuration::get('COMNPAY_GATEWAY_TPE_NUMBER')
            );
            $secretKey = Tools::getValue(
                'COMNPAY_GATEWAY_SECRET_KEY',
                Configuration::get('COMNPAY_GATEWAY_SECRET_KEY')
            );
        }

        $seuil_p3f = Tools::safeOutput(
            Tools::getValue('COMNPAY_TRIGGER_P3F', Configuration::get('COMNPAY_TRIGGER_P3F'))
        );

        $code_template = Tools::safeOutput(
            Tools::getValue('COMNPAY_CODE_TEMPLATE', Configuration::get('COMNPAY_CODE_TEMPLATE'))
        );

        if (($tpeNumber == false) || ($tpeNumber == "")) {
            $tpeNumber = 'PRESTASHOP';
        }
        if (($secretKey == false) || ($secretKey == "")) {
            $secretKey = 'UN6Ek9rm1aZQl44mMeR5';
        }
        if (Tools::getValue('COMNPAY_GATEWAY_CONFIG', Configuration::get('COMNPAY_GATEWAY_CONFIG')) == "PRODUCTION") {
            $plateformeHomologation = "";
            $plateformeProduction = " checked=\"checked\"";
        } else {
            $plateformeHomologation = " checked=\"checked\"";
            $plateformeProduction = "";
        }
        if (Tools::getValue('COMNPAY_GATEWAY_P3F', Configuration::get('COMNPAY_GATEWAY_P3F')) == "on") {
            $p3f_on = " checked=\"checked\"";
            $p3f_off = "";
        } else {
            $p3f_on = "";
            $p3f_off = " checked=\"checked\"";
        }

        if (!empty(Tools::getValue('COMNPAY_GATEWAY_CONFIG'))) {
            $activeTab_1 = '';
            $activeTab_2 = ' active';
            $activeTabList_1 = '';
            $activeTabList_2 = 'active';
        } else {
            $activeTab_1 = ' active';
            $activeTab_2 = '';
            $activeTabList_1 = 'active';
            $activeTabList_2 = '';
        }

        if ($this->context->language->iso_code == 'fr') {
            $imageName = "comnpay_header_admin.jpg";
            $imageNameBottom = "comnpay_header_admin_bottom.jpg";
        } else {
            $imageName = "comnpay_header_admin_en.jpg";
            $imageNameBottom = "comnpay_header_admin_bottom_en.jpg";
        }

        $this->context->smarty->assign(
            array(
                'image_header' => "../modules/".$this->name."/views/img/" . $imageName,
                'image_header_bottom' => "../modules/".$this->name."/views/img/" . $imageNameBottom,
                'activeTabList_1' => $activeTabList_1,
                'activeTabList_2' => $activeTabList_2,
                'activeTab_1' => $activeTab_1,
                'actionForm' => '',
                'label_tpe_number' => $this->l('TPE Number'),
                'value_tpe_number' => $tpeNumber,
                'label_secret_key' => $this->l('Secret Key'),
                'value_secret_key' => $secretKey,
                'plateformeProduction' => $plateformeProduction,
                'plateformeHomologation' => $plateformeHomologation,
                'p3f_on' => $p3f_on,
                'p3f_off' => $p3f_off,
                'seuil_p3f' => $seuil_p3f,
                'activeTab_2' => $activeTab_2,
                'code_template' => $code_template,
                'compteAFP' => Tools::getValue('compteAFP'),
                'lastname' => Tools::getValue('lastname'),
                'firstname' => Tools::getValue('firstname'),
                'email' => Tools::getValue('email'),
                'telephone' => Tools::getValue('telephone'),
                'raison' => Tools::getValue('raison'),
                'url_website' => Tools::getValue('url_website'),
                'secteur' => Tools::getValue('secteur'),
                'siret' => Tools::getValue('siret'),
                'adresse' => Tools::getValue('adresse'),
                'code_postal' => Tools::getValue('code_postal'),
                'ville' => Tools::getValue('ville'),
                'msg_information' => $msg_information,
                'msg_information_class' => $msg_information_class,
                'display_msg_information' => $display_msg_information,
                'msg_confirmation' => $msg_confirmation,
                'msg_confirmation_class' => $msg_confirmation_class,
                'display_msg_confirmation' => $display_msg_confirmation,
                'msg_souscription' => $msg_souscription,
                'msg_souscription_class' => $msg_souscription_class,
                'display_msg_souscription' => $display_msg_souscription
            )
        );
        return $this->display(__FILE__, '/views/templates/front/admin.tpl');
    }

    /**
        * Vérification du Numéro de TPE et de la secret key
    */
    public function checkConf($gateway, $tpe_no, $secret_key)
    {
        if ($gateway == "HOMOLOGATION") {
            $path = Configuration::get('COMNPAY_GATEWAY_HOMOLOGATION').":"
                .Configuration::get('COMNPAY_GATEWAY_PORT')
                .Configuration::get('COMNPAY_GATEWAY_PATH_VALIDATE');
        } elseif ($gateway == "PRODUCTION") {
            $path = Configuration::get('COMNPAY_GATEWAY_PRODUCTION').":"
                .Configuration::get('COMNPAY_GATEWAY_PORT')
                .Configuration::get('COMNPAY_GATEWAY_PATH_VALIDATE');
        }
        $data = "serialNumber=".$tpe_no."&key=".$secret_key;
        $opts = array('http' =>
                    array(
                        'method' => 'POST',
                        'timeout' => 5,
                        'header' => "Content-Type: application/x-www-form-urlencoded",
                        'content' => $data
                    )
                );
        $context = stream_context_create($opts);
        $result = Tools::file_get_contents($path, false, $context);

        if ($result === false) {
            // Erreur SSL ou de connexion ?
            return 2;
        }

        $json = Tools::jsonDecode($result);
        if ($json->ok == 0) {
            // Problème de configuration
            return 1;
        } else {
            // Ok
            return 0;
        }
    }

    public function insertDataBase($orderId, $transactionId, $type_tr)
    {
        if (!empty($orderId) && !empty($transactionId) && ($type_tr == "D" || $type_tr == "P3F")) {
            $now = date("Y-m-d H:i:s");
            $db = Db::getInstance();
            $requestSql = 'INSERT INTO `'
                ._DB_PREFIX_
                .'comnpay_transactiondata`
                (`order_id`, `transaction_id`, `datetime`, `type_tr`) VALUES("'.
                            (int)$orderId.'", "'.
                            pSQL($transactionId).'", "'.
                            $now.'", "'.
                            pSQL($type_tr).'")';
            try {
                $db->execute($requestSql);
            } catch (Exception $exception) {
                Logger::addLog("comnpay module: échec lors de l'insertion de la commande en base de données !
                Commande : ".$orderId, 3);
            }
        } else {
            return false;
        }
    }
}
