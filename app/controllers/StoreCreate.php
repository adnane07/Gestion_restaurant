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

class StoreCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `stores` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->stores_limit != -1 && $total_rows >= $this->user->plan_settings->stores_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->user);

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) && $this->user->plan_settings->custom_url_is_enabled ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['title'] = trim(query_clean($_POST['title']));
            $_POST['description'] = trim(query_clean($_POST['description']));
            $_POST['address'] = trim(query_clean($_POST['address']));
            $_POST['currency'] = trim(query_clean($_POST['currency']));
            $_POST['timezone'] = in_array($_POST['timezone'], \DateTimeZone::listIdentifiers()) ? query_clean($_POST['timezone']) : settings()->main->default_timezone;

            $_POST['domain_id'] = isset($_POST['domain_id']) && isset($domains[$_POST['domain_id']]) ? (!empty($_POST['domain_id']) ? (int) $_POST['domain_id'] : null) : null;
            $_POST['is_main_store'] = (bool) isset($_POST['is_main_store']) && isset($domains[$_POST['domain_id']]) && $domains[$_POST['domain_id']]->type == 0;

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url']) {

                $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                $is_existing_store = database()->query("SELECT `store_id` FROM `stores` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;

                if($is_existing_store) {
                    Alerts::add_error(l('store.error_message.url_exists'));
                }

            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $details = json_encode([
                    'address' => $_POST['address'],
                    'phone' => '',
                    'website' => '',
                    'email' => '',
                    'hours' => [
                        '1' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '2' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '3' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '4' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '5' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '6' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ],
                        '7' => [
                            'is_enabled' => 1,
                            'hours' => '',
                        ]
                    ]
                ]);
                $theme = 'new-york';

                if(!$_POST['url']) {
                    $is_existing_store = true;

                    /* Generate random url if not specified */
                    while($is_existing_store) {
                        $_POST['url'] = mb_strtolower(string_generate(10));

                        $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                        $is_existing_store = database()->query("SELECT `store_id` FROM `stores` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;
                    }

                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("INSERT INTO `stores`(`user_id`, `domain_id`, `url`, `name`, `title`, `description`, `details`, `currency`, `theme`, `timezone`, `email_reports_last_datetime`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssssssss', $this->user->user_id, $_POST['domain_id'], $_POST['url'], $_POST['name'], $_POST['title'], $_POST['description'], $details, $_POST['currency'], $theme, $_POST['timezone'], \Altum\Date::$date, \Altum\Date::$date);
                $stmt->execute();
                $store_id = $stmt->insert_id;
                $stmt->close();

                /* Update custom domain if needed */
                if($_POST['is_main_store']) {
                    $stmt = database()->prepare("UPDATE `domains` SET `store_id` = ?, `last_datetime` = ? WHERE `domain_id` = ?");
                    $stmt->bind_param('sss', $store_id, \Altum\Date::$date, $_POST['domain_id']);
                    $stmt->execute();
                    $stmt->close();

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $_POST['domain_id']);
                }

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('store/' . $store_id);
            }

        }

        /* Set default values */
        $values = [
            'url' => $_POST['url'] ?? '',
            'name' => $_POST['name'] ?? '',
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'address' => $_POST['address'] ?? '',
            'currency' => $_POST['currency'] ?? '',
            'timezone' => $_POST['timezone'] ?? '',
            'domain_id' => $_POST['domain_id'] ?? '',
            'is_main_store' => $_POST['is_main_store'] ?? '',
        ];

        /* Prepare the View */
        $data = [
            'domains' => $domains,
            'values' => $values
        ];

        $view = new \Altum\View('store-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
