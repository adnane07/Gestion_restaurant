<?php

use Abraham\TwitterOAuth\Util\JsonDecoder;
use PayPal\Api\Details;
use PayPal\Api\ItemList;
use phpseclib3\File\ASN1\Maps\Name;

 defined('ALTUMCODE') || die() ?>
<?php
$id_principale = $this->user->user_id;
?>
<div class="container">

<?= \Altum\Alerts::output_alerts() ?>

    <nav aria-label="breadcrumb">
        <ol class="custom-breadcrumbs small">
            <li>
                <a href="<?= url('dashboard') ?>"><?= l('dashboard.breadcrumb') ?></a><i class="fa fa-fw fa-angle-right"></i>
            </li>
            
            <li class="active" aria-current="page"><?= l('client.costumer') ?></li>
        </ol>
    </nav>

<div class="mb-3 d-flex justify-content-between">
        <div>
            <h1 class="h4 text-truncate"><?= l('client.costumer')?></h1>
        </div>
    </div>
   


    <div class="row justify-content-between mb-5">

    <form action="" method="post" role="form">
            <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
    </form>


        <table class="table ">
            <thead class="table-primary">
                 <tr>
                     <th scope="col">#</th>
                     <th scope="col"><?= l('client.name')?></th>
                     <th scope="col"><?= l('client.phone') ?></th>
                     <th scope="col"><?= l('client.total') ?></th>
                     <th scope="col"><?= l('client.orders')?></th>

                 </tr>
             </thead>
             <tbody >
 <?php
             $servername = "localhost";
             $username = "muhannad1_User";
             $password = "[M-bm7l\$xLtE";
             $dbname = "muhannad1_Database";
             
             // Create connection
             $conn = mysqli_connect($servername, $username, $password, $dbname);
           
                   
                    $sqlfinal = "SELECT * FROM orders INNER JOIN users WHERE users.user_id = orders.user_id  and users.user_id = $id_principale";
                    $resultfinal = mysqli_query($conn, $sqlfinal);

                    if (mysqli_num_rows($resultfinal) > 0) {
                        // output data of each row
                            foreach ($resultfinal as $row) {
                        // output data of each row
                        
                                $details = $row['details'];
                                $jsontest = json_decode($details);                                

                                echo "<tr>
                                        <th scope='row'>".$row['order_number']."</th>
                                        <td>".$jsontest->{'name'}."</td>
                                        <td>".$jsontest->{'phone'}."</td>
                                        <td>".$row['price']."</td>
                                        <td>".$row['ordered_items']."</td>
                                        </tr>";
                         } 
                } 
             
             else {
                echo "<span>results 0</span>";
              }
?>
             
            </tbody>
        </table>
    </div>
</div>