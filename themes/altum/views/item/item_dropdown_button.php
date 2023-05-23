<?php defined('ALTUMCODE') || die() ?>

<div class="dropdown">
    <button type="button" class="btn btn-link text-secondary dropdown-toggle dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport">
        <i class="fa fa-fw fa-ellipsis-v"></i>
    </button>

    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="<?= url('store-redirect?item_id=' . $data->id) ?>" target="_blank" rel="noreferrer"><i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('item.external_url') ?></a>
        <a class="dropdown-item" href="<?= url('statistics?item_id=' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('statistics.menu') ?></a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="<?= url('item/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-burn mr-2"></i> <?= l('item.menu') ?></a>
        <a href="#" data-toggle="modal" data-target="#item_duplicate_modal" data-item-id="<?= $data->id ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-copy mr-2"></i> <?= l('global.duplicate') ?></a>
        <a class="dropdown-item" href="<?= url('item-update/' . $data->id) ?>"><i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?></a>
        <a href="#" data-toggle="modal" data-target="#item_delete_modal" data-item-id="<?= $data->id ?>" data-resource-name="<?= $data->resource_name ?>" class="dropdown-item"><i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?></a>
    </div>
</div>
