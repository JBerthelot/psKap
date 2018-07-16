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

require_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/comnpay.php');
// Init
$comnpay = new Comnpay();
// Get the data
$transactionId =  Tools::getValue("idTransaction");
$result =  Tools::getValue("result");
$amountGateway = (float) Tools::getValue("montant");
$explodeTemp = explode("-", $transactionId);
$cart = new Cart($explodeTemp[1]);
if (!empty($_POST["data"])) {
    $typeTrIpn =  Tools::getValue("data");
} elseif (!empty($_GET["data"])) {
    $typeTrIpn = Tools::getValue("data");
} else {
    $typeTrIpn = $_REQUEST["data"];
}
$totalOrder = (float)$cart->getOrderTotal(true, Cart::BOTH);
$order = new Order();
$orderId = $order->getOrderByCartId($cart->id);
// Check same amount between the shopping cart and the gateway
$sameAmount = 0;
if ($typeTrIpn == 'P3F') {
    $amountTier = round(($totalOrder / 3), 2);
    if ((int)$amountTier == (int)$amountGateway) {
        $sameAmount = 1;
    }
} else {
    if ($amountGateway == $totalOrder) {
        $sameAmount = 1;
    }
}
// Check post data
if (!$comnpay->validSec($_POST, Configuration::get('COMNPAY_GATEWAY_SECRET_KEY'))) {
    Logger::addLog(
        "comnpay module: échec lors de la vérification des données POST ! transactionId ".$transactionId,
        4
    );
    header("Status: 400 Bad Request", false, 400);
    exit();
}

if ($typeTrIpn == 'P3F') {
    $typeTrAccepted = Configuration::get('COMNPAY_OS_ACCEPTED_P3F');
    $typeTrPending = Configuration::get('COMNPAY_OS_PENDING_P3F');
} elseif ($typeTrIpn == 'D') {
    $typeTrAccepted = Configuration::get('COMNPAY_OS_ACCEPTED');
    $typeTrPending = Configuration::get('COMNPAY_OS_PENDING');
} else {
    Logger::addLog(
        "comnpay module: échec lors de la vérification des données POST du type de transaction
        ! transactionId ".$transactionId,
        4
    );
    header("Status: 400 Bad Request", false, 400);
    exit();
}

if ($orderId) {
    // Existing order
    if ($result == "OK") {
        $order = new Order($orderId);
        if ($order->getCurrentState() == $typeTrAccepted) {
            // Paiement déjà confirmé (ipn reçu en double ?)
        } elseif ($order->getCurrentState() == $typeTrPending) {
            if ($sameAmount == 1) {
                // Change order state to payment paid
                Logger::addLog('comnpay module: payment is validated for transactionId '.$transactionId);
                $orderHistory = new OrderHistory();
                $orderHistory ->id_order = $orderId;
                $orderHistory ->changeIdOrderState((int)$typeTrAccepted, $orderId);
                $orderHistory ->addWithemail();
                $orderHistory ->save();
                $comnpay ->insertDataBase($orderId, $transactionId, $typeTrIpn);
                if (_PS_VERSION_ > '1.5' && _PS_VERSION_ < '1.5.2') {
                    $order ->current_state = $orderHistory->id_order_state;
                    $order ->update();
                }
            } else {
                Logger::addLog(
                    'comnpay module: different total amount between the gateway and
                    the shopping cart - '.$transactionId
                );
            }
        } else {
            Logger::addLog('comnpay module: incorrect order status...'.$transactionId);
        }
    } else {
        Logger::addLog('comnpay module: payment is refused or canceled for transactionId '.$transactionId);
    }
} else {
    if ($result == "OK") {
        if ($sameAmount == 1) {
            // Order creation
            $customer = new Customer((int)$cart->id_customer);
            $message = "Confirmation du paiement comNpay. Identifiant de transaction: ".$transactionId;
            $comnpay ->validateOrder(
                $cart->id,
                $typeTrAccepted,
                (float)$cart->getOrderTotal(true, Cart::BOTH),
                $comnpay->displayName,
                $message,
                array(),
                (int)$cart->id_currency,
                false,
                $customer->secure_key
            );
            $order = new Order($comnpay->currentOrder);
            $comnpay ->insertDataBase($order->id, $transactionId, $typeTrIpn);
        } else {
            Logger::addLog(
                'comnpay module: different total amount between the gateway
                and the shopping cart - '.$transactionId
            );
        }
    } else {
        Logger::addLog('comnpay module: payment is refused or canceled for transactionId '.$transactionId);
    }
}
