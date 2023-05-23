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

class Statistics extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        if(!$this->user->plan_settings->analytics_is_enabled) {
            redirect('dashboard');
        }

        if(isset($_GET['store_id'])) {
            $store_id = isset($_GET['store_id']) ? (int) $_GET['store_id'] : null;

            if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
                redirect('dashboard');
            }

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'store';
            $identifier_key = 'store_id';
            $identifier_value = $store->store_id;
            $external_url = $store->full_url;
        }

        else if(isset($_GET['menu_id'])) {
            $menu_id = isset($_GET['menu_id']) ? (int) $_GET['menu_id'] : null;

            if(!$menu = db()->where('menu_id', $menu_id)->where('user_id', $this->user->user_id)->getOne('menus')) {
                redirect('dashboard');
            }

            $store = db()->where('store_id', $menu->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url']);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'menu';
            $identifier_key = 'menu_id';
            $identifier_value = $menu->menu_id;
            $external_url = $store->full_url . $menu->url;
        }

        else if(isset($_GET['category_id'])) {
            $category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

            if(!$category = db()->where('category_id', $category_id)->where('user_id', $this->user->user_id)->getOne('categories')) {
                redirect('dashboard');
            }

            $menu = db()->where('menu_id', $category->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);

            $store = db()->where('store_id', $category->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url']);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'category';
            $identifier_key = 'category_id';
            $identifier_value = $category->category_id;
            $external_url = $store->full_url . $menu->url . '/' . $category->url;
        }

        else if(isset($_GET['item_id'])) {
            $item_id = isset($_GET['item_id']) ? (int) $_GET['item_id'] : null;

            if(!$item = db()->where('item_id', $item_id)->where('user_id', $this->user->user_id)->getOne('items')) {
                redirect('dashboard');
            }

            $category = db()->where('category_id', $item->category_id)->where('user_id', $this->user->user_id)->getOne('categories', ['category_id', 'url']);

            $menu = db()->where('menu_id', $item->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);

            $store = db()->where('store_id', $item->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url']);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

            $identifier_name = 'item';
            $identifier_key = 'item_id';
            $identifier_value = $item->item_id;
            $external_url = $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url;
        } else {
            redirect('dashboard');
        }

        /* Statistics related variables */
        $type = isset($_GET['type']) && in_array($_GET['type'], ['overview', 'entries', 'referrer_host', 'referrer_path', 'country', 'city_name', 'os', 'browser', 'device', 'language', 'utm_source', 'utm_medium', 'utm_campaign']) ? input_clean($_GET['type']) : 'overview';

        $datetime = \Altum\Date::get_start_end_dates_new();

        /* Get data based on what statistics are needed */
        switch($type) {
            case 'overview':

                /* Get the required statistics */
                $pageviews = [];
                $pageviews_chart = [];

                $pageviews_result = database()->query("
                    SELECT
                        COUNT(`id`) AS `pageviews`,
                        SUM(`is_unique`) AS `visitors`,
                        DATE_FORMAT(`datetime`, '{$datetime['query_date_format']}') AS `formatted_date`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `formatted_date`
                    ORDER BY
                        `formatted_date`
                ");

                /* Generate the raw chart data and save pageviews for later usage */
                while($row = $pageviews_result->fetch_object()) {
                    $pageviews[] = $row;

                    $row->formatted_date = $datetime['process']($row->formatted_date);

                    $pageviews_chart[$row->formatted_date] = [
                        'pageviews' => $row->pageviews,
                        'visitors' => $row->visitors
                    ];
                }

                $pageviews_chart = get_chart_data($pageviews_chart);

                $result = database()->query("
                    SELECT
                        *
                    FROM
                        `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    ORDER BY
                        `datetime` DESC
                    LIMIT 25
                ");

                break;

            case 'entries':

                /* Prepare the filtering system */
                $filters = (new \Altum\Filters([], [], ['datetime']));
                $filters->set_default_order_by('id', settings()->main->default_order_type);
                $filters->set_default_results_per_page(settings()->main->default_results_per_page);

                /* Prepare the paginator */
                $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `statistics` WHERE `{$identifier_key}` = {$identifier_value} AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}') {$filters->get_sql_where()}")->fetch_object()->total ?? 0;
                $paginator = (new \Altum\Paginator($total_rows, $filters->get_results_per_page(), $_GET['page'] ?? 1, url('statistics?' . $identifier_key . '=' . $identifier_value . '&type=' . $type . '&start_date=' . $datetime['start_date'] . '&end_date=' . $datetime['end_date'] . $filters->get_get() . '&page=%d')));

                $result = database()->query("
                    SELECT
                        *
                    FROM
                        `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    {$filters->get_sql_where()}
                    {$filters->get_sql_order_by()}
                    {$paginator->get_sql_limit()}
                ");

                break;

            case 'referrer_host':
            case 'country':
            case 'os':
            case 'browser':
            case 'device':
            case 'language':

                $columns = [
                    'referrer_host' => 'referrer_host',
                    'referrer_path' => 'referrer_path',
                    'country' => 'country_code',
                    'city_name' => 'city_name',
                    'os' => 'os_name',
                    'browser' => 'browser_name',
                    'device' => 'device_type',
                    'language' => 'browser_language'
                ];

                $result = database()->query("
                    SELECT
                        `{$columns[$type]}`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `{$columns[$type]}`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;

            case 'referrer_path':

                $referrer_host = input_clean($_GET['referrer_host']);

                $result = database()->query("
                    SELECT
                        `referrer_path`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND `referrer_host` = '{$referrer_host}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `referrer_path`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;

            case 'city_name':

                $country_code = isset($_GET['country_code']) ? input_clean($_GET['country_code']) : null;

                $result = database()->query("
                    SELECT
                        `city_name`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        " . ($country_code ? "AND `country_code` = '{$country_code}'" : null) . "
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `city_name`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;

            case 'utm_source':

                $result = database()->query("
                    SELECT
                        `utm_source`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                        AND `utm_source` IS NOT NULL
                    GROUP BY
                        `utm_source`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;

            case 'utm_medium':

                $utm_source = input_clean($_GET['utm_source']);

                $result = database()->query("
                    SELECT
                        `utm_medium`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND `utm_source` = '{$utm_source}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `utm_medium`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;

            case 'utm_campaign':

                $utm_source = input_clean($_GET['utm_source']);
                $utm_medium = input_clean($_GET['utm_medium']);

                $result = database()->query("
                    SELECT
                        `utm_campaign`,
                        COUNT(*) AS `total`
                    FROM
                         `statistics`
                    WHERE
                        `{$identifier_key}` = {$identifier_value}
                        AND `utm_source` = '{$utm_source}'
                        AND `utm_medium` = '{$utm_medium}'
                        AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')
                    GROUP BY
                        `utm_campaign`
                    ORDER BY
                        `total` DESC
                    LIMIT 250
                ");

                break;
        }

        switch($type) {
            case 'overview':

                $statistics_keys = [
                    'country_code',
                    'referrer_host',
                    'device_type',
                    'os_name',
                    'browser_name',
                    'browser_language'
                ];

                $latest = [];
                $statistics = [];
                foreach($statistics_keys as $key) {
                    $statistics[$key] = [];
                    $statistics[$key . '_total_sum'] = 0;
                }

                $has_data = $result->num_rows;

                /* Start processing the rows from the database */
                while($row = $result->fetch_object()) {
                    foreach($statistics_keys as $key) {

                        $statistics[$key][$row->{$key}] = isset($statistics[$key][$row->{$key}]) ? $statistics[$key][$row->{$key}] + 1 : 1;

                        $statistics[$key . '_total_sum']++;

                    }

                    $latest[] = $row;
                }

                foreach($statistics_keys as $key) {
                    arsort($statistics[$key]);
                }

                /* Prepare the statistics method View */
                $data = [
                    'identifier_name' => $identifier_name,
                    'identifier_key' => $identifier_key,
                    'identifier_value' => $identifier_value,
                    'latest' => $latest,
                    'statistics' => $statistics,
                    'datetime' => $datetime,
                    'pageviews' => $pageviews,
                    'pageviews_chart' => $pageviews_chart
                ];

                break;

            case 'entries':

                /* Store all the results from the database */
                $statistics = [];

                while($row = $result->fetch_object()) {
                    $statistics[] = $row;
                }

                /* Prepare the pagination view */
                $pagination = (new \Altum\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

                /* Prepare the statistics method View */
                $data = [
                    'rows' => $statistics,
                    'identifier_name' => $identifier_name,
                    'identifier_key' => $identifier_key,
                    'identifier_value' => $identifier_value,
                    'datetime' => $datetime,
                    'pagination' => $pagination,
                    'filters' => $filters,
                ];

                $has_data = count($statistics);

                break;

            case 'referrer_host':
            case 'country':
            case 'city_name':
            case 'os':
            case 'browser':
            case 'device':
            case 'language':
            case 'referrer_path':
            case 'utm_source':
            case 'utm_medium':
            case 'utm_campaign':

                /* Store all the results from the database */
                $statistics = [];
                $statistics_total_sum = 0;

                while($row = $result->fetch_object()) {
                    $statistics[] = $row;

                    $statistics_total_sum += $row->total;
                }

                /* Prepare the statistics method View */
                $data = [
                    'identifier_name' => $identifier_name,
                    'identifier_key' => $identifier_key,
                    'identifier_value' => $identifier_value,

                    'type' => $type,
                    'rows' => $statistics,
                    'total_sum' => $statistics_total_sum,
                    'datetime' => $datetime,

                    'referrer_host' => $referrer_host ?? null,
                    'country_code' => $country_code ?? null,
                    'utm_source' => $utm_source ?? null,
                    'utm_medium' => $utm_medium ?? null,
                ];

                $has_data = count($statistics);

                break;
        }

        /* Export handler */
        process_export_csv($statistics, 'basic');
        process_export_json($statistics, 'basic');

        $view = new \Altum\View('statistics/statistics_' . $type, (array) $this);
        $this->add_view_content('statistics', $view->run($data));

        /* Establish the account sub menu view */
        $data = [
            $identifier_key => $identifier_value,
            'resource_name' => ${$identifier_name}->name,
            'external_url' => $external_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('statistics.title'), ${$identifier_name}->name));

        /* Prepare the View */
        $data = [
            'identifier_name' => $identifier_name,
            'identifier_key' => $identifier_key,
            'identifier_value' => $identifier_value,
            'external_url' => $external_url,
            $identifier_name => ${$identifier_name},

            'type' => $type,
            'datetime' => $datetime,
            'has_data' => $has_data,

            'pageviews' => $pageviews ?? [],
            'pageviews_chart' => $pageviews_chart ?? [],
        ];

        $view = new \Altum\View('statistics/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function reset() {

        \Altum\Authentication::guard();

        if(!$this->user->plan_settings->analytics_is_enabled) {
            redirect('dashboard');
        }

        if(empty($_POST)) {
            redirect('dashboard');
        }

        $identifier_name = null;

        if(isset($_POST['store_id'])) {
            $identifier_key = 'store_id';
            $identifier_name = 'store';
            $table = 'stores';
        }

        else if(isset($_POST['category_id'])) {
            $identifier_key = 'category_id';
            $identifier_name = 'category';
            $table = 'categories';
        }

        else if(isset($_POST['menu_id'])) {
            $identifier_key = 'menu_id';
            $identifier_name = 'menu';
            $table = 'menus';
        }

        else if(isset($_POST['item_id'])) {
            $identifier_key = 'item_id';
            $identifier_name = 'item';
            $table = 'items';
        }

        if(!$identifier_name || !$table) {
            redirect('dashboard');
        }

        $id = (int) $_POST[$identifier_key];
        $datetime = \Altum\Date::get_start_end_dates_new($_POST['start_date'], $_POST['end_date']);

        /* Make sure the resource id is created by the logged in user */
        if(!$resource = db()->where($identifier_key, $id)->where('user_id', $this->user->user_id)->has($table)) {
            redirect('dashboard');
        }

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('delete')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('statistics?' . $identifier_key . '=' . $id);
        }

        //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

        if(!\Altum\Csrf::check()) {
            Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            redirect('statistics?' . $identifier_key . '=' . $id);
        }

        if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

            /* Clear statistics data */
            database()->query("DELETE FROM `statistics` WHERE `{$identifier_key}` = {$id} AND (`datetime` BETWEEN '{$datetime['query_start_date']}' AND '{$datetime['query_end_date']}')");

            /* Set a nice success message */
            Alerts::add_success(l('global.success_message.update2'));

            redirect('statistics?' . $identifier_key . '=' . $id);

        }

        redirect('statistics?' . $identifier_key . '=' . $id);

    }

}
