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

class ItemOption extends Controller {

    public function index() {

        die();

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

        $item_option_id = (int) query_clean($_POST['item_option_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$item_option = db()->where('item_option_id', $item_option_id)->where('user_id', $this->user->user_id)->getOne('items_options', ['store_id', 'menu_id', 'category_id', 'item_id', 'item_option_id', 'name'])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('item_option_id', $item_option->item_option_id)->delete('items_options');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $item_option->store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $item_option->name . '</strong>'));

            redirect('item/' . $item_option->item_id);

        }

        redirect('item/' . $item_option->item_id);
    }
}
