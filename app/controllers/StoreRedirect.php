<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Database;
use Altum\Models\User;

class StoreRedirect extends Controller {

    public function index() {

        if(isset($_GET['store_id'])) {
            $store_id = isset($_GET['store_id']) ? (int) $_GET['store_id'] : null;

            if(!$store = Database::get(['store_id', 'domain_id', 'user_id', 'url'], 'stores', ['store_id' => $store_id])) {
                redirect();
            }

            $this->store_user = (new User())->get_user_by_user_id($store->user_id);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->store_user);

            header('Location: ' . $store->full_url);

            die();
        }

        else if(isset($_GET['menu_id'])) {
            $menu_id = isset($_GET['menu_id']) ? (int) $_GET['menu_id'] : null;

            if(!$menu = Database::get(['store_id', 'url'], 'menus', ['menu_id' => $menu_id])) {
                redirect();
            }

            $store = db()->where('store_id', $menu->store_id)->getOne('stores', ['store_id', 'domain_id', 'user_id', 'url']);

            $this->store_user = (new User())->get_user_by_user_id($store->user_id);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->store_user);

            header('Location: ' . $store->full_url . $menu->url);

            die();
        }

        else if(isset($_GET['category_id'])) {
            $category_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;

            if(!$category = Database::get(['menu_id', 'store_id', 'url'], 'categories', ['category_id' => $category_id])) {
                redirect();
            }

            $menu = db()->where('menu_id', $category->menu_id)->getOne('menus', ['url']);

            $store = db()->where('store_id', $category->store_id)->getOne('stores', ['store_id', 'domain_id', 'user_id', 'url']);

            $this->store_user = (new User())->get_user_by_user_id($store->user_id);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->store_user);

            header('Location: ' . $store->full_url . $menu->url . '/' . $category->url);

            die();
        }

        else if(isset($_GET['item_id'])) {
            $item_id = isset($_GET['item_id']) ? (int) $_GET['item_id'] : null;

            if(!$item = Database::get(['category_id', 'menu_id', 'store_id', 'url'], 'items', ['item_id' => $item_id])) {
                redirect();
            }

            $category = db()->where('category_id', $item->category_id)->getOne('categories', ['url']);

            $menu = db()->where('menu_id', $item->menu_id)->getOne('menus', ['url']);

            $store = db()->where('store_id', $item->store_id)->getOne('stores', ['store_id', 'domain_id', 'user_id', 'url']);

            $this->store_user = (new User())->get_user_by_user_id($store->user_id);

            /* Generate the store full URL base */
            $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->store_user);

            header('Location: ' . $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url);

            die();
        } else {

            redirect();

        }

    }
}
