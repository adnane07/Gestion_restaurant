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

class CategoryUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $category_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$category = db()->where('category_id', $category_id)->where('user_id', $this->user->user_id)->getOne('categories')) {
            redirect('dashboard');
        }

        $menu = db()->where('menu_id', $category->menu_id)->where('user_id', $this->user->user_id)->getOne('menus');
        $store = db()->where('store_id', $category->store_id)->where('user_id', $this->user->user_id)->getOne('stores');

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['description'] = trim(query_clean($_POST['description']));
            $_POST['is_enabled'] = (int) (bool) isset($_POST['is_enabled']);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url'] && $_POST['url'] != $category->url) {

                if(db()->where('menu_id', $category->menu_id)->where('url', $_POST['url'])->getOne('categories', ['category_id'])) {
                    Alerts::add_error(l('category.error_message.url_exists'));
                }

            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if(!$_POST['url']) {
                    $_POST['url'] = mb_strtolower(string_generate(10));

                    /* Generate random url if not specified */
                    while(db()->where('menu_id', $category->menu_id)->where('url', $_POST['url'])->getOne('categories', ['category_id'])) {
                        $_POST['url'] = mb_strtolower(string_generate(10));
                    }
                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `categories` SET `url` = ?, `name` = ?, `description` = ?,`is_enabled` = ?, `last_datetime` = ? WHERE `category_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sssssss', $_POST['url'], $_POST['name'], $_POST['description'], $_POST['is_enabled'], \Altum\Date::$date, $category->category_id, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('category-update/' . $category->category_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'category_id' => $category->category_id,
            'resource_name' => $category->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('category_update.title'), $category->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category
        ];

        $view = new \Altum\View('category-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
