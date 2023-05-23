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
            <li class="active" aria-current="page"><?= l('menu_update.breadcrumb') ?></li>
        </ol>
    </nav>

   <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('menu_update.header'), $data->menu->name) ?></h1>

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

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <label for="url"><i class="fa fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('menu.input.url') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= $data->store->full_url ?></span>
                </div>
                <input type="text" id="url" name="url" class="form-control" value="<?= $data->menu->url ?>" placeholder="<?= l('menu.input.url_placeholder') ?>" />
            </div>
            <small class="form-text text-muted"><?= l('menu.input.url_help') ?></small>
        </div>

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('menu.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->menu->name ?>" placeholder="<?= l('menu.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="description"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('menu.input.description') ?></label>
            <input type="text" id="description" name="description" class="form-control" value="<?= $data->menu->description ?>" />
            <small class="form-text text-muted"><?= l('menu.input.description_help') ?></small>
        </div>

        <div class="form-group">
            <label for="image"><i class="fa fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('menu.input.image') ?></label>
            <?php if(!empty($data->menu->image)): ?>
                <div class="row">
                    <div class="m-1 col-6 col-xl-3">
                        <img src="<?= UPLOADS_FULL_URL . 'menu_images/' . $data->menu->image ?>" class="img-fluid" loading="lazy" />
                    </div>
                </div>
                <div class="custom-control custom-checkbox my-2">
                    <input id="image_remove" name="image_remove" type="checkbox" class="custom-control-input" onchange="this.checked ? document.querySelector('#image').classList.add('d-none') : document.querySelector('#image').classList.remove('d-none')">
                    <label class="custom-control-label" for="image_remove">
                        <span class="text-muted"><?= l('global.delete_file') ?></span>
                    </label>
                </div>
            <?php endif ?>
            <input id="image" type="file" name="image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('menu_images') ?>" class="form-control-file altum-file-input" />
            <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('menu_images')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->stores->menu_image_size_limit) ?></small>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= $data->menu->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('menu.input.is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('menu.input.is_enabled_help') ?></small>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </form>

</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>


<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'menu',
    'resource_id' => 'menu_id',
    'has_dynamic_resource_name' => true,
    'path' => 'menu/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'menu_duplicate_modal', 'resource_id' => 'menu_id', 'path' => 'menu/duplicate']), 'modals'); ?>
