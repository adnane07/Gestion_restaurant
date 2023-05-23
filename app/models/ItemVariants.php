<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

class ItemVariants extends Model {

    public function get_item_variant_by_store_id_and_item_variant_id($store_id, $item_variant_id) {

        /* Get the item */
        $item = null;

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_item_variant?store_id=' . $store_id . '&item_variant_id=' . $item_variant_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $item = database()->query("SELECT * FROM `items_variants` WHERE `store_id` = {$store_id} AND `item_variant_id` = '{$item_variant_id}'")->fetch_object() ?? null;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($item)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $item = $cache_instance->get();

        }

        return $item;

    }

    public function get_item_variants_by_store_id_and_item_id($store_id, $item_id) {

        /* Get the item variants */
        $item_variants = [];

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_item_variants?store_id=' . $store_id . '&item_id=' . $item_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $item_variants_result = database()->query("
                SELECT 
                    *
                FROM 
                    `items_variants` 
                WHERE 
                    `item_id` = {$item_id} 
            ");
            while($row = $item_variants_result->fetch_object()) $item_variants[] = $row;

            \Altum\Cache::$adapter->save(
                $cache_instance->set($item_variants)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store_id)
            );

        } else {

            /* Get cache */
            $item_variants = $cache_instance->get();

        }

        return $item_variants;

    }

}
