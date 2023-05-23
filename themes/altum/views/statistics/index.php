<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('dashboard') ?>"><?= l('dashboard.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('store/' . $data->{$data->identifier_name}->store_id) ?>"><?= l('store.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <?php if(in_array($data->identifier_name, ['menu', 'category', 'item'])): ?>
                <li>
                    <a href="<?= url('menu/' . $data->{$data->identifier_name}->menu_id) ?>"><?= l('menu.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
                </li>

                <?php if(in_array($data->identifier_name, ['category', 'item'])): ?>
                    <li>
                        <a href="<?= url('category/' . $data->{$data->identifier_name}->category_id) ?>"><?= l('category.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
                    </li>
                <?php endif ?>

                <?php if(in_array($data->identifier_name, ['item'])): ?>
                    <li>
                        <a href="<?= url('item/' . $data->{$data->identifier_name}->item_id) ?>"><?= l('item.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
                    </li>
                <?php endif ?>
            <?php endif ?>
            <li class="active" aria-current="page"><?= l('statistics.breadcrumb') ?></li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('statistics.header'), $data->{$data->identifier_name}->name) ?></h1>

        <div class="d-flex align-items-center col-auto p-0">
            <div data-toggle="tooltip" title="<?= l('global.reset') ?>">
                <button
                        type="button"
                        class="btn btn-link text-secondary"
                        data-toggle="modal"
                        data-target="#statistics_reset_modal"
                        aria-label="<?= l('global.reset') ?>"
                        data-<?= $data->identifier_name ?>-id="<?= $data->{$data->identifier_name}->{$data->identifier_key} ?>"
                        data-start-date="<?= $data->datetime['start_date'] ?>"
                        data-end-date="<?= $data->datetime['end_date'] ?>"
                >
                    <i class="fa fa-fw fa-sm fa-redo"></i>
                </button>
            </div>

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
                        data-clipboard-text="<?= $data->external_url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>

            <button
                    id="daterangepicker"
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    data-min-date="<?= \Altum\Date::get($data->{$data->identifier_name}->datetime, 4) ?>"
                    data-max-date="<?= \Altum\Date::get('', 4) ?>"
            >
                <i class="fa fa-fw fa-calendar mr-1"></i>
                <span>
                    <?php if($data->datetime['start_date'] == $data->datetime['end_date']): ?>
                        <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) ?>
                    <?php else: ?>
                        <?= \Altum\Date::get($data->datetime['start_date'], 2, \Altum\Date::$default_timezone) . ' - ' . \Altum\Date::get($data->datetime['end_date'], 2, \Altum\Date::$default_timezone) ?>
                    <?php endif ?>
                </span>
                <i class="fa fa-fw fa-caret-down ml-1"></i>
            </button>
        </div>
    </div>

    <p>
        <a href="<?= $data->external_url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->external_url ?>
        </a>
    </p>

    <?php if(!$data->has_data): ?>
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-column align-items-center justify-content-center py-3">
                    <img src="<?= ASSETS_FULL_URL . 'images/no_rows.svg' ?>" class="col-10 col-md-7 col-lg-4 mb-3" alt="<?= l('statistics.no_data') ?>" />
                    <h2 class="h4 text-muted"><?= l('statistics.no_data') ?></h2>
                    <p class="text-muted"><?= l('statistics.no_data_help') ?></p>
                </div>
            </div>
        </div>
    <?php else: ?>

        <ul class="account-header-navbar mb-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'overview' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=overview&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-list mr-1"></i>
                    <?= l('statistics.statistics.overview') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'entries' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=entries&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-chart-bar mr-1"></i>
                    <?= l('statistics.statistics.entries') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'country' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=country&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-globe mr-1"></i>
                    <?= l('statistics.statistics.country') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'city_name' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=city_name&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-city mr-1"></i>
                    <?= l('statistics.statistics.city_name') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= in_array($data->type, ['referrer_host', 'referrer_path']) ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=referrer_host&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-random mr-1"></i>
                    <?= l('statistics.statistics.referrer_host') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'device' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=device&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-laptop mr-1"></i>
                    <?= l('statistics.statistics.device') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'os' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=os&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-server mr-1"></i>
                    <?= l('statistics.statistics.os') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'browser' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=browser&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-window-restore mr-1"></i>
                    <?= l('statistics.statistics.browser') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $data->type == 'language' ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=language&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-language mr-1"></i>
                    <?= l('statistics.statistics.language') ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= in_array($data->type, ['utm_source', 'utm_medium', 'utm_campaign']) ? 'active' : null ?>" href="<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=utm_source&start_date=' . $data->datetime['start_date'] . '&end_date=' . $data->datetime['end_date']) ?>">
                    <i class="fa fa-fw fa-sm fa-link mr-1"></i>
                    <?= l('statistics.statistics.utms') ?>
                </a>
            </li>
        </ul>

        <?= $this->views['statistics'] ?>

    <?php endif ?>

    <?php ob_start() ?>
    <link href="<?= ASSETS_FULL_URL . 'css/daterangepicker.min.css' ?>" rel="stylesheet" media="screen,print">
    <?php \Altum\Event::add_content(ob_get_clean(), 'head') ?>

    <?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/moment.min.js' ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/daterangepicker.min.js' ?>"></script>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/moment-timezone-with-data-10-year-range.min.js' ?>"></script>

    <script>
        'use strict';

        moment.tz.setDefault(<?= json_encode($this->user->timezone) ?>);

        /* Daterangepicker */
        $('#daterangepicker').daterangepicker({
            startDate: <?= json_encode($data->datetime['start_date']) ?>,
            endDate: <?= json_encode($data->datetime['end_date']) ?>,
            minDate: $('#daterangepicker').data('min-date'),
            maxDate: $('#daterangepicker').data('max-date'),
            ranges: {
                <?= json_encode(l('global.date.today')) ?>: [moment(), moment()],
                <?= json_encode(l('global.date.yesterday')) ?>: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                <?= json_encode(l('global.date.last_7_days')) ?>: [moment().subtract(6, 'days'), moment()],
                <?= json_encode(l('global.date.last_30_days')) ?>: [moment().subtract(29, 'days'), moment()],
                <?= json_encode(l('global.date.this_month')) ?>: [moment().startOf('month'), moment().endOf('month')],
                <?= json_encode(l('global.date.last_month')) ?>: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                <?= json_encode(l('global.date.all_time')) ?>: [moment($('#daterangepicker').data('min-date')), moment()]
            },
            alwaysShowCalendars: true,
            linkedCalendars: false,
            singleCalendar: true,
            locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
        }, (start, end, label) => {

            /* Redirect */
            redirect(`<?= url('statistics?' . $data->identifier_key . '=' . $data->identifier_value . '&type=' . $data->type) ?>&start_date=${start.format('YYYY-MM-DD')}&end_date=${end.format('YYYY-MM-DD')}`, true);

        });

        <?php if(count($data->pageviews)): ?>
        let css = window.getComputedStyle(document.body)

        /* Pageviews chart */
        let pageviews_chart = document.getElementById('pageviews_chart').getContext('2d');

        let pageviews_color = css.getPropertyValue('--primary');
        let pageviews_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
        pageviews_gradient.addColorStop(0, 'rgba(102, 127, 234, .1)');
        pageviews_gradient.addColorStop(1, 'rgba(102, 127, 234, 0.025)');

        let visitors_color = css.getPropertyValue('--gray-500');
        let visitors_gradient = pageviews_chart.createLinearGradient(0, 0, 0, 250);
        visitors_gradient.addColorStop(0, 'rgba(136, 153, 175, .1)');
        visitors_gradient.addColorStop(1, 'rgba(136, 153, 175, 0.025)');

        /* Display chart */
        new Chart(pageviews_chart, {
            type: 'line',
            data: {
                labels: <?= $data->pageviews_chart['labels'] ?>,
                datasets: [
                    {
                        label: <?= json_encode(l('statistics.pageviews')) ?>,
                        data: <?= $data->pageviews_chart['pageviews'] ?? '[]' ?>,
                        backgroundColor: pageviews_gradient,
                        borderColor: pageviews_color,
                        fill: true
                    },
                    {
                        label: <?= json_encode(l('statistics.visitors')) ?>,
                        data: <?= $data->pageviews_chart['visitors'] ?? '[]' ?>,
                        backgroundColor: visitors_gradient,
                        borderColor: visitors_color,
                        fill: true
                    }
                ]
            },
            options: chart_options
        });

        <?php endif ?>
    </script>
    <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
</div>

<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/statistics_reset_modal.php', ['modal_id' => 'statistics_reset_modal', 'resource_id' => $data->identifier_key, 'path' => 'statistics/reset']), 'modals'); ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'store',
    'resource_id' => 'store_id',
    'has_dynamic_resource_name' => true,
    'path' => 'store/delete'
]), 'modals'); ?>
