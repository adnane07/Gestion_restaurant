<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="mb-3 d-flex justify-content-between">
        <div>
            <h1 class="h4 text-truncate"><?= l('dashboard.header') ?></h1>
        </div>
    </div>

    <div class="row justify-content-between mb-5">
        <div class="col-12 col-lg mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body d-flex">

                    <div>
                        <div class="card border-0 bg-primary-100 text-gray-800 mr-3">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <i class="fa fa-fw fa-store fa-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-muted"><?= l('dashboard.stores_total') ?></span>
                        <div class="card-title h4 m-0"><?= nr($data->stores_statistics->stores) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body d-flex">

                    <div>
                        <div class="card border-0 bg-primary-100 text-gray-800 mr-3">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <i class="fa fa-fw fa-bell fa-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-muted"><?= l('dashboard.orders_total') ?></span>
                        <div class="card-title h4 m-0"><?= nr($data->stores_statistics->orders) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg mb-3 mb-xl-0">
            <div class="card h-100">
                <div class="card-body d-flex">

                    <div>
                        <div class="card border-0 bg-primary-100 text-gray-800 mr-3">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <i class="fa fa-fw fa-chart-bar fa-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <span class="text-muted"><?= l('dashboard.stores_pageviews') ?></span>
                        <div class="card-title h4 m-0"><?= nr($data->stores_statistics->pageviews) ?> </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center mb-3">
        <h2 class="h6 text-uppercase text-muted mb-0 mr-3"><?= l('store.stores') ?></h2>

        <div class="flex-fill">
            <hr class="border-gray-100" />
        </div>

        <div class="ml-3">
            <?php if($this->user->plan_settings->stores_limit != -1 && $data->total_stores >= $this->user->plan_settings->stores_limit): ?>
                <button type="button" data-toggle="tooltip" title="<?= l('global.info_message.plan_feature_limit') ?>" class="btn btn-sm btn-primary disabled">
                    <i class="fa fa-fw fa-sm fa-plus"></i> <?= l('store.create') ?>
                </button>
            <?php else: ?>
                <a href="<?= url('store-create') ?>" class="btn btn-sm btn-primary"><i class="fa fa-fw fa-sm fa-plus"></i> <?= l('store.create') ?></a>
            <?php endif ?>
        </div>
       
       

        <div class="ml-3">
            <div class="dropdown">
                <button type="button" class="btn btn-sm <?= count($data->filters->get) ? 'btn-outline-primary' : 'btn-outline-secondary' ?> filters-button dropdown-toggle-simple" data-toggle="dropdown" data-boundary="viewport"><i class="fa fa-fw fa-sm fa-filter"></i></button>

                <div class="dropdown-menu dropdown-menu-right filters-dropdown">
                    <div class="dropdown-header d-flex justify-content-between">
                        <span class="h6 m-0"><?= l('global.filters.header') ?></span>

                        <?php if(count($data->filters->get)): ?>
                            <a href="<?= url('dashboard') ?>" class="text-muted"><?= l('global.filters.reset') ?></a>
                        <?php endif ?>
                    </div>

                    <div class="dropdown-divider"></div>

                    <form action="" method="get" role="form">
                        <div class="form-group px-4">
                            <label for="filters_search" class="small"><?= l('global.filters.search') ?></label>
                            <input type="search" name="search" id="filters_search" class="form-control form-control-sm" value="<?= $data->filters->search ?>" />
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_search_by" class="small"><?= l('global.filters.search_by') ?></label>
                            <select name="search_by" id="filters_search_by" class="form-control form-control-sm">
                                <option value="name" <?= $data->filters->search_by == 'name' ? 'selected="selected"' : null ?>><?= l('dashboard.filters.name') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_is_enabled" class="small"><?= l('global.filters.status') ?></label>
                            <select name="is_enabled" id="filters_is_enabled" class="form-control form-control-sm">
                                <option value=""><?= l('global.filters.all') ?></option>
                                <option value="1" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '1' ? 'selected="selected"' : null ?>><?= l('global.active') ?></option>
                                <option value="0" <?= isset($data->filters->filters['is_enabled']) && $data->filters->filters['is_enabled'] == '0' ? 'selected="selected"' : null ?>><?= l('global.disabled') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_by" class="small"><?= l('global.filters.order_by') ?></label>
                            <select name="order_by" id="filters_order_by" class="form-control form-control-sm">
                                <option value="datetime" <?= $data->filters->order_by == 'datetime' ? 'selected="selected"' : null ?>><?= l('global.filters.order_by_datetime') ?></option>
                                <option value="pageviews" <?= $data->filters->order_by == 'pageviews' ? 'selected="selected"' : null ?>><?= l('dashboard.filters.pageviews') ?></option>
                                <option value="orders" <?= $data->filters->order_by == 'orders' ? 'selected="selected"' : null ?>><?= l('dashboard.filters.orders') ?></option>
                                <option value="name" <?= $data->filters->order_by == 'name' ? 'selected="selected"' : null ?>><?= l('dashboard.filters.name') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_order_type" class="small"><?= l('global.filters.order_type') ?></label>
                            <select name="order_type" id="filters_order_type" class="form-control form-control-sm">
                                <option value="ASC" <?= $data->filters->order_type == 'ASC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_asc') ?></option>
                                <option value="DESC" <?= $data->filters->order_type == 'DESC' ? 'selected="selected"' : null ?>><?= l('global.filters.order_type_desc') ?></option>
                            </select>
                        </div>

                        <div class="form-group px-4">
                            <label for="filters_results_per_page" class="small"><?= l('global.filters.results_per_page') ?></label>
                            <select name="results_per_page" id="filters_results_per_page" class="form-control form-control-sm">
                                <?php foreach($data->filters->allowed_results_per_page as $key): ?>
                                    <option value="<?= $key ?>" <?= $data->filters->results_per_page == $key ? 'selected="selected"' : null ?>><?= $key ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group px-4 mt-4">
                            <button type="submit" name="submit" class="btn btn-sm btn-primary btn-block"><?= l('global.submit') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        


        
    </div>

    <?php if(count($data->stores)): ?>
        <div class="row">

            <?php foreach($data->stores as $row): ?>
                <div class="col-12 col-md-6 col-xl-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="h4 mb-0">
                                    <a href="<?= url('store/' . $row->store_id) ?>"><?= $row->name ?></a>
                                </h3>

                                <?= include_view(THEME_PATH . 'views/store/store_dropdown_button.php', ['id' => $row->store_id, 'resource_name' => $row->name]) ?>
                            </div>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <a href="<?= $row->full_url ?>" target="_blank" rel="noreferrer"><?= $row->full_url ?></a>
                                </small>
                            </p>

                            <?php if($this->user->plan_settings->ordering_is_enabled): ?>
                                <p class="m-0">
                                    <small class="text-muted">
                                        <i class="fa fa-fw fa-sm fa-bell text-muted mr-1"></i> <a href="<?= url('orders/' . $row->store_id) ?>"><?= sprintf(l('store.orders'), nr($row->orders)) ?></a>

                                        <?php
                                        $conversion_rate = $row->orders ? ((int) $row->orders / (int) $row->pageviews) * 100 : null;
                                        ?>
                                        <?php if($conversion_rate): ?>
                                            <span class="text-muted">
                                        - <?= sprintf(l('store.conversion_rate'), nr($conversion_rate, 2) . '%') ?>
                                    </span>
                                        <?php endif ?>
                                    </small>
                                </p>
                            <?php endif ?>

                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= sprintf(l('store.currency'), $row->currency) ?>
                                </small>
                            </p>
                            <p class="m-0">
                                <small class="text-muted">
                                    <i class="fa fa-fw fa-sm fa-clock text-muted mr-1"></i> <?= sprintf(l('store.timezone'), $row->timezone) ?>
                                </small>
                            </p>
                            <p class="m-0">
                                <small class="text-muted" data-toggle="tooltip" title="<?= \Altum\Date::get($row->datetime, 1) ?>">
                                    <i class="fa fa-fw fa-sm fa-calendar text-muted mr-1"></i> <?= sprintf(l('store.datetime'), \Altum\Date::get($row->datetime, 2)) ?>
                                </small>
                            </p>
                        </div>

                        <div class="card-footer bg-gray-50 border-0">
                            <div class="d-flex flex-lg-row justify-content-lg-between">
                                <div>
                                    <i class="fa fa-fw fa-sm fa-chart-pie text-muted mr-1"></i> <a href="<?= url('statistics?store_id=' . $row->store_id) ?>"><?= sprintf(l('store.pageviews'), nr($row->pageviews)) ?></a>
                                </div>

                                <div>
                                    <?php if($row->is_enabled): ?>
                                        <span class="badge badge-success"><i class="fa fa-fw fa-check"></i> <?= l('global.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-warning"><i class="fa fa-fw fa-eye-slash"></i> <?= l('global.disabled') ?></span>
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
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('dashboard.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('dashboard.no_data') ?></h2>
                    <p class="text-muted"><?= l('dashboard.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'store',
    'resource_id' => 'store_id',
    'has_dynamic_resource_name' => true,
    'path' => 'store/delete'
]), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/duplicate_modal.php', ['modal_id' => 'store_duplicate_modal', 'resource_id' => 'store_id', 'path' => 'store/duplicate']), 'modals'); ?>
