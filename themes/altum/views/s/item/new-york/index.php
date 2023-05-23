<?php defined('ALTUMCODE') || die() ?>

<?= $this->views['header'] ?>

<div class="container <?= $this->store->cart_is_enabled ? 'mt-7' : 'mt-5' ?> mt-md-5">

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= $data->store->full_url ?>"><?= l('s_store.breadcrumb') ?></a> <div class="svg-sm text-muted d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/chevron-right.svg') ?></div>
            </li>
            <li>
                <a href="<?= $data->store->full_url . $data->menu->url ?>"><?= $data->menu->name ?></a> <div class="svg-sm text-muted d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/chevron-right.svg') ?></div>
            </li>
            <li>
                <a href="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url ?>"><?= $data->category->name ?></a> <div class="svg-sm text-muted d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/chevron-right.svg') ?></div>
            </li>
            <li class="active" aria-current="page"><?= $data->item->name ?></li>
        </ol>
    </nav>

    <div class="row">
        <?php if(!empty($data->item->image)): ?>
        <div class="col-12 col-lg-5">
            <div class="store-item-main-image-wrapper mr-4">
                <img src="<?= UPLOADS_FULL_URL . 'item_images/' . $data->item->image ?>" class="store-item-main-image-background" loading="lazy" />
            </div>
        </div>
        <?php endif ?>

        <div class="col-12 col-lg-5">
            <h1 class="h3"><?= $data->item->name ?></h1>
            <p class="text-muted"><?= $data->item->description ?></p>

            <div id="item_price_wrapper" class="my-3 <?= $data->item->variants_is_enabled ? 'd-none' : null ?>">
                <span
                        id="item_price"
                        data-default-item-price="<?= $data->item->price ?>"
                        data-item-price="<?= $data->item->price ?>"
                        data-item-id="<?= $data->item->item_id ?>"
                        data-item-name="<?= $data->item->name ?>"
                        data-item-full-url="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url . '/' . $data->item->url ?>"
                        data-item-full-image="<?= $data->item->image ? UPLOADS_FULL_URL . 'item_images/' . $data->item->image : null ?>"
                        class="h1 m-0 mr-1"
                ><?= $data->item->price ?></span>
                <span class="text-muted"><?= $data->store->currency ?></span>
            </div>

            <?php if($data->item->variants_is_enabled): ?>
            <div class="my-5">
                <div class="d-flex align-items-center mb-3">
                    <span class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('s_item.variants') ?></span>

                    <div class="flex-fill">
                        <hr class="border-gray-100" />
                    </div>
                </div>

                <?php foreach($data->item_options as $row): ?>
                    <?php $row->options = json_decode($row->options) ?>

                    <div class="mb-3">
                        <div class="font-weight-bold text-muted mb-1"><?= $row->name ?></div>

                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <?php foreach($row->options as $option_key => $option_value): ?>
                            <label class="btn btn-outline-primary">
                                <input
                                        type="radio"
                                        name="item_option_id_<?= $row->item_option_id ?>"
                                        data-item-option-id="<?= $row->item_option_id ?>"
                                        data-item-option-key="<?= $option_key ?>"
                                        data-item-option-name="<?= $row->name; ?>"
                                        data-item-option-option="<?= $option_value ?>"
                                        value="<?= $row->item_option_id . '_' . $option_key ?>"
                                >
                                <?= $option_value ?>
                            </label>
                        <?php endforeach ?>
                        </div>
                    </div>

                <?php endforeach ?>
            </div>
            <?php endif ?>


            <?php if(count($data->item_extras)): ?>
            <div class="my-5">
                <div class="d-flex align-items-center mb-3">
                    <span class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('s_item.extras') ?></span>

                    <div class="flex-fill">
                        <hr class="border-gray-100" />
                    </div>
                </div>

                <?php foreach($data->item_extras as $row): ?>
                    <div class="d-flex mb-3">
                        <div class="custom-control custom-switch mr-3">
                            <input
                                    id="item_extra_<?= $row->item_extra_id ?>"
                                    name="item_extras[]"
                                    data-item-extra-id="<?= $row->item_extra_id ?>"
                                    data-item-extra-name="<?= $row->name ?>"
                                    data-item-extra-price="<?= $row->price ?>"
                                    type="checkbox"
                                    class="custom-control-input"
                            >
                            <label class="custom-control-label" for="item_extra_<?= $row->item_extra_id ?>"></label>
                        </div>

                        <div class="mr-3">
                            <span class="h6 mb-0">
                                <?= '+' . $row->price ?>
                            </span>
                            <span class="text-muted">
                                <?= $data->store->currency ?>
                            </span>
                        </div>

                        <div>
                            <div><?= $row->name ?></div>
                            <small class="text-muted"><?= $row->description ?></small>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <?php endif ?>

            <?php if($this->store->cart_is_enabled): ?>
            <div class="my-5">
                <button id="add_to_cart" type="button" class="btn btn-primary" <?= $data->item->variants_is_enabled ? 'disabled="disabled"' : null ?>>

                    <div id="add_to_cart_not_added">
                        <div class="svg-md d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/shopping-cart.svg') ?></div>
                        <?= l('s_item.add_to_cart') ?>
                    </div>

                    <div id="add_to_cart_added" class="d-none">
                        <div class="svg-md d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/check-circle.svg') ?></div>
                        <?= l('s_item.added_to_cart') ?>
                    </div>

                </button>
            </div>
            <?php endif ?>

        </div>
    </div>

</div>

<?= include_view(THEME_PATH . 'views/s/partials/share.php', ['external_url' => $data->store->full_url .  $data->menu->url . '/' . $data->category->url . '/' . $data->item->url]) ?>

<?php
    $js_item_variants = [];

    if($data->item->variants_is_enabled) {
        foreach ($data->item_variants as $row) {
            $js_item_variants_item = [];
            $js_item_variants_item['item_variant_id'] = $row->item_variant_id;
            $js_item_variants_item['price'] = $row->price;
            $js_item_variants_item['item_options_ids'] = $row->item_options_ids;

            $js_item_variants[] = $js_item_variants_item;
        }
    }
?>


<?php ob_start() ?>

<?php if($this->store->cart_is_enabled): ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/md5.min.js' ?>"></script>
<?php endif ?>

<script>
    'use strict';

    <?php if($this->store->cart_is_enabled): ?>
    let cart_name = <?= json_encode($data->store->store_id . '_cart') ?>;

    /* Add to cart button */
    document.querySelector('#add_to_cart').addEventListener('click', event => {
        let cart = null;

        try {
            cart = localStorage.getItem(cart_name) ? JSON.parse(localStorage.getItem(cart_name)) : [];

        } catch(error) {
            cart = [];
        }

        /* Get the selected extras */
        let item_extras = [];

        document.querySelectorAll('input[name="item_extras[]"]:checked').forEach(element => {
            item_extras.push({
                item_extra_id: element.getAttribute('data-item-extra-id'),
                name: element.getAttribute('data-item-extra-name')
            });
        });

        /* Save all the related details */
        let item = {
            item_id: parseFloat(document.querySelector('#item_price').getAttribute('data-item-id')),
            name: document.querySelector('#item_price').getAttribute('data-item-name'),
            full_url: document.querySelector('#item_price').getAttribute('data-item-full-url'),
            full_image: document.querySelector('#item_price').getAttribute('data-item-full-image'),
            item_extras,
            item_variant_id: selected_item_variant ? selected_item_variant.item_variant_id : null,
            item_variant_options: selected_item_variant ? selected_item_variant_options : null,
            final_price: calculate_final_item_price(),
            quantity: 1,
        };

        item['item_generated_id'] = md5(JSON.stringify(item));

        /* Check if we should add it to the cart or update the quantity of an already existing item in the cart */
        let item_already_added = cart.findIndex(element => element.item_generated_id == item['item_generated_id']);

        if(item_already_added == -1) {
            cart.push(item);
        } else {
            cart[item_already_added].quantity++;
        }

        localStorage.setItem(cart_name, JSON.stringify(cart));

        /* Display aid */
        document.querySelector('#add_to_cart_not_added').classList.add('d-none');
        document.querySelector('#add_to_cart_added').classList.remove('d-none');
        document.querySelector('#add_to_cart').classList.add('btn-success');
        document.querySelector('#add_to_cart').setAttribute('disabled', 'disabled');

        cart_count();

        setTimeout(() => {
            document.querySelector('#add_to_cart_not_added').classList.remove('d-none');
            document.querySelector('#add_to_cart_added').classList.add('d-none');
            document.querySelector('#add_to_cart').classList.remove('btn-success');
            document.querySelector('#add_to_cart').removeAttribute('disabled');
        }, 1500);
    });
    <?php endif ?>

    /* Calculate price */
    let calculate_final_item_price = () => {
        let item_price = parseFloat(document.querySelector('#item_price').getAttribute('data-item-price'));

        /* Get all the selected extras */
        let extra_price = 0;
        document.querySelectorAll('input[name="item_extras[]"]:checked').forEach(element => {
            extra_price += parseFloat(element.getAttribute('data-item-extra-price'));
        });

        /* Display the final price */
        let final_price = nr(item_price + extra_price, 0);
        document.querySelector('#item_price').textContent = final_price;

        return final_price;
    }

    /* Item extras logic */
    document.querySelectorAll('input[name="item_extras[]"]').forEach(element => {
        element.addEventListener('change', calculate_final_item_price);
    });

    /* Item variants logic */
    let item_variants = <?= json_encode($js_item_variants) ?>;

    let item_variants_list = [];
    let available_item_options_default = [];
    let selected_item_variant = null;
    let selected_item_variant_options = null;

    <?php if($data->item->variants_is_enabled): ?>
    /* Verify the potential available item options */
    for(let item_variant of item_variants) {
        let item_variants_list_variant = '';

        for(let item_variant_options_id of item_variant.item_options_ids) {
            available_item_options_default.push(`${item_variant_options_id.item_option_id}_${item_variant_options_id.option}`);

            item_variants_list_variant += `${item_variant_options_id.item_option_id}_${item_variant_options_id.option}+`;
        }

        item_variants_list.push({
            key: item_variants_list_variant,
            price: item_variant.price,
            item_variant_id: item_variant.item_variant_id
        });
    }

    /* Prepare the inputs */
    document.querySelectorAll('input[name^="item_option_id_"]').forEach(element => {

        /* Verify if enabled by default or not */
        if(!available_item_options_default.includes(element.value)) {
            /* Disable radio button */
            element.disabled = true;

            /* Disabled actual button */
            element.parentElement.classList.add('disabled');
        }

        /* On click for the inputs */
        element.addEventListener('click', event => {

            let selected_element_item_option_id = element.getAttribute('data-item-option-id');
            let selected_element_item_option_key = element.getAttribute('data-item-option-key');

            /* Go through all variants */
            document.querySelectorAll('input[name^="item_option_id_"]').forEach(item_option_element => {

                /* Avoid the already selected and the parents */
                if(
                    (
                        item_option_element.getAttribute('data-item-option-id') == selected_element_item_option_id
                        && item_option_element.getAttribute('data-item-option-key') == selected_element_item_option_key
                    )
                    ||
                    (
                        selected_element_item_option_id > item_option_element.getAttribute('data-item-option-id')
                    )
                ) {
                    // nothing.
                } else {

                    /* Deselect the input */
                    item_option_element.checked = false;

                    /* Deselect the button state */
                    item_option_element.parentElement.classList.remove('active');

                }
            });


            /* :) */
            let available_item_options = [];

            /* Verify all the selected buttons */
            let selected_potential_item_variant = '';

            document.querySelectorAll('input[name^="item_option_id_"]:checked').forEach(element => {
                selected_potential_item_variant += `${element.value}+`;
            });

            for(let item_variant of item_variants) {
                let potential_item_variant = '';
                let triggered = false;

                for(let item_variant_options_id of item_variant.item_options_ids) {
                    potential_item_variant += `${item_variant_options_id.item_option_id}_${item_variant_options_id.option}+`;

                    if(
                        selected_potential_item_variant == potential_item_variant
                        && selected_element_item_option_id == item_variant_options_id.item_option_id
                        && selected_element_item_option_key == item_variant_options_id.option
                    ) {
                        triggered = true;
                        continue;
                    }

                    if(triggered) {
                        available_item_options.push(`${item_variant_options_id.item_option_id}_${item_variant_options_id.option}`);
                    }

                }
            }

            /* Go through all variants */
            document.querySelectorAll('input[name^="item_option_id_"]').forEach(item_option_element => {

                /* Verify if we can have the element active */
                if(item_option_element.getAttribute('data-item-option-id') > selected_element_item_option_id) {

                    /* Remove the disabled state as it can be potentially used */
                    item_option_element.disabled = false;
                    item_option_element.parentElement.classList.remove('disabled');

                    if(!available_item_options.includes(item_option_element.value)) {

                        /* Disable radio button */
                        item_option_element.disabled = true;

                        /* Disabled actual button */
                        item_option_element.parentElement.classList.add('disabled');

                    }

                }

            });

            /* Check the clicked input */
            element.checked = true;

            /* Visually check the button */
            element.parentElement.classList.add('active');

            /* Verify all the selected buttons */
            let potential_item_variant = '';
            let potential_item_variant_options = [];

            document.querySelectorAll('input[name^="item_option_id_"]:checked').forEach(element => {
                potential_item_variant += `${element.value}+`;
                potential_item_variant_options.push({
                    name: element.getAttribute('data-item-option-name'),
                    option: element.getAttribute('data-item-option-option'),
                });
            });

            let found_item_variant = item_variants_list.find(element => {
               return element.key == potential_item_variant
            });

            /* Display the price in the listing and modify it */
            if(found_item_variant) {
                selected_item_variant = found_item_variant;
                selected_item_variant_options = potential_item_variant_options;
                document.querySelector('#item_price_wrapper').classList.remove('d-none');
                document.querySelector('#item_price').innerText = found_item_variant.price;
                document.querySelector('#item_price').setAttribute('data-item-price', found_item_variant.price);
                document.querySelector('#add_to_cart').removeAttribute('disabled');

                calculate_final_item_price();
            }

            /* Make sure the price is hidden if a variant is not found */
            else {
                selected_item_variant = null;
                selected_item_variant_options = null;
                document.querySelector('#item_price_wrapper').classList.add('d-none');
                document.querySelector('#add_to_cart').setAttribute('disabled', 'disabled');
            }

        });

    });
    <?php endif ?>
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
