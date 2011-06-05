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

# Now we need to declare and cleanup some variables
$acc_fk = $useraccount['ac_id_pk'];
$returnurl = Cleaner('o',$_POST['inReturn']);

# Default settings on newly created packages
if($_POST['inAction']=='Default'){

	$inUseBT   = Cleaner('o',$_POST['inUseBT']);
	$inMaxBW   = Cleaner('o',$_POST['inMaxBW']);
	$inMinBW   = Cleaner('o',$_POST['inMinBW']);
	$inMaxCon  = Cleaner('o',$_POST['inMaxCon']);
	$inUseFT   = Cleaner('o',$_POST['inUseFT']);
	$inDLsize  = Cleaner('o',$_POST['inDLsize']);
	$inDLspeed = Cleaner('o',$_POST['inDLspeed']);
	#$inDLtype  = Cleaner('o',$_POST['inDLtype']);

	# Update Throttle table with default settings for new packages.
	$sql = "UPDATE z_throttle SET tr_bwenabled_in = '".$inUseBT."',
							  	tr_dlenabled_in = '".$inUseFT."',
							 	tr_totalbw_fk   = '".$inMaxBW."',
							  	tr_minbw_fk     = '".$inMinBW."',
							  	tr_maxcon_fk    = '".$inMaxCon."',
							  	tr_filesize_fk  = '".$inDLsize."',
							  	tr_filespeed_fk = '".$inDLspeed."',
								tr_filetype_vc  = '*'
							  	WHERE tr_id_pk  = '1'";
							  
	DataExchange("w",$z_db_name,$sql);

	# Now we add some infomation to the system log.
	TriggerLog($useraccount['ac_id_pk'], $b="Default Bandwidth Throttle settings updated");
	header("location: " .GetNormalModuleURL($returnurl). "&r=ok");
	exit;
}

# Get the package to update
if($_POST['inAction']=='GetPackage'){
	$inPackage  = Cleaner('o',$_POST['inPackage']);
        if (isset($_POST['inPackage'])) {
            header("location: " . $returnurl . "&r=0&package=" . $inPackage . "");
            exit;
        }
}

# Update package bandwidth quotas
if($_POST['inAction']=='EditPackage'){

	$inUseBT    = Cleaner('o',$_POST['inUseBT']);
	$inMaxBW    = Cleaner('o',$_POST['inMaxBW']);
	$inMinBW    = Cleaner('o',$_POST['inMinBW']);
	$inMaxCon   = Cleaner('o',$_POST['inMaxCon']);
	$inUseFT    = Cleaner('o',$_POST['inUseFT']);
	$inDLsize   = Cleaner('o',$_POST['inDLsize']);
	$inDLspeed  = Cleaner('o',$_POST['inDLspeed']);
	$inQuotaID  = Cleaner('o',$_POST['inQuotaID']);
	$inPackage  = Cleaner('o',$_POST['inPackage']);
	#$inDLtype   = Cleaner('o',$_POST['inDLtype']);

	# Update quota table with new settings.
	$sql = "UPDATE z_quotas SET qt_bwenabled_in = '".$inUseBT."',
								qt_dlenabled_in = '".$inUseFT."',
								qt_totalbw_fk   = '".$inMaxBW."',
								qt_minbw_fk     = '".$inMinBW."',
								qt_maxcon_fk    = '".$inMaxCon."',
								qt_filesize_fk  = '".$inDLsize."',
								qt_filespeed_fk = '".$inDLspeed."',
								qt_filetype_vc  = '".$inDLtype."',
								qt_modified_in  = '1'
								WHERE qt_id_pk  = '".$inQuotaID."'";
							  
	DataExchange("w",$z_db_name,$sql);

	$sql = "SELECT * FROM z_packages WHERE pk_id_pk ='". $inPackage ."'";
	$listpackages = DataExchange("r",$z_db_name,$sql);
	$rowpackages = mysql_fetch_assoc($listpackages);

	# Write the package mod_bw .conf
	if ($inUseBT == 1) {$inUseBT = "On";} else {$inUseBT = "Off";}
	if ($inUseFT == 1) {$inUseFT = "On";} else {$inUseFT = "Off";}
	
	if (!file_exists(GetSystemOption('mod_bw') ."mod_bw/")) {
		mkdir(GetSystemOption('mod_bw') ."mod_bw/", 0777);
			if (ShowServerPlatform() <> "Windows") {
			@chmod(GetSystemOption('mod_bw') ."mod_bw/", 0777);
			}
	}
	
	$file = GetSystemOption('mod_bw') ."mod_bw/mod_bw_" . $rowpackages['pk_name_vc'] . ".conf";
	if ($inUseFT == "On"){
		$body = "BandwidthModule ".$inUseBT."
ForceBandWidthModule ".$inUseBT."
Bandwidth all ".$inMaxBW."
MinBandwidth all ".$inMinBW."
MaxConnection all ".$inMaxCon."
LargeFileLimit * ".$inDLsize." ".$inDLspeed."
BandWidthError 510";
	} else {
		$body = "BandwidthModule ".$inUseBT."
ForceBandWidthModule ".$inUseBT."
Bandwidth all ".$inMaxBW."
MinBandwidth all ".$inMinBW."
MaxConnection all ".$inMaxCon."
BandWidthError 510";
	}

	$fp = @fopen($file,'w');
	fwrite($fp,$body);
	fclose($fp);

	if (ShowServerPlatform() <> "Windows") {
		@chmod($file, 0777);
	}

	# Log the package as modified so the daemon will make changes to vhosts.
	$sql = "UPDATE z_quotas SET qt_modified_in = 1 WHERE qt_id_pk = ". $inQuotaID ."";
	DataExchange("w",$z_db_name,$sql);

	# Now we add some infomation to the system log.
	TriggerLog($useraccount['ac_id_pk'], $b="Bandwidth Throttle settings updated for package id: ". qt_id_pk ." ");
	header("location: " .GetNormalModuleURL($returnurl). "&r=ok");
	exit;
}

?>