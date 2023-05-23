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
            <li class="active" aria-current="page"><?= l('item_create.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h4 text-truncate"><?= l('item_create.header') ?></h1>
    <p></p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <label for="url"><i class="fa fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('item.input.url') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><?= $data->store->full_url . $data->menu->url . '/' . $data->category->url . '/' ?></span>
                </div>
                <input type="text" id="url" name="url" class="form-control" value="<?= $data->values['url'] ?>" placeholder="<?= l('item.input.url_placeholder') ?>" />
            </div>
            <small class="form-text text-muted"><?= l('item.input.url_help') ?></small>
        </div>

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('item.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->values['name'] ?>" placeholder="<?= l('item.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="description"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('item.input.description') ?></label>
            <input type="text" id="description" name="description" class="form-control" value="<?= $data->values['description'] ?>" />
            <small class="form-text text-muted"><?= l('item.input.description_help') ?></small>
        </div>

        <div class="form-group">
            <label for="image"><i class="fa fa-fw fa-sm fa-image text-muted mr-1"></i> <?= l('item.input.image') ?></label>
            <input id="image" type="file" name="image" accept="<?= \Altum\Uploads::get_whitelisted_file_extensions_accept('item_images') ?>" class="form-control-file altum-file-input" />
            <small class="form-text text-muted"><?= sprintf(l('global.accessibility.whitelisted_file_extensions'), \Altum\Uploads::get_whitelisted_file_extensions_accept('item_images')) . ' ' . sprintf(l('global.accessibility.file_size_limit'), settings()->stores->item_image_size_limit) ?></small>
        </div>

        <label for="price"><i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= l('item.input.price') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <input type="number" id="price" name="price" class="form-control" value="<?= $data->values['price'] ?? 1 ?>" step="any" required="required" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= $data->store->currency ?></span>
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.create') ?></button>
    </form>

</div>
