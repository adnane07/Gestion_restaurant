<?php defined('ALTUMCODE') || die() ?>

<p><?= sprintf(l('cron.email_reports.p1', $data->row->language), $data->row->name) ?></p>

<div>
    <table>
        <thead>
            <tr>
                <td></td>
                <td><?= l('cron.email_reports.pageviews', $data->row->language) ?></td>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td></td>
                <td>
                    <span class="text-muted">
                        <?= $data->previous_basic_analytics->pageviews ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle">
                    <?php $percentage = get_percentage_change($data->previous_basic_analytics->pageviews, $data->basic_analytics->pageviews) ?>

                    <?php if($percentage == 0): ?>
                        <span class="text-muted">-</span>
                    <?php else: ?>
                        <?= $percentage > 0 ? '<span style="color: #28a745 !important;">+' . round($percentage, 0) . '%</span>' : '<span style="color: #dc3545 !important;">-' . round($percentage, 0) . '%</span>'; ?>
                    <?php endif ?>
                </td>
                <td>
                    <h2 style="margin-bottom: 0">
                        <?= $data->basic_analytics->pageviews ?>
                    </h2>
                </td>
            </tr>
        </tbody>
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
                            <a href="<?= url('store/' . $data->row->store_id) ?>">
                                <?= l('cron.email_reports.button', $data->row->language) ?>
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
    <small class="text-muted"><?= sprintf(l('cron.email_reports.notice', $data->row->language), '<a href="' . url('store-update/' . $data->row->store_id) . '">', '</a>') ?></small>
</p>
