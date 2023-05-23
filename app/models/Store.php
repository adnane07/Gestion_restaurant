<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Models;

use Altum\Database;

class Store extends Model {

    public function get_store_full_url($store, $user, $domains = null) {

        /* Detect the URL of the store */
        if($store->domain_id) {

            /* Get available custom domains */
            if(!$domains) {
                $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($user, false);
            }

            if(isset($domains[$store->domain_id])) {

                if($store->store_id == $domains[$store->domain_id]->store_id) {

                    $store->full_url = $domains[$store->domain_id]->scheme . $domains[$store->domain_id]->host . '/';

                } else {

                    $store->full_url = $domains[$store->domain_id]->scheme . $domains[$store->domain_id]->host . '/' . $store->url . '/';

                }

            }

        } else {

            $store->full_url = SITE_URL . 's/' . $store->url . '/';

        }

        return $store->full_url;
    }

    public function get_store_by_url($store_url) {

        /* Get the store */
        $store = null;

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_store?url=' . $store_url);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $store = Database::$database->query("SELECT * FROM `stores` WHERE `url` = '{$store_url}' AND `domain_id` IS NULL")->fetch_object() ?? null;

            if($store) {
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($store)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store->store_id)
                );
            }

        } else {

            /* Get cache */
            $store = $cache_instance->get();

        }

        return $store;

    }

    public function get_store_by_url_and_domain_id($store_url, $domain_id) {

        /* Get the store */
        $store = null;

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_store?url=' . $store_url . '&domain_id=' . $domain_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $store = Database::$database->query("SELECT * FROM `stores` WHERE `url` = '{$store_url}' AND `domain_id` = {$domain_id}")->fetch_object() ?? null;

            if($store) {
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($store)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store->store_id)
                );
            }

        } else {

            /* Get cache */
            $store = $cache_instance->get();

        }

        return $store;

    }

    public function get_store_by_store_id($store_id) {

        /* Get the store */
        $store = null;

        /* Try to check if the store posts exists via the cache */
        $cache_instance = \Altum\Cache::$adapter->getItem('s_store?store_id=' . $store_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            /* Get data from the database */
            $store = Database::$database->query("SELECT * FROM `stores` WHERE `store_id` = '{$store_id}'")->fetch_object() ?? null;

            if($store) {
                \Altum\Cache::$adapter->save(
                    $cache_instance->set($store)->expiresAfter(CACHE_DEFAULT_SECONDS)->addTag('store_id=' . $store->store_id)
                );
            }

        } else {

            /* Get cache */
            $store = $cache_instance->get();

        }

        return $store;

    }

    public function delete($store_id) {

        $store = db()->where('store_id', $store_id)->getOne('stores', ['store_id', 'image', 'logo', 'favicon']);

        if(!$store) return;

        /* Offload deleting */
        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

            if(!empty($store->image)) {
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/store_images/' . $store->image,
                ]);
            }

            if(!empty($store->favicon)) {
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/store_favicons/' . $store->favicon,
                ]);
            }

            if(!empty($store->logo)) {
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/store_logos/' . $store->logo,
                ]);
            }
        }

        /* Local deleting */
        else {
            if(!empty($store->image) && file_exists(UPLOADS_PATH . 'store_images/' . $store->image)) {
                unlink(UPLOADS_PATH . 'store_images/' . $store->image);
            }

            if(!empty($store->favicon) && file_exists(UPLOADS_PATH . 'store_favicons/' . $store->favicon)) {
                unlink(UPLOADS_PATH . 'store_favicons/' . $store->favicon);
            }

            if(!empty($store->logo) && file_exists(UPLOADS_PATH . 'store_logos/' . $store->logo)) {
                unlink(UPLOADS_PATH . 'store_logos/' . $store->logo);
            }
        }

        /* Delete the items images */
        $result = database()->query("SELECT `image` FROM `items` WHERE `store_id` = {$store_id}");
        while($item = $result->fetch_object()) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/item_images/' . $item->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($item->image) && file_exists(UPLOADS_PATH . 'item_images/' . $item->image)) {
                    unlink(UPLOADS_PATH . 'item_images/' . $item->image);
                }
            }
        }

        /* Delete the menu images */
        $result = database()->query("SELECT `image` FROM `menus` WHERE `store_id` = {$store_id}");
        while($menu = $result->fetch_object()) {
            /* Offload deleting */
            if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                $s3->deleteObject([
                    'Bucket' => settings()->offload->storage_name,
                    'Key' => 'uploads/menu_images/' . $menu->image,
                ]);
            }

            /* Local deleting */
            else {
                /* Delete current file */
                if(!empty($menu->image) && file_exists(UPLOADS_PATH . 'menu_images/' . $menu->image)) {
                    unlink(UPLOADS_PATH . 'menu_images/' . $menu->image);
                }
            }
        }

        /* Delete the store */
        db()->where('store_id', $store_id)->delete('stores');

        /* Clear cache */
        \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store_id);

    }

}
