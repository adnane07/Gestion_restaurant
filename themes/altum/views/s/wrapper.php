<?php defined('ALTUMCODE') || die() ?>
<!DOCTYPE html>
<html lang="<?= \Altum\Language::$code ?>" dir="<?= l('direction') ?>">
    <head>
        <title><?= \Altum\Title::get() ?></title>
        <base href="<?= SITE_URL ?>">
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

        <?php if(\Altum\Meta::$description): ?>
            <meta name="description" content="<?= \Altum\Meta::$description ?>" />
        <?php endif ?>
        <?php if(\Altum\Meta::$keywords): ?>
            <meta name="keywords" content="<?= \Altum\Meta::$keywords ?>" />
        <?php endif ?>

        <?php if(\Altum\Meta::$open_graph['url']): ?>
            <!-- Open Graph / Facebook / Twitter -->
            <?php foreach(\Altum\Meta::$open_graph as $key => $value): ?>
                <?php if($value): ?>
                    <meta property="og:<?= $key ?>" content="<?= $value ?>" />
                    <meta property="twitter:<?= $key ?>" content="<?= $value ?>" />
                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>

        <?php if(isset($this->store) && $this->store_user->plan_settings->search_engine_block_is_enabled && !$this->store->is_se_visible): ?>
            <meta name="robots" content="noindex">
        <?php endif ?>

        <?php if(isset($this->store) && $this->store->favicon): ?>
            <link href="<?= UPLOADS_FULL_URL . 'store_favicons/' . $this->store->favicon ?>" rel="shortcut icon" />
        <?php else: ?>

            <?php if(!empty(settings()->main->favicon)): ?>
                <link href="<?= UPLOADS_FULL_URL . 'main/' . settings()->main->favicon ?>" rel="shortcut icon" />
            <?php endif ?>

        <?php endif ?>

        <link href="<?= ASSETS_FULL_URL . 'css/' . \Altum\ThemeStyle::get_file() . '?v=' . PRODUCT_CODE ?>" id="css_theme_style" rel="stylesheet" media="screen,print">
        <?php foreach(['store-custom.css'] as $file): ?>
            <link href="<?= ASSETS_FULL_URL . 'css/' . $file . '?v=' . PRODUCT_CODE ?>" rel="stylesheet" media="screen,print">
        <?php endforeach ?>

        <?= \Altum\Event::get_content('head') ?>

        <?php if(!empty(settings()->custom->head_js_store)): ?>
            <?= settings()->custom->head_js_store ?>
        <?php endif ?>

        <?php if(!empty(settings()->custom->head_css_store)): ?>
            <style><?= settings()->custom->head_css_store ?></style>
        <?php endif ?>

        <?php if(!empty($this->store->custom_css) && $this->store_user->plan_settings->custom_css_is_enabled): ?>
            <style><?= $this->store->custom_css ?></style>
        <?php endif ?>
    </head>

    <body class="<?= l('direction') == 'rtl' ? 'rtl' : null ?> <?= $this->store->theme ?>" data-theme-style="<?= \Altum\ThemeStyle::get() ?>">
        <?php require THEME_PATH . 'views/partials/cookie_consent.php' ?>

        <?php require THEME_PATH . 'views/s/partials/ads_header.php' ?>

        <main class="altum-animate altum-animate-fill-both altum-animate-fade-in">

            <?= $this->views['content'] ?>

        </main>

        <?php require THEME_PATH . 'views/s/partials/ads_footer.php' ?>

        <?= $this->views['footer'] ?>

        <?= \Altum\Event::get_content('modals') ?>

        <?php require THEME_PATH . 'views/partials/js_global_variables.php' ?>

        <?php foreach(['custom.js'] as $file): ?>
            <script src="<?= ASSETS_FULL_URL ?>js/<?= $file ?>?v=<?= PRODUCT_CODE ?>"></script>
        <?php endforeach ?>

        <?= \Altum\Event::get_content('javascript') ?>

        <?php if(!empty($this->store->custom_js) && $this->store_user->plan_settings->custom_js_is_enabled): ?>
            <?= $this->store->custom_js ?>
        <?php endif ?>
    </body>
</html>
