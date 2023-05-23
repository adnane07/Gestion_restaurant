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

class AdminStores extends Controller {

    public function index() {

        /* Prepare the filtering system */
        $filters = (new \Altum\Filters(['is_enabled', 'user_id', 'domain_id'], ['name'], ['datetime', 'name', 'pageviews', 'orders']));
        $filters->set_default_order_by('store_id', settings()->main->default_order_type);
        $filters->set_default_results_per_page(settings()->main->default_results_per_page);

        /* Prepare the paginator */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `stores` WHERE 1 = 1 {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('admin/stores?' . $filters->get_get() . '&page=%d')));

        /* Get the users */
        $stores = [];
        $stores_result = database()->query("
            SELECT
                `stores`.*, `users`.`name` AS `user_name`, `users`.`email` AS `user_email`
            FROM
                `stores`
            LEFT JOIN
                `users` ON `stores`.`user_id` = `users`.`user_id`
            WHERE
                1 = 1
                {$filters->get_sql_where('stores')}
                {$filters->get_sql_order_by('stores')}

            {$paginator->get_sql_limit()}
        ");
        while($row = $stores_result->fetch_object()) {
            $stores[] = $row;
        }

        /* Prepare the pagination view */
        $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Main View */
        $data = [
            'stores' => $stores,
            'filters' => $filters,
            'pagination' => $pagination
        ];

        $view = new \Altum\View('admin/stores/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function bulk() {

        /* Check for any errors */
        if(empty($_POST)) {
            redirect('admin/stores');
        }

        if(empty($_POST['selected'])) {
            redirect('admin/stores');
        }

        if(!isset($_POST['type']) || (isset($_POST['type']) && !in_array($_POST['type'], ['delete']))) {
            redirect('admin/stores');
        }

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            switch($_POST['type']) {
                case 'delete':

                    foreach($_POST['selected'] as $store_id) {
                        (new \Altum\Models\Store())->delete($store_id);
                    }
                    break;
            }

            /* Set a nice success message */
            Alerts::add_success(l('admin_bulk_delete_modal.success_message'));

        }

        redirect('admin/stores');
    }

    public function delete() {

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        //ALTUMCODE:DEMO if(DEMO) Alerts::add_error('This command is blocked on the demo.');

        if(!\Altum\Csrf::check('global_token')) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
        }

        if(!$store = db()->where('store_id', $store_id)->getOne('stores', ['store_id', 'name'])) {
            redirect('admin/stores');
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            (new \Altum\Models\Store())->delete($store_id);

            /* Set a nice success message */
            Alerts::add_success(sprintf(l('global.success_message.delete1'), '<strong>' . $store->name . '</strong>'));

        }

        redirect('admin/stores');
    }

}
