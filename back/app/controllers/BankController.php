<?php

class BankController extends BaseController {
	public function bank_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$trans=Transactions::where('transstatus','=',2)->where('issueuser','like',$userid.'%')->get();
				array_push($out,$trans->count());
				// $trans=Transactions::where('transstatus','=',4)->get();
				// array_push($out,$trans->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function bank_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('transstatus','=',2)->with('requser')->orderby('id')->where('issueuser','like',$userid.'%')->get();
				$fout=$request->toArray();
				for($i=0;$i<count($fout);$i++)
				{
					$acinfo=Pdaccount::where('hoa','=',$fout[$i]['hoa'])->where('ddocode','=',$fout[$i]['issueuser'])->first();
					$fout[$i]['acinfo']=$acinfo->toArray();
				}
				$out=json_encode($fout);
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function bank_tran(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('transstatus','=',4)->with('requser')->orderby('id')->get();
				$fout=$request->toArray();
				for($i=0;$i<count($fout);$i++)
				{
					$acinfo=Pdaccount::where('hoa','=',$fout[$i]['hoa'])->where('ddocode','=',$fout[$i]['issueuser'])->first();
					$fout[$i]['acinfo']=$acinfo->toArray();
				}
				$out=json_encode($fout);
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function bank_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$transid=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('id','=',$transid)->where('issueuser','like',$userid.'%')->where('transstatus','=',2)->with('requser')->orderby('id')->first();
				$fout=$request->toArray();
				$acinfo=Pdaccount::where('hoa','=',$fout['hoa'])->where('ddocode','=',$fout['issueuser'])->first();
				$fout['acinfo']=$acinfo->toArray();
				$out=json_encode($fout);
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function bank_tran_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$chq=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('chequeno','=',$chq)->where('issueuser','like',$userid.'%')->where('transstatus','=',4)->with('requser')->orderby('id')->first();
				$fout=$request->toArray();
				$acinfo=Pdaccount::where('hoa','=',$fout['hoa'])->where('ddocode','=',$fout['issueuser'])->first();
				$fout['acinfo']=$acinfo->toArray();
				$out=json_encode($fout);
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function bank_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				$ddocode = Input::get('ddocode');//new line
				array_push($out,'success');
				$userid=$user[0]['userid'];
				foreach($data as $x)
				{
					$trans=Transactions::where('id','=',$x['id'])->where('issueuser','=',$ddocode)->where('transstatus','=',2)->first();
					$hoa=$trans['hoa'];
					$ddo=$trans['issueuser'];
					$amt=$trans['partyamount'];
					$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$ddo)->first();
					//$balance=$account['balance'];
					$transit = $account['transitamount'];  
					$new_transit=$transit-$amt; 

					// $new_bal=$balance-$amt;
					// $loc=$account['loc'];
					// if($account['account_type']==2)
					// {
					// 	$new_loc=$loc-$amt;
					// }
					// else
					// {
					// 	$new_loc=$loc;
					// }
					
					// $date = new DateTime;
					// $trans->confirmdate=$date;
					// $trans->balance=$new_bal;
					// $trans->transstatus=4;
					// $trans->save();
					$date = new DateTime;
					$trans->confirmdate=$date;
					$account->transitamount=$new_transit; // new line
					$trans->transstatus=3;
					$trans->save();
					$account->save();

					$partytext = "";
						$tableheader = "";

						if($trans['multiflag'] == 2) {

							if($trans['pdtopdflag'] == 1) {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							} else {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							}

							$partyfile = "uploads/".$trans['partyfile'];

							$fp=fopen($partyfile,'r');

							$c=1;
							$x =1;

							while($datafile=fgetcsv($fp)){
								if($c==0)
								{

									if($trans['pdtopdflag'] == 1) {

										$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[3].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[4].'</td></tr>';

									} else {

										$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[5].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[6].'</td></tr>';
									}
									$x++;
									
								}
								else
								{
									$c=0;
								}
							}
						} else {

							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

							$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyamount'].'</td></tr>';

						}

					$userd = Users::where('userid', '=', $ddo)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Payment confirmed- PD portal";
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($trans['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "accepted", $message);
					$message = str_ireplace("{{chequeno}}", $trans['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $trans['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $trans['hoa'], $message);
					$message = str_ireplace("{{amount}}", $trans['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $account['balance'], $message);
					$message = str_ireplace("{{locbalance}}", $account['loc'], $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
					$message = str_ireplace("{{byname}}", 'by BANK', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					//sendEmail($to, $subject, $message);
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function bank_chqlist_accept(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				array_push($out,'success');
				$userid=$user[0]['userid'];
				foreach($data as $x)
				{
					$trans=Transactions::where('chequeno','=',$x['chqno'])->where('issueuser','like',$userid.'%')->where('transstatus','=',4)->first();
					$hoa=$trans['hoa'];
					$ddo=$trans['issueuser'];
					$amt=$trans['partyamount'];
					$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$ddo)->first();
					$balance=$account['balance'];
					if($hoa != "8011001050001000000NVN" && $userid != '05010307005' && $hoa != '8443001060001000000NVN') {
						$new_bal=$balance-$amt;
					}
					$loc=$account['loc'];
					if($account['account_type']==2 && $hoa != '8443001040001000000NVN' && $hoa != '8011001050001000000NVN' && $userid != '05010307005' && $hoa != '8443001060001000000NVN')
					{
						$new_loc=$loc-$amt;
					}
					else
					{
						$new_loc=$loc;
					}
					if($new_bal>=0 && $new_loc>=0)
					{
						$date = new DateTime;
						$trans->confirmdate=$date;
						$trans->balance=$new_bal;
						$trans->transstatus=3;
						$trans->save();
						if($hoa != "8011001050001000000NVN" && $userid != '05010307005' && $hoa != '8443001060001000000NVN') {
							$account->balance=$new_bal;
							$account->loc=$new_loc;
						}
						$account->modify_date=$date;
						$account->save();
					}
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function bank_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				$ddocode = Input::get('ddocode');

				array_push($out,'success');
				$userid=$user[0]['userid'];
				foreach($data as $x)
				{

					$trans=Transactions::where('id','=',$x['id'])->where('issueuser','=',$ddocode)->where('transstatus','=',2)->first();
					$date = new DateTime;
					
					//new lines
					$hoa=$trans['hoa'];
					$ddo=$trans['issueuser'];
					$amt=$trans['partyamount'];
					$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$ddo)->first();
					$transit = $account['transitamount'];
					$bal = $account['balance'];
					$loc = $account['loc'];
					$newbal = $bal + $amt;
					$newtransit = $transit - $amt;
					if($account['account_type']==2)
					{
						$newloc = $loc + $amt;
					}
					else
					{
						$newloc=$loc;
					}
					// new lines///


					$lapflag = $account['lapsableflag'];

					if($lapflag=='1')
					{
						$laprecid = $trans['laprecid'];
						$q1 = Transactions::where('id','=',$laprecid)->first();
						$lapexp = $q1->lapexp;
						$nlapexp = $lapexp - $amt;
						$q1->lapexp = $nlapexp;
						$q1->save();
					}

					$trans->confirmdate=$date;
					$trans->rejects=$rems;
					$trans->transstatus=21;
					$trans->save();

					//new lines
					$account->transitamount = $newtransit;
					$account->balance = $newbal;
					$account->loc = $newloc;
					$account->save();

					$filetext = "pdaccountlogs.txt";
					$content = "BankController::Cheque Rejected::Old Balance:$bal::New Balance:$newbal::Old Loc:$loc::New Loc:$newloc::Date:".date('d-m-Y H:i:s')."\n";

					file_put_contents($filetext, $content, FILE_APPEND);

					//new lines///

					$partytext = "";
						$tableheader = "";

						if($trans['multiflag'] == 2) {

							if($trans['pdtopdflag'] == 1) {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							} else {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							}

							$partyfile = "uploads/".$trans['partyfile'];

							$fp=fopen($partyfile,'r');

							$c=1;
							$x =1;

							while($datafile=fgetcsv($fp)){
								if($c==0)
								{

									if($trans['pdtopdflag'] == 1) {

										$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[3].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[4].'</td></tr>';

									} else {

										$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[5].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[6].'</td></tr>';
									}
									$x++;
									
								}
								else
								{
									$c=0;
								}
							}
						} else {

							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

							$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyamount'].'</td></tr>';

						}

					$userd = Users::where('userid', '=', $ddocode)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque rejected - PD portal";
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($trans['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "rejected", $message);
					$message = str_ireplace("{{chequeno}}", $trans['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $trans['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $trans['hoa'], $message);
					$message = str_ireplace("{{amount}}", $trans['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $newbal, $message);
					$message = str_ireplace("{{locbalance}}", $newloc, $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('{{dedtype}}', 'credited', $message);
					$message = str_ireplace("{{byname}}", 'by BANK', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					//sendEmail($to, $subject, $message);
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}


	public function bank_confirmed_cheques(){
		$value = Request::header('X-CSRFToken');

		$fdateexp=explode("-",Input::get('fdate'));
		$fdate = $fdateexp[2]."-".$fdateexp[1]."-".$fdateexp[0];
		$tdateexp=explode("-",Input::get('tdate'));
		$tdate = $tdateexp[2]."-".$tdateexp[1]."-".$tdateexp[0];
		$fdate = $fdate.' 00:00:00';
		$tdate = $tdate.' 23:59:59';

		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$q = Users::where('username','=',$userid)->first();
				$like = $q['userid'];
				$out = Transactions::where('confirmdate','<=',$tdate)->where('confirmdate','>=',$fdate)->where('transtype','=','1')->where('transstatus','=','3')->where('chqflag','=','1')->where('purpose','!=','n/a')->where('issueuser','LIKE',$like.'%')->with('requser')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function bank_cheques_status(){
		$value = Request::header('X-CSRFToken');
		$chqno = Input::get('chqno');
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$patharr=array("invalid");
			}
			else
			{
				$userid=$user[0]['userid'];
				$usrdesc = $user[0]['userdesc'];
				$q = Users::where('username','=',$userid)->first();
				$like = $q['userid'];
				$out = Transactions::where('chequeno','=',$chqno)->where('transtype','=','1')->where('purpose','!=','n/a')->where('issueuser','LIKE',$like.'%')->with('requser')->get();
				if($out->count() >0) {
				$account=Pdaccount::where('hoa','=',$out[0]['hoa'])->where('ddocode','=',$out[0]['issueuser'])->first();
				$sbitotalamt = 0;
				$count = 0;
				if($out[0]->transstatus == '3' || $out[0]->transstatus == '2') {
					if($out[0]->multiflag == 2) {
						$ddouser=Users::where('username','=',$out[0]['issueuser'])->first();
						$commamt="0";
						$commamt1=str_pad($commamt,20,0,STR_PAD_LEFT);
						$commamt1NEFT=str_pad($commamt,17,0,STR_PAD_LEFT);
						$msgtype="R41";
						$remm_acno=$account->pdacno;
						$remm_acno1=str_pad($remm_acno,17,0,STR_PAD_LEFT);
						$remm_name=$ddouser->userdesc;
						$remm_name1=str_pad($remm_name,35,' ',STR_PAD_RIGHT);
						$remm_addr="STATE BANK OF INDIA";
						$remm_addr1=str_pad($remm_addr,35,' ',STR_PAD_RIGHT);
						$space7="       ";
						$space88="                ";
						$space8="                          ";
						$transtypesbi = "01";
						$fiftycharspre = " ";
						$fiftychars = str_pad($fiftycharspre,50,' ',STR_PAD_RIGHT);
						$send_recv_code="   URGENT";//value=ATTN /FAST/URGENT/DETAIL/NRE
						$send_recv_code1=str_pad($send_recv_code,11,' ',STR_PAD_RIGHT);//sender-to-receiver-code
						$email=$ddouser->emailid;
						$email1=str_pad($email,35,' ',STR_PAD_RIGHT);//Email-Id

						$send_recv_code1NEFT=str_pad('URGENT',8,' ',STR_PAD_RIGHT);//sender-to-receiver-code


						$write_textSBI = "";
						$write_textINTRA = "";
						$write_textRTGS = "";
						$write_textNEFT = "";
						$patharr = array();
						$fileloc = $out[0]->partyfile;

						//$userdesc = $out[0]->requser->userdesc;

						$fp=fopen('uploads/'.$fileloc,'r');
						$c=1;
						$partydatasbi=array();
						$partydata=array();		
						
						while($data=fgetcsv($fp)){
							if($c==0)
							{
								$count++;
								$tranamt1=str_pad($data[6],14,0,STR_PAD_LEFT);
								$tranamt1NEFT=str_pad($data[6],14,0,STR_PAD_LEFT)."000";
								$bankcode1=str_pad($data[5],12,' ',STR_PAD_LEFT);
								$bankcode1NEFT=str_pad($data[5],11,' ',STR_PAD_LEFT);
								$benfacno1=str_pad(strtoupper($data[2]),25,' ',STR_PAD_RIGHT);
								$benfacno1NEFT=str_pad(strtoupper($data[2]),32,' ',STR_PAD_RIGHT);
								$benffn = substr($data[1],0,35);
								$benfname1=str_pad($benffn,35,' ',STR_PAD_RIGHT);
								$subbenfname=substr($benfname1,0,25);
								$benfacno2=substr($benfacno1,0,16);
								$benf_addr=strtoupper(substr($usrdesc,0,34));
								$benf_addr = preg_replace('/[\/_]/', '', $benf_addr);
								$benf_addr1=str_pad($benf_addr,34,' ',STR_PAD_RIGHT);
								$benf_addr1NEFT=str_pad($benf_addr,35,' ',STR_PAD_RIGHT);
								$tranamt1sbi=str_pad($data[6],14,0,STR_PAD_LEFT)."00";
								$benfacnosbi=str_pad(strtoupper($data[2]),17,0,STR_PAD_LEFT);


								$userrefno = str_pad($chqno.$count, 16, ' ', STR_PAD_RIGHT);

								if($out[0]['issueuser'] == "27021802004") {

									$userrefno = str_pad($data[7], 16, ' ', STR_PAD_RIGHT);
								}


								if(substr($data[5],0,4)=='SBIN')
								{
									$sbitotalamt = $sbitotalamt+$data[6];

									$write_textSBI=$write_textSBI."$transtypesbi$benfacnosbi$tranamt1sbi$fiftychars$space88\n";

								} else {

									$write_textINTRA=$write_textINTRA."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";

									$write_textRTGS=$write_textRTGS."$msgtype$benfacno2$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";

									$write_textNEFT=$write_textNEFT."$msgtype$tranamt1NEFT$commamt1NEFT$remm_acno1$remm_name1$remm_addr1$benfacno1NEFT$benfname1$benf_addr1NEFT$bankcode1NEFT$benf_addr1NEFT$send_recv_code1NEFT$subbenfname$space7$space8$userrefno$email1\n";
								}
								
							}
							else
							{
								$c=0;
							}
						}
						
					} else {

						$count++;

					 	$benfacno1=str_pad(strtoupper($out[0]->partyacno),25,' ',STR_PAD_RIGHT);
					 	$benfacno1NEFT=str_pad(strtoupper($out[0]->partyacno),32,' ',STR_PAD_RIGHT);
					 	$benffn = substr($out[0]->partyname,0,35);
					 	$benfname1=str_pad($benffn,35,' ',STR_PAD_RIGHT);
						$benf_addr=strtoupper(substr($out[0]->requser->userdesc,0,34));
						$benf_addr = preg_replace('/[\/_]/', '', $benf_addr);
						$benf_addr1=str_pad($benf_addr,34,' ',STR_PAD_RIGHT);
						$benf_addr1NEFT=str_pad($benf_addr,35,' ',STR_PAD_RIGHT);
						$bankcode1=str_pad($out[0]->partyifsc,12,' ',STR_PAD_LEFT);
						$bankcode1NEFT=str_pad($out[0]->partyifsc,11,' ',STR_PAD_LEFT);
						$subbenfname=substr($benfname1,0,25);
						$benfacno2=substr($benfacno1,0,16);
						$tranamt1=str_pad($out[0]->partyamount,14,0,STR_PAD_LEFT);
						$tranamt1sbi=str_pad($out[0]->partyamount,14,0,STR_PAD_LEFT)."00";
						$benfacnosbi=str_pad(strtoupper($out[0]->partyacno),17,0,STR_PAD_LEFT);

						$userrefno = str_pad($chqno.$count, 16, ' ', STR_PAD_RIGHT);


						if(substr($out[0]->partyifsc,0,4)=='SBIN')
						{

							$sbitotalamt = $sbitotalamt+$out[0]->partyamount;

							$write_textSBI=$write_textSBI."$transtypesbi$benfacnosbi$tranamt1sbi$fiftychars$space88\n";

						} else {

							$write_textINTRA=$write_textINTRA."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


							$write_textRTGS=$write_textRTGS."$msgtype$benfacno2$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


							$write_textNEFT=$write_textNEFT."$msgtype$tranamt1NEFT$commamt1NEFT$remm_acno1$remm_name1$remm_addr1$benfacno1NEFT$benfname1$benf_addr1NEFT$bankcode1NEFT$benf_addr1NEFT$send_recv_code1NEFT$subbenfname$space7$space8$userrefno$email1\n";
						}


					}

					//for sbi debit entry
						
					if($sbitotalamt != 0) {
						$pdacno = $account->pdacno;
						$pdacno = str_pad(strtoupper($pdacno),17,0,STR_PAD_LEFT);
						$sbitotalamt = str_pad($sbitotalamt,14,0,STR_PAD_LEFT)."00";
						$write_textSBI=$write_textSBI."51$pdacno$sbitotalamt$fiftychars$space88\n";
					}


					$pathsbi = 0;
					$pathintra = 0;
					$pathneft = 0;
					$pathrtgs = 0;

					if($write_textSBI != "") {

						$pathsbi = "uploads/banksbi".$chqno.".txt";
						if(!file_exists($pathsbi)) {

							$fp=fopen($pathsbi, 'w');
							fwrite($fp,$write_textSBI);
							fclose($fp);
						}
					}
					if($write_textNEFT != "") {

						$pathneft = "uploads/bankneft".$chqno.".txt";

						if(!file_exists($pathneft)) {

							$fp2=fopen($pathneft, 'w');
							fwrite($fp2,$write_textNEFT);
							fclose($fp2);
						}
					}
					if($write_textRTGS != "") {

						$pathrtgs = "uploads/bankrtgs".$chqno.".txt";

						if(!file_exists($pathrtgs)) {

							$fp3=fopen($pathrtgs, 'w');
							fwrite($fp3,$write_textRTGS);
							fclose($fp3);
						}
					}
					if($write_textINTRA != "") {

						$pathintra = "uploads/bankintra".$chqno.".txt";

						if(!file_exists($pathintra)) {

							$fp4=fopen($pathintra, 'w');
							fwrite($fp4,$write_textINTRA);
							fclose($fp4);
						}
					}

				    $patharr = array();
				    array_push($patharr, array("sbilink"=>$pathsbi, "neftlink"=>$pathneft, "rtgslink"=> $pathrtgs, "intralink"=>$pathintra));
				}

			} else {

				array_push($patharr, array("0"));
			}

				if(empty($patharr)) {

					array_push($patharr, array("sbilink"=>0, "neftlink"=>0, "rtgslink"=> 0, "intralink"=>0));
				} 
				array_push($patharr, json_decode($out));

			}


		}
		else
		{
			$patharr=array("invalid");
		}
		return $patharr;
	}

	public function bank_issue_single_party(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{

				$data=Input::get('partydets');
				array_push($out,'success');
				$userid=$data['issueuser'];

				$data['cheque'] = str_pad($data['cheque'],6,0,STR_PAD_LEFT);
				$stocode = substr($userid,0,4);
				

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '2';

				if($act==1 && $map == '')
				{
					array_push($out,'nomap');
				}
				else
				{
					$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$userid)->where('activation','=',2)->first();
					if($pdac->count()==0)
					{
						array_push($out,"error");
					}
					else
					{
						array_push($out,'success');
						if(intval($pdac['balance'])>=intval($data['amount']))
						{
							array_push($out,"success");
							$party=Party::where('partyacno','=',$data['acno'])->get();
							if($party->count()==0)
							{
								$nparty=array(
									'partyname'=>$data['name'],
									'partyacno'=>$data['acno'],
									'partybank'=>$data['bank'],
									'partyifsc'=>$data['ifsc'],
									'partybranch'=>$data['branch']
									);
								$npty=Party::create($nparty);
							}
							$date = new DateTime;

							//new lines
							$pamt = $data['amount'];
							$exbal = $pdac['balance'];
							$fibal = $exbal - $pamt;
							$ttltransit = $pdac['transitamount'] + $pamt;
							$filoc = $pdac['loc'] - $pamt;
							//new lines
							
							

							if($pdac['account_type']==2) //loc
							{
								//array_push($out,'r');
								if($pamt>10000000 && $stocode=='2702')
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'partyname'=>$data['name'],
										'partyacno'=>$data['acno'],
										'partybank'=>$data['bank'],
										'partyifsc'=>$data['ifsc'],
										'partyamount'=>$data['amount'],
										'issueuser'=>$userid,
										'hoa'=>$data['hoa'],
										'multiflag'=>1,
										'partybranch'=>$data['branch'],
										'transstatus'=>2, //1
										'purpose'=>$data['purpose'],
										'chqflag'=>'1'
									);
									
								}else
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'partyname'=>$data['name'],
										'partyacno'=>$data['acno'],
										'partybank'=>$data['bank'],
										'partyifsc'=>$data['ifsc'],
										'partyamount'=>$data['amount'],
										'issueuser'=>$userid,
										'hoa'=>$data['hoa'],
										'multiflag'=>1,
										'partybranch'=>$data['branch'],
										'transstatus'=>2,//2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1'
									);
								}
								
							}
							else
							{
							//	array_push($out,$ttltransit);
								$ntrans=array(
									'transtype'=>1,
									'transdate'=>$date,
									'chequeno'=>$data['cheque'],
									'partyname'=>$data['name'],
									'partyacno'=>$data['acno'],
									'partybank'=>$data['bank'],
									'partyifsc'=>$data['ifsc'],
									'partyamount'=>$data['amount'],
									'issueuser'=>$userid,
									'hoa'=>$data['hoa'],
									'multiflag'=>1,
									'partybranch'=>$data['branch'],
									'transstatus'=>$transstatus,
									'purpose'=>$data['purpose'],
									'chqflag'=>'1'
								);

								
							}

							if($data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

								$pdac->balance = $fibal;
								$pdac->transitamount = $ttltransit;
							}
							if($pdac['account_type']==2 && $data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {
								$pdac->loc = $filoc;
							}
							$pdac->save();
							$nt=Transactions::create($ntrans);
							array_push($out, $nt->id);
							
							$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
							if($leaf) {

								$leaf->usedflag='1';
								$leaf->save();
								
							} 



							// $partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$data['name'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['acno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['ifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['amount'].'</td></tr>';

							// $tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

							// if($user[0]['emailid']) {

							// 	$to = $user[0]['emailid'];
							// } else {

							// 	$to = "garamaiah@gmail.com";
							// }
							// $subject = "Cheque issued - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
							// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
							// $message = str_ireplace("{{transtype}}", "issued", $message);
							// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
							// $message = str_ireplace("{{ddocode}}", $userid, $message);
							// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
							// $message = str_ireplace("{{amount}}", $data['amount'], $message);
							// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
							// $message = str_ireplace("{{locbalance}}", $filoc, $message);
							// $message = str_ireplace("{{partydetails}}", $partytext, $message);
							// $message = str_ireplace("{{dedtype}}", 'debited', $message);
							// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
							// $message = str_ireplace("{{byname}}", "", $message);
							// //sendEmail($to, $subject, $message);
						}
						else
						{
							array_push($out['Insufficient Funds in the Account']);
						}


					}
				}
				

			}
		}
		else
		{
			array_push($out,'invalid');
		}

		


		return ")]}',\n".json_encode($out);
	}

	public function bank_issue_single_party_lapsable(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{

				$data=Input::get('partydets');
				array_push($out,'success');
				$userid=$data['issueuser'];
				$stocode = substr($userid,0,4);
				$data['cheque'] = str_pad($data['cheque'],6,0,STR_PAD_LEFT);
				

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '2';


				if($act==1 && $map == '')
				{
					array_push($out,'nomap');
				}
				else
				{
					$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$userid)->where('activation','=',2)->first();
					if($pdac->count()==0)
					{
						array_push($out,"error");
					}
					else
					{
						array_push($out,'success');
						if(intval($pdac['balance'])>=intval($data['amount']))
						{
							array_push($out,"success");
							$party=Party::where('partyacno','=',$data['acno'])->get();
							if($party->count()==0)
							{
								$nparty=array(
									'partyname'=>$data['name'],
									'partyacno'=>$data['acno'],
									'partybank'=>$data['bank'],
									'partyifsc'=>$data['ifsc'],
									'partybranch'=>$data['branch']
									);
								$npty=Party::create($nparty);
							}
							$date = new DateTime;

							//new lines
							$pamt = $data['amount'];
							$exbal = $pdac['balance'];
							$fibal = $exbal - $pamt;
							$ttltransit = $pdac['transitamount'] + $pamt;
							$filoc = $pdac['loc'] - $pamt;
							//new lines
							
							
							if($pdac['account_type']==2) //loc
							{
								//array_push($out,'r');
								if($pamt>10000000 && $stocode=='2702')
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'partyname'=>$data['name'],
										'partyacno'=>$data['acno'],
										'partybank'=>$data['bank'],
										'partyifsc'=>$data['ifsc'],
										'partyamount'=>$data['amount'],
										'issueuser'=>$userid,
										'hoa'=>$data['hoa'],
										'multiflag'=>1,
										'partybranch'=>$data['branch'],
										'transstatus'=>2,//1
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'laprecid'=>$data['lapid'],
										'lapremarks'=>$data['lapremarks']
									);
									
								}else
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'partyname'=>$data['name'],
										'partyacno'=>$data['acno'],
										'partybank'=>$data['bank'],
										'partyifsc'=>$data['ifsc'],
										'partyamount'=>$data['amount'],
										'issueuser'=>$userid,
										'hoa'=>$data['hoa'],
										'multiflag'=>1,
										'partybranch'=>$data['branch'],
										'transstatus'=>2, //2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'laprecid'=>$data['lapid'],
										'lapremarks'=>$data['lapremarks']
									);
								}
								
								//new lines
								
								// // new lines
							}
							else
							{
							//	array_push($out,$ttltransit);
								$ntrans=array(
									'transtype'=>1,
									'transdate'=>$date,
									'chequeno'=>$data['cheque'],
									'partyname'=>$data['name'],
									'partyacno'=>$data['acno'],
									'partybank'=>$data['bank'],
									'partyifsc'=>$data['ifsc'],
									'partyamount'=>$data['amount'],
									'issueuser'=>$userid,
									'hoa'=>$data['hoa'],
									'multiflag'=>1,
									'partybranch'=>$data['branch'],
									'transstatus'=>$transstatus,
									'purpose'=>$data['purpose'],
									'chqflag'=>'1',
									'laprecid'=>$data['lapid'],
									'lapremarks'=>$data['lapremarks']
								);
								
							}

							if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

								$pdac->balance = $fibal;
								$pdac->transitamount = $ttltransit;
							}
							if($pdac['account_type']==2 && $data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {
								$pdac->loc = $filoc;
							}
							$pdac->save();

							$nt=Transactions::create($ntrans);
							array_push($out, $nt->id);

							$ql = Transactions::where('id','=',$data['lapid'])->first();
							$ql->lapexp = $ql->lapexp + $pamt;
							$ql->save();

							
							$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
							if($leaf) {

								
								$leaf->usedflag='1';
								$leaf->save();

							
							}
							

							// $partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$data['name'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['acno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['ifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['amount'].'</td></tr>';

							// $tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

							// if($user[0]['emailid']) {

							// 	$to = $user[0]['emailid'];
							// } else {

							// 	$to = "garamaiah@gmail.com";
							// }
							// $subject = "Cheque issued - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
							// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
							// $message = str_ireplace("{{transtype}}", "issued", $message);
							// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
							// $message = str_ireplace("{{ddocode}}", $userid, $message);
							// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
							// $message = str_ireplace("{{amount}}", $data['amount'], $message);
							// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
							// $message = str_ireplace("{{locbalance}}", $filoc, $message);
							// $message = str_ireplace("{{partydetails}}", $partytext, $message);
							// $message = str_ireplace("{{dedtype}}", 'debited', $message);
							// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
							// $message = str_ireplace("{{byname}}", "", $message);
							// //sendEmail($to, $subject, $message);
						}
						else
						{
							array_push($out['Insufficient Funds in the Account']);
						}
					}
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_user_hoa(){
		$value=Request::header('X-CSRFToken');
		$userid = Input::get('userid');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$out=Pdaccount::where('ddocode','=',$userid)->where('activation','=',2)->with('scheme')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_bank_list(){
		
		$type = Input::get("type");
		$chqno = Input::get("chqno");
		$value = Request::header('X-CSRFToken');
		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',4)->get();
		$userid=$user[0]['userid'];
		$usrdesc = $user[0]['userdesc'];
		$q = Users::where('username','=',$userid)->first();
		$like = $q['userid'];
		$out = Transactions::where('chequeno','=',$chqno)->where('transtype','=','1')->where('purpose','!=','n/a')->where('issueuser','LIKE',$like.'%')->with('requser')->get();
		$account=Pdaccount::where('hoa','=',$out[0]['hoa'])->where('ddocode','=',$out[0]['issueuser'])->first();
		$ddouser=Users::where('username','=',$out[0]['issueuser'])->first();
		$commamt="0";
		$commamt1=str_pad($commamt,20,0,STR_PAD_LEFT);
		$commamt1NEFT=str_pad($commamt,17,0,STR_PAD_LEFT);
		$msgtype="R41";
		$remm_acno=$account->pdacno;
		$remm_acno1=str_pad($remm_acno,17,0,STR_PAD_LEFT);
		$remm_name=$ddouser->userdesc;
		$remm_name1=str_pad($remm_name,35,' ',STR_PAD_RIGHT);
		$remm_addr="STATE BANK OF INDIA";
		$remm_addr1=str_pad($remm_addr,35,' ',STR_PAD_RIGHT);
		$space7="       ";
		$space88="                ";
		$space8="                          ";
		$transtypesbi = "01";
		$fiftycharspre = " ";
		$fiftychars = str_pad($fiftycharspre,50,' ',STR_PAD_RIGHT);
		$send_recv_code="   URGENT";//value=ATTN /FAST/URGENT/DETAIL/NRE
		$send_recv_code1=str_pad($send_recv_code,11,' ',STR_PAD_RIGHT);//sender-to-receiver-code
		$send_recv_code1NEFT=str_pad('URGENT',8,' ',STR_PAD_RIGHT);//sender-to-receiver-code
		$email=$ddouser->emailid;
		$email1=str_pad($email,35,' ',STR_PAD_RIGHT);//Email-Id


		$write_textSBI = "";
		$write_textINTRA = "";
		$write_textRTGS = "";
		$write_textNEFT = "";

		$sbitotalamt = 0;
		$count = 0;

		if($type=="multi") {

			$fileloc = Input::get("fileloc");
			$usrdesc = Input::get("userdesc");

			$fp=fopen($fileloc,'r');
			$c=1;
			$partydatasbi=array();
			$partydata=array();

			
			
			while($data=fgetcsv($fp)){
				if($c==0)
				{

					$count++;
					$data[2] = preg_replace('/[^A-Za-z0-9\-]/', '', $data[2]);

					$data[1] = str_ireplace(",", "", $data[1]);
					$data[1] = str_ireplace(".", "", $data[1]);
					$usrdesc = str_ireplace(",", "", $usrdesc);
					$usrdesc = str_ireplace(".", "", $usrdesc);
					$tranamt1=str_pad($data[6],14,0,STR_PAD_LEFT);
					$tranamt1NEFT=str_pad($data[6],14,0,STR_PAD_LEFT)."000";
					$bankcode1=str_pad($data[5],12,' ',STR_PAD_LEFT);
					$bankcode1NEFT=str_pad($data[5],11,' ',STR_PAD_LEFT);
					$benfacno1=str_pad(strtoupper($data[2]),25,' ',STR_PAD_RIGHT);
					$benfacno1NEFT=str_pad(strtoupper($data[2]),32,' ',STR_PAD_RIGHT);
					$benffn = substr($data[1],0,35);
					$benfname1=str_pad($benffn,35,' ',STR_PAD_RIGHT);
					$subbenfname=substr($benfname1,0,25);
					$benfacno2=substr($benfacno1,0,16);
					$benf_addr=strtoupper(substr($usrdesc,0,34));
					$benf_addr = preg_replace('/[\/_]/', '', $benf_addr);
					$benf_addr1=str_pad($benf_addr,34,' ',STR_PAD_RIGHT);
					$benf_addr1NEFT=str_pad($benf_addr,35,' ',STR_PAD_RIGHT);
					$tranamt1sbi=str_pad($data[6],14,0,STR_PAD_LEFT)."00";
					$benfacnosbi=str_pad(strtoupper($data[2]),17,0,STR_PAD_LEFT);

					$userrefno = str_pad($chqno.$count, 16, ' ', STR_PAD_RIGHT);

					if(substr($data[5],0,4)=='SBIN')
					{

						$sbitotalamt = $sbitotalamt+$data[6];

						$write_textSBI=$write_textSBI."$transtypesbi$benfacnosbi$tranamt1sbi$fiftychars$space88\n";

					} else {

						$write_textINTRA=$write_textINTRA."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


						$write_textRTGS=$write_textRTGS."$msgtype$benfacno2$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


						$write_textNEFT=$write_textNEFT."$msgtype$tranamt1NEFT$commamt1NEFT$remm_acno1$remm_name1$remm_addr1$benfacno1NEFT$benfname1$benf_addr1NEFT$bankcode1NEFT$benf_addr1NEFT$send_recv_code1NEFT$subbenfname$space7$space8$userrefno$email1\n";
					}
					
				}
				else
				{
					$c=0;
				}
			}
		} else {

			$count++;

		 	$res = Input::get('res');
		 	$res['partyacno'] = preg_replace('/[^A-Za-z0-9\-]/', '', $res['partyacno']);
		 	$res['partyname'] = str_ireplace(",", "", $res['partyname']);
			$res['partyname'] = str_ireplace(".", "", $res['partyname']);
			$res['requser']['userdesc'] = str_ireplace(",", "", $res['requser']['userdesc']);
			$res['requser']['userdesc'] = str_ireplace(".", "", $res['requser']['userdesc']);
		 	$benfacno1=str_pad(strtoupper($res['partyacno']),25,' ',STR_PAD_RIGHT);
		 	$benffn = substr($res['partyname'],0,35);
		 	$benfname1=str_pad($benffn,35,' ',STR_PAD_RIGHT);
			$benf_addr=strtoupper(substr($res['requser']['userdesc'],0,34));
			$benf_addr = preg_replace('/[\/_]/', '', $benf_addr);
			$benf_addr1=str_pad($benf_addr,34,' ',STR_PAD_RIGHT);
			$benf_addr1NEFT=str_pad($benf_addr,35,' ',STR_PAD_RIGHT);
			$bankcode1=str_pad($res['partyifsc'],12,' ',STR_PAD_LEFT);
			$bankcode1NEFT=str_pad($res['partyifsc'],11,' ',STR_PAD_LEFT);
			$subbenfname=substr($benfname1,0,25);
			$benfacno2=substr($benfacno1,0,16);
			$tranamt1=str_pad($res['partyamount'],14,0,STR_PAD_LEFT);
			$tranamt1sbi=str_pad($res['partyamount'],14,0,STR_PAD_LEFT)."00";
			$benfacnosbi=str_pad(strtoupper($res['partyacno']),17,0,STR_PAD_LEFT);

			$userrefno = str_pad($chqno.$count, 16, ' ', STR_PAD_RIGHT);


			if(substr($res['partyifsc'],0,4)=='SBIN')
			{
				$sbitotalamt = $sbitotalamt+$res['partyamount'];
				$write_textSBI=$write_textSBI."$transtypesbi$benfacnosbi$tranamt1sbi$fiftychars$space88\n";

			} else {

				$write_textINTRA=$write_textINTRA."$msgtype$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


				$write_textRTGS=$write_textRTGS."$msgtype$benfacno2$tranamt1$commamt1$remm_acno1$remm_name1$remm_addr1$benfacno1$space7$benfname1$benf_addr1$bankcode1$benfacno1$space7$send_recv_code1$subbenfname$benfacno2\n";


				$write_textNEFT=$write_textNEFT."$msgtype$tranamt1NEFT$commamt1NEFT$remm_acno1$remm_name1$remm_addr1$benfacno1NEFT$benfname1$benf_addr1NEFT$bankcode1NEFT$benf_addr1NEFT$send_recv_code1NEFT$subbenfname$space7$space8$userrefno$email1\n";
			}
		}

		//for sbi debit entry
		
		if($sbitotalamt != 0) {
			$pdacno = $remm_acno;
			$pdacno = str_pad(strtoupper($pdacno),17,0,STR_PAD_LEFT);
			$sbitotalamt = str_pad($sbitotalamt,14,0,STR_PAD_LEFT)."00";
			$write_textSBI=$write_textSBI."51$pdacno$sbitotalamt$fiftychars$space88\n";
		}

		$pathsbi = 0;
		$pathintra = 0;
		$pathneft = 0;
		$pathrtgs = 0;

		if($write_textSBI != "") {

			$pathsbi = "uploads/banksbi".$chqno.".txt";
			$fp=fopen($pathsbi, 'w');
			fwrite($fp,$write_textSBI);
			fclose($fp);
		}
		if($write_textNEFT != "") {

			$pathneft = "uploads/bankneft".$chqno.".txt";

			$fp2=fopen($pathneft, 'w');
			fwrite($fp2,$write_textNEFT);
			fclose($fp2);
		}
		if($write_textRTGS != "") {

			$pathrtgs = "uploads/bankrtgs".$chqno.".txt";

			$fp3=fopen($pathrtgs, 'w');
			fwrite($fp3,$write_textRTGS);
			fclose($fp3);
		}
		if($write_textINTRA != "") {

			$pathintra = "uploads/bankintra".$chqno.".txt";

			$fp4=fopen($pathintra, 'w');
			fwrite($fp4,$write_textINTRA);
			fclose($fp4);
		}

	    $patharr = array();
	    array_push($patharr, array("sbilink"=>$pathsbi, "neftlink"=>$pathneft, "rtgslink"=> $pathrtgs, "intralink"=>$pathintra));

	    return $patharr;

			
	}
}