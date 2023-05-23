<?php defined('ALTUMCODE') || die() ?>

<p><?= l('s_cart.email_orders.p1', $data->store_user->language) ?></p>

<div style="margin-top: 30px">
    <table>
        <tbody>
            <tr>
                <td>
                    <strong><?= l('s_cart.type', $data->store_user->language) ?></strong>
                </td>
                <td>
                    <span class="text-muted"><?= l('s_cart.type_' . $data->type, $data->store_user->language) ?></span>
                </td>
            </tr>
            <tr>
                <td>
                    <strong><?= l('s_cart.datetime', $data->store_user->language) ?></strong>
                </td>
                <td>
                    <span class="text-muted"><?= $data->datetime ?></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div style="margin-top: 30px">
    <table>
        <thead>
            <tr>
                <td><?= l('s_cart.item', $data->store_user->language) ?></td>
                <td><?= l('s_cart.quantity', $data->store_user->language) ?></td>
                <td><?= l('s_cart.price', $data->store_user->language) ?></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach($data->items as $item): ?>

                <tr>
                    <td>
                        <?= $item['item']->name ?>
                    </td>
                    <td>
                        <span class="text-muted"><?= $item['quantity'] ?></span>
                    </td>
                    <td>
                        <span class="text-muted"><?= $item['price'] . ' ' . $data->store->currency ?></span>
                    </td>
                </tr>

                <?php if(count($item['data']['extras'])): ?>
                    <tr>
                        <td colspan="3" class="text-muted">
                            <?= implode(', ', $item['data']['extras']) ?>
                        </td>
                    </tr>
                <?php endif ?>

                <?php if(count($item['data']['variant_options'])): ?>
                    <tr>
                        <td colspan="3" class="text-muted">
                            <?php foreach($item['data']['variant_options'] as $variant_option): ?>
                                <strong><?= $variant_option['name'] ?>: </strong> <?= $variant_option['value'] ?>
                            <?php endforeach ?>
                        </td>
                    </tr>
                <?php endif ?>

            <?php endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2"><?= l('s_cart.total', $data->store_user->language) ?></td>
                <td><?= $data->final_price . ' ' . $data->store->currency ?></td>
            </tr>
        </tfoot>

    </table>
</div>

<div style="margin-top: 30px">
    <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
        <tbody>
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td>
                            <a href="<?= url('order/' . $data->order_id) ?>">
                                <?= sprintf(l('s_cart.email_orders.button', $data->store_user->language), $data->order_number) ?>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<p>
    <small class="text-muted"><?= sprintf(l('s_cart.email_orders.notice', $data->store_user->language), '<a href="' . url('store-update/' . $data->store->store_id) . '">', '</a>') ?></small>
</p>
