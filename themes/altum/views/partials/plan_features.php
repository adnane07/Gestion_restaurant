<?php defined('ALTUMCODE') || die() ?>

<ul class="list-style-none m-0">
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->stores_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->stores_limit ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.stores_limit'), '<strong>' . ($data->plan_settings->stores_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->stores_limit)) . '</strong>') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->menus_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->menus_limit ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.menus_limit'), '<strong>' . ($data->plan_settings->menus_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->menus_limit)) . '</strong>') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->categories_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->categories_limit ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.categories_limit'), '<strong>' . ($data->plan_settings->categories_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->categories_limit)) . '</strong>') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->items_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->items_limit ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.items_limit'), '<strong>' . ($data->plan_settings->items_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->items_limit)) . '</strong>') ?>
        </div>
    </li>

    <?php if(settings()->stores->domains_is_enabled): ?>
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->domains_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->domains_limit ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.domains_limit'), '<strong>' . ($data->plan_settings->domains_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->domains_limit)) . '</strong>') ?>
        </div>
    </li>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('teams')): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->teams_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->teams_limit ? null : 'text-muted' ?>">
                <?= sprintf(l('global.plan_settings.teams_limit'), '<strong>' . ($data->plan_settings->teams_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->teams_limit)) . '</strong>') ?>
            </div>
        </li>

        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->team_members_limit ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->team_members_limit ? null : 'text-muted' ?>">
                <?= sprintf(l('global.plan_settings.team_members_limit'), '<strong>' . ($data->plan_settings->team_members_limit == -1 ? l('global.unlimited') : nr($data->plan_settings->team_members_limit)) . '</strong>') ?>
            </div>
        </li>
    <?php endif ?>

    <?php if(\Altum\Plugin::is_active('affiliate') && settings()->affiliate->is_enabled): ?>
        <li class="d-flex align-items-baseline mb-2">
            <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->affiliate_commission_percentage ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
            <div class="<?= $data->plan_settings->affiliate_commission_percentage ? null : 'text-muted' ?>">
                <?= sprintf(l('global.plan_settings.affiliate_commission_percentage'), '<strong>' . nr($data->plan_settings->affiliate_commission_percentage) . '%</strong>') ?>
            </div>
        </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->statistics_retention ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->statistics_retention ? null : 'text-muted' ?>">
            <?= sprintf(l('global.plan_settings.statistics_retention'), '<strong>' . ($data->plan_settings->statistics_retention == -1 ? l('global.unlimited') : nr($data->plan_settings->statistics_retention)) . '</strong>') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->ordering_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->ordering_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.ordering_is_enabled') ?>
        </div>
    </li>

    <?php if(settings()->stores->additional_domains_is_enabled): ?>
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->additional_domains_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->additional_domains_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.additional_domains_is_enabled') ?>
        </div>
    </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->analytics_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->analytics_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.analytics_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->qr_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->qr_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.qr_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->password_protection_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->password_protection_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.password_protection_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->removable_branding_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->removable_branding_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.removable_branding_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->custom_url_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->custom_url_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.custom_url_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->search_engine_block_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->search_engine_block_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.search_engine_block_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->custom_css_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->custom_css_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.custom_css_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->custom_js_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->custom_js_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.custom_js_is_enabled') ?>
        </div>
    </li>

    <?php if(settings()->stores->email_reports_is_enabled): ?>
    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->email_reports_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->email_reports_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.email_reports_is_enabled') ?>
        </div>
    </li>
    <?php endif ?>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->online_payments_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->online_payments_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.online_payments_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->api_is_enabled ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->api_is_enabled ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.api_is_enabled') ?>
        </div>
    </li>

    <li class="d-flex align-items-baseline mb-2">
        <i class="fa fa-fw fa-sm mr-3 <?= $data->plan_settings->no_ads ? 'fa-check text-success' : 'fa-times text-muted' ?>"></i>
        <div class="<?= $data->plan_settings->no_ads ? null : 'text-muted' ?>">
            <?= l('global.plan_settings.no_ads') ?>
        </div>
    </li>
</ul>
