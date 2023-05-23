<?php defined('ALTUMCODE') || die() ?>


<script src="https://cdn.rawgit.com/amanuel/JS-HTML5-QRCode-Generator/master/qrcode.js"></script>
<script src="https://cdn.rawgit.com/amanuel/JS-HTML5-QRCode-Generator/master/html5-qrcode.js"></script>




<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('dashboard') ?>"><?= l('dashboard.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li>
                <a href="<?= url('store/' . $data->store->store_id) ?>"><?= l('store.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            <li class="active" aria-current="page"><?= l('store_qr.breadcrumb') ?></li>
        </ol>
    </nav>

   <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="h4 text-truncate mb-0"><?= sprintf(l('store_qr.header'), $data->store->name) ?></h1>

       <div class="d-flex align-items-center col-auto p-0">
            <div>
                <button
                        id="url_copy"
                        type="button"
                        class="btn btn-link text-secondary"
                        data-toggle="tooltip"
                        title="<?= l('global.clipboard_copy') ?>"
                        aria-label="<?= l('global.clipboard_copy') ?>"
                        data-copy="<?= l('global.clipboard_copy') ?>"
                        data-copied="<?= l('global.clipboard_copied') ?>"
                        data-clipboard-text="<?= $data->store->full_url ?>"
                >
                    <i class="fa fa-fw fa-sm fa-copy"></i>
                </button>
            </div>
        </div>
    </div>

    <p>
        <a href="<?= $data->store->full_url ?>" target="_blank" rel="noreferrer">
            <i class="fa fa-fw fa-sm fa-external-link-alt text-muted mr-1"></i> <?= $data->store->full_url ?>
        </a>
    </p>

    <div class="row">
        <div class="col-12 col-lg-6 mb-4 mb-lg-0 d-print-none">
            <div class="card">
                <div class="card-header bg-gray-50 border-0 font-weight-bold">
                    <?= l('store_qr.configurator.header') ?>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label for="foreground_color"><?= l('store_qr.configurator.foreground_color') ?></label>
                        <input type="color" id="foreground_color" name="foreground_color" class="form-control" value="#000000" />
                    </div>

                    <div class="form-group">
                        <label for="background_color"><?= l('store_qr.configurator.background_color') ?></label>
                        <input type="color" id="background_color" name="background_color" class="form-control" value="#ffffff" />
                    </div>

                    <div class="form-group">
                        <label for="corner_radius"><?= l('store_qr.configurator.corner_radius') ?></label>
                        <input type="range" id="corner_radius" name="corner_radius" class="form-control-range" min="0" max="0.5" step="0.1" value="0" />
                    </div>

                    <div class="form-group">
                        <label for="type"><?= l('store_qr.configurator.type') ?></label>
                        <select name="type" id="type" class="form-control">
                            <option value="normal" selected="selected"><?= l('store_qr.configurator.type_normal') ?></option>
                            <option value="text"><?= l('store_qr.configurator.type_text') ?></option>
                            <option value="image"><?= l('store_qr.configurator.type_image') ?></option>
                        </select>
                    </div>

                    <div id="type_text" class="d-none">
                        <div class="form-group">
                            <label for="text"><?= l('store_qr.configurator.text') ?></label>
                            <input type="text" id="text" name="text" class="form-control" value="" />
                        </div>

                        <div class="form-group">
                            <label for="text_color"><?= l('store_qr.configurator.text_color') ?></label>
                            <input type="color" id="text_color" name="text_color" class="form-control" value="#000000" />
                        </div>

                        <div class="form-group">
                            <label for="text_size"><?= l('store_qr.configurator.text_size') ?></label>
                            <input type="range" id="text_size" name="text_size" class="form-control-range" min="0.05" max="0.1" step="0.005" value="0.005" />
                        </div>
                    </div>

                    <div id="type_image" class="d-none">
                        <div class="form-group">
                            <label for="image"><?= l('store_qr.configurator.image') ?></label>
                            <input id="image" type="file" name="image" accept=".png, .jpg, .jpeg" class="form-control-file altum-file-input" />
                            <img id="image-buffer" src="" class="d-none" />
                        </div>

                        <div class="form-group">
                            <label for="image_size"><?= l('store_qr.configurator.image_size') ?></label>
                            <input type="range" id="image_size" class="form-control-range" min="0.05" max="0.2" step="0.005" value="0.005" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ecc"><?= l('store_qr.configurator.ecc') ?></label>
                        <select name="ecc" id="ecc" class="form-control">
                            <option value="L" selected="selected"><?= l('store_qr.configurator.ecc_l') ?></option>
                            <option value="M"><?= l('store_qr.configurator.ecc_m') ?></option>
                            <option value="Q"><?= l('store_qr.configurator.ecc_q') ?></option>
                            <option value="H"><?= l('store_qr.configurator.ecc_h') ?></option>
                        </select>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card mb-4">
                <div id="qr"></div>
            </div>

            <div class="row mb-4">
                <div class="col-12 col-lg-6 mb-3 mbdownload-lg-0">
                    <button type="button" onclick="window.print()" class="btn btn-block btn-outline-secondary d-print-none">
                        <i class="fa fa-fw fa-sm fa-file-pdf"></i> <?= l('store_qr.print') ?>
                    </button>
                </div>

                <div class="col-12 col-lg-6">
                    <button id="download" type="button" class="btn btn-block btn-primary d-print-none">
                        <i class="fa fa-fw fa-sm fa-download"></i> <?= l('global.download') ?>
                    </button>
                </div>
            </div>

            <p class="text-muted text-center"><?= l('store_qr.subheader') ?></p>
        </div>
    </div>


    <hr>
    <div class="row mb-8">
        <div class="col-12 col-lg-12">
            <h1 class="h4 text-truncate mb-0"><?= l('store.qr_wifi') ?></h1>
            <div style="height: 30px;"></div>
               <div class="row">
                   <div class="col-5 col-lg-5 ">
                             
                            <div style="height: 30px;"></div>
                        <form id="form">    
                            <div class="form-group has-feedback" >
                                <label for="ssid" class="control-label">SSID <i class="fa fa-asterisk" style="font-size:7px ;color: red;"></i></label>
                                <div class="input-group">
                                    <input type="text" id="ssid"  class="form-control" placeholder="SSID" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="enc" class="control-label">Encryption <i class="fa fa-asterisk" style="font-size:7px ;color: red;"></i></label>
                                <div class="input-group">
                                     <select name="" id="enc" class="form-control radio">
                                        <option value="WPA" id="type_wep" name="T">WPA/WPA2/WPA3</option>
                                        <option value="WEP" id="type_wpa" name="T">WEP</option>
                                    </select>
                                 </div>
                            </div>

                            <div class="form-group " id="key-p">
                                <label class="control-label">Password <i class="fa fa-asterisk" style="font-size:7px ;color: red;"></i></label>
                                <div class="input-group">
                                    <input type="password" id="password"  class="form-control" placeholder="password" required>
                                </div>
                            </div>

                            <label><span>Is Hidden?</span><input type="checkbox" id="hidden"></label>

                    </form>

                   </div>    
            
                    <div class="col-lg-6 col-6 offset-1   ">
                      
                              <div >
                                    <div id="qrcodewifi"  >
                                    </div>                             
                                </div>

                            <div class="row mb-4">
                                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                                    <button type="button" onclick="window.print()" class="btn btn-block btn-outline-secondary d-print-none">
                                        <i class="fa fa-fw fa-sm fa-file-pdf"></i> <?= l('store_qr.print') ?>
                                    </button>
                            </div>

                            <div class="col-12 col-lg-6">
                                <button id="downloadwifi" type="button" class="btn btn-block btn-primary d-print-none">
                                    <i class="fa fa-fw fa-sm fa-download"></i> <?= l('global.download') ?>
                                 </button>
                            </div>
                        </div>

                         <p class="text-muted text-center"><?= l('store_qr.subheader') ?></p>
                    
                   
                </div>
            </div>      
        </div>        
    </div>
   



  
    
</div>

<script>
    function updateQRCode() {
        var ssid = document.getElementById("ssid").value;
        var pw = document.getElementById("password").value;
        var enc = document.getElementById("enc").value;
        var hidden = document.getElementById("hidden").checked;
        var qrcode = document.getElementById("qrcodewifi");

        var text = "WIFI:S:" + ssid + ";T:" + enc + ";P:" + pw + ";H:" + hidden + ";;";

        qrcode.replaceChild(showQRCode(text), qrcode.lastChild);
    }

        document.getElementById("form").onchange = updateQRCode;
</script>
<?php include_view(THEME_PATH . 'views/partials/clipboard_js.php') ?>

<?php ob_start() ?>
    <script src="<?= ASSETS_FULL_URL . 'js/libraries/jquery-qrcode.min.js' ?>"></script>

    <script>
        'use strict';

        /* Download handler */
        document.querySelector('#download').addEventListener('click', () => {
            let a = document.createElement('a');
            a.href = document.querySelector('#qr img').getAttribute('src');
            a.download = 'qr.png';
            a.click();
        });
        /* Download handler */
        document.querySelector('#downloadwifi').addEventListener('click', () => {
            let a = document.createElement('a');
            a.href = document.querySelector('#qrcodewifi img').getAttribute('src');
            a.download = 'qrcodewifi.png';
            a.click();
        });

        let generate_qr = () => {
            let qr_url = <?= json_encode($data->store->full_url . '?referrer=qr') ?>;

            let mode = 0;
            let mode_size = 0.1;

            switch(document.querySelector('#type').value) {
                case 'normal':
                    mode = 0;
                    break;

                case 'text':
                    mode = 2;
                    mode_size = parseFloat(document.querySelector('#text_size').value)
                    break;

                case 'image':
                    mode = 4;
                    mode_size = parseFloat(document.querySelector('#image_size').value)
                    break;
            }

            let default_options = {
                // render method: 'canvas', 'image' or 'div'
                render: 'image',

                // version range somewhere in 1 .. 40
                minVersion: 1,
                maxVersion: 40,

                // error correction level: 'L', 'M', 'Q' or 'H'
                ecLevel: document.querySelector('#ecc').value,

                // offset in pixel if drawn onto existing canvas
                left: 0,
                top: 0,

                // size in pixel
                size: 1000,

                // code color or image element
                fill: document.querySelector('#foreground_color').value,

                // background color or image element, null for transparent background
                background: document.querySelector('#background_color').value,

                // content
                text: qr_url,

                // corner radius relative to module width: 0.0 .. 0.5
                radius: document.querySelector('#corner_radius').value,

                // quiet zone in modules
                quiet: 0,

                // modes
                // 0: normal
                // 1: label strip
                // 2: label box
                // 3: image strip
                // 4: image box
                mode: mode,

                mSize: mode_size,
                mPosX: 0.5,
                mPosY: 0.5,

                label: document.querySelector('#text').value,
                fontname: 'arial',
                fontcolor: document.querySelector('#text_color').value,

                image: document.querySelector('#image-buffer')
            };

            /* Delete already existing image generated */
            document.querySelector('#qr img') && document.querySelector('#qr img').remove();

            $('#qr').qrcode(default_options);
        }

        generate_qr();

        /* Corner radius */
        document.querySelector('#corner_radius').addEventListener('change', generate_qr);

        /* Type */
        document.querySelector('#type').addEventListener('change', event => {
            let type = document.querySelector('#type').value;

            switch(type) {
                case 'normal':
                    document.querySelector('#type_text').classList.add('d-none');
                    document.querySelector('#type_image').classList.add('d-none');
                    break;

                case 'text':
                    document.querySelector('#type_text').classList.remove('d-none');
                    document.querySelector('#type_image').classList.add('d-none')
                    break;

                case 'image':
                    document.querySelector('#type_text').classList.add('d-none');
                    document.querySelector('#type_image').classList.remove('d-none')
                    break;
            }

            generate_qr();

        });

        ['foreground_color', 'background_color', 'text', 'text_size', 'text_color', 'corner_radius'].forEach(name => {
            document.querySelector(`input[name="${name}"]`).addEventListener('change', generate_qr);
        })

        /* Ecc */
        document.querySelector(`select[name="ecc"]`).addEventListener('change', generate_qr);

        /* Image */
        document.querySelector('#image').addEventListener('change', () => {
            const input = document.querySelector('#image');

            if(input.files && input.files[0]) {
                const reader = new window.FileReader();

                reader.onload = event => {
                    document.querySelector('#image-buffer').setAttribute('src', event.target.result);

                    setTimeout(generate_qr, 250);
                };

                reader.readAsDataURL(input.files[0]);
            }
        });

        /* Image size */
        document.querySelector('#image_size').addEventListener('change', generate_qr);

    </script>
<?php \Altum\Event::add_content(ob_get_clean(), 'javascript') ?>

<?php \Altum\Event::add_content(include_view(THEME_PATH . 'views/partials/universal_delete_modal_form.php', [
    'name' => 'store',
    'resource_id' => 'store_id',
    'has_dynamic_resource_name' => true,
    'path' => 'store/delete'
]), 'modals'); ?>


