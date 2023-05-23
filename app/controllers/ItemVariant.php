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

class ItemVariant extends Controller {

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

        $item_variant_id = (int) query_clean($_POST['item_variant_id']);

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        /* Make sure the store id is created by the logged in user */
        if(!$item_variant = Database::get(['store_id', 'menu_id', 'category_id', 'item_id', 'item_variant_id'], 'items_variants', ['user_id' => $this->user->user_id, 'item_variant_id' => $item_variant_id])) {
            redirect('dashboard');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Delete the resource */
            db()->where('item_variant_id', $item_variant->item_variant_id)->delete('items_variants');

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $item_variant->store_id);

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.delete2'));

            redirect('item/' . $item_variant->item_id);

        }

        redirect('item/' . $item_variant->item_id);
    }
}
