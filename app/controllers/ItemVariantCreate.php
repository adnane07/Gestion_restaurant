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

class ItemVariantCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $item_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item = db()->where('item_id', $item_id)->where('user_id', $this->user->user_id)->getOne('items', ['item_id', 'category_id', 'menu_id', 'store_id', 'url'])) {
            redirect('dashboard');
        }

        /* Get all the available options for this item */
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

        $category = db()->where('category_id', $item->category_id)->where('user_id', $this->user->user_id)->getOne('categories', ['category_id', 'url']);

        $menu = db()->where('menu_id', $item->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);

        $store = db()->where('store_id', $item->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency']);

        if(!empty($_POST)) {
            $_POST['price'] = (float) trim(query_clean($_POST['price']));
            $is_enabled = 1;

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
                $stmt = database()->prepare("INSERT INTO `items_variants` (`item_id`, `category_id`, `menu_id`, `store_id`, `user_id`, `price`, `item_options_ids`, `is_enabled`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssssss', $item->item_id, $category->category_id, $menu->menu_id, $store->store_id, $this->user->user_id, $_POST['price'], $item_options_ids, $is_enabled, \Altum\Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(l('global.success_message.create2'));

                redirect('item/' . $item->item_id);
            }

        }

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item,
            'item_options' => $item_options
        ];

        $view = new \Altum\View('item-variant-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
