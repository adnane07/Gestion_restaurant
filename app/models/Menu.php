<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class Menu extends Model {

    public function get_menu_by_store_id_and_url($store_id, $url) {

        /* Get the menu */
        $post = null;

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('menu?store_id=' . $store_id . '&url=' . $url);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $post = database()->query("SELECT * FROM `menus` WHERE `store_id` = {$store_id} AND `url` = '{$url}'")->fetch_object() ?? null;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($post)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $post = $cache_instance->get();

        }

        return $post;

    }

    public function get_menus_by_store_id($store_id) {

        /* Get the store posts */
        $menus = [];

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('r_menus?store_id=' . $store_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $menus_result = database()->query("
                SELECT 
                    *
                FROM 
                    `menus` 
                WHERE 
                    `store_id` = {$store_id}
                    AND `is_enabled` = 1
                ORDER BY `order`
            ");
            while($row = $menus_result->fetch_object()) $menus[] = $row;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($menus)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $menus = $cache_instance->get();

        }

        return $menus;

    }

}
