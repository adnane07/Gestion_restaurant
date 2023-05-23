<?php defined('ALTUMCODE') || die() ?>

<div class="modal fade" id="item_extra_delete_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="modal-title">
                        <i class="fa fa-fw fa-sm fa-trash-alt text-muted mr-2"></i>
                        <?= l('item_extra_delete_modal.header') ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= l('global.close') ?>">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form name="item_extra_delete_modal" method="post" action="<?= url('item-extra/delete') ?>" role="form">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" required="required" />
                    <input type="hidden" name="item_extra_id" value="" />

                    <p class="text-muted"><?= l('item_extra_delete_modal.subheader') ?></p>

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
    $('#item_extra_delete_modal').on('show.bs.modal', event => {
        let item_extra_id = $(event.relatedTarget).data('item-extra-id');

        $(event.currentTarget).find('input[name="item_extra_id"]').val(item_extra_id);
    });
</script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
