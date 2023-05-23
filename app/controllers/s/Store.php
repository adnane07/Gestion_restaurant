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
use Altum\Date;
use Altum\Meta;
use Altum\Models\User;
use Altum\Title;
use MaxMind\Db\Reader;

class Store extends Controller {
    public $store = null;
    public $store_user = null;
    public $has_access = null;

    public function index() {

        $this->init();

        /* Check if the password form is submitted */
        if(!$this->has_access && !empty($_POST)) {

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!password_verify($_POST['password'], $this->store->password)) {
                Alerts::add_field_error('password', l('s_store.password.error_message'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Set a cookie */
                setcookie('store_password_' . $this->store->store_id, $this->store->password, time()+60*60*24*30);

                header('Location: ' . $this->store->full_url); die();

            }

        }

        /* Display the password form */
        if(!$this->has_access) {

            /* Set a custom title */
            Title::set(l('s_store.password.title'));

            /* Main View */
            $data = [
                'store' => $this->store,
            ];

            $view = new \Altum\View('s/store/' . $this->store->theme . '/password', (array) $this);

        }

        /* No password or access granted */
        else {

            $this->create_statistics($this->store->store_id);

            /* Calculate open hours */
            $now = (new \DateTime())->setTimezone(new \DateTimeZone($this->store->timezone));
            $day = mb_strtolower($now->format('N'));

            /* Get the available items */
            $menus = (new \Altum\Models\Menu())->get_menus_by_store_id($this->store->store_id);

            /* Set a custom title */
            Title::set($this->store->title);

            /* Set the meta tags */
            Meta::set_description(string_truncate($this->store->description, 200));
            Meta::set_social_url($this->store->full_url);
            Meta::set_social_title($this->store->title);
            Meta::set_social_description(string_truncate($this->store->description, 200));
            Meta::set_social_image($this->store->image ? UPLOADS_FULL_URL . 'store_images/' . $this->store->image : null);

            /* Prepare the header */
            $view = new \Altum\View('s/partials/header', (array) $this);
            $this->add_view_content('header', $view->run(['store' => $this->store]));

            /* Main View */
            $data = [
                'store' => $this->store,
                'store_user' => $this->store_user,
                'menus' => $menus,

                'day' => $day
            ];

            $view = new \Altum\View('s/store/' . $this->store->theme . '/index', (array) $this);
        }

        $this->add_view_content('content', $view->run($data));
    }

    public function init() {

        /* Check against potential custom domains */
        if(isset(\Altum\Router::$data['domain'])) {

            /* Check if custom domain has 1 store or multiple */
            if(\Altum\Router::$data['domain']->store_id) {

                $store = $this->store = (new \Altum\Models\Store())->get_store_by_store_id(\Altum\Router::$data['domain']->store_id);

                /* Determine the store base url */
                $store->full_url = \Altum\Router::$data['domain']->scheme . \Altum\Router::$data['domain']->host . '/';

            } else {
                /* Get the Store details */
                $url = isset($this->params[0]) ? query_clean($this->params[0]) : null;

                $store = $this->store = (new \Altum\Models\Store())->get_store_by_url_and_domain_id($url, \Altum\Router::$data['domain']->domain_id);

                if($store) {
                    /* Determine the store base url */
                    $store->full_url = \Altum\Router::$data['domain']->scheme . \Altum\Router::$data['domain']->host . '/' . $store->url . '/';
                }
            }

            /* Redirect if the store doesn't exit or is not active */
            if(!$store || ($store && !$store->is_enabled)) {

                /* Redirect by custom not found page if possible */
                if(\Altum\Router::$data['domain']->custom_not_found_url) {
                    header('Location: ' . \Altum\Router::$data['domain']->custom_not_found_url);
                    die();
                }

                /* Redirect to the main homepage */
                else {
                    redirect();
                }
            }
        }

        /* Check the store via url */
        else {

            /* Get the Store details */
            $url = isset($this->params[0]) ? query_clean($this->params[0]) : null;

            $store = $this->store = (new \Altum\Models\Store())->get_store_by_url($url);

            if(!$store || ($store && (!$store->is_enabled || $store->domain_id))) {
                redirect();
            }

            $store->full_url = SITE_URL . 's/' . $store->url . '/';
        }

        $this->store_user = (new User())->get_user_by_user_id($this->store->user_id);

        /* Make sure to check if the user is active */
        if($this->store_user->status != 1) {
            redirect();
        }

        /* Process the plan of the user */
        (new User())->process_user_plan_expiration_by_user($this->store_user);

        /* Check if the user has access to the store */
        $has_access = !$store->password || ($store->password && isset($_COOKIE['store_password']) && $_COOKIE['store_password'] == $store->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->store_user->plan_settings->password_protection_is_enabled) {
            $has_access = true;
        }

        $this->has_access = $has_access;

        /* Parse some details */
        foreach(['details', 'socials', 'paypal', 'stripe', 'offline_payment', 'business', 'ordering'] as $key) {
            $store->{$key} = json_decode($store->{$key});
        }

        $this->store->cart_is_enabled = $this->store_user->plan_settings->ordering_is_enabled && ($this->store->ordering->on_premise_is_enabled || $this->store->ordering->takeaway_is_enabled || $this->store->ordering->delivery_is_enabled);

        /* Set the default language of the user */
        \Altum\Language::set_by_name($this->store_user->language, false);
    }

    /* Insert statistics log */
    public function create_statistics($store_id = null, $menu_id = null, $category_id = null, $item_id = null) {

        $cookie_name = 's_statistics_' . $store_id . '_' . $menu_id . '_' . $category_id . '_' . $item_id;

        if(isset($_COOKIE[$cookie_name]) && (int) $_COOKIE[$cookie_name] >= 3) {
            return;
        }

        if(!$this->store_user->plan_settings->analytics_is_enabled) {
            return;
        }

        /* Detect extra details about the user */
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

        /* Do not track bots */
        if($whichbrowser->device->type == 'bot') {
            return;
        }

        /* Detect extra details about the user */
        $browser_name = $whichbrowser->browser->name ?? null;
        $os_name = $whichbrowser->os->name ?? null;
        $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
        $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);
        $is_unique = isset($_COOKIE[$cookie_name]) ? 0 : 1;

        /* Detect the location */
        try {
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-City.mmdb'))->get(get_ip());
        } catch(\Exception $exception) {
            /* :) */
        }
        $country_code = isset($maxmind) && isset($maxmind['country']) ? $maxmind['country']['iso_code'] : null;
        $city_name = isset($maxmind) && isset($maxmind['city']) ? $maxmind['city']['names']['en'] : null;

        /* Process referrer */
        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;

        if(!isset($referrer)) {
            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if the referrer comes from the same location */
        if(isset($referrer) && isset($referrer['host']) && $referrer['host'] == parse_url($this->store->full_url)['host']) {
            $is_unique = 0;

            $referrer = [
                'host' => null,
                'path' => null
            ];
        }

        /* Check if referrer actually comes from the QR code */
        if(isset($_GET['referrer']) && $_GET['referrer'] == 'qr') {
            $referrer = [
                'host' => 'qr',
                'path' => null
            ];
        }

        $utm_source = $_GET['utm_source'] ?? null;
        $utm_medium = $_GET['utm_medium'] ?? null;
        $utm_campaign = $_GET['utm_campaign'] ?? null;

        /* Insert or update the log */
        db()->insert('statistics', [
            'store_id' => $store_id,
            'menu_id' => $menu_id,
            'category_id' => $category_id,
            'item_id' => $item_id,
            'country_code' => $country_code,
            'city_name' => $city_name,
            'os_name' => $os_name,
            'browser_name' => $browser_name,
            'referrer_host' => $referrer['host'],
            'referrer_path' => $referrer['path'],
            'device_type' => $device_type,
            'browser_language' => $browser_language,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'is_unique' => $is_unique,
            'datetime' => Date::$date,
        ]);

        /* Add the unique hit to the store table as well */
        if($store_id && !$menu_id && !$category_id && !$item_id) db()->where('store_id', $store_id)->update('stores', ['pageviews' => db()->inc()]);
        if($menu_id && !$category_id && !$item_id) db()->where('menu_id', $menu_id)->update('menus', ['pageviews' => db()->inc()]);
        if($category_id && !$item_id) db()->where('category_id', $category_id)->update('categories', ['pageviews' => db()->inc()]);
        if($item_id) db()->where('item_id', $item_id)->update('items', ['pageviews' => db()->inc()]);

        /* Set cookie to try and avoid multiple entrances */
        $cookie_new_value = isset($_COOKIE[$cookie_name]) ? (int) $_COOKIE[$cookie_name] + 1 : 0;
        setcookie($cookie_name, (int) $cookie_new_value, time()+60*60*24*1);
    }

}
