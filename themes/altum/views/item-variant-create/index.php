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
            <li class="active" aria-current="page"><?= l('item_variant_create.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h4 text-truncate"><?= l('item_variant_create.header') ?></h1>
    <p></p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <?php foreach($data->item_options as $row): ?>
        <div class="form-group">
            <label for="item_option_id_<?= $row->item_option_id ?>"><?= $row->name ?></label>
            <select id="item_option_id_<?= $row->item_option_id ?>" name="item_options_ids[<?= $row->item_option_id ?>]" class="form-control">
                <?php foreach($row->options as $key => $value): ?>
                    <option value="<?= $key ?>"><?= $value ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <?php endforeach ?>

        <label for="price"><i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= l('item.input.price') ?></label>
        <div class="mb-3">
            <div class="input-group">
                <input type="number" id="price" name="price" class="form-control" value="1" step="any" required="required" />
                <div class="input-group-append">
                    <span class="input-group-text"><?= $data->store->currency ?></span>
                </div>
            </div>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.create') ?></button>
    </form>

</div>
