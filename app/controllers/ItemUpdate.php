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
use Altum\Title;
use Altum\Uploads;

class ItemUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $item_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$item = db()->where('item_id', $item_id)->where('user_id', $this->user->user_id)->getOne('items')) {
            redirect('dashboard');
        }

        $category = db()->where('category_id', $item->category_id)->where('user_id', $this->user->user_id)->getOne('categories', ['category_id', 'url']);
        $menu = db()->where('menu_id', $item->menu_id)->where('user_id', $this->user->user_id)->getOne('menus', ['menu_id', 'url']);
        $store = db()->where('store_id', $item->store_id)->where('user_id', $this->user->user_id)->getOne('stores', ['store_id', 'domain_id', 'url', 'currency']);

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['description'] = trim(query_clean($_POST['description']));
            $_POST['price'] = (float) trim(query_clean($_POST['price']));
            $_POST['variants_is_enabled'] = (int) (bool) isset($_POST['variants_is_enabled']);
            $_POST['is_enabled'] = (int) (bool) isset($_POST['is_enabled']);

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url'] && $_POST['url'] != $item->url) {

                if(db()->where('category_id', $item->category_id)->where('url', $_POST['url'])->getOne('items', ['item_id'])) {
                    Alerts::add_error(l('category.error_message.url_exists'));
                }

            }

            /* Image uploads */
            $image = !empty($_FILES['image']['name']) && !isset($_POST['image_remove']);

            /* Check for any errors on the image image */
            if($image) {
                $image_file_name = $_FILES['image']['name'];
                $image_file_extension = explode('.', $image_file_name);
                $image_file_extension = mb_strtolower(end($image_file_extension));
                $image_file_temp = $_FILES['image']['tmp_name'];

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->item_image_size_limit));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($image_file_extension, Uploads::get_whitelisted_file_extensions('item_images'))) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'item_images/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'item_images/'));
                    }
                }

                if($_FILES['image']['size'] > settings()->stores->item_image_size_limit * 1000000) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->item_image_size_limit));
                }

                if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                    /* Generate new name for image */
                    $image_new_name = md5(time() . rand()) . '.' . $image_file_extension;

                    /* Try to compress the image */
                    if(\Altum\Plugin::is_active('image-optimizer')) {
                        \Altum\Plugin\ImageOptimizer::optimize($image_file_temp, $image_new_name);
                    }

                    /* Offload uploading */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        try {
                            $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                            /* Delete current image */
                            $s3->deleteObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/item_images/' . $item->image,
                            ]);

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/item_images/' . $image_new_name,
                                'ContentType' => mime_content_type($image_file_temp),
                                'SourceFile' => $image_file_temp,
                                'ACL' => 'public-read'
                            ]);
                        } catch (\Exception $exception) {
                            Alerts::add_error($exception->getMessage());
                        }
                    }

                    /* Local uploading */
                    else {
                        /* Delete current file */
                        if(!empty($item->image) && file_exists(UPLOADS_PATH . 'item_images/' . $item->image)) {
                            unlink(UPLOADS_PATH . 'item_images/' . $item->image);
                        }

                        /* Upload the original */
                        move_uploaded_file($image_file_temp, UPLOADS_PATH . 'item_images/' . $image_new_name);
                    }

                    /* Execute query */
                    Database::$database->query("UPDATE `items` SET `image` = '{$image_new_name}' WHERE `item_id` = {$item->item_id}");
                }
            }

            /* Check for the removal of the already uploaded file */
            if(isset($_POST['image_remove'])) {
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

                /* Database query */
                db()->where('item_id', $item->item_id)->update('items', ['image' => null]);
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if(!$_POST['url']) {
                    $_POST['url'] = mb_strtolower(string_generate(10));

                    /* Generate random url if not specified */
                    while(db()->where('store_id', $category->store_id)->where('url', $_POST['url'])->getOne('categories', ['category_id'])) {
                        $_POST['url'] = mb_strtolower(string_generate(10));
                    }
                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `items` SET `url` = ?, `name` = ?, `description` = ?, `price` = ?, `variants_is_enabled` = ?, `is_enabled` = ?, `last_datetime` = ? WHERE `item_id` = ? AND `user_id` = ?");
                $stmt->bind_param('sssssssss', $_POST['url'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['variants_is_enabled'], $_POST['is_enabled'], \Altum\Date::$date, $item->item_id, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('item-update/' . $item->item_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'item_id' => $item->item_id,
            'resource_name' => $item->name,
            'external_url' => $store->full_url . $menu->url . '/' . $category->url . '/' . $item->url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('item_update.title'), $item->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'menu' => $menu,
            'category' => $category,
            'item' => $item
        ];

        $view = new \Altum\View('item-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
