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

class Client extends Controller {

    public function index() {

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->user, false);

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled'], ['name'], ['datetime', 'pageviews', 'name', 'orders']));
        $filters->set_default_order_by('store_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);
       


        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `stores` WHERE `user_id` = {$this->user->user_id} {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('client/' . $store->store_id . '?page=%d')));

        /* Get the stores */
        $stores = [];
        $stores_result = database()->query("
            SELECT
                *
            FROM
                `stores`
            WHERE
                `user_id` = {$this->user->user_id}
                {$filters->get_sql_where()}
                {$filters->get_sql_order_by()}

            {$paginator->get_sql_limit()}
        ");
        while($row = $stores_result->fetch_object()) {

            /* Generate the store full URL base */
            $row->full_url = (new \Altum\Models\Store())->get_store_full_url($row, $this->user, $domains);

            $stores[] = $row;
        }

        /* Get some extra data for the widgets */
        $stores_statistics = database()->query("SELECT COUNT(*) AS `stores`, SUM(`pageviews`) AS `pageviews`, SUM(`orders`) AS `orders` FROM `stores` WHERE `user_id` = {$this->user->user_id}")->fetch_object();

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);
   
     
                /* Set a custom title */
        Title::set(sprintf(l('client.costumer'), $pending_orders));
        
        
        /* Prepare the View */
        $data = [
            'stores' => $stores,
            'store_id' => $store->store_id,

            'total_stores' => $total_rows,
            'pagination' => $pagination,
            'filters' => $filters,
            'stores_statistics' => $stores_statistics,

            'resource_name' => $store->name,
           'external_url' => $store->full_url

        ];
       
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));
       
        $view = new \Altum\View('client/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

       
    }

}
