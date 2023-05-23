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
            <li>
                <a href="<?= url('item/' . $data->item->item_id) ?>"><?= l('item.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <?= l('item_extra.breadcrumb') ?><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('item_extra_update.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('item_extra_update.header'), $data->item_extra->name) ?></h1>
        <?= include_view(THEME_PATH . 'views/item-extra/item_extra_dropdown_button.php', ['id' => $data->item_extra->item_extra_id, 'resource_name' => $data->item_extra->name]) ?>
    </div>
    <p></p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('item_extra.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->item_extra->name ?>" placeholder="<?= l('item_extra.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="description"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('item_extra.input.description') ?></label>
            <input type="text" id="description" name="description" class="form-control" value="<?= $data->item_extra->description ?>" />
            <small class="form-text text-muted"><?= l('item_extra.input.description_help') ?></small>
        </div>

        <label for="price"><i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= l('item_extra.input.price') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <input type="number" id="price" name="price" class="form-control" value="<?= $data->item_extra->price ?>" step="any" required="required" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= $data->store->currency ?></span>
                </div>
            </div>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= $data->item_extra->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('item_extra.input.is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('item_extra.input.is_enabled_help') ?></small>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </form>

</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_extra',
    'resource_id' => 'item_extra_id',
    'has_dynamic_resource_name' => true,
    'path' => 'item-extra/delete'
]), 'modals'); ?>
