<?php defined('ALTUMCODE') || die() ?>


<?php 
  $test =$_GET["table"];

 session_start();
 $_SESSION["numtable"] =$test;

?>


<?= $this->views['header'] ?>

<div class="container my-4">
    <div class="d-flex">
        <p class="text-black text-uppercase font-weight-bold mr-3">
            <?= $data->store->name ?>
        </p>

        <?php if($data->store->details->hours->{$data->day}->is_enabled): ?>
            <?php if(!empty($data->store->details->hours->{$data->day}->hours)): ?>
            <div>
                <div class="svg-sm mr-1 d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/clock.svg') ?></div>

                <?= $data->store->details->hours->{$data->day}->hours ?>
            </div>
            <?php endif ?>
        <?php else: ?>
            <span class="text-danger">
                <?= l('s_store.closed') ?>
            </span>
        <?php endif ?>
    </div>

    <div class="d-flex flex-column flex-lg-row">

        <?php if(!empty($data->store->details->address)): ?>
        <a href="<?= 'https://www.google.com/maps/search/?api=1&query=' . $data->store->details->address ?>" target="_blank" class="col-12 col-lg-2 mb-3 mb-lg-0 mr-3 text-truncate btn btn-sm btn-link bg-primary-100">
            <div class="svg-sm mr-1 d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/location-marker.svg') ?></div> <span><?= $data->store->details->address ?></span>
        </a>
        <?php endif ?>

        <?php if(!empty($data->store->details->phone)): ?>
        <a href="<?= 'tel:' . $data->store->details->phone ?>" target="_blank" class="col-12 col-lg-2 mb-3 mb-lg-0 mr-3 text-truncate btn btn-sm btn-link bg-primary-100">
            <div class="svg-sm mr-1 d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/phone.svg') ?></div> <span><?= $data->store->details->phone ?></span>
        </a>
        <?php endif ?>

        <?php if(!empty($data->store->details->email)): ?>
        <a href="<?= 'mailto:' . $data->store->details->email ?>" target="_blank" class="col-12 col-lg-2 mb-3 mb-lg-0 mr-3 text-truncate btn btn-sm btn-link bg-primary-100">
            <div class="svg-sm mr-1 d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/at-symbol.svg') ?></div> <span><?= l('s_store.email') ?></span>
        </a>
        <?php endif ?>

        <?php if(!empty($data->store->details->website)): ?>
        <a href="<?= $data->store->details->website ?>" target="_blank" class="col-12 col-lg-2 mb-3 mb-lg-0 mr-3 text-truncate btn btn-sm btn-link bg-primary-100">
            <div class="svg-sm mr-1 d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/globe.svg') ?></div> <span><?= l('s_store.website') ?></span>
        </a>
        <?php endif ?>

    </div>
</div>

<div class="container <?= $this->store->cart_is_enabled ? 'mt-7' : 'mt-5' ?> mt-md-5">

    <?php if(count($data->menus)): ?>
    <h1 class="h3">
        <?= l('s_store.menus') ?>
    </h1>

    <div class="row">
        <?php foreach($data->menus as $row): ?>
        <div class="col-12 col-md-6 col-lg-4 mb-5">
            <div class="card border-0 bg-gray-50 h-100">
                <?php if(!empty($row->image)): ?>
                    <div class="store-menu-image-wrapper">
                        <img src="<?= UPLOADS_FULL_URL . 'menu_images/' . $row->image ?>" class="store-menu-image-background" loading="lazy" />
                    </div>
                <?php endif ?>

                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-4">
                        <h3 class="h4 card-title"><?= $row->name ?></h3>
                        <p class="card-subtitle mt-1 text-muted"><?= $row->description ?></p>
                    </div>

                    <a href="<?= $data->store->full_url . $row->url ?>" class="btn btn-sm btn-block btn-outline-primary stretched-link">
                        <?= l('s_store.view_menu') ?>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>

</div>

<?= include_view(THEME_PATH . 'views/s/partials/share.php', ['external_url' => $data->store->full_url]) ?>
