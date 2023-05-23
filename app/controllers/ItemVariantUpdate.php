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

class ItemVariantUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $item_variant_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item_variant = Database::get('*', 'items_variants', ['item_variant_id' => $item_variant_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        $item_variant->item_options_ids = json_decode($item_variant->item_options_ids);

        /* Get all the available options for this item */
        $item_options = [];
        $item_options_result = database()->query("
            SELECT
                *
            FROM
                `items_options`
            WHERE
                `item_id` = {$item_variant->item_id}
                AND `user_id` = {$this->user->user_id}
        ");
        while($row = $item_options_result->fetch_object()) {
            $row->options = json_decode($row->options);
            $item_options[$row->item_option_id] = $row;
        }

        $item = Database::get(['item_id', 'url'], 'items', ['item_id' => $item_variant->item_id, 'user_id' => $this->user->user_id]);
        $category = Database::get(['category_id', 'url'], 'categories', ['category_id' => $item_variant->category_id, 'user_id' => $this->user->user_id]);
        $menu = Database::get(['menu_id', 'url'], 'menus', ['menu_id' => $item_variant->menu_id, 'user_id' => $this->user->user_id]);
        $store = Database::get(['store_id', 'domain_id', 'url', 'currency'], 'stores', ['store_id' => $item_variant->store_id, 'user_id' => $this->user->user_id]);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        if(!empty($_POST)) {
            $_POST['price'] = (float) trim(query_clean($_POST['price']));
            $_POST['is_enabled'] = (int) (bool) isset($_POST['is_enabled']);

            /* Process the submitted options */
            $item_options_ids = [];

            foreach($_POST['item_options_ids'] as $key => $value) {
                if(isset($item_options[$key])) {
                    $item_options_ids[] = [
                        'item_option_id' => (int) $key,
                        'option' => (int) $value
                    ];
                } else {
                    unset($_POST['item_options_ids'][$key]);
                }
            }

            $item_options_ids = json_encode($item_options_ids);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `items_variants` SET `item_options_ids` = ?, `price` = ?, `last_datetime` = ? WHERE `item_variant_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sssss', $item_options_ids, $_POST['price'], \Altum\Date::$date, $item_variant->item_variant_id, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(l('global.success_message.update2'));

                redirect('item-variant-update/' . $item_variant->item_variant_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'item_variant_id' => $item_variant->item_variant_id,
            'resource_name' => $item_variant->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item,
            'item_variant' => $item_variant,
            'item_options' => $item_options
        ];

        $view = new \Altum\View('item-variant-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
