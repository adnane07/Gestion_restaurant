<?php defined('ALTUMCODE') || die() ?>

<?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/sortable.min.js' ?>"></script>

    <script>
        'use strict';

        let initiate_sortable_blocks = (id_type = '') => {
            Sortable.create(document.querySelector('[data-blocks]'), {
                animation: 150,
                handle: '[data-draggable]',
                onUpdate: () => {

                    let blocks = [];

                    document.querySelectorAll(`[data-blocks] [data-${id_type}-id]`).forEach((element, index) => {
                        let block = {order: index};

                        block[`${id_type}_id`] = element.getAttribute(`data-${id_type}-id`);

                        blocks.push(block);
                    });

                    fetch(`${url}${id_type}/order_ajax`, {
                        method: 'POST',
                        body: JSON.stringify({
                            blocks,
                            global_token,
                            store_id: <?= json_encode($data->store->store_id) ?>
                        }),
                        headers: {
                            'Content-Type': 'application/json; charset=UTF-8'
                        }
                    })
                        .then(response => {
                            return response.ok ? response.json() : Promise.reject(response);
                        })
                        .then(data => {
                            /* :) */
                        })
                        .catch(error => {
                            /* :) */
                        });

                }
            });
        }

        initiate_sortable_blocks(<?= json_encode($data->id_type) ?>)
    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>
