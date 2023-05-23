<?php defined('ALTUMCODE') || die() ?>

<div>
    <div class="form-group">
        <label for="branding"><?= l('admin_settings.stores.branding') ?></label>
        <textarea id="branding" name="branding" class="form-control form-control-lg"><?= settings()->stores->branding ?></textarea>
        <small class="form-text text-muted"><?= l('admin_settings.stores.branding_help') ?></small>
    </div>

    <?php if(!in_array(settings()->license->type, ['Extended License', 'extended'])): ?>
        <div class="alert alert-primary" role="alert">
            You need to own the Extended License in order to use the Custom Domains system.
        </div>
    <?php endif ?>

    <div class="<?= !in_array(settings()->license->type, ['Extended License', 'extended']) ? 'container-disabled' : null ?>">
        <div class="form-group">
            <label for="domains_is_enabled"><?= l('admin_settings.stores.domains_is_enabled') ?></label>
            <select id="domains_is_enabled" name="domains_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->stores->domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->stores->domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
            <small class="form-text text-muted"><?= l('admin_settings.stores.domains_is_enabled_help') ?></small>
        </div>

        <div class="form-group">
            <label for="additional_domains_is_enabled"><?= l('admin_settings.stores.additional_domains_is_enabled') ?></label>
            <select id="additional_domains_is_enabled" name="additional_domains_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->stores->additional_domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->stores->additional_domains_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
            <small class="form-text text-muted"><?= l('admin_settings.stores.additional_domains_is_enabled_help') ?></small>
        </div>

        <div class="form-group">
            <label for="main_domain_is_enabled"><?= l('admin_settings.stores.main_domain_is_enabled') ?></label>
            <select id="main_domain_is_enabled" name="main_domain_is_enabled" class="form-control form-control-lg">
                <option value="1" <?= settings()->stores->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= l('global.yes') ?></option>
                <option value="0" <?= !settings()->stores->main_domain_is_enabled ? 'selected="selected"' : null ?>><?= l('global.no') ?></option>
            </select>
            <small class="form-text text-muted"><?= l('admin_settings.stores.main_domain_is_enabled_help') ?></small>
        </div>
    </div>

    <div class="form-group">
        <label for="email_reports_is_enabled"><?= l('admin_settings.stores.email_reports_is_enabled') ?></label>
        <select id="email_reports_is_enabled" name="email_reports_is_enabled" class="form-control form-control-lg">
            <option value="0" <?= !settings()->stores->email_reports_is_enabled ? 'selected="selected"' : null ?>><?= l('global.disabled') ?></option>
            <option value="weekly" <?= settings()->stores->email_reports_is_enabled == 'weekly' ? 'selected="selected"' : null ?>><?= l('admin_settings.stores.email_reports_is_enabled_weekly') ?></option>
            <option value="monthly" <?= settings()->stores->email_reports_is_enabled == 'monthly' ? 'selected="selected"' : null ?>><?= l('admin_settings.stores.email_reports_is_enabled_monthly') ?></option>
        </select>
        <small class="form-text text-muted"><?= l('admin_settings.stores.email_reports_is_enabled_help') ?></small><br />
    </div>

    <div class="form-group">
        <label for="logo_size_limit"><?= l('admin_settings.stores.logo_size_limit') ?></label>
        <input id="logo_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="logo_size_limit" class="form-control form-control-lg" value="<?= settings()->stores->logo_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.stores.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="favicon_size_limit"><?= l('admin_settings.stores.favicon_size_limit') ?></label>
        <input id="favicon_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="favicon_size_limit" class="form-control form-control-lg" value="<?= settings()->stores->favicon_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.stores.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="image_size_limit"><?= l('admin_settings.stores.image_size_limit') ?></label>
        <input id="image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="image_size_limit" class="form-control form-control-lg" value="<?= settings()->stores->image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.stores.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="menu_image_size_limit"><?= l('admin_settings.stores.menu_image_size_limit') ?></label>
        <input id="menu_image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="menu_image_size_limit" class="form-control form-control-lg" value="<?= settings()->stores->menu_image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.stores.size_limit_help') ?></small>
    </div>

    <div class="form-group">
        <label for="item_image_size_limit"><?= l('admin_settings.stores.item_image_size_limit') ?></label>
        <input id="item_image_size_limit" type="number" min="0" max="<?= get_max_upload() ?>" step="any" name="item_image_size_limit" class="form-control form-control-lg" value="<?= settings()->stores->item_image_size_limit ?>" />
        <small class="form-text text-muted"><?= l('admin_settings.stores.size_limit_help') ?></small>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
