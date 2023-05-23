<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Meta;
use Altum\Title;

class Category extends Controller {
    public $store;
    public $store_user = null;

    public $menu;

    public $category;

    public function index() {

        /* Parse & control the store */
        require_once APP_PATH . 'controllers/s/Store.php';
        $store_controller = new \Altum\Controllers\Store((array) $this);

        $store_controller->init();

        /* Check if the user has access */
        if(!$store_controller->has_access) {
            header('Location: ' . $store_controller->store->full_url); die();
        }

        /* Set the needed variables for the wrapper */
        $this->store_user = $store_controller->store_user;
        $this->store = $store_controller->store;

        /* Init the menu */
        require_once APP_PATH . 'controllers/s/Menu.php';
        $menu_controller = new \Altum\Controllers\Menu((array) $this);
        $menu_controller->init($this->store->store_id);
        $this->menu = $menu_controller->menu;

        /* Category */
        $this->init($this->store->store_id);

        /* Add statistics */
        $store_controller->create_statistics($this->store->store_id, $this->menu->menu_id, $this->category->category_id);

        /* Get the available items */
        $items = (new \Altum\Models\Item())->get_items_by_store_id_and_category_id($this->store->store_id, $this->category->category_id);

        /* Set a custom title */
        Title::set(sprintf(l('s_category.title'), $this->category->name, $this->menu->name, $this->store->name));

        /* Set the meta tags */
        Meta::set_description(string_truncate($this->category->description, 200));
        Meta::set_social_url($this->store->full_url . $this->menu->url . '/' . $this->category->url);
        Meta::set_social_title(sprintf(l('s_category.title'), $this->category->name, $this->menu->name, $this->store->name));
        Meta::set_social_description(string_truncate($this->category->description, 200));

        /* Prepare the header */
        $view = new \Altum\View('s/partials/header', (array) $this);
        $this->add_view_content('header', $view->run(['store' => $this->store]));

        /* Main View */
        $data = [
            'store' => $this->store,
            'store_user' => $this->store_user,
            'menu' => $this->menu,
            'category' => $this->category,
            'items' => $items
        ];

        $view = new \Altum\View('s/category/' . $this->store->theme . '/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function init($store_id = null) {
        /* Get the category details */
        if(isset(\Altum\Router::$data['domain']) && \Altum\Router::$data['domain']->store_id) {
            $url = isset($this->params[1]) ? query_clean($this->params[1]) : null;
        } else {
            $url = isset($this->params[2]) ? query_clean($this->params[2]) : null;
        }

        $category = $this->category = (new \Altum\Models\Category())->get_category_by_store_id_and_url($store_id, $url);

        if(!$category || ($category && !$category->is_enabled)) {
            redirect();
        }
    }

}
