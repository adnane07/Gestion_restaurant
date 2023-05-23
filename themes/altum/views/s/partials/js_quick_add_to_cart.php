<?php defined('ALTUMCODE') || die() ?>

<?php if($data->store->cart_is_enabled): ?>
    <?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/md5.min.js' ?>"></script>

    <script>
        'use strict';

        let cart_name = <?= json_encode($data->store->store_id . '_cart') ?>;

        /* Add to cart button */
        document.querySelectorAll('.add_to_cart').forEach(element => {
            element.addEventListener('click', event => {

                let cart = null;

                try {
                    cart = localStorage.getItem(cart_name) ? JSON.parse(localStorage.getItem(cart_name)) : [];

                } catch (error) {
                    cart = [];
                }

                /* Get the selected extras */
                let item_extras = [];

                /* Save all the related details */
                let item = {
                    item_id: parseFloat(element.getAttribute('data-item-id')),
                    name: element.getAttribute('data-item-name'),
                    full_url: element.getAttribute('data-item-full-url'),
                    full_image: element.getAttribute('data-item-full-image'),
                    item_extras,
                    item_variant_id: null,
                    item_variant_options: null,
                    final_price: parseFloat(element.getAttribute('data-item-price')),
                    quantity: 1,
                };

                item['item_generated_id'] = md5(JSON.stringify(item));

                /* Check if we should add it to the cart or update the quantity of an already existing item in the cart */
                let item_already_added = cart.findIndex(element => element.item_generated_id == item['item_generated_id']);

                if (item_already_added == -1) {
                    cart.push(item);
                } else {
                    cart[item_already_added].quantity++;
                }

                localStorage.setItem(cart_name, JSON.stringify(cart));

                /* Display aid */
                element.querySelector('.add_to_cart_not_added').classList.add('d-none');
                element.querySelector('.add_to_cart_added').classList.remove('d-none');
                element.classList.add('btn-success');
                element.setAttribute('disabled', 'disabled');

                cart_count();

                setTimeout(() => {
                    element.querySelector('.add_to_cart_not_added').classList.remove('d-none');
                    element.querySelector('.add_to_cart_added').classList.add('d-none');
                    element.classList.remove('btn-success');
                    element.removeAttribute('disabled');
                }, 1500);
            });
        });
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
