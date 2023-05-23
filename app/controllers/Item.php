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

class Item extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        $item_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item = db()->where('item_id', $item_id)->where('user_id', $this->user->user_id)->getOne('items')) {
            redirect('dashboard');
        }

        $category = db()->where('category_id', $item->category_id)->where('user_id', $this->user->user_id)->getOne('categories', ['category_id', 'url']);

        $menu = db()->where('menu_id', $item->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);

        $store = db()->where('store_id', $item->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency']);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Get the item extras */
        $item_extras = [];
        $item_extras_result = database()->query("
            SELECT
                *
            FROM
                `items_extras`
            WHERE
                `item_id` = {$item->item_id}
                AND `user_id` = {$this->user->user_id}
        ");
        while($row = $item_extras_result->fetch_object()) $item_extras[] = $row;

        /* We need extra data if the product has variants enabled */
        if($item->variants_is_enabled) {

            /* Get the item options */
            $item_options = [];
            $item_options_result = database()->query("
                SELECT
                    *
                FROM
                    `items_options`
                WHERE
                    `item_id` = {$item->item_id}
                    AND `user_id` = {$this->user->user_id}
            ");
            while($row = $item_options_result->fetch_object()) {
                $row->options = json_decode($row->options);
                $item_options[$row->item_option_id] = $row;
            }

            /* Get the item variants */
            $item_variants = [];
            $item_variants_result = database()->query("
                SELECT
                    *
                FROM
                    `items_variants`
                WHERE
                    `item_id` = {$item->item_id}
                    AND `user_id` = {$this->user->user_id}
            ");
            while($row = $item_variants_result->fetch_object()) {
                $row->item_options_ids = json_decode($row->item_options_ids);

                $item_variants[] = $row;
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'item_id' => $item->item_id,
            'resource_name' => $item->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('item.title'), $item->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item,
            'item_extras' => $item_extras,
            'item_options' => $item_options ?? null,
            'item_variants' => $item_variants ?? null,
        ];

        $view = new \Altum\View('item/index', (array) $this);

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
        $total_rows = db()->where('user_id', $this->user->user_id)->getValue('items', 'COUNT(*)') ?? 0;
        if($this->user->plan_settings->items_limit != -1 && $total_rows >= $this->user->plan_settings->items_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        $item_id = (int) $_POST['item_id'];

        //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('dashboard');
        }

        /* Verify the main resource */
        if(!$item = db()->where('item_id', $item_id)->where('user_id', $this->user->user_id)->getOne('items')) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Generate random url if not specified */
            $url = mb_strtolower(string_generate(10));
            while(db()->where('category_id', $item->category_id)->where('url', $url)->getValue('items', 'item_id')) {
                $url = mb_strtolower(string_generate(10));
            }

            /* Duplicate the file */
            $image = \Altum\Uploads::copy_uploaded_file($item->image, \Altum\Uploads::get_path('item_images'), \Altum\Uploads::get_path('item_images'));

            /* Insert to database */
            $item_id = db()->insert('items', [
                'category_id' => $item->category_id,
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

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . input_clean($item->name) . '</strong>'));

            /* Redirect */
            redirect('item/' . $item_id);

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

        $item_id = (int) query_clean($_POST['item_id']);

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$item = Database::get(['store_id', 'menu_id', 'category_id', 'item_id', 'image', 'name'], 'items', ['user_id' => $this->user->user_id, 'item_id' => $item_id])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the image if needed */
            if(!empty($item->image) && file_exists(UPLOADS_PATH . 'item_images/' . $item->image)) {
                unlink(UPLOADS_PATH . 'item_images/' . $item->image);
            }

            /* Delete the item */
            db()->where('item_id', $item->item_id)->delete('items');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $item->store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $item->name . '</strong>'));

            redirect('category/' . $item->category_id);

        }

        redirect('category/' . $item->category_id);
    }

    public function order_ajax() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
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
            $block['item_id'] = (int) $block['item_id'];
            $block['order'] = (int) $block['order'];

            /* Update the order */
            db()->where('item_id', $block['item_id'])->where('user_id', $this->user->user_id)->update('items', ['order' => $block['order']]);

        }

        /* Clear the cache */
        \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

        die();
    }

}
