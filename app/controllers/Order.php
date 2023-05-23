<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Title;

class Order extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $order_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$order = db()->where('order_id', $order_id)->where('user_id', $this->user->user_id)->getOne('orders')) {
            redirect('dashboard');
        }

        $order->details = json_decode($order->details);

        $store = db()->where('store_id', $order->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency', 'name']);

        /* Get the categories */
        $order_items = [];
        $order_items_result = database()->query("
            SELECT
                *
            FROM
                `orders_items`
            WHERE
                `order_id` = {$order->order_id}
        ");
        while($row = $order_items_result->fetch_object()) {
            $row->data = json_decode($row->data);

            $order_items[] = $row;
        }

        /* Set a custom title */
        Title::set(sprintf(l('order.title'), $order->order_number, $store->name));

        /* Establish the account sub order view */
        $data = [
            'order_id' => $order->order_id,
            'resource_name' => $order->order_number,
            'processor' => $order->processor
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'order' => $order,
            'order_items' => $order_items,
        ];

        $view = new \Altum\View('order/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function complete() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        if(empty($_POST)) {
            redirect('dashboard');
        }

        $order_id = (int) query_clean($_POST['order_id']);

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$order = db()->where('order_id', $order_id)->where('user_id', $this->user->user_id)->getOne('orders', ['store_id', 'order_id'])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Database query */
            database()->query("UPDATE `orders` SET `status` = 1 WHERE `order_id` = {$order->order_id}");

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $order->store_id);

            /* Set a nice success message */
            Alerts::add_success(l('order.success_message_complete'));

            redirect('orders/' . $order->store_id);

        }

        redirect('orders/' . $order->store_id);
    }

    public function delete() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        if(empty($_POST)) {
            redirect('dashboard');
        }

        $order_id = (int) query_clean($_POST['order_id']);

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$order = db()->where('order_id', $order_id)->where('user_id', $this->user->user_id)->getOne('orders', ['store_id', 'order_id'])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Database query */
            db()->where('order_id', $order->order_id)->delete('orders');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $order->store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $order->order_id . '</strong>'));

            redirect('orders/' . $order->store_id);

        }

        redirect('orders/' . $order->store_id);
    }
}
