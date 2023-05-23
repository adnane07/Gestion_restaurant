<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('dashboard') ?>"><?= l('dashboard.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('store/' . $data->store->store_id) ?>"><?= l('store.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('menu/' . $data->menu->menu_id) ?>"><?= l('menu.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('category/' . $data->category->category_id) ?>"><?= l('category.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('item.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('item.header'), $data->item->name) ?></h1>

        <div class="d-flex align-items-center col-auto p-0">
            <div>
                <button
                        id="url_copy"
                        type="button"
                        class="btn btn-link text-secondary"
                        data-toggle="tooltip"
                        title="<?= l('global.clipboard_copy') ?>"
                        aria-label="<?= l('global.clipboard_copy') ?>"
                        data-copy="<?= l('global.clipboard_copy') ?>"
                        data-copied="<?= l('global.clipboard_copied') ?>"
                        data-clipboard-text="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url . '/' . $data->item->url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>

            <?= include_view(THEME_PATH . 'views/item/item_dropdown_button.php', ['id' => $data->item->item_id, 'resource_name' => $data->item->name]) ?>
        </div>
    </div>

    <p>
        <a href="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url . '/' . $data->item->url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->store->full_url . $data->menu->url . '/' . $data->category->url . '/' . $data->item->url ?>
        </a>
    </p>

    <?php if($data->item->variants_is_enabled): ?>
        <div class="d-flex align-items-center mb-3">
            <h2 class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('item_option.item_options') ?></h2>

            <div class="flex-fill">
                <hr class="border-gray-100" />
            </div>

            <div class="ml-3">
                <a href="<?= url('item-option-create/' . $data->item->item_id) ?>" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('item_option.create') ?></a>
            </div>
        </div>

        <?php if(count($data->item_options)): ?>
            <div class="row">

                <?php foreach($data->item_options as $row): ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between">
                                    <h3 class="h4 card-title">
                                        <?= $row->name ?>
                                    </h3>

                                    <?= include_view(THEME_PATH . 'views/item-option/item_option_dropdown_button.php', ['id' => $row->item_option_id, 'resource_name' => $row->name]) ?>
                                </div>

                                <p class="m-0">
                                    <small class="text-muted">
                                        <i class="fa fa-fw fa-sm fa-bars text-muted mr-1"></i> <?= sprintf(l('item_option.options'), implode(', ', $row->options)) ?>
                                    </small>
                                </p>
                                <p class="m-0">
                                    <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                        <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('category.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                    </small>
                                </p>
                            </div>

                            <div class="card-footer bg-gray-50 border-0">
                                <div class="d-flex flex-lg-row justify-content-lg-between">
                                    <div>
                                    </div>

                                    <div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center justify-content-center py-3">
                        <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('item_option.no_data') ?>" />
                        <h2 class="h4 text-muted"><?= l('item_option.no_data') ?></h2>
                        <p class="text-muted"><?= l('item_option.no_data_help') ?></p>
                    </div>
                </div>
            </div>
        <?php endif ?>


        <div class="d-flex align-items-center mb-3">
            <h2 class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('item_variant.item_variants') ?></h2>

            <div class="flex-fill">
                <hr class="border-gray-100" />
            </div>

            <div class="ml-3">
                <a href="<?= url('item-variant-create/' . $data->item->item_id) ?>" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('item_variant.create') ?></a>
            </div>
        </div>

        <?php if(count($data->item_variants)): ?>
            <div class="row">

                <?php foreach($data->item_variants as $row): ?>
                    <div class="col-12 col-md-6 col-xl-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div class="d-flex justify-content-between">
                                    <h3 class="h4 card-title"></h3>

                                    <?= include_view(THEME_PATH . 'views/item-variant/item_variant_dropdown_button.php', ['id' => $row->item_variant_id]) ?>
                                </div>

                                <div class="mb-3">
                                    <?php foreach($row->item_options_ids as $item_option_id): ?>
                                        <p class="m-0">
                                            <strong><?= $data->item_options[$item_option_id->item_option_id]->name ?>:</strong> <span><?= $data->item_options[$item_option_id->item_option_id]->options[$item_option_id->option] ?></span>
                                        </p>
                                    <?php endforeach ?>
                                </div>

                                <p class="m-0">
                                    <small class="text-muted">
                                        <i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= sprintf(l('item_extra.price_currency'), $row->price, $data->store->currency) ?>
                                    </small>
                                </p>
                                <p class="m-0">
                                    <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                        <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('category.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                    </small>
                                </p>
                            </div>

                            <div class="card-footer bg-gray-50 border-0">
                                <div class="d-flex flex-lg-row justify-content-lg-between">
                                    <div>
                                    </div>

                                    <div>
                                        <?php if($row->is_enabled): ?>
                                            <span class="badge badge-success"><i class="fa fa-fw fa-check"></i> <?= l('global.active') ?></span>
                                        <?php else: ?>
                                            <span class="badge badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= l('global.disabled') ?></span>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>

        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center justify-content-center py-3">
                        <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('item_variant.no_data') ?>" />
                        <h2 class="h4 text-muted"><?= l('item_variant.no_data') ?></h2>
                        <p class="text-muted"><?= l('item_variant.no_data_help') ?></p>
                    </div>
                </div>
            </div>
        <?php endif ?>
    <?php endif ?>



    <div class="d-flex align-items-center mb-3">
        <h2 class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('item_extra.item_extras') ?></h2>

        <div class="flex-fill">
            <hr class="border-gray-100" />
        </div>

        <div class="ml-3">
            <a href="<?= url('item-extra-create/' . $data->item->item_id) ?>" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('item_extra.create') ?></a>
        </div>
    </div>

    <?php if(count($data->item_extras)): ?>
        <div class="row">

            <?php foreach($data->item_extras as $row): ?>
                <div class="col-12 col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h4 mb-0">
                                    <?= $row->name ?>
                                </h3>

                                <?= include_view(THEME_PATH . 'views/item-extra/item_extra_dropdown_button.php', ['id' => $row->item_extra_id, 'resource_name' => $row->name]) ?>
                            </div>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= sprintf(l('item_extra.price_currency'), $row->price, $data->store->currency) ?>
                                </small>
                            </p>
                            <p class="m-0">
                                <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                    <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('category.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                </small>
                            </p>
                        </div>

                        <div class="card-footer bg-gray-50 border-0">
                            <div class="d-flex flex-lg-row justify-content-lg-between">
                                <div>
                                </div>

                                <div>
                                    <?php if($row->is_enabled): ?>
                                        <span class="badge badge-success"><i class="fa fa-fw fa-check"></i> <?= l('global.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= l('global.disabled') ?></span>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>

    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('item_extra.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('item_extra.no_data') ?></h2>
                    <p class="text-muted"><?= l('item_extra.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item',
    'resource_id' => 'item_id',
    'has_dynamic_resource_name' => true,
    'path' => 'item/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_extra',
    'resource_id' => 'item_extra_id',
    'has_dynamic_resource_name' => true,
    'path' => 'item-extra/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_option',
    'resource_id' => 'item_option_id',
    'has_dynamic_resource_name' => true,
    'path' => 'item-option/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_variant',
    'resource_id' => 'item_variant_id',
    'has_dynamic_resource_name' => false,
    'path' => 'item-variant/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'item_duplicate_modal', 'resource_id' => 'item_id', 'path' => 'item/duplicate']), 'modals'); ?>
