<?php defined('ALTUMCODE') || die() ?>

<div>
    <p class="text-muted"><?= l('admin_settings.ads.ads_help') ?></p>

    <div class="form-group">
        <label for="header"><?= l('admin_settings.ads.header') ?></label>
        <textarea id="header" name="header" class="form-control form-control-lg"><?= settings()->ads->header ?></textarea>
    </div>

    <div class="form-group">
        <label for="footer"><?= l('admin_settings.ads.footer') ?></label>
        <textarea id="footer" name="footer" class="form-control form-control-lg"><?= settings()->ads->footer ?></textarea>
    </div>

    <div class="form-group">
        <label for="header_stores"><?= l('admin_settings.ads.header_stores') ?></label>
        <textarea id="header_stores" name="header_stores" class="form-control form-control-lg"><?= settings()->ads->header_stores ?></textarea>
    </div>

    <div class="form-group">
        <label for="footer_stores"><?= l('admin_settings.ads.footer_stores') ?></label>
        <textarea id="footer_stores" name="footer_stores" class="form-control form-control-lg"><?= settings()->ads->footer_stores ?></textarea>
    </div>
</div>

<button type="submit" name="submit" class="btn btn-lg btn-block btn-primary mt-4"><?= l('global.update') ?></button>
