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

class ComnpayRetourModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        require_once(dirname(__FILE__).'./../../../../config/config.inc.php');
        include_once(dirname(__FILE__).'./../../../../init.php');
        require_once(dirname(__FILE__).'./../../comnpay.php');
        // Init
        $comnpay = new Comnpay();
        $context = Context::getContext();

        if (!$comnpay->isAvailable()) {
            return;
        }

        // Get the result
        $result = Tools::getValue('result');

        if ($result == "OK") {
            $transactionId = Tools::getValue('transactionId');
            $explodeTemp = explode("-", $transactionId);
            $cartId = $explodeTemp[1];
            $typeTrIpn = $context->cookie->typeTr;
            $cartIdRef = Tools::getValue('id_cart');

            if ($typeTrIpn == 'P3F') {
                $typeTrPending = Configuration::get('COMNPAY_OS_PENDING_P3F');
            } elseif ($typeTrIpn == 'D') {
                $typeTrPending = Configuration::get('COMNPAY_OS_PENDING');
            } else {
                Logger::addLog(
                    "comnpay module: échec lors de la vérification des données POST du type de transaction !
                    transactionId ".$transactionId,
                    4
                );
                header("Status: 400 Bad Request", false, 400);
                exit();
            }

            if ($cartId!=$cartIdRef) {
                // La référence a été modifiée
                Logger::addLog(
                    "comnpay module: échec lors de la vérification du numéro de transaction !
                    transactionId=".$transactionId.", cartId=".$cartIdRef,
                    4
                );
                Tools::redirect('index.php?controller=order&step=1');
            }
            $cart = new Cart($cartId);
            $customer = new Customer((int)$cart->id_customer);
            $order = new Order();
            $orderId = $order->getOrderByCartId($cartId);
            if (!$orderId) {
                $message = "En attente de la confirmation du paiement comNpay.
                Identifiant de transaction: ".$transactionId;
                $comnpay ->validateOrder(
                    $cart->id,
                    $typeTrPending,
                    (float)$cart->getOrderTotal(true, Cart::BOTH),
                    $comnpay->displayName,
                    $message,
                    array(),
                    (int)$cart->id_currency,
                    false,
                    $customer->secure_key
                );
                $order = new Order($comnpay->currentOrder);
            } else {
                // Ipn has been received
                $order = new Order($orderId);
            }
            Tools::redirect(
                'index.php?controller=order-confirmation&id_cart='
                .$cart->id.'&id_module='.$comnpay->id.'&id_order='.$order->id.'&key='.$customer->secure_key
            );
        } else {
            Tools::redirect('index.php?controller=order&step=3');
        }
    }
}
