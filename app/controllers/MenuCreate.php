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
use Altum\Uploads;

class MenuCreate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('create')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = Database::get(['store_id', 'domain_id', 'url', 'currency'], 'stores', ['store_id' => $store_id, 'user_id' => $this->user->user_id])) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Check for the plan limit */
        $total_rows = database()->query("SELECT COUNT(*) AS `total` FROM `menus` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;

        if($this->user->plan_settings->menus_limit != -1 && $total_rows >= $this->user->plan_settings->menus_limit) {
            Alerts::add_info(l('global.info_message.plan_feature_limit'));
            redirect('dashboard');
        }

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['description'] = trim(query_clean($_POST['description']));

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if($_POST['url']) {

                if(db()->where('store_id', $store->store_id)->where('url', $_POST['url'])->getOne('menus', ['menu_id'])) {
                    Alerts::add_error(l('menu.error_message.url_exists'));
                }

            }

            /* Image uploads */
            $image = !empty($_FILES['image']['name']);

            /* Check for any errors on the image image */
            if($image) {
                $image_file_name = $_FILES['image']['name'];
                $image_file_extension = explode('.', $image_file_name);
                $image_file_extension = mb_strtolower(end($image_file_extension));
                $image_file_temp = $_FILES['image']['tmp_name'];

                if($_FILES['image']['error'] == UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->menu_image_size_limit));
                }

                if($_FILES['image']['error'] && $_FILES['image']['error'] != UPLOAD_ERR_INI_SIZE) {
                    Alerts::add_error(l('global.error_message.file_upload'));
                }

                if(!in_array($image_file_extension, Uploads::get_whitelisted_file_extensions('menu_images'))) {
                    Alerts::add_error(l('global.error_message.invalid_file_type'));
                }

                if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                    if(!is_writable(UPLOADS_PATH . 'menu_images/')) {
                        Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . 'menu_images/'));
                    }
                }

                if($_FILES['image']['size'] > settings()->stores->menu_image_size_limit * 1000000) {
                    Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->menu_image_size_limit));
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

                            /* Upload image */
                            $result = $s3->putObject([
                                'Bucket' => settings()->offload->storage_name,
                                'Key' => 'uploads/menu_images/' . $image_new_name,
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
                        /* Upload the original */
                        move_uploaded_file($image_file_temp, UPLOADS_PATH . 'menu_images/' . $image_new_name);
                    }

                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                if(!$_POST['url']) {
                    $_POST['url'] = mb_strtolower(string_generate(10));

                    /* Generate random url if not specified */
                    while(db()->where('store_id', $store->store_id)->where('url', $_POST['url'])->getOne('menus', ['menu_id'])) {
                        $_POST['url'] = mb_strtolower(string_generate(10));
                    }
                }
                $image = $image_new_name ?? null;

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("INSERT INTO `menus`(`store_id`, `user_id`, `url`, `name`, `description`, `image`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss', $store->store_id, $this->user->user_id, $_POST['url'], $_POST['name'], $_POST['description'], $image, \Altum\Date::$date);
                $stmt->execute();
                $menu_id = $stmt->insert_id;
                $stmt->close();

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.create1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('menu/' . $menu_id);
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
            'values' => $values
        ];

        $view = new \Altum\View('menu-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
