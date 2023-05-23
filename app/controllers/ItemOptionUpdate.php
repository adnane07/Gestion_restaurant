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

class ItemOptionUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $item_option_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item_option = Database::get('*', 'items_options', ['item_option_id' => $item_option_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        $item_option->options = json_decode($item_option->options);

        $item = Database::get(['item_id', 'url'], 'items', ['item_id' => $item_option->item_id, 'user_id' => $this->user->user_id]);
        $category = Database::get(['category_id', 'url'], 'categories', ['category_id' => $item_option->category_id, 'user_id' => $this->user->user_id]);
        $menu = Database::get(['menu_id', 'url'], 'menus', ['menu_id' => $item_option->menu_id, 'user_id' => $this->user->user_id]);
        $store = Database::get(['store_id', 'domain_id', 'url', 'currency'], 'stores', ['store_id' => $item_option->store_id, 'user_id' => $this->user->user_id]);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        if(!empty($_POST)) {
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['options'] = explode(',', query_clean($_POST['options']));
            $_POST['options'] = array_map('trim', $_POST['options']);
            $_POST['options'] = json_encode($_POST['options']);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `items_options` SET `name` = ?, `options` = ?, `last_datetime` = ? WHERE `item_option_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sssss', $_POST['name'], $_POST['options'], \Altum\Date::$date, $item_option->item_option_id, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('item-option-update/' . $item_option->item_option_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'item_option_id' => $item_option->item_option_id,
            'resource_name' => $item_option->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('item_option_update.title'), $item_option->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item,
            'item_option' => $item_option
        ];

        $view = new \Altum\View('item-option-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
