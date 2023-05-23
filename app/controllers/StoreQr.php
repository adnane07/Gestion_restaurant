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
use Altum\Title;

class StoreQr extends Controller {

    public function index() {

        \Altum\Authentication::guard();

       // if(!$this->user->plan_settings->qr_is_enabled) {
         //   Alerts::add_info(l('global.info_message.plan_feature_no_access'));
           // redirect('dashboard');
        //}

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->getOne('stores')) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Establish the account sub menu view */
        $data = [
            'store_id' => $store->store_id,
            'resource_name' => $store->name,
            'external_url' => $store->full_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('store_qr.title'), $store->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'orders' => $orders
        ];

        $view = new \Altum\View('store-qr/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }


}
