<?php defined('ALTUMCODE') || die() ?>
<style>


.dropdown .dropbtn {
  font-size: 16px;  
  border: none;
  color:var(--gray-500);
  outline: none;
  
  width: 160px;
  padding: 20px 16px;
  background-color: inherit;
  font-family: inherit;
  margin: 0;
}


.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  z-index: 1;
}



.dropdown:hover .dropdown-content {
    color:black;
  display: block;
}
</style>
<nav class="navbar app-sub-navbar navbar-expand-lg navbar-light bg-white">
    <div class="container d-flex flex-column flex-lg-row align-items-start align-items-lg-center overflow-y">

        <ul class="app-sub-navbar-ul">

            <?php if(in_array(\Altum\Router::$controller_key, ['store', 'store-qr', 'qr-table' , 'store-update','orders-daily', 'orders-of' ,'orders', 'client']) || (in_array(\Altum\Router::$controller_key, ['statistics', 'orders-statistics' ]) && isset($_GET['store_id']))): ?>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'store' ? 'active' : null ?>" href="<?= url('store/' . $data->store_id) ?>">
                        <i class="fa fa-fw fa-sm fa-store mr-2"></i> <?= l('store.menu') ?>
                    </a>
                </li>
                
                     

                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa fa-fw fa-sm fa-qrcode mr-2"></i><?= l('store_qr.menu') ?>  <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-content">
                         <li class="nav-item">
                  
                             <a class="nav-link <?= \Altum\Router::$controller_key == 'store_qr' ? 'active' : null ?>" href="<?= url('store-qr/' . $data->store_id) ?>">  
                                  <?= l('store_qr.menu') ?>
                             </a>
                         </li>
                            <li class="nav-item">
                                 <a class="nav-link <?= \Altum\Router::$controller_key == 'qr-table' ? 'active' : null ?>" href="<?= url('qr-table/' . $data->store_id) ?>">
                                    <?= l('qr_table.title') ?>
                                 </a>
                            </li>
                        </div>
                    </div>

                <li class="nav-item">
                    <a href="<?= url('client/' .$data->store_id ) ?>"  class="nav-link <?= \Altum\Router::$controller_key == 'client' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-bars mr-2"></i> <?= l('client.costumer') ?>
                    </a>
                </li>

                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa fa-fw fa-sm fa-bell mr-2"></i> <?= l('orders.menu') ?>  <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-content">
                             <li class="nav-item">
                                 <a class="nav-link <?= \Altum\Router::$controller_key == 'orders' ? 'active' : null ?>" href="<?= url('orders/' . $data->store_id) ?>">
                                     <?= l('orders.menu') ?>
                                 </a>
                            </li>
                            <li class="nav-item">
                                 <a class="nav-link <?= \Altum\Router::$controller_key == 'orders-daily' ? 'active' : null ?>" href="<?= url('orders-daily/' . $data->store_id) ?>">
                                    <?= l('orders.header.today') ?>
                                 </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= \Altum\Router::$controller_key == 'orders-of' ? 'active' : null ?>" href="<?= url('orders-of/' . $data->store_id) ?>">
                                    <?= l('orders-of.title') ?>
                                 </a>
                             </li>
                        </div>
                    </div>



                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('store.external_url') ?>
                    </a>
                </li>

                <div class="dropdown">
                    <button class="dropbtn"><i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?=l('statistics.menu')?>  <i class="fa fa-caret-down"></i></button>
                    <div class="dropdown-content">
                <div <?= $this->user->plan_settings->analytics_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->analytics_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'statistics' ? 'active' : null ?>" href="<?= url('statistics?store_id=' . $data->store_id) ?>">
                                 <?= l('statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <div <?= $this->user->plan_settings->ordering_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->ordering_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'orders-statistics' ? 'active' : null ?>" href="<?= url('orders-statistics?store_id=' . $data->store_id) ?>">
                                <?= l('orders_statistics.menu') ?>
                            </a>
                        </li>

                        </div>
                </div></div>
                </div>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'store-update' ? 'active' : null ?>" href="<?= url('store-update/' . $data->store_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

               

            <?php elseif(in_array(\Altum\Router::$controller_key, ['order'])): ?>

                <li class="nav-item">
                    <span class="nav-link <?= \Altum\Router::$controller_key == 'order' ? 'active' : null ?>">
                        <i class="fa fa-fw fa-sm fa-bell mr-2"></i> <?= l('order.menu') ?>
                    </span>
                </li>

                <?php if(in_array($data->processor, ['stripe', 'paypal'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('store-invoice/' . $data->order_id) ?>" target="_blank">
                            <i class="fa fa-fw fa-sm fa-file-invoice mr-2"></i> <?= l('store_invoice.menu') ?>
                        </a>
                    </li>
                <?php endif ?>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#order_delete_modal" data-store-id="<?= $data->order_id ?>" data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['menu', 'menu-update']) || (in_array(\Altum\Router::$controller_key, ['statistics', 'orders-statistics']) && isset($_GET['menu_id']))): ?>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'menu' ? 'active' : null ?>" href="<?= url('menu/' . $data->menu_id) ?>">
                        <i class="fa fa-fw fa-sm fa-list mr-2"></i> <?= l('menu.menu') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('menu.external_url') ?>
                    </a>
                </li>

                <div <?= $this->user->plan_settings->analytics_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->analytics_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'statistics' ? 'active' : null ?>" href="<?= url('statistics?menu_id=' . $data->menu_id) ?>">
                                <i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <div <?= $this->user->plan_settings->ordering_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->ordering_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'orders-statistics' ? 'active' : null ?>" href="<?= url('orders-statistics?menu_id=' . $data->menu_id) ?>">
                                <i class="fa fa-fw fa-sm fa-coins mr-2"></i> <?= l('orders_statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'menu-update' ? 'active' : null ?>" href="<?= url('menu-update/' . $data->menu_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#menu_delete_modal" data-menu-id="<?= $data->menu_id ?>" data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['category', 'category-update']) || (in_array(\Altum\Router::$controller_key, ['statistics', 'orders-statistics']) && isset($_GET['category_id']))): ?>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'category' ? 'active' : null ?>" href="<?= url('category/' . $data->category_id) ?>">
                        <i class="fa fa-fw fa-sm fa-shopping-bag mr-2"></i> <?= l('category.menu') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('category.external_url') ?>
                    </a>
                </li>

                <div <?= $this->user->plan_settings->analytics_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->analytics_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'statistics' ? 'active' : null ?>" href="<?= url('statistics?category_id=' . $data->category_id) ?>">
                                <i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <div <?= $this->user->plan_settings->ordering_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->ordering_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'orders-statistics' ? 'active' : null ?>" href="<?= url('orders-statistics?category_id=' . $data->category_id) ?>">
                                <i class="fa fa-fw fa-sm fa-coins mr-2"></i> <?= l('orders_statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'category-update' ? 'active' : null ?>" href="<?= url('category-update/' . $data->category_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#category_delete_modal" data-category-id="<?= $data->category_id ?>"  data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['item', 'item-update']) || (in_array(\Altum\Router::$controller_key, ['statistics', 'orders-statistics']) && isset($_GET['item_id']))): ?>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'item' ? 'active' : null ?>" href="<?= url('item/' . $data->item_id) ?>">
                        <i class="fa fa-fw fa-sm fa-burn mr-2"></i> <?= l('item.menu') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('item.external_url') ?>
                    </a>
                </li>

                <div <?= $this->user->plan_settings->analytics_is_enabled ? null : 'data-toggle="tooltip" title="' . l('global.info_message.plan_feature_no_access') . '"' ?>>
                    <div class="<?= $this->user->plan_settings->analytics_is_enabled ? null : 'container-disabled' ?>">

                        <li class="nav-item">
                            <a class="nav-link <?= \Altum\Router::$controller_key == 'statistics' ? 'active' : null ?>" href="<?= url('statistics?item_id=' . $data->item_id) ?>">
                                <i class="fa fa-fw fa-sm fa-chart-bar mr-2"></i> <?= l('statistics.menu') ?>
                            </a>
                        </li>

                    </div>
                </div>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'item-update' ? 'active' : null ?>" href="<?= url('item-update/' . $data->item_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#item_delete_modal" data-item-id="<?= $data->item_id ?>" data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['item-extra-update'])): ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('item.external_url') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'item-extra-update' ? 'active' : null ?>" href="<?= url('item-extra-update/' . $data->item_extra_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#item_extra_delete_modal" data-item-extra-id="<?= $data->item_extra_id ?>" data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['item-option-update'])): ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('item.external_url') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'item-option-update' ? 'active' : null ?>" href="<?= url('item-option-update/' . $data->item_option_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#item_option_delete_modal" data-item-option-id="<?= $data->item_option_id ?>" data-resource-name="<?= $data->resource_name ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php elseif(in_array(\Altum\Router::$controller_key, ['item-variant-update'])): ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?= $data->external_url ?>" target="_blank">
                        <i class="fa fa-fw fa-sm fa-external-link-alt mr-2"></i> <?= l('item.external_url') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?= \Altum\Router::$controller_key == 'item-variant-update' ? 'active' : null ?>" href="<?= url('item-variant-update/' . $data->item_variant_id) ?>">
                        <i class="fa fa-fw fa-sm fa-pencil-alt mr-2"></i> <?= l('global.edit') ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#item_variant_delete_modal" data-item-variant-id="<?= $data->item_variant_id ?>">
                        <i class="fa fa-fw fa-sm fa-trash-alt mr-2"></i> <?= l('global.delete') ?>
                    </a>
                </li>

            <?php endif ?>

        </ul>

    </div>
</nav>
