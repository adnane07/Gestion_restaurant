<?php defined('ALTUMCODE') || die() ?>

<nav aria-label="breadcrumb">
    <ol class="custom-breadcrumbs small">
        <li>
            <a href="<?= url('admin/users') ?>"><?= l('admin_users.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
        </li>
        <li class="active" aria-current="page"><?= l('admin_user_update.breadcrumb') ?></li>
    </ol>
</nav>

<div class="d-flex justify-content-between mb-4">
    <h1 class="h3 mb-0 text-truncate"><i class="fa fa-fw fa-xs fa-user text-primary-900 mr-2"></i> <?= l('admin_user_update.header') ?></h1>

    <?= include_view(THEME_PATH . 'views/admin/users/admin_user_dropdown_button.php', ['id' => $data->user->user_id, 'resource_name' => $data->user->name]) ?>
</div>

<?= \Altum\Alerts::output_alerts() ?>

<?php //ALTUMCODE:DEMO if(DEMO) {$data->user->email = 'hidden@demo.com'; $data->user->name = $data->user->ip = 'hidden on demo';} ?>

<div class="card <?= \Altum\Alerts::has_field_errors() ? 'border-danger' : null ?>">
    <div class="card-body">

        <form action="" method="post" role="form" enctype="multipart/form-data">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

            <div class="form-group">
                <label for="user_id"><?= l('admin_users.main.user_id') ?></label>
                <input type="text" id="user_id" name="user_id" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('user_id') ? 'is-invalid' : null ?>" value="<?= $data->user->user_id ?>" disabled="disabled" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="name"><?= l('admin_users.main.name') ?></label>
                <input id="name" type="text" name="name" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('name') ? 'is-invalid' : null ?>" value="<?= $data->user->name ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('name') ?>
            </div>

            <div class="form-group">
                <label for="email"><?= l('admin_users.main.email') ?></label>
                <input id="email" type="email" name="email" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('email') ? 'is-invalid' : null ?>" value="<?= $data->user->email ?>" required="required" />
                <?= \Altum\Alerts::output_field_error('email') ?>
            </div>

            <div class="form-group">
                <label for="status"><?= l('admin_users.main.status') ?></label>
                <select id="status" name="status" class="form-control form-control-lg">
                    <option value="2" <?= $data->user->status == 2 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_disabled') ?></option>
                    <option value="1" <?= $data->user->status == 1 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_active') ?></option>
                    <option value="0" <?= $data->user->status == 0 ? 'selected="selected"' : null ?>><?= l('admin_users.main.status_unconfirmed') ?></option>
                </select>
            </div>

            <div class="form-group">
                <label for="type"><?= l('admin_users.main.type') ?></label>
                <select id="type" name="type" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->type == 1 ? 'selected="selected"' : null ?>><?= l('admin_users.main.type_admin') ?></option>
                    <option value="0" <?= $data->user->type == 0 ? 'selected="selected"' : null ?>><?= l('admin_users.main.type_user') ?></option>
                </select>
                <small class="form-text text-muted"><?= l('admin_users.main.type_help') ?></small>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= l('admin_user_update.plan.header') ?></h2>

            <div class="form-group">
                <label for="plan_id"><?= l('admin_users.main.plan_id') ?></label>
                <select id="plan_id" name="plan_id" class="form-control form-control-lg">
                    <option value="free" <?= $data->user->plan->plan_id == 'free' ? 'selected="selected"' : null ?>><?= settings()->plan_free->name ?></option>
                    <option value="custom" <?= $data->user->plan->plan_id == 'custom' ? 'selected="selected"' : null ?>><?= settings()->plan_custom->name ?></option>

                    <?php foreach($data->plans as $plan): ?>
                        <option value="<?= $plan->plan_id ?>" <?= $data->user->plan->plan_id == $plan->plan_id ? 'selected="selected"' : null ?>><?= $plan->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="form-group">
                <label for="plan_trial_done"><?= l('admin_users.main.plan_trial_done') ?></label>
                <select id="plan_trial_done" name="plan_trial_done" class="form-control form-control-lg">
                    <option value="1" <?= $data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                    <option value="0" <?= !$data->user->plan_trial_done ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
                </select>
            </div>

            <div id="plan_expiration_date_container" class="form-group">
                <label for="plan_expiration_date"><?= l('admin_users.main.plan_expiration_date') ?></label>
                <input id="plan_expiration_date" type="text" name="plan_expiration_date" class="form-control form-control-lg" autocomplete="off" value="<?= $data->user->plan_expiration_date ?>">
                <div class="invalid-feedback">
                    <?= l('admin_user_update.plan.plan_expiration_date_invalid') ?>
                </div>
            </div>

            <div id="plan_settings" style="display: none">
                <div class="form-group">
                    <label for="stores_limit"><?= l('admin_plans.plan.stores_limit') ?></label>
                    <input type="number" id="stores_limit" name="stores_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->stores_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="menus_limit"><?= l('admin_plans.plan.menus_limit') ?></label>
                    <input type="number" id="menus_limit" name="menus_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->menus_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="categories_limit"><?= l('admin_plans.plan.categories_limit') ?></label>
                    <input type="number" id="categories_limit" name="categories_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->categories_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="items_limit"><?= l('admin_plans.plan.items_limit') ?></label>
                    <input type="number" id="items_limit" name="items_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->items_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <div class="form-group">
                    <label for="domains_limit"><?= l('admin_plans.plan.domains_limit') ?></label>
                    <input type="number" id="domains_limit" name="domains_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->domains_limit ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                </div>

                <?php if(\Altum\Plugin::is_active('teams')): ?>
                    <div class="form-group">
                        <label for="teams_limit"><?= l('admin_plans.plan.teams_limit') ?></label>
                        <input type="number" id="teams_limit" name="teams_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->teams_limit ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                    </div>

                    <div class="form-group">
                        <label for="team_members_limit"><?= l('admin_plans.plan.team_members_limit') ?></label>
                        <input type="number" id="team_members_limit" name="team_members_limit" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->team_members_limit ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.unlimited') ?></small>
                    </div>
                <?php endif ?>

                <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
                    <div class="form-group">
                        <label for="affiliate_commission_percentage"><?= l('admin_plans.plan.affiliate_commission_percentage') ?></label>
                        <input type="number" id="affiliate_commission_percentage" name="affiliate_commission_percentage" min="0" max="100" class="form-control form-control-lg" value="<?= $data->user->plan->settings->affiliate_commission_percentage ?>" />
                        <small class="form-text text-muted"><?= l('admin_plans.plan.affiliate_commission_percentage_help') ?></small>
                    </div>
                <?php endif ?>

                <div class="form-group">
                    <label for="statistics_retention"><?= l('admin_plans.plan.statistics_retention') ?></label>
                    <input type="number" id="statistics_retention" name="statistics_retention" min="-1" class="form-control form-control-lg" value="<?= $data->user->plan->settings->statistics_retention ?>" />
                    <small class="form-text text-muted"><?= l('admin_plans.plan.statistics_retention_help') ?></small>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="ordering_is_enabled" name="ordering_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->ordering_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="ordering_is_enabled"><?= l('admin_plans.plan.ordering_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.ordering_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="additional_domains_is_enabled" name="additional_domains_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->additional_domains_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="additional_domains_is_enabled"><?= l('admin_plans.plan.additional_domains_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.additional_domains_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="no_ads" name="no_ads" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->no_ads ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="no_ads"><?= l('admin_plans.plan.no_ads') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.no_ads_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="analytics_is_enabled" name="analytics_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->analytics_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="analytics_is_enabled"><?= l('admin_plans.plan.analytics_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.analytics_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="qr_is_enabled" name="qr_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->qr_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="qr_is_enabled"><?= l('admin_plans.plan.qr_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.qr_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="removable_branding_is_enabled" name="removable_branding_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->removable_branding_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="removable_branding_is_enabled"><?= l('admin_plans.plan.removable_branding_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.removable_branding_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="custom_url_is_enabled" name="custom_url_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_url_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_url_is_enabled"><?= l('admin_plans.plan.custom_url_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_url_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="password_protection_is_enabled" name="password_protection_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->password_protection_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="password_protection_is_enabled"><?= l('admin_plans.plan.password_protection_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.password_protection_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="search_engine_block_is_enabled" name="search_engine_block_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->search_engine_block_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="search_engine_block_is_enabled"><?= l('admin_plans.plan.search_engine_block_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.search_engine_block_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="custom_css_is_enabled" name="custom_css_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_css_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_css_is_enabled"><?= l('admin_plans.plan.custom_css_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_css_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="custom_js_is_enabled" name="custom_js_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->custom_js_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="custom_js_is_enabled"><?= l('admin_plans.plan.custom_js_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.custom_js_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="email_reports_is_enabled" name="email_reports_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->email_reports_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="email_reports_is_enabled"><?= l('admin_plans.plan.email_reports_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.email_reports_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="online_payments_is_enabled" name="online_payments_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->online_payments_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="online_payments_is_enabled"><?= l('admin_plans.plan.online_payments_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.online_payments_is_enabled_help') ?></small></div>
                </div>

                <div class="custom-control custom-switch my-3">
                    <input id="api_is_enabled" name="api_is_enabled" type="checkbox" class="custom-control-input" <?= $data->user->plan->settings->api_is_enabled ? 'checked="checked"' : null ?>>
                    <label class="custom-control-label" for="api_is_enabled"><?= l('admin_plans.plan.api_is_enabled') ?></label>
                    <div><small class="form-text text-muted"><?= l('admin_plans.plan.api_is_enabled_help') ?></small></div>
                </div>
            </div>

            <div class="mt-5"></div>

            <h2 class="h4"><?= l('admin_user_update.change_password.header') ?></h2>
            <p class="text-muted"><?= l('admin_user_update.change_password.subheader') ?></p>

            <div class="form-group">
                <label for="new_password"><?= l('admin_user_update.change_password.new_password') ?></label>
                <input id="new_password" type="password" name="new_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <div class="form-group">
                <label for="repeat_password"><?= l('admin_user_update.change_password.repeat_password') ?></label>
                <input id="repeat_password" type="password" name="repeat_password" class="form-control form-control-lg <?= \Altum\Alerts::has_field_errors('new_password') ? 'is-invalid' : null ?>" />
                <?= \Altum\Alerts::output_field_error('new_password') ?>
            </div>

            <button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
        </form>
    </div>
</div>

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

    let check_plan_id = () => {
        let selected_plan_id = document.querySelector('[name="plan_id"]').value;

        if(selected_plan_id == 'free') {
            document.querySelector('#plan_expiration_date_container').style.display = 'none';
        } else {
            document.querySelector('#plan_expiration_date_container').style.display = 'block';
        }

        if(selected_plan_id == 'custom') {
            document.querySelector('#plan_settings').style.display = 'block';
        } else {
            document.querySelector('#plan_settings').style.display = 'none';
        }
    };

    check_plan_id();

    /* Dont show expiration date when the chosen plan is the free one */
    document.querySelector('[name="plan_id"]').addEventListener('change', check_plan_id);

    /* Check for expiration date to show a warning if expired */
    let check_plan_expiration_date = () => {
        let plan_expiration_date = document.querySelector('[name="plan_expiration_date"]');

        let plan_expiration_date_object = new Date(plan_expiration_date.value);
        let today_date_object = new Date();

        if(plan_expiration_date_object < today_date_object) {
            plan_expiration_date.classList.add('is-invalid');
        } else {
            plan_expiration_date.classList.remove('is-invalid');
        }
    };

    check_plan_expiration_date();
    document.querySelector('[name="plan_expiration_date"]').addEventListener('change', check_plan_expiration_date);

    /* Daterangepicker */
    $('[name="plan_expiration_date"]').daterangepicker({
        startDate: <?= json_encode($data->user->plan_expiration_date) ?>,
        minDate: new Date(),
        alwaysShowCalendars: true,
        singleCalendar: true,
        singleDatePicker: true,
        locale: <?= json_encode(require APP_PATH . 'includes/daterangepicker_translations.php') ?>,
    }, (start, end, label) => {
        check_plan_expiration_date()
    });

</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_url.php', [
    'name' => 'user',
    'resource_id' => 'user_id',
    'has_dynamic_resource_name' => true,
    'path' => 'admin/users/delete/'
]), 'modals'); ?>
<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/admin/users/user_login_modal.php'), 'modals'); ?>
