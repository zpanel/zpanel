<?php
##############################################################################
# ZPanel - A free to use hositng control panel for Microsoft(R) Windows(TM)  #
# Copyright (C) Bobby Allen  & The ZPanel Development team, 2009-Present     #
# Email: ballen@zpanel.co.uk                                                 #
# Website: http://www.zpanel.co.uk                                           #
# -------------------------------------------------------------------------- #
# BY USING THIS SOFTWARE/SCRIPT OR ANY FUNCTION PROVIDED IN THE SOURCE CODE  #
# YOU AGREE THAT YOU MUST NOT DO THE FOLLOWING:-                             #
#                                                                            #
#     1) REMOVE THE COPYRIGHT INFOMATION                                     #
#     2) RE-PACKAGE AND/OR RE-BRAND THIS SOFTWARE                            #
#     3) AGREE TO THE FOLLOWING DISCLAIMER...                                #
#                                                                            #
# DISCLAIMER                                                                 #
# -------------------------------------------------------------------------- #
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS        #
# "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED  #
# TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR #
# PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR           #
# CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,      #
# EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,        #
# PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS;#
# OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,   #
# WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR    #
# OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF     #
# ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                 #
##############################################################################
include('inc/zAccountDetails.php');
include('conf/zcnf.php');
include('lang/' .GetPrefdLang($personalinfo['ap_language_vc']). '.php');
if ($_SESSION['zUsername'] == 'zadmin'){

$speedarray = array('-1', '0', '10240', '20480', '30720', '40960', '51200', '102400', '153600', '204800', '256000', '307200', '358400', '409600', '512000', '614400', '716800', '819200', '921600', '1048576');
$filearray = array('0', '500', '1024', '5120', '10240', '51200', '102400');
$connectionarray = array('0', '10', '25', '50', '75', '100', '150', '200', '250', '300', '400', '500', '1000', '2000', '3000', '4000', '5000', '10000');
?>

<a href="#" onclick="toggle_visibility('faq');"><img src="modules/admin/bandwidth_throttle/help_large.png" border="0" /></a> <?php echo $lang['419']."<br>"; ?>
<br>
<div class="zannouce" id="faq" style="display:none;">
<table class="none" cellpadding="0" cellspacing="0">
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['403']; ?></b></td><td> <?php echo $lang['412']; ?></td>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['404']; ?></b></td><td> <?php echo $lang['413']; ?><br><br></td>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['405']; ?></b></td><td> <?php echo $lang['414']; ?><br><br></td>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['406']; ?></b></td><td> <?php echo $lang['415']; ?><br><br></td>
</tr>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['407']; ?></b></td><td> <?php echo $lang['416']; ?><br><br></td>
</tr>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['408']; ?></b></td><td> <?php echo $lang['417']; ?><br><br></td>
</tr>
</tr>
<tr valign="top">
<td nowrap="nowrap"><b><?php echo $lang['409']; ?></b></td><td> <?php echo $lang['418']; ?><br><br></td>
</tr>
</table>
   
</div>

<?php
if((isset($_GET['r'])) && ($_GET['r']=='ok')){
echo "<br><div class=\"zannouce\">".$lang['400']."</div>";
}

# Get a list of the packages.
# If zadmin then get all packages
if ($_SESSION['zUsername'] == 'zadmin'){
$sql = "SELECT * FROM z_packages WHERE pk_deleted_ts IS NULL";
} else {
# If not zadmin, only get packages under user account
$sql = "SELECT * FROM z_packages WHERE pk_reseller_fk=" .$useraccount['ac_id_pk']. " AND pk_deleted_ts IS NULL";
}
$listpackages = DataExchange("r",$z_db_name,$sql);
$rowpackages = mysql_fetch_assoc($listpackages);
$totalpackages = DataExchange("t",$z_db_name,$sql);

# Get bandwidth throttle information.
$sql = "SELECT * FROM z_throttle WHERE tr_id_pk ='1'";
$listthrottle = DataExchange("r",$z_db_name,$sql);
$rowthrottle = mysql_fetch_assoc($listthrottle);
$totalthrottle = DataExchange("t",$z_db_name,$sql);
?>

<h2><?php echo $lang['401']; ?></h2>
<form id="frmZBT" name="frmZBT" method="post" action="runner.php?load=obj_modbw">
  <table class="zgrid">
    <tr>
      <th><?php echo $lang['77']; ?></th>
      <td>
	  <select id="inPackage" name="inPackage">
	  <?php do{ ?>
	  <?php echo '<option value="'.Cleaner('o',$rowpackages['pk_id_pk']).'">'.Cleaner('o',$rowpackages['pk_name_vc']).'</option>'; ?>
	  <?php } while ($rowpackages = mysql_fetch_assoc($listpackages)); ?>
	  </select>
	  </td>
      <td><input type="submit" name="inSelect" id="inSelect" value="<?php echo $lang['330']; ?>" /></td>
    </tr>
  </table>
  <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
  <input type="hidden" name="inAction" value="GetPackage" />
</form>


<?php
if((isset($_GET['package'])) && ($_GET['package']!='')){
$inPackage = $_GET['package'];
# Edit or update package bandwidth quotas
# Get package bandwidth quotas information.
$sql = "SELECT * FROM z_quotas LEFT JOIN z_packages ON z_quotas.qt_package_fk=z_packages.pk_id_pk WHERE qt_package_fk ='". $inPackage ."'";
$listpackage = DataExchange("r",$z_db_name,$sql);
$rowpackage = mysql_fetch_assoc($listpackage);
$totalpackage = DataExchange("t",$z_db_name,$sql);
?>
<br><h2><?php echo $lang['410'] ." ". $rowpackage['pk_name_vc']; ?></h2>
<form id="frmZBT" name="frmZBT" method="post" action="runner.php?load=obj_modbw">
  <table class="zgrid">
    <tr>
      <th><?php echo $lang['403']; ?></th>
	  <td>
	  <select id="inUseBT" name="inUseBT" style="width:150px">
	  <option value="1" <?php if ($rowpackage['qt_bwenabled_in'] == 1){echo "SELECTED";} ?>><?php echo $lang['285']; ?></option>
	  <option value="0" <?php if ($rowpackage['qt_bwenabled_in'] == 0){echo "SELECTED";} ?>><?php echo $lang['286']; ?></option>
	  </select>
	  </td>
	</tr>
	<tr>
      <th><?php echo $lang['404']; ?></th>
	  <td>
	  <select id="inMaxBW" name="inMaxBW" style="width:150px">
	  <?php foreach ($speedarray as $speed){
	  	if ($speed != "-1"){
	  		echo '<option	value="'.$speed.'" ';
	  		if ($rowpackage['qt_totalbw_fk'] == $speed){echo "SELECTED";}
	  		echo '>'.GetSpeedModBW($speed).'</option>';
		}
	  } ?>										
	  </select>
	</td>
	</tr>
	<tr>
      <th><?php echo $lang['405']; ?></th>
	  <td>
	  <select id="inMinBW" name="inMinBW" style="width:150px">
	  <?php foreach ($speedarray as $speed){
	  echo '<option	value="'.$speed.'" ';
	  if ($rowpackage['qt_minbw_fk'] == $speed){echo "SELECTED";}
	  echo '>'.GetSpeedModBW($speed).'</option>';
	  } ?>										
	  </select>
	  </td>
	</tr>
	<tr>
      <th><?php echo $lang['406']; ?></th>
	  <td><select id="inMaxCon" name="inMaxCon" style="width:150px">
	  <?php foreach ($connectionarray as $connectionlimit){
	  echo '<option	value="'.$connectionlimit.'" ';
	  if ($rowpackage['qt_maxcon_fk'] == $connectionlimit){echo "SELECTED";}
	  echo '>'.GetConnectionBW($connectionlimit).'</option>';
	  } ?>										
	  </select>
	  </td>
	</tr>
	<tr>
      <th><?php echo $lang['407']; ?></th>
	  <td>
	  <select id="inUseFT" name="inUseFT" style="width:150px">
	  <option	value="1" <?php if ($rowpackage['qt_dlenabled_in'] == 1){echo "SELECTED";} ?>><?php echo $lang['285']; ?></option>
	  <option value="0" <?php if ($rowpackage['qt_dlenabled_in'] == 0){echo "SELECTED";} ?>><?php echo $lang['286']; ?></option>
	  </select>
	  </td>
	</tr>
	<tr>
	  <th><?php echo $lang['408']; ?></th><td>
	  <select id="inDLsize" name="inDLsize" style="width:150px">
	  <?php foreach ($filearray as $size){
	  echo '<option	value="'.$size.'" ';
	  if ($rowpackage['qt_filesize_fk'] == $size){echo "SELECTED";}
	  echo '>'.GetFileSizeModBW($size).'</option>';
	  } ?>	
	  </select>
	  </td>
	</tr>
	<tr>
	  <th><?php echo $lang['409']; ?></th><td>
	  <select id="inDLspeed" name="inDLspeed" style="width:150px">
<?php foreach ($speedarray as $speed){
	  	if ($speed != "-1"){
	  	echo '<option	value="'.$speed.'" ';
	 	 if ($rowpackage['qt_filespeed_fk'] == $speed){echo "SELECTED";}
	  	echo '>'.GetSpeedModBW($speed).'</option>';
	  	}
	  } ?>	
	  </select>
	  </td>
	</tr>
	<tr>
      <th><input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th><td></td>
    </tr>
  </table>
  <input type="hidden" name="inPackage" value="<?php echo $inPackage; ?>" />
  <input type="hidden" name="inQuotaID" value="<?php echo $rowpackage['qt_id_pk']; ?>" />
  <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
  <input type="hidden" name="inAction" value="EditPackage" />
</form>
<?php
}else{
# List default settings for new packages
?>
<br><h2><?php echo $lang['402']; ?></h2>
<form id="frmZBT" name="frmZBT" method="post" action="runner.php?load=obj_modbw">
  <table class="zgrid">
    <tr>
      <th><?php echo $lang['403']; ?></th>
	  <td>
	  <select id="inUseBT" name="inUseBT" style="width:150px">
	  <option value="1" <?php if ($rowthrottle['tr_bwenabled_in'] == 1){echo "SELECTED";} ?>><?php echo $lang['285']; ?></option>
	  <option value="0" <?php if ($rowthrottle['tr_bwenabled_in'] == 0){echo "SELECTED";} ?>><?php echo $lang['286']; ?></option>
	  </select>
	  </td>
	</tr>
	<tr>
      <th><?php echo $lang['404']; ?></th>
	  <td>
	  <select id="inMaxBW" name="inMaxBW" style="width:150px">
	  <?php foreach ($speedarray as $speed){
	  	if ($speed != "-1"){
	  		echo '<option	value="'.$speed.'" ';
	  		if ($rowpackage['qt_totalbw_fk'] == $speed){echo "SELECTED";}
	  		echo '>'.GetSpeedModBW($speed).'</option>';
		}
	  } ?>									
	  </select>
	</td>
	</tr>
	<tr>
      <th><?php echo $lang['405']; ?></th>
	  <td>
	  <select id="inMinBW" name="inMinBW" style="width:150px">
	  <?php foreach ($speedarray as $speed){
	  echo '<option	value="'.$speed.'" ';
	  if ($rowthrottle['tr_minbw_fk'] == $speed){echo "SELECTED";}
	  echo '>'.GetSpeedModBW($speed).'</option>';
	  } ?>										
	  </select>
	  </td>
	</tr>
	<tr>
      <th><?php echo $lang['406']; ?></th>
	  <td><select id="inMaxCon" name="inMaxCon" style="width:150px">
	  <?php foreach ($connectionarray as $connectionlimit){
	  echo '<option	value="'.$connectionlimit.'" ';
	  if ($rowthrottle['tr_maxcon_fk'] == $connectionlimit){echo "SELECTED";}
	  echo '>'.GetConnectionBW($connectionlimit).'</option>';
	  } ?>										
	  </select></td>
	</tr>
	<tr>
      <th><?php echo $lang['407']; ?></th>
	  <td>
	  <select id="inUseFT" name="inUseFT" style="width:150px">
	  <option	value="1" <?php if ($rowthrottle['tr_dlenabled_in'] == 1){echo "SELECTED";} ?>><?php echo $lang['285']; ?></option>
	  <option value="0" <?php if ($rowthrottle['tr_dlenabled_in'] == 0){echo "SELECTED";} ?>><?php echo $lang['286']; ?></option>
	  </select>
	  </td>
	</tr>
	<tr>
	  <th><?php echo $lang['408']; ?></th><td>
	  <select id="inDLsize" name="inDLsize" style="width:150px">
	  <?php foreach ($filearray as $size){
	  echo '<option	value="'.$size.'" ';
	  if ($rowthrottle['tr_filesize_fk'] == $size){echo "SELECTED";}
	  echo '>'.GetFileSizeModBW($size).'</option>';
	  } ?>	
	  </select>
	  </td>
	</tr>
	<tr>
	  <th><?php echo $lang['409']; ?></th><td>
	  <select id="inDLspeed" name="inDLspeed" style="width:150px">
<?php foreach ($speedarray as $speed){
	  	if ($speed != "-1"){
	  		echo '<option	value="'.$speed.'" ';
	  		if ($rowthrottle['tr_filespeed_fk'] == $speed){echo "SELECTED";}
	  		echo '>'.GetSpeedModBW($speed).'</option>';
	  	}
	  } ?>	
	  </select>
	  </td>
	</tr>
	<tr>
      <th><input type="submit" name="inSubmit" id="inSubmit" value="<?php echo $lang['101']; ?>" /></th><td></td>
    </tr>
  </table>
  <input type="hidden" name="inReturn" value="<?php echo GetFullURL(); ?>" />
  <input type="hidden" name="inAction" value="Default" />
</form>

<?php
}

# Packages using bandwidth throttle
# Get the packages.
$sql = "SELECT * FROM z_quotas LEFT JOIN z_packages ON z_quotas.qt_package_fk=z_packages.pk_id_pk WHERE z_quotas.qt_bwenabled_in  ='1' AND z_packages.pk_deleted_ts IS NULL";
$listpackages = DataExchange("r",$z_db_name,$sql);
$rowpackages = mysql_fetch_assoc($listpackages);
$totalpackages = DataExchange("t",$z_db_name,$sql);
if ($rowpackages['qt_dlenabled_in'] == 1){$dl = "Yes";} else{$dl = "No";}
if ($totalpackages <> 0){
?>
<br><h2><?php echo $lang['411']; ?></h2>
  <table class="zgrid">
    <tr>
      <th>Package</th><th>Max BW</th><th>Min BW</th><th>Connections</th><th>Downloads</th><th>File Size</th><th>DL Speed</th>
	</tr>
	  <?php do{ ?>
	<tr>
      <td><?php echo Cleaner('o',$rowpackages['pk_name_vc']); ?></td>
	  <td><?php echo GetSpeedModBW(Cleaner('o',$rowpackages['qt_totalbw_fk'])); ?></td>
	  <td><?php echo GetSpeedModBW(Cleaner('o',$rowpackages['qt_minbw_fk'])); ?></td>
	  <td><?php echo Cleaner('o',$rowpackages['qt_maxcon_fk']); ?></td>
	  <td><?php echo $dl; ?></td>
	  <td><?php echo GetFileSizeModBW(Cleaner('o',$rowpackages['qt_filesize_fk'])); ?></td>
	  <td><?php echo GetSpeedModBW(Cleaner('o',$rowpackages['qt_filespeed_fk'])); ?></td>
    </tr>
	  <?php } while ($rowpackages = mysql_fetch_assoc($listpackages)); ?>
  </table>
<?php
}
?>
<?php
} else {
echo $lang['420'];
}
?>

<?php
# Module functions
function GetSpeedModBW($speed){
if ($speed == "-1"){$speed = "Max for Each";}
if ($speed == "0"){$speed = "No Limit";}
if ($speed == "10240"){$speed = "10 kbs";}
if ($speed == "20480"){$speed = "20 kbs";}
if ($speed == "30720"){$speed = "30 kbs";}
if ($speed == "40960"){$speed = "40 kbs";}
if ($speed == "51200"){$speed = "50 kbs";}
if ($speed == "102400"){$speed = "100 Kbs";}
if ($speed == "153600"){$speed = "150 Kbs";}
if ($speed == "204800"){$speed = "200 Kbs";}
if ($speed == "256000"){$speed = "250 Kbs";}
if ($speed == "307200"){$speed = "300 Kbs";}
if ($speed == "358400"){$speed = "350 Kbs";}
if ($speed == "409600"){$speed = "400 Kbs";}
if ($speed == "512000"){$speed = "500 Kbs";}
if ($speed == "614400"){$speed = "600 Kbs";}
if ($speed == "716800"){$speed = "700 Kbs";}
if ($speed == "819200"){$speed = "800 Kbs";}
if ($speed == "921600"){$speed = "900 Kbs";}
if ($speed == "1048576"){$speed = "1 Mbs";}
return $speed;
}

function GetFileSizeModBW($size){
if ($size == "0"){$size = "Any Size";}
if ($size == "500"){$size = ".5 MB";}
if ($size == "1024"){$size = "1 MB";}
if ($size == "5120"){$size = "5 MB";}
if ($size == "10240"){$size = "10 MB";}
if ($size == "51200"){$size = "50 MB";}
if ($size == "102400"){$size = "100 MB";}
return $size;
}

function GetConnectionBW($connection){
if ($connection == "-1"){$connection = "Max for Each";}
if ($connection == "0"){$connection = "No Limit";}
return $connection;
}
?>

<script type="text/javascript">
    <!--
    function toggle_visibility(id) {
        var e = document.getElementById(id);
        if(e.style.display == 'none')
            e.style.display = 'block';
        else
            e.style.display = 'none';
    }
    //-->
</script>

