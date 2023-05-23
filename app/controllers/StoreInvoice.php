<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;


class StoreInvoice extends Controller {

    public function index() {

        $order_id = isset($this->params[0]) ? (int) $this->params[0] : null;
        $hash = $_GET['hash'] ?? null;

        /* Get details about the order */
        if(!$order = db()->where('order_id', $order_id)->getOne('orders')) {
            redirect();
        }

        if(!in_array($order->processor, ['stripe', 'paypal'])) {
            redirect();
        }

        $payment_hash = md5($order->order_id . $order->order_number . $order->datetime);

        if((\Altum\Authentication::check() && $order->user_id != $this->user->user_id) && $hash != $payment_hash) {
            redirect();
        }

        /* Make sure a payment exists */
        if(!$payment = db()->where('order_id', $order->order_id)->getOne('customers_payments')) {
            redirect();
        }

        /* Get details about the store */
        if(!$store = db()->where('store_id', $order->store_id)->getOne('stores')) {
            redirect();
        }

        /* Try to see if we get details from the billing */
        $store->business = json_decode($store->business);
        $payment->billing = json_decode($payment->billing);


        /* Prepare the View */
        $data = [
            'payment' => $payment,
            'order' => $order,
            'store' => $store,
        ];

        $view = new \Altum\View('store-invoice/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
