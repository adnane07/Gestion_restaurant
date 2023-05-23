<?php defined('ALTUMCODE') || die() ?>

<footer class="container store-footer">

    <div class="d-flex flex-column flex-lg-row justify-content-between">
        <div class="d-flex flex-column mb-4 mb-lg-0">
            <div><?= sprintf(l('global.footer.copyright'), date('Y'), $this->store->name) ?></div>

            <?php if(!$this->store->is_removed_branding || ($this->store->is_removed_branding && !$this->store_user->plan_settings->removable_branding_is_enabled)) :?>
                <div class="mt-2">
                    <a href="<?= url() ?>" class="text-muted small" target="_blank"><?= settings()->stores->branding ?></a>
                </div>
            <?php endif ?>
        </div>

        <div class="mb-4 mb-lg-0">
            <?php foreach(require APP_PATH . 'includes/s/socials.php' as $key => $value): ?>
                <?php if($this->store->socials->{$key}): ?>

                    <a href="<?= sprintf($value['format'], $this->store->socials->{$key}) ?>" target="_blank" class="mx-2" title="<?= $value['name'] ?>"><div class="svg-md text-muted d-inline-block"><?= include_view(ASSETS_PATH . '/images/s/' . $key . '.svg') ?></div></a>

                <?php endif ?>
            <?php endforeach ?>
        </div>

        <?php if(count(\Altum\ThemeStyle::$themes) > 1): ?>
            <div class="mb-0 mb-lg-0">
                <a href="#" data-choose-theme-style="dark" class="text-muted <?= \Altum\ThemeStyle::get() == 'dark' ? 'd-none' : null ?>">
                    <?= sprintf(l('global.theme_style'), l('global.theme_style_dark')) ?>
                </a>
                <a href="#" data-choose-theme-style="light" class="text-muted <?= \Altum\ThemeStyle::get() == 'light' ? 'd-none' : null ?>">
                    <?= sprintf(l('global.theme_style'), l('global.theme_style_light')) ?>
                </a>
            </div>

        <?php ob_start() ?>
            <script>
                'use strict';

                document.querySelectorAll('[data-choose-theme-style]').forEach(theme => {

                    theme.addEventListener('click', event => {

                        let chosen_theme_style = event.currentTarget.getAttribute('data-choose-theme-style');

                        /* Set a cookie with the new theme style */
                        set_cookie('theme_style', chosen_theme_style, 30);

                        /* Change the css and button on the page */
                        let css = document.querySelector(`#css_theme_style`);

                        document.querySelector(`[data-theme-style]`).setAttribute('data-theme-style', chosen_theme_style);

                        switch(chosen_theme_style) {
                            case 'dark':
                                css.setAttribute('href', <?= json_encode(ASSETS_FULL_URL . 'css/' . \Altum\ThemeStyle::$themes['dark'][l('direction')] . '?v=' . PRODUCT_CODE) ?>);
                                document.querySelector(`[data-choose-theme-style="dark"]`).classList.add('d-none');
                                document.querySelector(`[data-choose-theme-style="light"]`).classList.remove('d-none');
                                break;

                            case 'light':
                                css.setAttribute('href', <?= json_encode(ASSETS_FULL_URL . 'css/' . \Altum\ThemeStyle::$themes['light'][l('direction')] . '?v=' . PRODUCT_CODE) ?>);
                                document.querySelector(`[data-choose-theme-style="dark"]`).classList.remove('d-none');
                                document.querySelector(`[data-choose-theme-style="light"]`).classList.add('d-none');
                                break;
                        }

                        event.preventDefault();
                    });

                })
            </script>
            <?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
        <?php endif ?>
    </div>

</footer>
