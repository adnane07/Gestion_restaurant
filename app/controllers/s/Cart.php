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
use Altum\Title;
use PayPal\Api\Amount;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;

class Cart extends Controller {
    public $store;
    public $store_user = null;

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

        if(!$this->store->cart_is_enabled) {
            header('Location: ' . $store_controller->store->full_url); die();
        }

        if(!empty($_POST)) {
            $order_number = $this->store->orders + 1;
            $_POST['type'] = in_array($_POST['type'], ['on_premise', 'takeaway', 'delivery']) ? trim(query_clean($_POST['type'])) : 'on_premise';
            $_POST['processor'] = in_array($_POST['processor'], ['paypal', 'stripe', 'offline_payment']) && $this->store->{$_POST['processor']}->is_enabled ? trim(query_clean($_POST['processor'])) : null;
            $final_price = 0;
            $ordered_items = 0;

            $details = null;

            switch($_POST['type']) {
                case 'on_premise':
                    $details = [
                        'phone' => trim(query_clean($_POST['phone']))
                        
                    ];
                    break;

                case 'takeaway':
                    $details = [
                        'phone' => trim(query_clean($_POST['phone']))
                    ];
                    break;

                case 'delivery':
                    $details = [
                        'phone' => trim(query_clean($_POST['phone'])),
                        'address' => trim(query_clean($_POST['address']))
                    ];
                    break;
        }

            $details['name'] = trim(query_clean($_POST['name']));
            $details['message'] = trim(query_clean($_POST['message']));
            $details['number'] = trim(query_clean($_POST['number']));


            $details = json_encode($details);

            /* Go through each ordered item to make sure everything is in order */
            if(is_array($_POST['items'])) {
                foreach($_POST['items'] as $item_key => $item_value) {
                    $_POST['items'][$item_key]['item_id'] = (int) $_POST['items'][$item_key]['item_id'];
                    $_POST['items'][$item_key]['quantity'] = (int) $_POST['items'][$item_key]['quantity'];
                    if($_POST['items'][$item_key]['quantity'] <= 0 || $_POST['items'][$item_key]['quantity'] > 25) {
                        $_POST['items'][$item_key]['quantity'] = 1;
                    }

                    /* Check the item */
                    $item = db()->where('item_id', $item_value['item_id'])->where('store_id', $this->store->store_id)->where('is_enabled', 1)->getOne('items');

                    /* Make sure the item is enabled and exists */
                    if(!$item) {
                        unset($_POST['items'][$item_key]);
                        continue;
                    }

                    $ordered_items += $_POST['items'][$item_key]['quantity'];
                    $_POST['items'][$item_key]['item'] = $item;
                    $_POST['items'][$item_key]['price'] = 0;
                    $_POST['items'][$item_key]['data'] = [
                        'name' => $item->name,
                        'image' => $item->image,
                        'extras' => [],
                        'variant_options' => []
                    ];


                    /* Iterate over the extras if needed */
                    if(isset($_POST['items'][$item_key]['extras']) && is_array($_POST['items'][$item_key]['extras']) && count($_POST['items'][$item_key]['extras'])) {
                        foreach($_POST['items'][$item_key]['extras'] as $item_extra_key => $item_extra_value) {
                            $_POST['items'][$item_key]['extras'][$item_extra_key] = (int) $_POST['items'][$item_key]['extras'][$item_extra_key];

                            /* Check the item */
                            $item_extra = db()->where('item_extra_id', $item_extra_value)->where('store_id', $this->store->store_id)->where('is_enabled', 1)->getOne('items_extras');

                            /* Make sure the item extra is enabled and exists */
                            if(!$item_extra) {
                                unset($_POST['items'][$item_key]['extras'][$item_extra_key]);
                                continue;
                            }

                            /* Add to the price */
                            $_POST['items'][$item_key]['price'] += (float) $item_extra->price;

                            /* Add to the data */
                            $_POST['items'][$item_key]['data']['extras'][] = $item_extra->name;
                        }

                        $_POST['items'][$item_key]['item_extras_ids'] = json_encode($_POST['items'][$item_key]['extras']);

                    } else {
                        $_POST['items'][$item_key]['item_extras_ids'] = null;
                    }

                    /* Check the item variant if any */
                    if($_POST['items'][$item_key]['item_variant_id']) {
                        $_POST['items'][$item_key]['item_variant_id'] = (int) $_POST['items'][$item_key]['item_variant_id'];


                        /* Get the item_variant details */
                        $item_variant = (new \Altum\Models\ItemVariants())->get_item_variant_by_store_id_and_item_variant_id($this->store->store_id, $_POST['items'][$item_key]['item_variant_id']);

                        /* Make sure the item variant is enabled and exists */
                        if(!$item_variant || ($item_variant && !$item_variant->is_enabled)) {
                            unset($_POST['items'][$item_key]);
                            continue;
                        }

                        /* Get options for this item variant */
                        $item_options_ids = json_decode($item_variant->item_options_ids ?? null) ?? [];

                        $item_options_ids_list = array_reduce($item_options_ids, function($carry, $item) {
                                $carry[] = $item->item_option_id;

                                return $carry;
                            }, []) ?? null;

                        $item_variant_options = (new \Altum\Models\ItemOptions())->get_item_options_by_store_id_and_item_options_ids($this->store->store_id, $item_options_ids_list);

                        foreach($item_options_ids as $key => $value) {
                            $item_variant_option_array_key = array_search($value->item_option_id, array_column($item_variant_options, 'item_option_id'));
                            $item_variant_option_options = json_decode($item_variant_options[$item_variant_option_array_key]->options);

                            $option_name = $item_variant_options[$item_variant_option_array_key]->name;
                            $option_value = $item_variant_option_options[$value->option];

                            /* Add to the data */
                            $_POST['items'][$item_key]['data']['variant_options'][] = [
                                'name' => $option_name,
                                'value' => $option_value
                            ];
                        }

                        /* Add to the price */
                        $_POST['items'][$item_key]['price'] += (float) $item_variant->price;

                    } else {

                        $_POST['items'][$item_key]['item_variant_id'] = null;

                        /* Add to the price */
                        $_POST['items'][$item_key]['price'] += (float) $item->price;
                    }

                    /* Add to the price */
                    $_POST['items'][$item_key]['price'] = $_POST['items'][$item_key]['price'] * $_POST['items'][$item_key]['quantity'];

                    /* Add to the final price */
                    $final_price += (float) $_POST['items'][$item_key]['price'];
                }

            }

            /* Check for any errors */
            if(!\Altum\Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!$_POST['processor']) {
                header('Location: ' . $this->store->full_url . '?page=cart'); die();
            }

            if(!is_array($_POST['items']) || (is_array($_POST['items']) && !count($_POST['items']))) {
                header('Location: ' . $this->store->full_url . '?page=cart'); die();
            }

            if($final_price < $this->store->ordering->{$_POST['type'] . '_minimum_value'}) {
                Alerts::add_error(sprintf(l('s_cart.order_minimum_value'), $this->store->ordering->{$_POST['type'] . '_minimum_value'}, $this->store->currency));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {

                /* Check for delivery price */
                $has_delivery_cost = false;
                if(
                    $_POST['type'] == 'delivery'
                    && $this->store->ordering->delivery_cost > 0
                    && (
                        (
                            $this->store->ordering->delivery_free_minimum_value !== 0
                            && $final_price < $this->store->ordering->delivery_free_minimum_value
                        ) || $this->store->ordering->delivery_free_minimum_value == 0
                    )
                ) {
                    $final_price += (float) $this->store->ordering->delivery_cost;
                    $has_delivery_cost = true;
                }

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("INSERT INTO `orders` (`store_id`, `user_id`, `order_number`, `type`, `processor`, `details`, `price`, `ordered_items`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssssss', $this->store->store_id, $this->store->user_id, $order_number, $_POST['type'], $_POST['processor'], $details, $final_price, $ordered_items, \Altum\Date::$date);
                $stmt->execute();
                $order_id = $stmt->insert_id;
                $stmt->close();

                /* Insert all the ordered items */
                $stmt_orders_items = database()->prepare("INSERT INTO `orders_items` (`order_id`, `item_variant_id`, `item_id`, `category_id`, `menu_id`, `store_id`, `item_extras_ids`, `data`, `price`, `quantity`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                /* Prepare the statement and execute query */
                $stmt_items = database()->prepare("UPDATE `items` SET `orders` = `orders` + 1 WHERE `item_id` = ? AND `user_id` = ?");

                /* Check for delivery price */
                if($has_delivery_cost) {
                    $ordered_item_data = json_encode(['name' => l('s_cart.ordering_delivery_cost')]);

                    /* Prepare the statement and execute query */
                    $null = null;
                    $quantity = 1;
                    $stmt_orders_items->bind_param('sssssssssss', $order_id, $null, $null, $null, $null, $this->store->store_id, $null, $ordered_item_data, $this->store->ordering->delivery_cost, $quantity, \Altum\Date::$date);
                    $stmt_orders_items->execute();

                }

                foreach($_POST['items'] as $row) {
                    $ordered_item_data = json_encode($row['data']);

                    /* Prepare the statement and execute query */
                    $stmt_orders_items->bind_param('sssssssssss', $order_id, $row['item_variant_id'], $row['item']->item_id, $row['item']->category_id, $row['item']->menu_id, $row['item']->store_id, $row['item_extras_ids'], $ordered_item_data, $row['price'], $row['quantity'], \Altum\Date::$date);
                    $stmt_orders_items->execute();

                    $stmt_items->bind_param('ss', $row['item']->item_id, $this->store->user_id);
                    $stmt_items->execute();

                }
                $stmt_orders_items->close();
                $stmt_items->close();

                /* Prepare the statement and execute query */
                $stmt = database()->prepare("UPDATE `stores` SET `orders` = `orders` + 1 WHERE `store_id` = ? AND `user_id` = ?");
                $stmt->bind_param('ss', $this->store->store_id, $this->store->user_id);
                $stmt->execute();
                $stmt->close();

                /* Send out an email notification if needed */
                if($this->store->email_orders_is_enabled) {

                    /* Prepare the email title */
                    $email_title = sprintf(l('s_cart.email_orders.title', $this->store_user->language), $this->store->name, $final_price, $this->store->currency);

                    /* Prepare the View for the email content */
                    $data = [
                        'store_user'                => $this->store_user,
                        'store'                     => $this->store,
                        'order_id'                  => $order_id,
                        'final_price'               => $final_price,
                        'order_number'              => $order_number,
                        'items'                     => $_POST['items'],
                        'type'                      => $_POST['type'],
                        'datetime'                  => Date::get('', -1, $this->store_user->timezone),
                    ];

                    $email_content = (new \Altum\View('s/partials/email_orders'))->run($data);

                    /* Send the email */
                    send_mail($this->store_user->email, $email_title, $email_content);
                }

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('store_id=' . $this->store->store_id);

                /* Do extra stuff based on the processor selected */
                switch($_POST['processor']) {
                    case 'paypal':

                        $this->paypal_create($order_id, $order_number, $final_price);

                    break;

                    case 'stripe':

                        $this->stripe_create($order_id, $order_number, $final_price);

                    break;

                    case 'offline_payment':

                        header('Location: ' . $this->store->full_url . '?page=cart&order=done'); die();

                    break;
                }

            }
        }

        /* Set a custom title */
        Title::set(sprintf(l('s_cart.title'), $this->store->name));

        /* Prepare the header */
        $view = new \Altum\View('s/partials/header', (array) $this);
        $this->add_view_content('header', $view->run(['store' => $this->store]));

        /* Main View */
        $data = [
            'store' => $this->store,
            'store_user' => $this->store_user,
        ];

        $view = new \Altum\View('s/cart/' . $this->store->theme . '/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function stripe_create($order_id, $order_number, $price) {
        try {
            /* Initiate Stripe */
            \Stripe\Stripe::setApiKey($this->store->stripe->secret_key);
            \Stripe\Stripe::setApiVersion('2020-08-27');

            /* Final price */
            $stripe_formatted_price = in_array($this->store->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '') * 100;

            $stripe_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => sprintf(l('s_cart.payment_name'), $this->store->name, $order_number),
                    'amount' => $stripe_formatted_price,
                    'currency' => $this->store->currency,
                    'quantity' => 1,
                ]],
                'metadata' => [
                    'order_id' => $order_id,
                    'store_id' => $this->store->store_id,
                    'user_id' => $this->store_user->user_id,
                ],
                'success_url' => $this->store->full_url . '?page=cart&order=done&return_type=success',
                'cancel_url' => $this->store->full_url . '?page=cart&return_type=cancel',
            ]);
        } catch (\Exception $exception) {
            if(DEBUG) {
                Alerts::add_error($exception->getCode() . ' - ' . $exception->getMessage());
            }

            Alerts::add_error(l('global.error_message.basic'));
            header('Location: ' . $this->store->full_url . '?page=cart'); die();
        }

        header('Location: ' . $this->store->full_url . '?page=cart&stripe_session_id=' . $stripe_session->id); die();
    }

    public function stripe_webhook() {

        /* Parse & control the store */
        require_once APP_PATH . 'controllers/s/Store.php';
        $store_controller = new \Altum\Controllers\Store((array) $this);

        $store_controller->init();

        /* Set the needed variables for the wrapper */
        $this->store_user = $store_controller->store_user;
        $this->store = $store_controller->store;

        if(!$this->store->cart_is_enabled || !$this->store->stripe->is_enabled || !$this->store_user->plan_settings->online_payments_is_enabled) {
            http_response_code(400);
            die();
        }

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->store->stripe->secret_key);
        \Stripe\Stripe::setApiVersion('2020-08-27');

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $this->store->stripe->webhook_secret
            );

            if(!in_array($event->type, ['checkout.session.completed'])) {
                die();
            }

            $session = $event->data->object;

            /* Get some needed variables */
            $payment_id = $session->id;
            $payer_id = $session->customer;
            $payer_object = \Stripe\Customer::retrieve($payer_id);
            $total_amount = in_array($this->store->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_total : $session->amount_total / 100;

            /* Process meta data */
            $metadata = $session->metadata;
            $order_id = (int) $metadata->order_id;

            /* :) */
            $processor = 'stripe';
            $billing = json_encode([
                'name' => $payer_object->name,
                'email' => $payer_object->email
            ]);

            /* Make sure the transaction is not already existing */
            if(db()->where('payment_id', $payment_id)->where('processor', 'stripe')->getOne('customers_payments', ['customer_payment_id'])) {
                http_response_code(400);
                die();
            }

            /* Update the order payment status */
            $stmt = database()->prepare("UPDATE `orders` SET `is_paid` = 1 WHERE `order_id` = ?");
            $stmt->bind_param('s', $order_id);
            $stmt->execute();
            $stmt->close();

            /* Insert the payment log */
            $stmt = database()->prepare("INSERT INTO `customers_payments` (`order_id`, `store_id`, `user_id`, `processor`, `payment_id`, `billing`, `total_amount`, `currency`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssss', $order_id, $this->store->store_id, $this->store_user->user_id, $processor, $payment_id, $billing, $total_amount, $this->store->currency, Date::$date);
            $stmt->execute();
            $stmt->close();

            echo 'successful';

        } catch(\UnexpectedValueException $e) {

            // Invalid payload
            http_response_code(400);
            exit();

        } catch(\Stripe\Error\SignatureVerification $e) {

            // Invalid signature
            http_response_code(400);
            exit();

        }
    }

    private function paypal_create($order_id, $order_number, $price) {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->store->paypal->client_id, $this->store->paypal->secret));
        $paypal->setConfig(['mode' => $this->store->paypal->mode]);

        /* Make sure the price is right depending on the currency */
        $price = in_array(settings()->payment->currency, ['JPY', 'TWD', 'HUF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '');

        /* Payment experience */
        $flowConfig = new FlowConfig();
        $flowConfig->setLandingPageType('Billing');
        $flowConfig->setUserAction('commit');
        $flowConfig->setReturnUriHttpMethod('GET');

        $presentation = new Presentation();
        $presentation->setBrandName($this->store->name);

        $inputFields = new InputFields();
        $inputFields->setAllowNote(true)
            ->setNoShipping(1)
            ->setAddressOverride(0);

        $webProfile = new WebProfile();
        $webProfile->setName($this->store->name . uniqid())
            ->setFlowConfig($flowConfig)
            ->setPresentation($presentation)
            ->setInputFields($inputFields)
            ->setTemporary(true);

        /* Create the experience profile */
        try {
            $createdProfileResponse = $webProfile->create($paypal);
        } catch (\Exception $exception) {

            if(DEBUG) {
                Alerts::add_error($exception->getCode() . ' - ' . $exception->getMessage());
            }

            Alerts::add_error(l('global.error_message.basic'));
            header('Location: ' . $this->store->full_url . '?page=cart'); die();

        }

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName(sprintf(l('s_cart.payment_name'), $this->store->name, $order_number))
            ->setCurrency($this->store->currency)
            ->setQuantity(1)
            ->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amount = new Amount();
        $amount->setCurrency($this->store->currency)
            ->setTotal($price);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->store->full_url . '?page=paypal_webhook&order_id=' . $order_id . '&return_type=success')
            ->setCancelUrl($this->store->full_url . '?page=cart&return_type=cancel');

        $payment = new Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions([$transaction])
            ->setExperienceProfileId($createdProfileResponse->getId());

        try {
            $payment->create($paypal);
        } catch (\Exception $exception) {

            if(DEBUG) {
                Alerts::add_error($exception->getCode() . ' - ' . $exception->getMessage());
            }

            Alerts::add_error(l('global.error_message.basic'));
            header('Location: ' . $this->store->full_url . '?page=cart'); die();

        }

        $payment_url = $payment->getApprovalLink();

        header('Location: ' . $payment_url); die();
    }

    public function paypal_webhook() {

        /* Parse & control the store */
        require_once APP_PATH . 'controllers/s/Store.php';
        $store_controller = new \Altum\Controllers\Store((array) $this);

        $store_controller->init();

        /* Set the needed variables for the wrapper */
        $this->store_user = $store_controller->store_user;
        $this->store = $store_controller->store;

        if(!$this->store->cart_is_enabled || !$this->store->paypal->is_enabled || !$this->store_user->plan_settings->online_payments_is_enabled) {
            http_response_code(400);
            die();
        }

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->store->paypal->client_id, $this->store->paypal->secret));
        $paypal->setConfig(['mode' => $this->store->paypal->mode]);

        /* Return confirmation processing */
        if(isset($_GET['return_type']) && isset($_GET['order_id']) && isset($_GET['page']) && $_GET['page'] == 'paypal_webhook' && isset($_GET['paymentId'], $_GET['PayerID'])) {

            $payment_id = $_GET['paymentId'];
            $payer_id = $_GET['PayerID'];

            try {
                $payment = Payment::get($payment_id, $paypal);
                $payer_info = $payment->getPayer()->getPayerInfo();
                $total_amount = $payment->getTransactions()[0]->getAmount()->getTotal();

                /* Execute the payment */
                $execute = new PaymentExecution();
                $execute->setPayerId($payer_id);

                $result = $payment->execute($execute, $paypal);

                /* Get status after execution */
                $payment_status = $payment->getState();

            } catch (\Exception $exception) {

                if(DEBUG) {
                    Alerts::add_error($exception->getCode() . ' - ' . $exception->getMessage());
                }

                Alerts::add_error(l('global.error_message.basic'));
                header('Location: ' . $this->store->full_url . '?page=cart'); die();

            }

            /* Make sure the payment is approved */
            if($payment_status != 'approved') {
                Alerts::add_error(l('global.error_message.basic'));
                header('Location: ' . $this->store->full_url . '?page=cart'); die();
            }

            /* Process meta data */
            $order_id = (int) $_GET['order_id'];

            /* :) */
            $processor = 'paypal';
            $billing = json_encode([
                'name' => $payer_info->getFirstName() . ' ' . $payer_info->getLastName(),
                'email' => $payer_info->getEmail()
            ]);

            /* Make sure the transaction is not already existing */
            if(db()->where('payment_id', $payment_id)->where('processor', 'paypal')->getOne('customers_payments', ['customer_payment_id'])) {
                http_response_code(400);
                die();
            }

            /* Update the order payment status */
            $stmt = database()->prepare("UPDATE `orders` SET `is_paid` = 1 WHERE `order_id` = ?");
            $stmt->bind_param('s', $order_id);
            $stmt->execute();
            $stmt->close();

            /* Insert the payment log */
            $stmt = database()->prepare("INSERT INTO `customers_payments` (`order_id`, `store_id`, `user_id`, `processor`, `payment_id`, `billing`, `total_amount`, `currency`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssss', $order_id, $this->store->store_id, $this->store_user->user_id, $processor, $payment_id, $billing, $total_amount, $this->store->currency, Date::$date);
            $stmt->execute();
            $stmt->close();

            header('Location: ' . $this->store->full_url . '?page=cart&order=done&return_type=success'); die();
        }

        header('Location: ' . $this->store->full_url . '?page=cart&return_type=cancel'); die();
    }
}
