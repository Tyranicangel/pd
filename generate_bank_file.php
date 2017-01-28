<?php
	$db1= pg_connect("host=localhost dbname=pdac user=postgres password=noobtard123") or die('Could not connect:'.pg_last_error());
	if($user_id=='chqapao')
        {
                $branch='MAIN';
		$remm_addr="STATE BANK OF INDIA MAIN OFFICE";
		$remm_acno="33881456443";
        }
        else
        {
                $branch='KBLOCK';
		$remm_addr="STATE BANK OF INDIA SECRETARIAT";
		$remm_acno="33881454741";
        }
		$space7="       ";
        $space8="                          ";
        $space8Char="        ";
        $space4Char = "    ";
	$fname="bank_neft_MAIN_".$c_date.'_'.$c_time.".txt";
	$fname2="sbi_neft_MAIN_".$c_date.'_'.$c_time.".txt";
	$fname3="temp_bank_neft_MAIN_".$c_date."_".$c_time.".txt";
	$fname4="temp_sbi_neft_MAIN_".$c_date."_".$c_time.".txt";
	$path=$fname;
	$path2=$fname2;
	$path3=$fname3;
	$path4=$fname4;
	$fp=fopen($path, 'w');
	$fp3=fopen($path3, 'w');
	$fp4=fopen($path4, 'w');
	$write_text='';
	$write_text2="";
	$write_text3="";
	$write_text4="";
	$cqno=$_POST['chq'];
	$neft = "NEFT";
	$totalNet=0;
	$totalNet2=0;
	$currentDateExp = explode("-", $c_date);
	$currentDate = $currentDateExp[2]."/".$currentDateExp[1]."/".$currentDateExp[0];
//        $query = pg_query("SELECT * FROM chq_section WHERE chequeno='000185'");
	$query=pg_query("SELECT * FROM chq_section WHERE printflag='0' AND branch='$branch'");

	$qqq=pg_query("UPDATE chq_section SET chequeno='$cqno',printflag='1',chqprepdate='$c_date' WHERE printflag='0' AND branch='$branch'");
	$result=pg_fetch_all($query);
	$nonsbin = 0;
	for($i=0;$i<count($result);$i++)
        {
		$tkno=bigintval($result[$i]['transid']);
	        $qcheck=pg_query("SELECT * FROM party_table WHERE tokenno='$tkno'");
                while($rcheck=pg_fetch_array($qcheck,null,PGSQL_ASSOC))
                {
			$bankcode=strtoupper($rcheck['ifsccode']);

			if(substr($bankcode,0,4)!='SBIN') {
 				
				$nonsbin++;			
								
			}

		}

	}
	//adding 2 because the first line of the SBI bank file will contain this number+1
	$sbin = $nonsbin+3;
	//making the total non sbi count to 6 digit as we are adding these 6 digit at the end of chq number
	$sbin = str_pad($sbin,6,'0',STR_PAD_LEFT);
	//starting with '2' because this is from second line
	$twelveChar = bigintval($cqno."000002");
        $twelveChar2 = bigintval($cqno.$sbin);

	for($i=0;$i<count($result);$i++)
	{
		$tkno=bigintval($result[$i]['transid']);
		$chqNo = "CHEQUENO".$cqno;
		$chqNo25 = str_pad($chqNo,25," ");
		$qq=pg_fetch_array(pg_query("SELECT * FROM npayments WHERE transid='$tkno'"),null,PGSQL_ASSOC);
		$ddo=$qq['ddocode'];
		$q2=pg_fetch_array(pg_query("SELECT * FROM mddo WHERE ddocode='$ddo'"),null,PGSQL_ASSOC);
		$q1=pg_query("SELECT * FROM party_table WHERE tokenno='$tkno'");
		while($r1=pg_fetch_array($q1,null,PGSQL_ASSOC))
		{
			$bacno=strtoupper($r1['bankacno']);
			$benfacno=$bacno;
			$dedn=$qq['dedn'];
			$amt=str_pad($dedn,14,0,STR_PAD_LEFT);
			$amtNew=str_pad($dedn,13," ",STR_PAD_LEFT);
			$amtNew = $amtNew.".00";
			$tranamt1=str_pad($r1['net'],14,0,STR_PAD_LEFT);
			$tranamtNew=str_pad($r1['net'],13," ",STR_PAD_LEFT);
			$tranamtNew = $tranamtNew.".00";
			$commamt="0";
			$commamt1=str_pad($commamt,20,0,STR_PAD_LEFT);
			if($benfacno == '004301601000076' || $benfacno == '004400201005095' || $benfacno == '000805002144' || $benfacno == '06112000004047' || $benfacno == '03172560000175' || $benfacno == '0538686004' || $benfacno == '002102000032841'){
			$benfname=strtoupper(substr(trim($q2['ddodesg']),0,30));
			$chqNo = trim($q2['ddodesg']);
			$chqNo25 = str_pad($chqNo, 25, " ");
			}
			else {
			$benfname=strtoupper(substr(trim($r1['partyname']),0,30));
			}
			$bankcode=strtoupper($r1['ifsccode']);
			$msgtype="R41";
			$remm_acno1=str_pad($remm_acno,17,0,STR_PAD_LEFT);
			$remm_name="PAO GOVT OF AP Hyderabad salary";
			$remm_name1=str_pad($remm_name,35,' ',STR_PAD_RIGHT);//Remitters A/c
			$remm_addr1=str_pad($remm_addr,35,' ',STR_PAD_RIGHT);//Remitters Address
			$benfacno1=str_pad($benfacno,25,' ',STR_PAD_RIGHT);//Beneficiary A/c
			$benfacno2=substr($benfacno1,0,16);//Beneficiary A/c2
			$benfacno3=str_pad($benfacno,17," ");
			$benfacnosbi = str_pad($benfacno,17,'0', STR_PAD_LEFT);
			$space7="       ";
			$space8="                          ";
			$space8Char="        ";
			$space4Char = "    ";
			$benfname1=str_pad($benfname,35,' ',STR_PAD_RIGHT);//Beneficiary Name
			$benf_addr=strtoupper(substr($q2['ddodesg'],0,34));//
			$benf_addr = preg_replace('/[\/_]/', '', $benf_addr);
			$benf_addr1=str_pad($benf_addr,34,' ',STR_PAD_RIGHT);//Beneficiary Address
			$ifsccode="ANDB0001103";//rECEIVER-BANK-IFSC-CODE
			$det_pay="110310027500128";
			$det_pay1=str_pad($det_pay,35,' ',STR_PAD_LEFT);//details of payment
			$send_recv_code="   URGENT";//value=ATTN /FAST/URGENT/DETAIL/NRE
			$send_recv_code1=str_pad($send_recv_code,11,' ',STR_PAD_RIGHT);//sender-to-receiver-code
			$send_recv_info=$det_pay;
			$send_recv_info1=str_pad($send_recv_info,25,' ',STR_PAD_RIGHT);//sender-to-receiver-info
			$user_refno=$det_pay;
			$user_refno1=str_pad($user_refno,14,' ',STR_PAD_RIGHT);//UESER-REF-NO
			$email="EMLdpao-sect@ap.nic.in";
			$email1=str_pad($email,35,' ',STR_PAD_RIGHT);//Email-Id
			$subbenfname=substr($benfname1,0,25);
			$bankcode1=str_pad($bankcode,12,' ',STR_PAD_LEFT);
			$bankcodeNew=str_pad($bankcode,11,' ',STR_PAD_LEFT);
			if(substr($bankcode,0,4)=='SBIN')
			{
				 $write_text2=$write_text2."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$space7$space8$benfacno2$email1\n";
				
				$twelveCharNew2 = str_pad($twelveChar2,  12, '0', STR_PAD_LEFT);

				$write_text4=$write_text4."$benfacnosbi$bankcodeNew$currentDate $space8Char$space8Char$tranamtNew$twelveCharNew2$chqNo25$space4Char\n";
				$totalNet2 = $totalNet2+$r1['net'];
				$twelveChar2++;
			}
			else
			{
				
				$write_text=$write_text."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$space7$space8$benfacno2$email1\n";

				$twelveCharNew = str_pad($twelveChar,  12, '0', STR_PAD_LEFT);
         			if(substr($bankcode,0,2)=='SB' || substr($bankcode,0,2)=='ST') {
			           $benfacno3=str_pad($benfacno,17,"0", STR_PAD_LEFT);
				}

				$write_text3=$write_text3."$benfacno3$bankcodeNew$currentDate $space8Char$space8Char$tranamtNew$twelveCharNew$chqNo25$neft\n";

				$twelveChar++;

				//adding all the net amounts
				$totalNet = $totalNet+$r1['net'];
			}
		}
		if($qq['dedn']!='0')

		{
			$bacno=strtoupper($q2['bankacno']);
			$benfacno=$bacno;
			$benfacno1=str_pad($benfacno,25,' ',STR_PAD_RIGHT);//Beneficiary A/c
			$benfacno2=substr($benfacno1,0,16);//Beneficiary A/c2
			$benfacnosbi = str_pad($benfacno,17,'0', STR_PAD_LEFT);
			$benfname=strtoupper(substr(trim($q2['ddodesg']),0,30));
			$benfname1=str_pad($benfname,35,' ',STR_PAD_RIGHT);//Beneficiary Name
			$bankcode=strtoupper($q2['ifsccode']);
			$bankcode1=str_pad($bankcode,12,' ',STR_PAD_LEFT);
			$bankcodeNew=str_pad($bankcode,11,' ',STR_PAD_LEFT);
			$subbenfname=substr($benfname1,0,25);
			$write_text2=$write_text2."$msgtype$amt$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$space7$space8$benfacno2$email1\n";

			$twelveCharNew2 = str_pad($twelveChar2,  12, '0', STR_PAD_LEFT);

			$write_text4=$write_text4."$benfacnosbi$bankcodeNew$currentDate $space8Char$space8Char$amtNew$twelveCharNew2$chqNo25$space4Char\n";
                                $totalNet2 = $totalNet2+$qq['dedn'];
                                $twelveChar2++;


		}
	}
	fwrite($fp,$write_text);
	fclose($fp);
	$fp2=fopen($path2, 'w');
	fwrite($fp2,$write_text2);
        fclose($fp2);
	$totalNet = $totalNet.".00";
	$totalNet = str_pad($totalNet,16,' ',STR_PAD_LEFT);
	$totalNet2 = $totalNet2.".00";
        $totalNet2 = str_pad($totalNet2,16,' ',STR_PAD_LEFT);

	$startingSerial = $cqno."000001";
	$write_text31 = "00000033881456443      02724$currentDate $totalNet$space8Char$space8Char$startingSerial$chqNo25$neft\n";
	$write_text3 = $write_text31.$write_text3;
	
	$nonsbiNew = $nonsbin + 2;
	$nonsbiNew = str_pad($nonsbiNew,6,'0',STR_PAD_LEFT);
	$startingSerial2 = $cqno.$nonsbiNew;

	$write_text41 = "00000033881456443      02724$currentDate $totalNet2$space8Char$space8Char$startingSerial2$chqNo25$space4Char\n";	
	$write_text4 = $write_text41.$write_text4;	
	fwrite($fp3,$write_text3);
	fwrite($fp4,$write_text4);
	fclose($fp3);
	echo json_encode(array($fname,$fname2,$fname3,$fname4));

?>
