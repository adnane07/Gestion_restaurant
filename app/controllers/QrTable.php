<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Title;

class QrTable extends Controller {

    public function index() {

        \Altum\Authentication::guard();

       // if(!$this->user->plan_settings->qr_is_enabled) {
         //   Alerts::add_info(l('global.info_message.plan_feature_no_access'));
           // redirect('dashboard');
        //}

        $store_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$store = db()->where('store_id', $store_id)->where('user_id', $this->user->user_id)->getOne('stores')) {
            redirect('dashboard');
        }

        /* Generate the store full URL base */
        $store->full_url = (new \Altum\Models\Store())->get_store_full_url($store, $this->user);

        /* Establish the account sub menu view */
        $data = [
            'store_id' => $store->store_id,
            'resource_name' => $store->name,
            'external_url' => $store->full_url
        ];
        $app_sub_menu = new \Altum\View('partials/app_sub_menu', (array) $this);
        $this->add_view_content('app_sub_menu', $app_sub_menu->run($data));

        /* Set a custom title */
        Title::set(sprintf(l('store_qr.title'), $store->name));

        /* Prepare the View */
        $data = [
            'store' => $store,
            'order' => $order
        ];

        $view = new \Altum\View('qr-table/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function insert() {


       
             $servername = "localhost";
             $username = "muhannad1_User";
             $password = "[M-bm7l\$xLtE";
             $dbname = "muhannad1_Database";
        
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
       
             
             
           // Taking all 3 values from the form data(input)
               $table_number =  $_POST['table_number'];
               $table_person = $_POST['table_person'];
               $id_principale = $this->user->user_id;
            
               // Performing insert query execution
               // here our table name is college
               $sql = "INSERT INTO tables  VALUES (NULL , $table_number,$table_person,$id_principale)";
               mysqli_query($conn, $sql);
                 
                 mysqli_close($conn);
            
            redirect('qr-table/' . $id_principale);
           }

           public function delete() {

      
             $servername = "localhost";
             $username = "muhannad1_User";
             $password = "[M-bm7l\$xLtE";
             $dbname = "muhannad1_Database";
        
        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);
       
             
             
           // Taking all 3 values from the form data(input)
               $table_id =  $_POST['table_id'];

               $id_principale = $this->user->user_id;
            
               // Performing insert query execution
               // here our table name is college
               $sql = "DELETE FROM tables WHERE id = $table_id";
               mysqli_query($conn, $sql);
                 
                 mysqli_close($conn);
            
            redirect('qr-table/' . $id_principale);
        }
       }
