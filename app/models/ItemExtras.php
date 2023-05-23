<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class ItemExtras extends Model {

    public function get_item_extras_by_store_id_and_item_id($store_id, $item_id) {

        /* Get the item extras */
        $item_extras = [];

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_item_extras?store_id=' . $store_id . '&item_id=' . $item_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $item_extras_result = database()->query("
                SELECT 
                    *
                FROM 
                    `items_extras` 
                WHERE 
                    `item_id` = {$item_id} 
            ");
            while($row = $item_extras_result->fetch_object()) $item_extras[] = $row;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($item_extras)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $item_extras = $cache_instance->get();

        }

        return $item_extras;

    }

    public function get_item_extras_by_store_id_and_item_extras_ids($store_id, $item_extras_ids) {

        $item_extras_ids = implode(',', $item_extras_ids);

        /* Get the item extras */
        $item_extras = [];

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_item_extras?store_id=' . $store_id . '&item_extras_ids=' . $item_extras_ids);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $item_extras_result = database()->query("
                SELECT 
                    *
                FROM 
                    `items_extras` 
                WHERE 
                    `item_extra_id` IN ({$item_extras_ids})
            ");
            while($row = $item_extras_result->fetch_object()) $item_extras[] = $row;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($item_extras)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $item_extras = $cache_instance->get();

        }

        return $item_extras;

    }

}
