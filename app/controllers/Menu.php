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

class Menu extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $menu_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$menu = db()->where('menu_id', $menu_id)->where('user_id', $this->user->user_id)->getOne('menus')) {
            redirect('dashboard');
        }

        $store = Database::get(['store_id', 'domain_id', 'url'], 'stores', ['store_id' => $menu->store_id, 'user_id' => $this->user->user_id]);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `categories` WHERE `menu_id` = {$menu->menu_id} AND `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('menu/' . $menu->menu_id . '?page=%d')));

        /* Get the categories */
        $categories = [];
        $categories_result = database()->query("
            SELECT
                *
            FROM
                `categories`
            WHERE
                `menu_id` = {$menu->menu_id}
                AND `user_id` = {$this->user->user_id}
            ORDER BY `order`

            {$paginator->get_sql_limit()}
        ");
        while($row = $categories_result->fetch_object()) $categories[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Establish the account sub menu view */
        $data = [
            'menu_id' => $menu->menu_id,
            'resource_name' => $menu->name,
            'external_url' => $store->full_url . $menu->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('menu.title'), $menu->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'categories' => $categories,
            'total_categories' => $total_rows,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('menu/index', (array) $this);

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
        $total_rows = db()->where('user_id', $this->user->user_id)->getValue('menus', 'COUNT(*)') ?? 0;
        if($this->user->plan_settings->menus_limit != -1 && $total_rows >= $this->user->plan_settings->menus_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        $menu_id = (int) $_POST['menu_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('dashboard');
        }

        /* Verify the main resource */
        if(!$menu = db()->where('menu_id', $menu_id)->where('user_id', $this->user->user_id)->getOne('menus')) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));
            while(db()->where('store_id', $menu->store_id)->where('url', $url)->getValue('menus', 'menu_id')) {
                $url = mb_strtolower(string_generate(10));
            }

            /* Duplicate the file */
            $image = \Altum\Uploads::copy_uploaded_file($menu->image, \Altum\Uploads::get_path('menu_images'), \Altum\Uploads::get_path('menu_images'));

            /* Insert to database */
            $menu_id = db()->insert('menus', [
                'store_id' => $menu->store_id,
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

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . input_clean($menu->name) . '</strong>'));

            /* Redirect */
            redirect('menu/' . $menu_id);

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

        $menu_id = (int) query_clean($_POST['menu_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$menu = Database::get(['store_id', 'menu_id', 'image', 'name'], 'menus', ['user_id' => $this->user->user_id, 'menu_id' => $menu_id])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the items images first */
            $result = database()->query("SELECT `image` FROM `items` WHERE `menu_id` = {$menu->menu_id}");
            while($item = $result->fetch_object()) {
                if(!empty($item->image) && file_exists(UPLOADS_PATH . 'item_images/' . $item->image)) {
                    unlink(UPLOADS_PATH . 'item_images/' . $item->image);
                }
            }

            /* Delete the image if needed */
            if(!empty($menu->image) && file_exists(UPLOADS_PATH . 'menu_images/' . $menu->image)) {
                unlink(UPLOADS_PATH . 'menu_images/' . $menu->image);
            }

            /* Delete the menu */
            database()->query("DELETE FROM `menus` WHERE `menu_id` = {$menu->menu_id}");

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $menu->store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $menu->name . '</strong>'));

            redirect('store/' . $menu->store_id);

        }

        redirect('store/' . $menu->store_id);
    }

    public function order_ajax() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            die();
        }

        $_POST = json_decode(file_get_contents('php://input'), true);

        $store_id = (int) $_POST['store_id'];

        if(empty($_POST)) {
            die();
        }

        if(!isset($_POST['blocks']) || (isset($_POST['blocks']) && !is_array($_POST['blocks']))) {
            die();
        }

        if(!\Altum\Csrf::check('global_token')) {
            die();
        }

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            die();
        }

        foreach($_POST['blocks'] as $block) {
            $block['menu_id'] = (int) $block['menu_id'];
            $block['order'] = (int) $block['order'];

            /* Update the order */
            db()->where('menu_id', $block['menu_id'])->where('user_id', $this->user->user_id)->update('menus', ['order' => $block['order']]);

        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

        die();
    }
}
