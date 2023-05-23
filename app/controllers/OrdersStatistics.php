<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Title;

class OrdersStatistics extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!$this->user->plan_settings->ordering_is_enabled) {
            redirect('dashboard');
        }

        if(isset($_GET['store_id'])) {
            $store_id = isset($_GET['store_id']) ? (int) $_GET['store_id'] : null;

            if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
                redirect('dashboard');
            }

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'store';
            $identifier_key = 'store_id';
            $identifier_value = $store->store_id;
            $external_url = $store->full_url;
        }

        else if(isset($_GET['menu_id'])) {
            $menu_id = isset($_GET['menu_id']) ? (int) $_GET['menu_id'] : null;

            if(!$menu = db()->where('menu_id', $menu_id)->where('user_id', $this->user->user_id)->getOne('menus')) {
                redirect('dashboard');
            }

            $store = db()->where('store_id', $menu->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency']);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'menu';
            $identifier_key = 'menu_id';
            $identifier_value = $menu->menu_id;
            $external_url = $store->full_url . $menu->url;
        }

        else if(isset($_GET['category_id'])) {
            $category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

            if(!$category = db()->where('category_id', $category_id)->where('user_id', $this->user->user_id)->getOne('categories')) {
                redirect('dashboard');
            }

            $menu = db()->where('menu_id', $category->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);

            $store = db()->where('store_id', $category->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency']);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'category';
            $identifier_key = 'category_id';
            $identifier_value = $category->category_id;
            $external_url = $store->full_url . $menu->url . '/' . $category->url;

        } else {
            redirect('dashboard');
        }

        /* Statistics related variables */
        $datetime = \Altum\Date::get_start_end_dates_new();

        /* Get the required statistics */
        $orders_items_chart = [];

        $orders_result = database()->query("
            SELECT
                SUM(`quantity`) AS `ordered_items`,
                SUM(`price`) AS `value`,
                DATE_FORMAT(`datetime`, '{$datetime['query_date_format']}') AS `formatted_date`
            FROM
                 `orders_items`
            WHERE
                `{$identifier_key}` = {$identifier_value}
                AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");

        /* Generate the raw chart data and save pageviews for later usage */
        while($row = $orders_result->fetch_object()) {
            $row->formatted_date = $datetime['process']($row->formatted_date);

            $orders_items_chart[$row->formatted_date] = [
                'ordered_items' => $row->ordered_items,
                'value' => number_format($row->value, 2, '.', '')
            ];
        }

        $orders_items_chart = get_chart_data($orders_items_chart);

        /* Get ordered items */
        $result = database()->query("
            SELECT
                `orders_items`.`item_id`,
                SUM(`orders_items`.`quantity`) AS `orders`,
                SUM(`orders_items`.`price`) AS `value`,
                `items`.`name`
            FROM
                 `orders_items`
            LEFT JOIN `items` ON `items`.`item_id` = `orders_items`.`item_id`
            WHERE
                `orders_items`.`{$identifier_key}` = {$identifier_value}
                AND (`orders_items`.`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                AND `orders_items`.`item_id` IS NOT NULL
            GROUP BY
                `orders_items`.`item_id`
            ORDER BY
                `orders` DESC
            LIMIT 25
        ");

        /* Store all the results from the database */
        $orders_items = [];

        while($row = $result->fetch_object()) {
            $orders_items[] = $row;
        }

        /* Export handler */
        process_export_csv($orders_items, 'basic');
        process_export_json($orders_items, 'basic');

        /* Establish the account sub menu view */
        $data = [
            $identifier_key => $identifier_value,
            'resource_name' => ${$identifier_name}->name,
            'external_url' => $external_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('orders_statistics.title'), ${$identifier_name}->name));

        /* Prepare the View */
        $data = [
            'identifier_name' => $identifier_name,
            'identifier_key' => $identifier_key,
            'identifier_value' => $identifier_value,
            'external_url' => $external_url,
            $identifier_name => ${$identifier_name},
            'store' => $store,

            'datetime' => $datetime,
            'orders_items_chart' => $orders_items_chart,
            'orders_items' => $orders_items,
        ];

        $view = new \Altum\View('orders-statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
