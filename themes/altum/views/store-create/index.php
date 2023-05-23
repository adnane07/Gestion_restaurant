<?php defined('ALTUMCODE') || die() ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('dashboard') ?>"><?= l('dashboard.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('store_create.breadcrumb') ?></li>
        </ol>
    </nav>

    <h1 class="h4 text-truncate"><?= l('store_create.header') ?></h1>
    <p></p>

    <form action="" method="post" role="form">
        <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

        <?php if(count($data->domains) && (settings()->stores->domains_is_enabled || settings()->stores->additional_domains_is_enabled)): ?>
            <div class="form-group">
                <label for="domain_id"><i class="fa fa-fw fa-sm fa-globe text-muted mr-1"></i> <?= l('store.input.domain_id') ?></label>
                <select id="domain_id" name="domain_id" class="form-control">
                    <?php if(settings()->stores->main_domain_is_enabled || \Altum\Authentication::is_admin()): ?>
                        <option value=""><?= SITE_URL . 's/' ?></option>
                    <?php endif ?>

                    <?php foreach($data->domains as $row): ?>
                        <option value="<?= $row->domain_id ?>" data-type="<?= $row->type ?>" <?= $data->values['domain_id'] && $data->values['domain_id'] == $row->domain_id ? 'selected="selected"' : null ?>><?= $row->url ?></option>
                    <?php endforeach ?>
                </select>
                <small class="form-text text-muted"><?= l('store.input.domain_id_help') ?></small>
            </div>

            <div id="is_main_store_wrapper" class="custom-control custom-switch my-3">
                <input id="is_main_store" name="is_main_store" type="checkbox" class="custom-control-input" <?= $data->values['is_main_store'] ? 'checked="checked"' : null ?>>
                <label class="custom-control-label" for="is_main_store"><?= l('store.input.is_main_store') ?></label>
                <small class="form-text text-muted"><?= l('store.input.is_main_store_help') ?></small>
            </div>

            <div <?= $this->user->plan_settings->custom_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <div class="<?= $this->user->plan_settings->custom_url_is_enabled ? null : 'container-disabled' ?>">
                    <div class="form-group">
                        <label for="url"><i class="fa fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('store.input.url') ?></label>
                        <input type="text" id="url" name="url" class="form-control" value="<?= $data->values['url'] ?>" onchange="update_this_value(this, get_slug)" onkeyup="update_this_value(this, get_slug)" placeholder="<?= l('store.input.url_placeholder') ?>" />
                        <small class="form-text text-muted"><?= l('store.input.url_help') ?></small>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div <?= $this->user->plan_settings->custom_url_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                <div class="<?= $this->user->plan_settings->custom_url_is_enabled ? null : 'container-disabled' ?>">
                    <label for="url"><i class="fa fa-fw fa-sm fa-bolt text-muted mr-1"></i> <?= l('store.input.url') ?></label>
                    <div class="mb-3">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><?= SITE_URL . 's/' ?></span>
                            </div>
                            <input type="text" id="url" name="url" class="form-control" value="<?= $data->values['url'] ?>" onchange="update_this_value(this, get_slug)" onkeyup="update_this_value(this, get_slug)" placeholder="<?= l('store.input.url_placeholder') ?>" />
                        </div>
                        <small class="form-text text-muted"><?= l('store.input.url_help') ?></small>
                    </div>
                </div>
            </div>
        <?php endif ?>

        <div class="form-group">
            <label for="name"><i class="fa fa-fw fa-sm fa-signature text-muted mr-1"></i> <?= l('store.input.name') ?></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= $data->values['name'] ?>" placeholder="<?= l('store.input.name_placeholder') ?>" required="required" />
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="title"><i class="fa fa-fw fa-sm fa-heading text-muted mr-1"></i> <?= l('store.input.title') ?></label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= $data->values['title'] ?>" required="required" />
                    <small class="form-text text-muted"><?= l('store.input.title_help') ?></small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="description"><i class="fa fa-fw fa-sm fa-pen text-muted mr-1"></i> <?= l('store.input.description') ?></label>
                    <input type="text" id="description" name="description" class="form-control" value="<?= $data->values['description'] ?>" />
                    <small class="form-text text-muted"><?= l('store.input.description_help') ?></small>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="address"><i class="fa fa-fw fa-sm fa-map-pin text-muted mr-1"></i> <?= l('store.input.address') ?></label>
            <input type="text" id="address" name="address" class="form-control" value="<?= $data->values['address'] ?>" />
            <small class="form-text text-muted"><?= l('store.input.address_help') ?></small>
        </div>

        <div class="form-group">
            <label for="currency"><i class="fa fa-fw fa-sm fa-coins text-muted mr-1"></i> <?= l('store.input.currency') ?></label>
            <input type="text" id="currency" name="currency" class="form-control" value="<?= $data->values['currency'] ?>" required="required" />
            <small class="form-text text-muted"><?= l('store.input.currency_help') ?></small>
        </div>

        <div class="form-group">
            <label for="timezone"><i class="fa fa-fw fa-sm fa-clock text-muted mr-1"></i> <?= l('store.input.timezone') ?></label>
            <select id="timezone" name="timezone" class="form-control">
                <?php foreach(DateTimeZone::listIdentifiers() as $timezone) echo '<option value="' . $timezone . '" ' . ($data->values['timezone'] && $data->values['timezone'] == $timezone ? 'selected="selected"' : null) . '>' . $timezone . '</option>' ?>
            </select>
            <small class="form-text text-muted"><?= l('store.input.timezone_help') ?></small>
        </div>

        <p><small class="form-text text-muted"><i class="fa fa-fw fa-sm fa-info-circle"></i> <?= l('store_create.info') ?></small></p>
        <button type="submit" name="submit" class="btn btn-block btn-primary"><?= l('global.create') ?></button>
    </form>

</div>

<?php ob_start() ?>
<script>
    'use strict';

    /* Is main store handler */
    let is_main_store_handler = () => {
        if(document.querySelector('#is_main_store').checked) {
            document.querySelector('#url').setAttribute('disabled', 'disabled');
        } else {
            document.querySelector('#url').removeAttribute('disabled');
        }
    }

    document.querySelector('#is_main_store') && document.querySelector('#is_main_store').addEventListener('change', is_main_store_handler);

    /* Domain Id Handler */
    let domain_id_handler = () => {
        let domain_id = document.querySelector('select[name="domain_id"]').value;

        if(document.querySelector(`select[name="domain_id"] option[value="${domain_id}"]`).getAttribute('data-type') == '0') {
            document.querySelector('#is_main_store_wrapper').classList.remove('d-none');
        } else {
            document.querySelector('#is_main_store_wrapper').classList.add('d-none');
            document.querySelector('#is_main_store').checked = false;
        }

        is_main_store_handler();
    }

    domain_id_handler();

    document.querySelector('select[name="domain_id"]') && document.querySelector('select[name="domain_id"]').addEventListener('change', domain_id_handler);
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
