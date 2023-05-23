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
            <li class="active" aria-current="page"><?= l('category_update.breadcrumb') ?></li>
        </ol>
    </nav>

   <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('category_update.header'), $data->category->name) ?></h1>

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
                        data-clipboard-text="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>

           <?= include_view(THEME_PATH . 'views/category/category_dropdown_button.php', ['id' => $data->category->category_id, 'resource_name' => $data->category->name]) ?>
       </div>
    </div>

    <p>
        <a href="<?= $data->store->full_url . $data->menu->url . '/' . $data->category->url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->store->full_url . $data->menu->url . '/' . $data->category->url ?>
        </a>
    </p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <label for="url"><i class="fa fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('category.input.url') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= $data->store->full_url . $data->menu->url . '/' ?></span>
                </div>
                <input type="text" id="url" name="url" class="form-control" value="<?= $data->category->url ?>" placeholder="<?= l('category.input.url_placeholder') ?>" />
            </div>
            <small class="form-text text-muted"><?= l('category.input.url_help') ?></small>
        </div>

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('category.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->category->name ?>" placeholder="<?= l('category.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="description"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('category.input.description') ?></label>
            <input type="text" id="description" name="description" class="form-control" value="<?= $data->category->description ?>" />
            <small class="form-text text-muted"><?= l('category.input.description_help') ?></small>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= $data->category->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('category.input.is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('category.input.is_enabled_help') ?></small>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </form>

</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'category',
    'resource_id' => 'category_id',
    'has_dynamic_resource_name' => true,
    'path' => 'category/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'category_duplicate_modal', 'resource_id' => 'category_id', 'path' => 'category/duplicate']), 'modals'); ?>
