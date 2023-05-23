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

class Store extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `menus` WHERE `store_id` = {$store->store_id} AND `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('store/' . $store->store_id . '?page=%d')));

        /* Get the menus */
        $menus = [];
        $menus_result = database()->query("
            SELECT
                *
            FROM
                `menus`
            WHERE
                `store_id` = {$store->store_id}
                AND `user_id` = {$this->user->user_id}
            ORDER BY `order`
            {$paginator->get_sql_limit()}
        ");
        while($row = $menus_result->fetch_object()) $menus[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        $date = \Altum\Date::get_start_end_dates(null, null);

        /* Get the required statistics */
        $orders = [];
        $orders_chart = [];

        $orders_result = database()->query("
            SELECT
                COUNT(`order_id`) AS `orders`,
                SUM(`price`) AS `value`,
                DATE_FORMAT(`datetime`, '%Y-%m-%d') AS `formatted_date`
            FROM
                 `orders`
            WHERE
                `store_id` = {$store->store_id}
                AND (`datetime` BETWEEN '{$date->start_date_query}' AND '{$date->end_date_query}')
            GROUP BY
                `formatted_date`
            ORDER BY
                `formatted_date`
        ");

        /* Generate the raw chart data and save pageviews for later usage */
        while($row = $orders_result->fetch_object()) {
            $orders[] = $row;

            $label = \Altum\Date::get($row->formatted_date, 5);

            $orders_chart[$label] = [
                'orders' => $row->orders,
                'value' => $row->value
            ];
        }

        $orders_chart = get_chart_data($orders_chart);

        /* Establish the account sub menu view */
        $data = [
            'store_id' => $store->store_id,
            'resource_name' => $store->name,
            'external_url' => $store->full_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('store.title'), $store->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menus' => $menus,
            'total_menus' => $total_rows,
            'pagination' => $pagination,
            'orders_chart' => $orders_chart,
            'orders' => $orders
        ];

        $view = new \Altum\View('store/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function duplicate() {
        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        if(empty($_POST)) {
            redirect('dashboard');
        }

        /* Make sure that the user didn't exceed the limit */
        $total_rows = db()->where('user_id', $this->user->user_id)->getValue('stores', 'COUNT(*)') ?? 0;
        if($this->user->plan_settings->stores_limit != -1 && $total_rows >= $this->user->plan_settings->stores_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        $store_id = (int) $_POST['store_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('dashboard');
        }

        /* Verify the main resource */
        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));
            while(db()->where('url', $url)->getValue('stores', 'store_id')) {
                $url = mb_strtolower(string_generate(10));
            }

            /* Duplicate the files */
            $logo = \Altum\Uploads::copy_uploaded_file($store->image, \Altum\Uploads::get_path('store_logos'), \Altum\Uploads::get_path('store_logos'));
            $favicon = \Altum\Uploads::copy_uploaded_file($store->image, \Altum\Uploads::get_path('store_favicons'), \Altum\Uploads::get_path('store_favicons'));
            $image = \Altum\Uploads::copy_uploaded_file($store->image, \Altum\Uploads::get_path('store_images'), \Altum\Uploads::get_path('store_images'));

            /* Insert to database */
            $store_id = db()->insert('stores', [
                'user_id' => $this->user->user_id,
                'url' => $url,
                'name' => $store->name,
                'title' => $store->title,
                'description' => $store->description,
                'details' => $store->details,
                'socials' => $store->socials,
                'currency' => $store->currency,
                'password' => $store->password,
                'image' => $image,
                'favicon' => $favicon,
                'logo' => $logo,
                'theme' => $store->theme,
                'timezone' => $store->timezone,
                'custom_css' => $store->custom_css,
                'custom_js' => $store->custom_js,
                'is_se_visible' => $store->is_se_visible,
                'is_removed_branding' => $store->is_removed_branding,
                'email_reports_is_enabled' => $store->email_reports_is_enabled,
                'email_orders_is_enabled' => $store->email_orders_is_enabled,
                'ordering' => $store->ordering,
                'stripe' => $store->stripe,
                'paypal' => $store->paypal,
                'offline_payment' => $store->offline_payment,
                'business' => $store->business,
                'is_enabled' => $store->is_enabled,
                'datetime' => \Altum\Date::$date,
            ]);

            /* Get all menus to duplicate */
            $menus = db()->where('store_id', $store->store_id)->get('menus');

            /* Duplicate all of them */
            foreach($menus as $menu) {
                $url = mb_strtolower(string_generate(10));

                /* Duplicate the file */
                $image = \Altum\Uploads::copy_uploaded_file($menu->image, \Altum\Uploads::get_path('menu_images'), \Altum\Uploads::get_path('menu_images'));

                /* Insert to database */
                $menu_id = db()->insert('menus', [
                    'store_id' => $store_id,
                    'user_id' => $this->user->user_id,
                    'url' => $url,
                    'name' => $menu->name,
                    'description' => $menu->description,
                    'image' => $image,
                    'is_enabled' => $menu->is_enabled,
                    'datetime' => \Altum\Date::$date,
                ]);

                /* Get all categories to duplicate */
                $categories = db()->where('menu_id', $menu->menu_id)->get('categories');

                /* Duplicate all of them */
                foreach($categories as $category) {
                    $url = mb_strtolower(string_generate(10));

                    /* Insert to database */
                    $category_id = db()->insert('categories', [
                        'menu_id' => $menu_id,
                        'store_id' => $category->store_id,
                        'user_id' => $this->user->user_id,
                        'url' => $url,
                        'name' => $category->name,
                        'description' => $category->description,
                        'is_enabled' => $category->is_enabled,
                        'datetime' => \Altum\Date::$date,
                    ]);

                    /* Get all items to duplicate */
                    $items = db()->where('category_id', $category->category_id)->get('items');

                    /* Duplicate all of them */
                    foreach($items as $item) {
                        $url = mb_strtolower(string_generate(10));

                        /* Duplicate the file */
                        $image = \Altum\Uploads::copy_uploaded_file($item->image, \Altum\Uploads::get_path('item_images'), \Altum\Uploads::get_path('item_images'));

                        /* Insert to database */
                        db()->insert('items', [
                            'category_id' => $category_id,
                            'menu_id' => $item->menu_id,
                            'store_id' => $item->store_id,
                            'user_id' => $this->user->user_id,
                            'url' => $url,
                            'name' => $item->name,
                            'description' => $item->description,
                            'image' => $image,
                            'price' => $item->price,
                            'variants_is_enabled' => $item->variants_is_enabled,
                            'is_enabled' => $item->is_enabled,
                            'datetime' => \Altum\Date::$date,
                        ]);
                    }
                }
            }

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . input_clean($menu->name) . '</strong>'));

            /* Redirect */
            redirect('store/' . $store_id);

        }

        redirect('dashboard');
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

        $store_id = (int) query_clean($_POST['store_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$store = Database::get(['store_id', 'domain_id', 'image', 'logo', 'favicon', 'name'], 'stores', ['user_id' => $this->user->user_id, 'store_id' => $store_id])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new \Altum\Models\Store())->delete($store->store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $store->name . '</strong>'));

            redirect('dashboard');

        }

        redirect('dashboard');
    }
}
