<?php defined('ALTUMCODE') || die() ?>

<?php 
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
               $sql = "SELECT * from tables where user_id = $id_principale";
               $tables = mysqli_query($conn, $sql);
                 
                 mysqli_close($conn); ?>

<div class="container">
    <?= \Altum\Alerts::output_alerts() ?>

    <div class="card border-primary  w-95" >
            <div class="card-header"><h4><?= l('add_table.title') ?></h4></div>
            <div class="card-body ">
                    <form action="<?= url('qr-table/insert') ?> " method="post">
                    <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />

                        <div class="row">
                            <div class="col-lg-6 col-6">
                                <label for="" class="text-primary"><?= l('add_table.add') ?></label>
                            </div>
                            <div class="col-lg-6 col-6">
                                <label for="" class="text-primary"><?= l('add_table.number') ?></label>
                            </div>
                        </div>
                        <div id="row_input">
                        <div class="row" >
                            <div class="col-lg-6 col-6" id="col_table">
                                <input type="number" class="form-control table_number" min=1 max=20  name="table_number" required>
                            </div>
                            <div class="col-lg-6 col-6" id="col_number">
                                <input type="number" class="form-control table_person" min=1 max=15  name="table_person" required>
                            </div>
                        </div>
                        </div>
                        <div class="row">
                            <div style="height: 30px;"></div>
                        </div>
                        <div class="row">
                        
                            <div class="col-lg-12 col-12">
                                <input type="submit" id="Button_SaveNewFund" value="<?= l('add_table.save') ?>" class="form-control btn-primary" />
                            </div>
                        </div>
                       
                    </form>

            </div>


    </div> 
    <div class="row" style="height: 30px;"></div>
   <div class="row ">
   <table class="table">
      <thead>
        <tr>
          <th scope="col"><?= l('add_table.add') ?></th>
          <th scope="col"><?= l('add_table.number') ?></th>
          <th scope="col"><?= l('statistics.statistics.referrer_qr') ?></th>
          <th scope="col"><?= l('delete_modal.header') ?></th>
        </tr>
      </thead><tbody>
    <?php if (mysqli_num_rows($tables) > 0) {
  // output data of each row
  while($row = mysqli_fetch_assoc($tables)) {
    echo "
      
        <tr>
          <th scope='row'>" . $row["table_number"]. "</th>
          <td>" . $row["table_person"]. "</td>
          <td> "; ?> 
          <script src="themes/altum/assets/js/qrcode.min.js"></script>  
          <script type="text/javascript" src="http://static.runoob.com/assets/qrcode/qrcode.min.js"></script>
          <?php echo " <div id='qrcode' hidden v-loading='PanoramaInfo.bgenerateing'></div>
                        <button class='btn btn-success' onclick='dow".$row['table_number']."()'>";?><?= l('global.download') ?><?php echo "</button> ";?>


<script>
function downloadURI(uri, name) {
  var link = document.createElement("a");
  link.download = name;
  link.href = uri;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  delete link;
};

function dow<?php echo$row["table_number"]?>()
{
  console.log('onload');
  let qrcode = new QRCode(document.getElementById("qrcode"),
             {
              text: "<?= $data->store->full_url ?>?table=<?php echo$row["table_number"]?>" ,
              width: 250,
              height: 250,
              colorDark : "#000000",
              colorLight : "#ffffff",
              correctLevel : QRCode.CorrectLevel.H
            });  
  setTimeout(
    function ()
    {
        let dataUrl = document.querySelector('#qrcode').querySelector('img').src;
        downloadURI(dataUrl, '<?php echo$row["table_number"]?>qrcode.png');
    }
    ,1000);

};

</script>


<?php echo "</td>
       
          <td>" ;?>
          
          <form method="post" role="form" action="<?= url('qr-table/delete') ?>">
                <input type="hidden" name="token" value="<?= \Altum\Csrf::get() ?>" />
                <input type="hidden" name="table_id" value="<?= $row['id'] ?>" />

                <button type="submit" name="submit" class="btn btn-danger "><?= l('add_table.delete') ?></button>
            </form>
       
       <?php echo " </td>
        </tr>
      ";
  }
}
?></tbody>
    </table>


   </div>
  