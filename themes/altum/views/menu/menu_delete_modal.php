<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="menu_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="modal-title">
                        <i class="fa fa-fw fa-sm fa-trash-alt text-muted mr-2"></i>
                        <?= l('menu_delete_modal.header') ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form name="menu_delete_modal" method="post" action="<?= url('menu/delete') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="menu_id" value="" />

                    <p class="text-muted"><?= l('menu_delete_modal.subheader') ?></p>

                    <div class="mt-4">
                        <button type="submit" name="submit" class="btn btn-block btn-danger"><?= l('global.delete') ?></button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php ob_start() ?>
<script>
    'use strict';

    /* On modal show load new data */
    $('#menu_delete_modal').on('show.bs.modal', event => {
        let menu_id = $(event.relatedTarget).data('menu-id');

        $(event.currentTarget).find('input[name="menu_id"]').val(menu_id);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
