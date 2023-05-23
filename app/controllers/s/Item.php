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

class Item extends Controller {
    public $store;
    public $store_user = null;

    public $menu;

    public $category;

    public $item;

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

        /* Init the category */
        require_once APP_PATH . 'controllers/s/Category.php';
        $category_controller = new \Altum\Controllers\Category((array) $this);
        $category_controller->init($this->store->store_id);
        $this->category = $category_controller->category;

        /* Item */
        $this->init($this->store->store_id);

        /* Add statistics */
        $store_controller->create_statistics($this->store->store_id, $this->menu->menu_id, $this->category->category_id, $this->item->item_id);

        /* Get all the extras available */
        $item_extras = (new \Altum\Models\ItemExtras())->get_item_extras_by_store_id_and_item_id($this->store->store_id, $this->item->item_id);

        /* Get item options & variations if needed */
        if($this->item->variants_is_enabled) {
            $item_variants = (new \Altum\Models\ItemVariants())->get_item_variants_by_store_id_and_item_id($this->store->store_id, $this->item->item_id);

            $item_options_ids = [];

            foreach($item_variants as $row) {
                if(!$row->is_enabled) continue;

                $row->item_options_ids = json_decode($row->item_options_ids);

                $item_options_ids = array_reduce($row->item_options_ids, function($carry, $item) {
                    $carry[] = $item->item_option_id;

                    return $carry;
                }, []);

            }

            $item_options = (new \Altum\Models\ItemOptions())->get_item_options_by_store_id_and_item_options_ids($this->store->store_id, $item_options_ids);
        }

        /* Set a custom title */
        Title::set(sprintf(l('s_item.title'), $this->item->name, $this->category->name, $this->menu->name, $this->store->name));

        /* Set the meta tags */
        Meta::set_description(string_truncate($this->item->description, 200));
        Meta::set_social_url($this->store->full_url . $this->menu->url . '/' . $this->category->url . '/' . $this->item->url);
        Meta::set_social_title(sprintf(l('s_item.title'), $this->item->name, $this->category->name, $this->menu->name, $this->store->name));
        Meta::set_social_description(string_truncate($this->item->description, 200));
        Meta::set_social_image($this->item->image ? UPLOADS_FULL_URL . 'item_images/' . $this->item->image : null);

        /* Prepare the header */
        $view = new \Altum\View('s/partials/header', (array) $this);
        $this->add_view_content('header', $view->run(['store' => $this->store]));

        /* Main View */
        $data = [
            'store' => $this->store,
            'store_user' => $this->store_user,
            'menu' => $this->menu,
            'category' => $this->category,
            'item' => $this->item,
            'item_extras' => $item_extras,
            'item_variants' => $item_variants ?? null,
            'item_options' => $item_options ?? null
        ];

        $view = new \Altum\View('s/item/' . $this->store->theme . '/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function init($store_id = null) {
        /* Get the item details */
        if(isset(\Altum\Router::$data['domain']) && \Altum\Router::$data['domain']->store_id) {
            $url = isset($this->params[2]) ? query_clean($this->params[2]) : null;
        } else {
            $url = isset($this->params[3]) ? query_clean($this->params[3]) : null;
        }

        $item = $this->item = (new \Altum\Models\Item())->get_item_by_store_id_and_url($store_id, $url);

        if(!$item || ($item && !$item->is_enabled)) {
            redirect();
        }
    }

}
