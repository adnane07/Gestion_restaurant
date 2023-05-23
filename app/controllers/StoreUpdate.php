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
use Altum\Uploads;

class StoreUpdate extends Controller {

    public function index() {

        \Altum\Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('dashboard');
        }

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        foreach(['details', 'socials', 'paypal', 'stripe', 'offline_payment', 'business', 'ordering'] as $key) {
            $store->{$key} = json_decode($store->{$key});
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Get available custom domains */
        $domains = (new \Altum\Models\Domain())->get_available_domains_by_user($this->user, true, $store->store_id);

        if(!empty($_POST)) {
            $_POST['url'] = !empty($_POST['url']) ? get_slug(query_clean($_POST['url'])) : false;
            $_POST['name'] = trim(query_clean($_POST['name']));
            $_POST['title'] = trim(query_clean($_POST['title']));
            $_POST['description'] = trim(query_clean($_POST['description']));
            $_POST['address'] = trim(query_clean($_POST['address']));
            $_POST['phone'] = trim(query_clean($_POST['phone']));
            $_POST['website'] = trim(query_clean($_POST['website']));
            $_POST['email'] = trim(query_clean($_POST['email']));
            $_POST['currency'] = trim(query_clean($_POST['currency']));
            $_POST['password'] = !empty($_POST['password']) ?
                ($_POST['password'] != $store->password ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $store->password)
                : null;
            $_POST['timezone']  = in_array($_POST['timezone'], \DateTimeZone::listIdentifiers()) ? query_clean($_POST['timezone']) : settings()->main->default_timezone;
            $_POST['custom_css'] = trim(input_clean($_POST['custom_css']));
            $_POST['custom_js'] = trim($_POST['custom_js']);
            $_POST['is_se_visible'] = $this->user->plan_settings->search_engine_block_is_enabled ? (int) (bool) isset($_POST['is_se_visible']) : 1;
            $_POST['is_removed_branding'] = (int) (bool) isset($_POST['is_removed_branding']);
            $_POST['email_reports_is_enabled'] = (int) (bool) isset($_POST['email_reports_is_enabled']);
            $_POST['ordering_on_premise_is_enabled'] = (int) (bool) isset($_POST['ordering_on_premise_is_enabled']);
            $_POST['ordering_on_premise_minimum_value'] = (float) $_POST['ordering_on_premise_minimum_value'];
            $_POST['ordering_takeaway_is_enabled'] = (int) (bool) isset($_POST['ordering_takeaway_is_enabled']);
            $_POST['ordering_takeaway_minimum_value'] = (float) $_POST['ordering_takeaway_minimum_value'];
            $_POST['ordering_delivery_is_enabled'] = (int) (bool) isset($_POST['ordering_delivery_is_enabled']);
            $_POST['ordering_delivery_minimum_value'] = (float) $_POST['ordering_delivery_minimum_value'];
            $_POST['ordering_delivery_cost'] = (float) $_POST['ordering_delivery_cost'];
            $_POST['ordering_delivery_free_minimum_value'] = (float) $_POST['ordering_delivery_free_minimum_value'];
            $_POST['email_orders_is_enabled'] = (int) (bool) isset($_POST['email_orders_is_enabled']);

            /* Payments */
            $_POST['paypal_is_enabled'] = (bool) $_POST['paypal_is_enabled'];
            $_POST['paypal_mode'] = in_array($_POST['paypal_mode'], ['live', 'sandbox']) ? query_clean($_POST['paypal_mode']) : 'sandbox';
            $_POST['paypal_client_id'] = trim(query_clean($_POST['paypal_client_id']));
            $_POST['paypal_secret'] = trim(query_clean($_POST['paypal_secret']));
            $_POST['stripe_is_enabled'] = (bool) $_POST['stripe_is_enabled'];
            $_POST['stripe_publishable_key'] = trim(query_clean($_POST['stripe_publishable_key']));
            $_POST['stripe_secret_key'] = trim(query_clean($_POST['stripe_secret_key']));
            $_POST['stripe_webhook_secret'] = trim(query_clean($_POST['stripe_webhook_secret']));
            $_POST['offline_payment_is_enabled'] = (bool) $_POST['offline_payment_is_enabled'];

            /* Business */
            $_POST['business_invoice_is_enabled'] = (bool) $_POST['business_invoice_is_enabled'];
            $_POST['business_invoice_nr_prefix'] = trim(query_clean($_POST['business_invoice_nr_prefix']));
            $_POST['business_name'] = trim(query_clean($_POST['business_name']));
            $_POST['business_address'] = trim(query_clean($_POST['business_address']));
            $_POST['business_city'] = trim(query_clean($_POST['business_city']));
            $_POST['business_county'] = trim(query_clean($_POST['business_county']));
            $_POST['business_zip'] = trim(query_clean($_POST['business_zip']));
            $_POST['business_country'] = trim(query_clean($_POST['business_country']));
            $_POST['business_email'] = trim(query_clean($_POST['business_email']));
            $_POST['business_phone'] = trim(query_clean($_POST['business_phone']));
            $_POST['business_tax_type'] = trim(query_clean($_POST['business_tax_type']));
            $_POST['business_tax_id'] = trim(query_clean($_POST['business_tax_id']));
            $_POST['business_custom_key_one'] = trim(query_clean($_POST['business_custom_key_one']));
            $_POST['business_custom_value_one'] = trim(query_clean($_POST['business_custom_value_one']));
            $_POST['business_custom_key_two'] = trim(query_clean($_POST['business_custom_key_two']));
            $_POST['business_custom_value_two'] = trim(query_clean($_POST['business_custom_value_two']));

            /* Others */
            $_POST['is_enabled'] = (int) (bool) isset($_POST['is_enabled']);

            $_POST['domain_id'] = isset($_POST['domain_id']) && isset($domains[$_POST['domain_id']]) ? (!empty($_POST['domain_id']) ? (int) $_POST['domain_id'] : null) : null;
            $_POST['is_main_store'] = (bool) isset($_POST['is_main_store']) && isset($domains[$_POST['domain_id']]) && $domains[$_POST['domain_id']]->type == 0;

            $hours = [];
            foreach([1, 2, 3, 4, 5, 6, 7] as $key) {
                $hours[$key] = [];

                $_POST['hours'][$key]['is_enabled'] = (bool) isset($_POST['hours'][$key]['is_enabled']);
                $_POST['hours'][$key]['hours'] = trim(query_clean($_POST['hours'][$key]['hours']));

                $hours[$key] = [
                    'is_enabled' => $_POST['hours'][$key]['is_enabled'],
                    'hours' => $_POST['hours'][$key]['hours'],
                ];
            }

            /* Make sure the socials sent are proper */
            $socials = require APP_PATH . 'includes/s/socials.php';

            foreach($_POST['socials'] as $key => $value) {

                if(!array_key_exists($key, $socials)) {
                    unset($_POST['socials'][$key]);
                } else {
                    $_POST['socials'][$key] = query_clean($_POST['socials'][$key]);
                }

            }

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            /* Check for duplicate url if needed */
            if(
                ($_POST['url'] && $this->user->plan_settings->custom_url_is_enabled && $_POST['url'] != $store->url)
                || ($store->domain_id != $_POST['domain_id'])
            ) {

                $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                $is_existing_store = database()->query("SELECT `store_id` FROM `stores` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;

                if($is_existing_store) {
                    Alerts::add_error(l('store.error_message.url_exists'));
                }

            }

            /* Image uploads */
            $image_allowed_extensions = [
                'logo' => Uploads::get_whitelisted_file_extensions('store_logos'),
                'favicon' => Uploads::get_whitelisted_file_extensions('store_favicons'),
                'image' => Uploads::get_whitelisted_file_extensions('store_images'),
            ];
            $image = [
                'logo' => !empty($_FILES['logo']['name']) && !isset($_POST['logo_remove']),
                'favicon' => !empty($_FILES['favicon']['name']) && !isset($_POST['favicon_remove']),
                'image' => !empty($_FILES['image']['name']) && !isset($_POST['image_remove']),
            ];
            $image_upload_path = [
                'logo' => 'store_logos',
                'favicon' => 'store_favicons',
                'image' => 'store_images',
            ];

            foreach(['logo', 'favicon', 'image'] as $image_key) {
                if($image[$image_key]) {
                    $file_name = $_FILES[$image_key]['name'];
                    $file_extension = explode('.', $file_name);
                    $file_extension = mb_strtolower(end($file_extension));
                    $file_temp = $_FILES[$image_key]['tmp_name'];

                    if($_FILES[$image_key]['error'] == UPLOAD_ERR_INI_SIZE) {
                        Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->{$image_key . '_size_limit'}));
                    }

                    if($_FILES[$image_key]['error'] && $_FILES[$image_key]['error'] != UPLOAD_ERR_INI_SIZE) {
                        Alerts::add_error(l('global.error_message.file_upload'));
                    }

                    if(!in_array($file_extension, $image_allowed_extensions[$image_key])) {
                        Alerts::add_error(l('global.error_message.invalid_file_type'));
                    }

                    if(!\Altum\Plugin::is_active('offload') || (\Altum\Plugin::is_active('offload') && !settings()->offload->uploads_url)) {
                        if(!is_writable(UPLOADS_PATH . $image_upload_path[$image_key] . '/')) {
                            Alerts::add_error(sprintf(l('global.error_message.directory_not_writable'), UPLOADS_PATH . $image_upload_path[$image_key] . '/'));
                        }
                    }

                    if($_FILES[$image_key]['size'] > settings()->stores->{$image_key . '_size_limit'} * 1000000) {
                        Alerts::add_error(sprintf(l('global.error_message.file_size_limit'), settings()->stores->{$image_key . '_size_limit'}));
                    }

                    if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                        /* Generate new name for image */
                        $image_new_name = md5(time() . rand()) . '.' . $file_extension;

                        /* Try to compress the image */
                        if(\Altum\Plugin::is_active('image-optimizer')) {
                            \Altum\Plugin\ImageOptimizer::optimize($file_temp, $image_new_name);
                        }

                        /* Offload uploading */
                        if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                            try {
                                $s3 = new \Aws\S3\S3Client(get_aws_s3_config());

                                /* Delete current image */
                                $s3->deleteObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => UPLOADS_URL_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key},
                                ]);

                                /* Upload image */
                                $result = $s3->putObject([
                                    'Bucket' => settings()->offload->storage_name,
                                    'Key' => UPLOADS_URL_PATH . $image_upload_path[$image_key] . '/' . $image_new_name,
                                    'ContentType' => mime_content_type($file_temp),
                                    'SourceFile' => $file_temp,
                                    'ACL' => 'public-read'
                                ]);
                            } catch (\Exception $exception) {
                                Alerts::add_error($exception->getMessage());
                            }
                        }

                        /* Local uploading */
                        else {
                            /* Delete current image */
                            if(!empty($store->{$image_key}) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key})) {
                                unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key});
                            }

                            /* Upload the original */
                            move_uploaded_file($file_temp, UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $image_new_name);
                        }

                        /* Database query */
                        database()->query("UPDATE `stores` SET `{$image_key}` = '{$image_new_name}' WHERE `store_id` = {$store->store_id}");

                    }
                }

                /* Check for the removal of the already uploaded file */
                if(isset($_POST[$image_key . '_remove'])) {

                    /* Offload deleting */
                    if(\Altum\Plugin::is_active('offload') && settings()->offload->uploads_url) {
                        $s3 = new \Aws\S3\S3Client(get_aws_s3_config());
                        $s3->deleteObject([
                            'Bucket' => settings()->offload->storage_name,
                            'Key' => UPLOADS_URL_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key},
                        ]);
                    }

                    /* Local deleting */
                    else {
                        /* Delete current file */
                        if(!empty($store->{$image_key}) && file_exists(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key})) {
                            unlink(UPLOADS_PATH . $image_upload_path[$image_key] . '/' . $store->{$image_key});
                        }
                    }

                    /* Database query */
                    db()->where('store_id', $store->store_id)->update('stores', [$image_key => null]);
                }
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $details = json_encode([
                    'address' => $_POST['address'],
                    'phone' => $_POST['phone'],
                    'website' => $_POST['website'],
                    'email' => $_POST['email'],
                    'hours' => $hours
                ]);
                $socials = json_encode($_POST['socials']);
                $ordering = json_encode([
                    'on_premise_is_enabled' => $_POST['ordering_on_premise_is_enabled'],
                    'delivery_is_enabled' => $_POST['ordering_delivery_is_enabled'],
                    'takeaway_is_enabled' => $_POST['ordering_takeaway_is_enabled'],
                    'on_premise_minimum_value' => $_POST['ordering_on_premise_minimum_value'],
                    'delivery_minimum_value' => $_POST['ordering_delivery_minimum_value'],
                    'delivery_cost' => $_POST['ordering_delivery_cost'],
                    'delivery_free_minimum_value' => $_POST['ordering_delivery_free_minimum_value'],
                    'takeaway_minimum_value' => $_POST['ordering_takeaway_minimum_value'],
                ]);
                $paypal = json_encode([
                    'is_enabled' => $_POST['paypal_is_enabled'],
                    'mode' => $_POST['paypal_mode'],
                    'client_id' => $_POST['paypal_client_id'],
                    'secret' => $_POST['paypal_secret'],
                ]);
                $stripe = json_encode([
                    'is_enabled' => $_POST['stripe_is_enabled'],
                    'publishable_key' => $_POST['stripe_publishable_key'],
                    'secret_key' => $_POST['stripe_secret_key'],
                    'webhook_secret' => $_POST['stripe_webhook_secret'],
                ]);
                $offline_payment = json_encode([
                    'is_enabled' => $_POST['offline_payment_is_enabled'],
                ]);
                $business = json_encode([
                    'invoice_is_enabled' => $_POST['business_invoice_is_enabled'],
                    'invoice_nr_prefix' => $_POST['business_invoice_nr_prefix'],
                    'name' => $_POST['business_name'],
                    'address' => $_POST['business_address'],
                    'city' => $_POST['business_city'],
                    'county' => $_POST['business_county'],
                    'zip' => $_POST['business_zip'],
                    'country' => $_POST['business_country'],
                    'email' => $_POST['business_email'],
                    'phone' => $_POST['business_phone'],
                    'tax_type' => $_POST['business_tax_type'],
                    'tax_id' => $_POST['business_tax_id'],
                    'custom_key_one' => $_POST['business_custom_key_one'],
                    'custom_value_one' => $_POST['business_custom_value_one'],
                    'custom_key_two' => $_POST['business_custom_key_two'],
                    'custom_value_two' => $_POST['business_custom_value_two'],
                ]);

                if(!$_POST['url']) {
                    $is_existing_store = true;

                    /* Generate random url if not specified */
                    while($is_existing_store) {
                        $_POST['url'] = mb_strtolower(string_generate(10));

                        $domain_id_where = $_POST['domain_id'] ? "AND `domain_id` = {$_POST['domain_id']}" : "AND `domain_id` IS NULL";
                        $is_existing_store = database()->query("SELECT `store_id` FROM `stores` WHERE `url` = '{$_POST['url']}' {$domain_id_where}")->num_rows;
                    }

                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("
                    UPDATE 
                        `stores` 
                    SET 
                        `domain_id` = ?,
                        `url` = ?,
                        `name` = ?,
                        `title` = ?,
                        `description` = ?,
                        `details` = ?,
                        `socials` = ?,
                        `currency` = ?,
                        `password` = ?,
                        `timezone` = ?,
                        `custom_css` = ?,
                        `custom_js` = ?,
                        `is_se_visible` = ?,
                        `is_removed_branding` = ?,
                        `email_reports_is_enabled` = ?,
                        `email_orders_is_enabled` = ?,
                        `ordering` = ?,
                        `paypal` = ?,
                        `stripe` = ?,
                        `offline_payment` = ?,
                        `business` = ?,
                        `is_enabled` = ?,
                        `last_datetime` = ? 
                    WHERE 
                        `store_id` = ? 
                        AND `user_id` = ?
                  ");
                $stmt->bind_param(
                    'sssssssssssssssssssssssss',
                    $_POST['domain_id'],
                    $_POST['url'],
                    $_POST['name'],
                    $_POST['title'],
                    $_POST['description'],
                    $details,
                    $socials,
                    $_POST['currency'],
                    $_POST['password'],
                    $_POST['timezone'],
                    $_POST['custom_css'],
                    $_POST['custom_js'],
                    $_POST['is_se_visible'],
                    $_POST['is_removed_branding'],
                    $_POST['email_reports_is_enabled'],
                    $_POST['email_orders_is_enabled'],
                    $ordering,
                    $paypal,
                    $stripe,
                    $offline_payment,
                    $business,
                    $_POST['is_enabled'],
                    \Altum\Date::$date,
                    $store->store_id,
                    $this->user->user_id
                );
                $stmt->execute();
                $stmt->close();

                /* Update custom domain if needed */
                if($_POST['is_main_store']) {

                    /* If the main status page of a particular domain is changing, update the old domain as well to "free" it */
                    if($_POST['domain_id'] != $store->domain_id) {
                        /* Database query */
                        db()->where('domain_id', $store->domain_id)->update('domains', [
                            'store_id' => null,
                            'last_datetime' => \Altum\Date::$date,
                        ]);
                    }

                    /* Database query */
                    db()->where('domain_id', $_POST['domain_id'])->update('domains', [
                        'store_id' => $store_id,
                        'last_datetime' => \Altum\Date::$date,
                    ]);

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $_POST['domain_id']);
                }

                /* Update old main custom domain if needed */
                if(!$_POST['is_main_store'] && $store->domain_id && $domains[$store->domain_id]->store_id == $store->store_id) {
                    /* Database query */
                    db()->where('domain_id', $store->domain_id)->update('domains', [
                        'store_id' => null,
                        'last_datetime' => \Altum\Date::$date,
                    ]);

                    /* Clear the cache */
                    \Altum\Cache::$adapter->deleteItemsByTag('domain_id=' . $_POST['domain_id']);
                }

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $store->store_id);
                \Altum\Cache::$adapter->deleteItemsByTag('user_id=' . $this->user->user_id);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . $_POST['name'] . '</strong>'));

                redirect('store-update/' . $store->store_id);
            }

        }

        /* Establish the account sub menu view */
        $data = [
            'store_id' => $store->store_id,
            'resource_name' => $store->name,
            'external_url' => $store->full_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('store_update.title'), $store->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'domains' => $domains
        ];

        $view = new \Altum\View('store-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
