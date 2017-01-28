<?php

$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());

if(!isset($_POST['ddocode']) || !isset($_POST['hoa'])) {

echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >
DDOCODE: <input type='text' name='ddocode' /> &nbsp; &nbsp;
HOA: <input type='text' name='hoa' /> &nbsp; &nbsp;
<button  type='submit'>SUBMIT</button>
</form>";

} else {

echo "<form method='post' action='".$_SERVER['PHP_SELF']."' >
DDOCODE: <input type='text' name='ddocode' /> &nbsp; &nbsp;
HOA: <input type='text' name='hoa' /> &nbsp; &nbsp;
<button  type='submit'>SUBMIT</button>
</form>
<script type='text/javascript' src='front/scripts/jquery.js'></script>
<script>
$(document).ready(function(){
$('#updatepdacinfo').click(function(){

        $.ajax({

                type:'POST',

                url:'update_pdaccountinfo_action.php',

                data:{ddocode:$('#ddocode').val(), hoa:$('#hoa').val(), obal:$('#obal').val(),bal:$('#bal').val(),loc:$('#loc').val(),status:$('#status').val(),transitamount:$('#transitamount').val()},

                success:function(result)

                {   
                        $('.npaymentBox').html('');
                        alert(result);
                }

        });


});
});
</script>
";

$ddocode = $_POST['ddocode'];
$hoa = $_POST['hoa'];
$result = pg_query("SELECT * FROM pdaccountinfo WHERE ddocode='$ddocode' AND hoa='$hoa' ");
if(pg_num_rows($result) != 0) {
$row = pg_fetch_array($result);
echo "<div class='npaymentBox'>
DDO: <input type='text' name='ddocode' id='ddocode' value='".$row['ddocode']."' /><br>
HOA: <input type='text' name='hoa' id='hoa' value='".$row['hoa']."' /><br>
Opening balance: <input type='text' name='obal' id='obal' value='".$row['obalance']."' /><br>
Balance: <input type='text' name='bal' id='bal' value='".$row['balance']."' /><br>
LOC: <input type='number' name='loc' id='loc' value='".$row['loc']."' /><br>
Transitamount: <input type='number' name='transitamount' id='transitamount' value='".$row['transitamount']."' /><br>
Status: <input type='number' name='status' id='status' value='".$row['status']."' /><br>
<button id='updatepdacinfo'>UPDATE</button>
</div>
";


} else {

echo "<h2>This token no doesnot exist</h2>";


}

}

?>
