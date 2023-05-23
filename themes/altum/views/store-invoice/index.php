<?php defined('ALTUMCODE') || die() ?>

<div class="container my-5 d-flex justify-content-center">
    <div class="col-12 col-lg-10">

        <div class="d-print-none d-flex justify-content-between mb-5">
            <a href="<?= url('order/' . $data->order->order_id) ?>" class="text-muted" data-toggle="tooltip" title="<?= $this->language->global->go_back_button ?>"><i class="fa fa-fw fa-arrow-left"></i></a>

            <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa fa-fw fa-sm fa-print"></i> <?= $this->language->invoice->print ?></button>
        </div>

        <div class="card bg-gray-50 border-0">
            <div class="card-body p-5">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <?php if($data->store->logo): ?>
                            <img src="<?= UPLOADS_FULL_URL . 'store_logos/' . $data->store->logo ?>" class="img-fluid navbar-logo invoice-logo" alt="<?= $data->store->name ?>" />
                        <?php else: ?>
                            <h1><?= $data->store->name ?></h1>
                        <?php endif ?>
                    </div>

                    <div class="d-flex flex-column">
                        <h4><?= $this->language->invoice->invoice ?></h4>

                        <table>
                            <tbody>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->invoice_nr ?>:</td>
                                <td><?= $data->store->business->invoice_nr_prefix . $data->order->order_number ?></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->invoice_date ?>:</td>
                                <td><?= \Altum\Date::get($data->payment->datetime, 1) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-7">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-6 mb-md-0">
                            <h5><?= $this->language->invoice->vendor ?></h5>

                            <table>
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->name ?>:</td>
                                    <td><?= $data->store->business->name ?></td>
                                </tr>

                                <?php if(!empty($data->store->business->address)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->address ?>:</td>
                                        <td><?= $data->store->business->address ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->city)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->city ?>:</td>
                                        <td><?= $data->store->business->city ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->county)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->county ?>:</td>
                                        <td><?= $data->store->business->county ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->zip)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->zip ?>:</td>
                                        <td><?= $data->store->business->zip ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->country)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->country ?>:</td>
                                        <td><?= get_countries_array()[$data->store->business->country] ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->email)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->email ?>:</td>
                                        <td><?= $data->store->business->email ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->phone)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->phone ?>:</td>
                                        <td><?= $data->store->business->phone ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->tax_type) && !empty($data->store->business->tax_id)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->store->business->tax_type ?>:</td>
                                        <td><?= $data->store->business->tax_id ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->custom_key_one) && !empty($data->store->business->custom_value_one)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->store->business->custom_key_one ?>:</td>
                                        <td><?= $data->store->business->custom_value_one ?></td>
                                    </tr>
                                <?php endif ?>

                                <?php if(!empty($data->store->business->custom_key_two) && !empty($data->store->business->custom_value_two)): ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $data->store->business->custom_key_two ?>:</td>
                                        <td><?= $data->store->business->custom_value_two ?></td>
                                    </tr>
                                <?php endif ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-12 col-md-6">
                            <h5><?= $this->language->invoice->customer ?></h5>

                            <table>
                                <tbody>

                                    <tr>
                                        <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->name ?>:</td>
                                        <td><?= $data->payment->billing->name ?></td>
                                    </tr>

                                    <?php if(!empty($data->payment->billing->email)): ?>
                                        <tr>
                                            <td class="font-weight-bold text-muted pr-3"><?= $this->language->invoice->email ?>:</td>
                                            <td><?= $data->payment->billing->email ?></td>
                                        </tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-7">
                    <table class="table invoice-table">
                        <thead>
                            <tr>
                                <th><?= $this->language->invoice->table->item ?></th>
                                <th class="text-right"><?= $this->language->invoice->table->amount ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span><?= sprintf($this->language->store_invoice->order_number, $data->order->order_number) ?></span>
                                    </div>
                                </td>
                                <td class="text-right"><?= $data->payment->total_amount . ' ' . $data->payment->currency ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td class="d-flex flex-column">
                                <span class="font-weight-bold"><?= $this->language->invoice->table->total ?></span>
                                <small><?= sprintf($this->language->invoice->table->paid_via, $data->payment->processor) ?></small>
                            </td>
                            <td class="text-right font-weight-bold"><?= $data->payment->total_amount . ' ' . $data->payment->currency ?></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
