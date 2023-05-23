<?php defined('ALTUMCODE') || die() ?>

<nav class="navbar navbar-light bg-white fixed-top store-navbar d-lg-none">
    <div class="container">
        <a class="navbar-brand text-truncate col p-0" href="<?= $data->store->full_url ?>">
            <?php if($data->store->logo): ?>
                <img src="<?= UPLOADS_FULL_URL . 'store_logos/' . $data->store->logo ?>" class="img-fluid store-navbar-logo mr-3" alt="<?= $data->store->title ?>" loading="lazy" />
            <?php endif ?>

            <?= $data->store->title ?>
        </a>

        <?php if($this->store->cart_is_enabled): ?>
        <ul class="navbar-nav col-auto p-0">
            <li class="nav-item">
                <a class="nav-link store-cart-link" href="<?= $data->store->full_url . '?page=cart' ?>">
                    <div class="svg-md d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/shopping-cart.svg') ?></div>
                    <span class="badge badge-danger badge-pill"></span>
                </a>
            </li>
        </ul>
        <?php endif ?>
    </div>
</nav>

<div class="container mt-5 d-none d-lg-block">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex position-relative">
            <?php if($data->store->logo): ?>
                <div class="mr-4">
                    <img src="<?= UPLOADS_FULL_URL . 'store_logos/' . $data->store->logo ?>" class="img-fluid store-logo" alt="<?= $data->store->title ?>" loading="lazy" />
                </div>
            <?php endif ?>

            <div class="d-flex flex-column">
                <a href="<?= $data->store->full_url ?>" class="stretched-link">
                    <span class="h1 mb-0 store-title"><?= $data->store->title ?></span>
                </a>

                <?php if($data->store->description): ?>
                    <span class="store-description">
                        <?= $data->store->description ?>
                    </span>
                <?php endif ?>
            </div>
        </div>

        <?php if($this->store->cart_is_enabled): ?>
        <a href="<?= $data->store->full_url . '?page=cart' ?>" class="btn btn-outline-primary store-cart-link">
            <div class="svg-md d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/shopping-cart.svg') ?></div>
            <?= l('s_cart.menu') ?>
            <span class="badge badge-danger badge-pill"></span>
        </a>
        <?php endif ?>
    </div>
</div>

<?php /* Only display the cover background if there is an image and only on the store index */ ?>
<?php if(!empty($data->store->image) && \Altum\Router::$controller_key == 'store'): ?>
<div class="container mt-8 mb-5 my-lg-5">
    <a href="<?= $data->store->full_url ?>">
        <div class="store-cover-wrapper">
            <div
                class="store-cover-background"
                style="<?= !empty($data->store->image) ? 'background-image: url(\'' . UPLOADS_FULL_URL . 'store_images/' . $data->store->image . '\')' : null ?>"
            ></div>
        </div>
    </a>
</div>
<?php else: ?>
    <div class="container my-2 d-lg-none">&nbsp;</div>
<?php endif ?>

<?php if($this->store->cart_is_enabled): ?>
<?php ob_start() ?>
<script>
    'use strict';

    let cart_count = () => {

        let cart_name = <?= json_encode($data->store->store_id . '_cart') ?>;

        let cart = localStorage.getItem(cart_name) ? JSON.parse(localStorage.getItem(cart_name)) : [];

        document.querySelectorAll('.store-cart-link').forEach(element => {

            if(cart.length) {
                element.querySelector('span').innerText = cart.length;
            } else {
                element.querySelector('span').innerText = '';
            }

        });

    }

    cart_count();

    /* Listen for changes on the localstorage on other potential tabs */
    window.addEventListener('storage', () => {
        cart_count();
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
<?php endif ?>
