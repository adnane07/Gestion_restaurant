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
                <?= l('item_option.breadcrumb') ?><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('item_option_update.breadcrumb') ?></li>
        </ol>
    </nav>


    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('item_option_update.header'), $data->item_option->name) ?></h1>
        <?= include_view(THEME_PATH . 'views/item-option/item_option_dropdown_button.php', ['id' => $data->item_option->item_option_id, 'resource_name' => $data->item_option->name]) ?>
    </div>
    <p></p>

    <form action="" method="post" role="form" enctype="multipart/form-data">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('item_option.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->item_option->name ?>" placeholder="<?= l('item_option.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="form-group">
            <label for="options"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('item_option.input.options') ?></label>
            <input type="text" id="options" name="options" class="form-control" value="<?= implode(',', $data->item_option->options) ?>" />
            <small class="form-text text-muted"><?= l('item_option.input.options_help') ?></small>
        </div>

        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.update') ?></button>
    </form>

</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'item_option',
    'resource_id' => 'item_option_id',
    'has_dynamic_resource_name' => true,
    'path' => 'item-option/delete'
]), 'modals'); ?>
