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
            <li class="active" aria-current="page"><?= l('orders.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('orders.header'), $data->store->name) ?></h1>

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
                        data-clipboard-text="<?= $data->store->full_url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>
        </div>
    </div>

    <p>
        <a href="<?= $data->store->full_url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->store->full_url ?>
        </a>
    </p>
  
    <?php if(count($data->orders)): ?>
        <div class="row">

            <?php foreach($data->orders as $row): ?>
                <div class="col-12 col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h4 mb-0">
                                    <a href="<?= url('order/' . $row->order_id) ?>"><?= sprintf(l('orders.view'), $row->order_number) ?></a>
                                </h3>

                                <?= ''// include_view(THEME_PATH . 'views/menu/menu_dropdown_button.php', ['id' => $row->menu_id]) ?>
                            </div>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-list-ol text-muted mr-1"></i> <?= sprintf(l('orders.ordered_items'), nr($row->ordered_items)) ?>
                                </small>
                            </p>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-dollar-sign text-muted mr-1"></i> <?= sprintf(l('orders.price_currency'), $row->price, $data->store->currency) ?>
                                </small>
                            </p>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-money-check-alt text-muted mr-1"></i> <?= sprintf(l('orders.processor'), l('order.processor_' . $row->processor)) ?>
                                </small>
                            </p>
                           
                            
                            <?php if(in_array($row->processor, ['stripe', 'paypal'])): ?>
                                <p class="m-0">
                                    <small class="text-muted">
                                        <i class="fa fa-fw fa-sm <?= $row->is_paid ? 'fa-check' : 'fa-times' ?> text-muted mr-1"></i> <?= sprintf(l('orders.is_paid'), l('global.' . $row->is_paid ? 'yes' : 'no')) ?>
                                    </small>
                                </p>
                            <?php endif ?>

                            <p class="m-0">
                                <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                    <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('orders.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                </small>
                            </p>
                        </div>

                        <div class="card-footer bg-gray-50 border-0">
                            <div class="d-flex flex-lg-row justify-content-lg-between">
                                <div>
                                    <span class="badge badge-primary"><?= l('order.type_' . $row->type) ?></span>
                                </div>

                                <div>
                                    <?php if($row->status): ?>
                                        <span class="badge badge-success"><i class="fa fa-fw fa-check"></i> <?= l('order.status_complete') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fa fa-fw fa-clock"></i> <?= l('order.status_pending') ?></span>
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
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('orders.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('orders.no_data') ?></h2>
                    <p class="text-muted"><?= l('orders.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>


<?php ob_start() ?>
<script>
    'use strict';

    setInterval(() => {
        location.reload();
    }, 20000);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>


<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'store',
    'resource_id' => 'store_id',
    'has_dynamic_resource_name' => true,
    'path' => 'store/delete'
]), 'modals'); ?>
