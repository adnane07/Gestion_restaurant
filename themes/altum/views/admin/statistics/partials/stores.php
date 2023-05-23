<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-store fa-xs text-muted"></i> <?= l('admin_statistics.stores.stores.header') ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['stores']) ?></span> <?= l('admin_statistics.stores.stores.chart') ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="stores"></canvas>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-list fa-xs text-muted"></i> <?= l('admin_statistics.stores.menus.header') ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['menus']) ?></span> <?= l('admin_statistics.stores.menus.chart') ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="menus"></canvas>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-shopping-bag fa-xs text-muted"></i> <?= l('admin_statistics.stores.categories.header') ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['categories']) ?></span> <?= l('admin_statistics.stores.categories.chart') ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="categories"></canvas>
        </div>
    </div>
</div>

<div class="card mb-5">
    <div class="card-body">
        <h2 class="h4"><i class="fa fa-fw fa-burn fa-xs text-muted"></i> <?= l('admin_statistics.stores.items.header') ?></h2>
        <div class="d-flex flex-column flex-xl-row">
            <div class="mb-2 mb-xl-0 mr-4">
                <span class="font-weight-bold"><?= nr($data->total['items']) ?></span> <?= l('admin_statistics.stores.items.chart') ?>
            </div>
        </div>

        <div class="chart-container">
            <canvas id="items"></canvas>
        </div>
    </div>
</div>

<?php $html = ob_get_clean() ?>

<?php ob_start() ?>
<script>
    'use strict';

    let color = css.getPropertyValue('--primary');
    let color_gradient = null;

    /* Display chart */
    let stores_chart = document.getElementById('stores').getContext('2d');
    color_gradient = stores_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(stores_chart, {
        type: 'line',
        data: {
            labels: <?= $data->stores_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_statistics.stores.stores.chart')) ?>,
                    data: <?= $data->stores_chart['stores'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });

    let menus_chart = document.getElementById('menus').getContext('2d');
    color_gradient = menus_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)');

    new Chart(menus_chart, {
        type: 'line',
        data: {
            labels: <?= $data->menus_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_statistics.stores.menus.chart')) ?>,
                    data: <?= $data->menus_chart['menus'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });

    let categories_chart = document.getElementById('categories').getContext('2d');
    color_gradient = categories_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)')

    new Chart(categories_chart, {
        type: 'line',
        data: {
            labels: <?= $data->categories_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_statistics.stores.categories.chart')) ?>,
                    data: <?= $data->categories_chart['categories'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });

    let items_chart = document.getElementById('items').getContext('2d');
    color_gradient = items_chart.createLinearGradient(0, 0, 0, 250);
    color_gradient.addColorStop(0, 'rgba(63, 136, 253, .1)');
    color_gradient.addColorStop(1, 'rgba(63, 136, 253, 0.025)')

    new Chart(items_chart, {
        type: 'line',
        data: {
            labels: <?= $data->items_chart['labels'] ?>,
            datasets: [
                {
                    label: <?= json_encode(l('admin_statistics.stores.items.chart')) ?>,
                    data: <?= $data->items_chart['items'] ?? '[]' ?>,
                    backgroundColor: color_gradient,
                    borderColor: color,
                    fill: true
                }
            ]
        },
        options: chart_options
    });
</script>
<?php $javascript = ob_get_clean() ?>

<?php return (object) ['html' => $html, 'javascript' => $javascript] ?>
