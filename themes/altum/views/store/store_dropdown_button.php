<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="<?= url('store-qr/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-qrcode mr-2"></i> <?= l('store_qr.menu') ?></a>
        <a class="dropdown-item" href="<?= url('store-redirect?store_id=' . $data->id) ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('store.external_url') ?></a>
        <a class="dropdown-item" href="<?= url('orders/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-bell mr-2"></i> <?= l('orders.menu') ?></a>
        <a class="dropdown-item" href="<?= url('statistics?store_id=' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('statistics.menu') ?></a>
        <a class="dropdown-item" href="<?= url('orders-statistics?store_id=' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-coins mr-2"></i> <?= l('orders_statistics.menu') ?></a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= url('store/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-store mr-2"></i> <?= l('store.menu') ?></a>
        <a href="#" data-toggle="modal" data-target="#store_duplicate_modal" data-store-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-copy mr-2"></i> <?= l('global.duplicate') ?></a>
        <a class="dropdown-item" href="<?= url('store-update/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
        <a href="#" data-toggle="modal" data-target="#store_delete_modal" data-store-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>
