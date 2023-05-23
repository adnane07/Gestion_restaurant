
<?php defined('ALTUMCODE') || die() ?>

<div class="">
    <div class="container">
        <?= \Altum\Alerts::output_alerts() ?>

        <div class="row">
            <div class="col-12 col-lg-6 d-flex flex-column justify-content-center align-items-center align-items-lg-start text-center text-lg-left">
                <h1 class="index-header mb-4"><?= l('index.header') ?></h1>
                <p class="index-subheader"><?= sprintf(l('index.subheader'), '<span class="text-primary-800 font-weight-bold">', '</span>') ?></p>

                <ul class="list-style-none my-4">
                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw mr-2 fa-check-circle text-primary"></i>
                        <div class="text-muted">
                            <?= l('index.feature.one') ?>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw mr-2 fa-check-circle text-primary"></i>
                        <div class="text-muted">
                            <?= l('index.feature.two') ?>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-2">
                        <i class="fa fa-fw mr-2 fa-check-circle text-primary"></i>
                        <div class="text-muted">
                            <?= l('index.feature.three') ?>
                        </div>
                    </li>
                </ul>

                <div>
                    <a href="<?= url('register') ?>" class="btn btn-lg btn-primary index-button"><?= l('index.button') ?></a>
                </div>
            </div>

            <div class="col-10 col-md-8 col-lg-4 offset-1 offset-md-2 mt-4 mt-lg-0">
                <img src="<?= ASSETS_URL_PATH . 'images/index/hero.png' ?>" class="img-fluid index-hero" loading="lazy" />
            </div>
        </div>

    </div>
</div>

<div class="my-10"></div>

<div class="bg-primary-800 py-6">
    <div class="container">
        <div class="d-flex flex-column align-items-center">
            <div class="text-center">
                <h2 class="text-gray-50"><?= l('index.example.header') ?></h2>
                <p class="text-gray-100"><?= l('index.example.subheader') ?></p>
            </div>

            <div class="mt-4">
                <a href="<?= url('s/demo') ?>" target="_blank">
                    <div class="index-qr"></div>
                </a>
            </div>

            <div class="mt-5">
                <a href="<?= url('s/demo') ?>" class="btn btn-light" target="_blank">
                    <i class="fa fa-fw fa-sm fa-external-link-alt"></i>
                    <?= l('index.example.button') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="my-10"></div>

<div class="container">
    <div class="text-center d-flex flex-column align-items-center mb-5">
        <h2 class="mt-2"><?= sprintf(l('index.demo.header'), '<span class="text-primary text-underline">', '</span>') ?></h2>
        <ul class="list-style-none d-flex flex-column flex-lg-row mt-4">
            <li class="d-flex align-items-baseline mb-2 mr-lg-4">
                <span class="font-weight-bold text-primary-800 h5 mr-1">1.</span>
                <div class="text-muted">
                    <?= l('index.demo.one') ?>
                </div>
            </li>

            <li class="d-flex align-items-baseline mb-2 mr-lg-4">
                <span class="font-weight-bold text-primary-800 h5 mr-1">2.</span>
                <div class="text-muted">
                    <?= l('index.demo.two') ?>
                </div>
            </li>

            <li class="d-flex align-items-baseline mb-2 mr-lg-4">
                <span class="font-weight-bold text-primary-800 h5 mr-1">3.</span>
                <div class="text-muted">
                    <?= l('index.demo.three') ?>
                </div>
            </li>
        </ul>
    </div>

    <img src="<?= ASSETS_URL_PATH . 'images/index/demo.png' ?>" class="img-fluid rounded shadow" loading="lazy" />
</div>

<div class="my-10"></div>

<div class="container">
    <div class="row justify-content-between">
        <div class="col-12 col-md-4 d-flex flex-column justify-content-center order-1 order-md-0">
            <small class="text-uppercase font-weight-bold text-muted mb-2"><?= l('index.lightweight.name') ?></small>

            <div>
                <h2 class="h4 mb-3"><?= l('index.lightweight.header') ?></h2>

                <p class="text-muted"><?= l('index.lightweight.subheader') ?></p>
            </div>
        </div>

        <div class="col-12 col-md-7 text-center mb-5 mb-md-0 order-0 order-md-1">
            <img src="<?= ASSETS_FULL_URL . 'images/index/lightweight.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>
    </div>
</div>

<div class="my-10"></div>

<div class="container">
    <div class="row justify-content-between">
        <div class="col-12 col-md-7 text-center mb-5 mb-md-0">
            <img src="<?= ASSETS_FULL_URL . 'images/index/analytics.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>

        <div class="col-12 col-md-4 d-flex flex-column justify-content-center">
            <small class="text-uppercase font-weight-bold text-muted mb-2"><?= l('index.analytics.name') ?></small>

            <div>
                <h2 class="h4 mb-3"><?= l('index.analytics.header') ?></h2>

                <p class="text-muted"><?= l('index.analytics.subheader') ?></p>
            </div>
        </div>
    </div>
</div>

<div class="my-10"></div>

<div class="container">
    <div class="row justify-content-between">
        <div class="col-12 col-md-4 d-flex flex-column justify-content-center order-1 order-md-0">
            <small class="text-uppercase font-weight-bold text-muted mb-2"><?= l('index.extras_options_variants.name') ?></small>

            <div>
                <h2 class="h4 mb-3"><?= l('index.extras_options_variants.header') ?></h2>

                <p class="text-muted"><?= l('index.extras_options_variants.subheader') ?></p>
            </div>
        </div>

        <div class="col-12 col-md-7 text-center mb-5 mb-md-0 order-0 order-md-1">
            <img src="<?= ASSETS_FULL_URL . 'images/index/extras_options_variants.png' ?>" class="img-fluid shadow" loading="lazy" />
        </div>
    </div>
</div>

<div class="my-10"></div>

<div class="container">
    <div class="text-center mb-5">
        <small class="text-primary font-weight-bold text-uppercase"><?= l('index.pricing.subheader') ?></small>
        <h2 class="mt-2"><?= l('index.pricing.header') ?></h2>
    </div>

    <?= $this->views['plans'] ?>
</div>

<div class="my-10"></div>

<?php if(settings()->users->register_is_enabled): ?>
    <div class="bg-primary-800 py-6">
        <div class="container">
            <div class="d-flex flex-column flex-lg-row justify-content-around align-items-lg-center">
                <div>
                    <h2 class="text-gray-100"><?= l('index.cta.header') ?></h2>
                    <p class="text-gray-200"><?= l('index.cta.subheader') ?></p>
                </div>

                <div>
                    <a href="<?= url('register') ?>" class="btn btn-primary"><?= l('index.cta.register') ?></a>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>


<?php ob_start() ?>
<script src="<?= ASSETS_FULL_URL . 'js/libraries/jquery-qrcode.min.js' ?>"></script>
<script>
    'use strict';

    let generate_qr = () => {
        let qr_url = <?= json_encode(url('s/demo?referrer=qr')) ?>;

        let default_options = {
            render: 'image',
            minVersion: 1,
            maxVersion: 40,
            ecLevel: 'H',
            left: 0,
            top: 0,
            size: 600,
            fill: '#000',
            background: '#fff',
            text: qr_url,
            radius: 0,
            quiet: 0,
            mode: 0,
            mSize: 0.1,
            mPosX: 0.5,
            mPosY: 0.5,
            fontname: 'arial',
        };

        $('.index-qr').qrcode(default_options);
    }

    generate_qr();
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

