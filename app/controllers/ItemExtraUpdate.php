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

class ItemExtraUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $item_extra_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item_extra = Database::get('*', 'items_extras', ['item_extra_id' => $item_extra_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        $item = Database::get(['item_id', 'url'], 'items', ['item_id' => $item_extra->item_id, 'user_id' => $this->user->user_id]);
        $category = Database::get(['category_id', 'url'], 'categories', ['category_id' => $item_extra->category_id, 'user_id' => $this->user->user_id]);
        $menu = Database::get(['menu_id', 'url'], 'menus', ['menu_id' => $item_extra->menu_id, 'user_id' => $this->user->user_id]);
        $store = Database::get(['store_id', 'domain_id', 'url', 'currency'], 'stores', ['store_id' => $item_extra->store_id, 'user_id' => $this->user->user_id]);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['description'] = trim(query_clean($_POST['description']));
            $_POST['price'] = (float) trim(query_clean($_POST['price']));
            $_POST['is_enabled'] = (int) (bool) isset($_POST['is_enabled']);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `items_extras` SET `name` = ?, `description` = ?, `price` = ?, `is_enabled` = ?, `last_datetime` = ? WHERE `item_extra_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sssssss', $_POST['name'], $_POST['description'], $_POST['price'], $_POST['is_enabled'], \Altum\Date::$date, $item_extra->item_extra_id, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('item-extra-update/' . $item_extra->item_extra_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'item_extra_id' => $item_extra->item_extra_id,
            'resource_name' => $item_extra->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('item_extra_update.title'), $item_extra->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item,
            'item_extra' => $item_extra
        ];

        $view = new \Altum\View('item-extra-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
