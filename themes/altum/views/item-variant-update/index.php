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
                <?= l('item_variant.breadcrumb') ?><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('item_variant_update.breadcrumb') ?></li>
        </ol>
    </nav>


    <div class="d-flex align-items-baseline">
        <h1 class="h4 text-truncate mb-0"><?= l('item_variant_update.header') ?></h1>
        <?= include_view(THEME_PATH . 'views/item-variant/item_variant_dropdown_button.php', ['id' => $data->item_variant->item_variant_id]) ?>
    </div>
    <p></p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <?php foreach($data->item_options as $row): ?>
        <div class="form-group">
            <label for="item_option_id_<?= $row->item_option_id ?>"><?= $row->name ?></label>
            <select id="item_option_id_<?= $row->item_option_id ?>" name="item_options_ids[<?= $row->item_option_id ?>]" class="form-control">
                <?php foreach($row->options as $key => $value): ?>
                    <?php
                    $option = null;

                    foreach($data->item_variant->item_options_ids as $item_option) {
                        if($item_option->item_option_id == $row->item_option_id && $item_option->option == $key) {
                            $option = $key;
                            break;
                        }
                    }
                    ?>

                    <option value="<?= $key ?>" <?= $option == $key ? 'selected="selected"' : null ?>><?= $value ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <?php endforeach ?>

        <label for="price"><i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= l('item.input.price') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <input type="number" id="price" name="price" class="form-control" value="<?= $data->item_variant->price ?>" step="any" required="required" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= $data->store->currency ?></span>
                </div>
            </div>
        </div>

        <div class="custom-control custom-switch my-3">
            <input id="is_enabled" name="is_enabled" type="checkbox" class="custom-control-input" <?= $data->item_variant->is_enabled ? 'checked="checked"' : null?>>
            <label class="custom-control-label" for="is_enabled"><?= l('item_variant.input.is_enabled') ?></label>
            <small class="form-text text-muted"><?= l('item_variant.input.is_enabled_help') ?></small>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </form>

</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_variant',
    'resource_id' => 'item_variant_id',
    'has_dynamic_resource_name' => false,
    'path' => 'item-variant/delete'
]), 'modals'); ?>
