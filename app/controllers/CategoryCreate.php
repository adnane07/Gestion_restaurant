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

class CategoryCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $menu_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$menu = db()->where('menu_id', $menu_id)->where('user_id', $this->user->user_id)->getOne('menus')) {
            redirect('dashboard');
        }

        $store = Database::get(['store_id', 'domain_id', 'url', 'currency'], 'stores', ['store_id' => $menu->store_id, 'user_id' => $this->user->user_id]);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `categories` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->categories_limit != -1 && $total_rows >= $this->user->plan_settings->categories_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['description'] = trim(query_clean($_POST['description']));

            //ALTUMCODE.DEMO: if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url']) {

                if(db()->where('menu_id', $menu->menu_id)->where('url', $_POST['url'])->getOne('categories', ['category_id'])) {
                    Alerts::add_error(l('category.error_message.url_exists'));
                }

            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if(!$_POST['url']) {
                    $_POST['url'] = mb_strtolower(string_generate(10));

                    /* Generate random url if not specified */
                    while(db()->where('menu_id', $menu->menu_id)->where('url', $_POST['url'])->getOne('categories', ['category_id'])) {
                        $_POST['url'] = mb_strtolower(string_generate(10));
                    }
                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("INSERT INTO `categories` (`menu_id`, `store_id`, `user_id`, `url`, `name`, `description`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss', $menu->menu_id, $menu->store_id, $this->user->user_id, $_POST['url'], $_POST['name'], $_POST['description'], \Altum\Date::$date);
                $stmt->execute();
                $category_id = $stmt->insert_id;
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('category/' . $category_id);
            }

        }

        /* Set default values */
        $values = [
            'url' => $_POST['url'] ?? '',
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'values' => $values
        ];

        $view = new \Altum\View('category-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
