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
use Altum\Database;
use Altum\Title;

class OrdersOf extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `orders` WHERE `store_id` = {$store->store_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('orders-of/' . $store->store_id . '?page=%d')));

        /* Get the payments list for the user */
        $orders = [];
        $pending_orders = 0;
        $orders_result = Database::$database->query("SELECT * FROM `orders` WHERE `store_id` = {$store->store_id} ORDER BY `order_id` DESC LIMIT {$paginator->getSqlOffset()}, {$paginator->getItemsPerPage()}");
        while($row = $orders_result->fetch_object()) {
            $orders[] = $row;

            if(!$row->status) {
                $pending_orders++;
            }
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Establish the account sub menu view */
        $data = [
            'store_id' => $store->store_id,
            'resource_name' => $store->name,
            'external_url' => $store->full_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('orders-of.title'), $pending_orders));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'orders' => $orders,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('orders-of/index', (array) $this);

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

            redirect('orders-of/' . $order->store_id);

        }

        redirect('orders-of/' . $order->store_id);
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
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $order->order_number . '</strong>'));

            redirect('orders-of/' . $order->store_id);

        }

        redirect('orders-of/' . $order->store_id);
    }


   
 
    
}
