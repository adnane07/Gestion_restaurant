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
            <li class="active" aria-current="page"><?= l('menu.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('menu.header'), $data->menu->name) ?></h1>

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
                        data-clipboard-text="<?= $data->store->full_url . $data->menu->url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>

            <?= include_view(THEME_PATH . 'views/menu/menu_dropdown_button.php', ['id' => $data->menu->menu_id, 'resource_name' => $data->menu->name]) ?>
        </div>
    </div>

    <p>
        <a href="<?= $data->store->full_url . $data->menu->url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->store->full_url . $data->menu->url ?>
        </a>
    </p>

    <div class="d-flex align-items-center mb-3">
        <h2 class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('category.categories') ?></h2>

        <div class="flex-fill">
            <hr class="border-gray-100" />
        </div>

        <div class="ml-3">
            <?php if($this->user->plan_settings->categories_limit != -1 && $data->total_categories >= $this->user->plan_settings->categories_limit): ?>
                <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-sm btn-primary disabled">
                    <i class="fa fa-fw fa-sm fa-plus"></i> <?= l('category.create') ?>
                </button>
            <?php else: ?>
                <a href="<?= url('category-create/' . $data->menu->menu_id) ?>" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('category.create') ?></a>
            <?php endif ?>
        </div>
    </div>

    <?php if(count($data->categories)): ?>
        <div class="row" data-blocks>

            <?php foreach($data->categories as $row): ?>
                <div data-draggable data-category-id="<?= $row->category_id ?>" class="col-12 col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h4 mb-0">
                                    <a href="<?= url('category/' . $row->category_id) ?>"><?= $row->name ?></a>
                                </h3>

                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-link text-secondary draggable">
                                        <i class="fa fa-fw fa-expand-arrows-alt"></i>
                                    </button>

                                    <?= include_view(THEME_PATH . 'views/category/category_dropdown_button.php', ['id' => $row->category_id, 'resource_name' => $row->name]) ?>
                                </div>
                            </div>

                            <p class="m-0">
                                <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                    <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('category.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                </small>
                            </p>
                        </div>

                        <div class="card-footer bg-gray-50 border-0">
                            <div class="d-flex flex-lg-row justify-content-lg-between">
                                <div>
                                    <i class="fa fa-fw fa-sm fa-chart-pie text-muted mr-1"></i> <a href="<?= url('statistics?category_id=' . $row->category_id) ?>"><?= sprintf(l('category.pageviews'), nr($row->pageviews)) ?></a>
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

        <div class="mt-3"><?= $data->pagination ?></div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('menu.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('menu.no_data') ?></h2>
                    <p class="text-muted"><?= l('menu.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php include_view(THEME_PATH . 'views/partials/js_sortable_blocks.php', ['id_type' => 'category', 'store' => $data->store]) ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'category',
    'resource_id' => 'category_id',
    'has_dynamic_resource_name' => true,
    'path' => 'category/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'menu',
    'resource_id' => 'menu_id',
    'has_dynamic_resource_name' => true,
    'path' => 'menu/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'category_duplicate_modal', 'resource_id' => 'category_id', 'path' => 'category/duplicate']), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'menu_duplicate_modal', 'resource_id' => 'menu_id', 'path' => 'menu/duplicate']), 'modals'); ?>
