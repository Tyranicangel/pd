<?php

class AdminCheckerController extends BaseController {


	public function checker_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$hoas = array();
		$sas = array();
		$stos = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$username = $user[0]['username'];	
					
							
				$request=Requests::where('requestuser','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',33)->get();
				array_push($out,$request->count());
				
				$trans=Transactions::where('issueuser','=',$userid)->where('transstatus','=',61)->get();
				array_push($out,$trans->count());
				$request=Loc::where('requestuser','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',33)->get();
				array_push($out,$request->count());		

				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function checker_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('issueuser','=',$userid)->where('transstatus','=',61)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function checker_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$transid=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('id','=',$transid)->where('issueuser','=',$userid)->where('transstatus','=',61)->with('requser')->orderby('id')->get();
				$fout=$request->toArray();
				for($i=0;$i<count($fout);$i++)
				{
					$acinfo=Pdaccount::where('hoa','=',$fout[$i]['hoa'])->where('ddocode','=',$fout[$i]['issueuser'])->first();
					$fout[$i]['acinfo']=$acinfo->toArray();


					if($acinfo->lapsableflag=='1')
					{
						$recid = $fout[$i]['laprecid'];

						$q2 = Transactions::where('id','=',$recid)->with('laptrans')->first()->toArray();
						$fout[$i]['laprecinfo'] = $q2;

					}else
					{
						$fout[$i]['laprecinfo'] = '';
					}
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

	public function checker_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				
				$userid=$user[0]['userid'];

				$ntransstatus = 62;

				$thistrans = Transactions::where('id','=',$data)->where('issueuser','=',$userid)->where('transstatus','=',61)->first();

				$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans['hoa'])->where('ddocode','=',$userid)->where('activation','=',2)->first();

				$stocode = substr($thistrans['issueuser'],0,4);

				if($pdaccountinfo['account_type'] == 2) //loc
				{

					if($thistrans['partyamount']>=10000000 && $stocode=='2702' && $thistrans['hoa'] != '8443001040001000000NVN') {

						$ntransstatus = 1;
					} else {

						$ntransstatus = 2;
					}
				}
				
				$date=new DateTime;



				$trans=Transactions::query()->where('id','=', $data)->where('issueuser','=',$userid)->where('transstatus','=',61)->update(array('transstatus'=>$ntransstatus,'rejects'=>$rems));
				array_push($out,"success");

				if($thistrans['multiflag'] == 2) {

						if($thistrans['pdtopdflag'] == 1) {

							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						} else {

							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						}

						$partyfile = "uploads/".$thistrans['partyfile'];

						$fp=fopen($partyfile,'r');

						$c=1;
						$x =1;

						$partytext = "";

						while($datafile=fgetcsv($fp)){
							if($c==0)
							{

								if($thistrans['pdtopdflag'] == 1) {

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

						$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans['partyamount'].'</td></tr>';

					}

				$userd = Users::where('userid', '=', $userid)->where('user_role','=',2)->first();

				if($userd->emailid) {

					$to = $userd->emailid;
				} else {

					$to = "garamaiah@gmail.com";
				}

				$to = "garamaiah@gmail.com";
				$subject = "Cheque forwarded - PD portal";
				// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
				// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans['transdate'])), $message);
				// $message = str_ireplace("{{transtype}}", "forwarded", $message);
				// $message = str_ireplace("{{chequeno}}", $thistrans['chequeno'], $message);
				// $message = str_ireplace("{{ddocode}}", $thistrans['issueuser'], $message);
				// $message = str_ireplace("{{hoa}}", $thistrans['hoa'], $message);
				// $message = str_ireplace("{{amount}}", $thistrans['partyamount'], $message);
				// $message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
				// $message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
				// $message = str_ireplace("{{partydetails}}", $partytext, $message);
				// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
				// $message = str_ireplace("{{byname}}", 'by PD admin checker', $message);
				// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
				//sendEmail($to, $subject, $message);

				// $trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','>',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>0));
				// $trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','<=',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>0));
				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function checker_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
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
				$trans=Transactions::where('id','=', $data)->where('issueuser','=',$userid)->where('transstatus','=',61)->first();
					
					//new lines
					$hoa=$trans['hoa'];
					$ddo=$trans['issueuser'];
					$amt=$trans['partyamount'];
					$chqn = $trans['chequeno'];
					$partytext = "";
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

						$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$trans['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$amt.'</td></tr>';

					}
					$transdate = date("d/m/Y", strtotime($trans['transdate']));
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

					$trans->delete();

					//new lines
					$account->transitamount = $newtransit;
					$account->balance = $newbal;
					$account->loc = $newloc;
					$account->save();

					$chequeleaves = Chequeleaves::where('user', '=', $ddo)->where('chequeno', $chqn)->update(array('usedflag' => 0));


					$userd = Users::where('userid', '=', $userid)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque rejected - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					// $message = str_ireplace("{{chequedate}}", $transdate, $message);
					// $message = str_ireplace("{{transtype}}", "rejected", $message);
					// $message = str_ireplace("{{chequeno}}", $chqn, $message);
					// $message = str_ireplace("{{ddocode}}", $ddo, $message);
					// $message = str_ireplace("{{hoa}}", $hoa, $message);
					// $message = str_ireplace("{{amount}}", $amt, $message);
					// $message = str_ireplace("{{pdbalance}}", $newbal, $message);
					// $message = str_ireplace("{{locbalance}}", $newloc, $message);
					// $message = str_ireplace("{{partydetails}}", $partytext, $message);
					// $message = str_ireplace("{{dedtype}}", 'credited', $message);
					// $message = str_ireplace("{{byname}}", 'by PD admin checker', $message);
					// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
					//sendEmail($to, $subject, $message);
					//sendEmail('garamaiah@gmail.com', $subject, $message);
				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function checker_loclist(){
		$value = Request::header('X-CSRFToken');
		$hoas = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];

				$request=Loc::where('requestuser','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',33)->with('requser')->with('schemes')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function checker_loc_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];

				$request=Loc::where('requestuser','=',$data['requser'])->where('hoa','=',$data['reqhoa'])->where('requestflag','=',0)->where('conf_flag','=',33)->with('requser')->with('schemes')->orderby('id','desc')->first();

				$r=$request->toArray();

				$acinfo=Pdaccount::where('hoa','=',$r['hoa'])->where('ddocode','=',$r['requestuser'])->first();
				$r['accounts']=$acinfo->toArray();

				$out=json_encode($r);
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function accept_loc_checker(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				

				if($data['approval']=='reject')
				{
					$date=new DateTime;
					$request=Loc::where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',33)->where('hoa','=',$data['hoa'])->where('id', '=', $data['locid'])->first();
					$request->requestflag=1;
					$request->conf_flag=2;
					$request->remarks=$data['remarks'];
					$request->save();
				}
				else
				{
					$date=new DateTime;
					$request=Loc::where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->where('hoa','=',$data['hoa'])->where('conf_flag','=',33)->where('id', '=', $data['locid'])->first();
					$request->requestflag=0;
					$request->conf_flag = 3;
					$request->remarks=$data['remarks'];
					$request->save();
					//$out = $request->toArray();
				}

				$account = Pdaccount::where('ddocode','=',$data['user'])->where('hoa','=',$data['hoa'])->first();

					$mailtext = "A LOC request has been forwarded by PD admin checker with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'>".$data['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'>Requested Amount: </div><div style='width:200px;float:left;'>Rs. ".$request['reqamount']."</div>";

					$userd = Users::where('userid', '=', $userid)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Loc request forwarded - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
					// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
					// $message = str_ireplace("{{pdbalance}}", $account['balance'], $message);
					// $message = str_ireplace("{{locbalance}}", $account['loc'], $message);
					//sendEmail($to, $subject, $message);
				
			}
		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);		
	}

	public function checker_requests(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$username = $user[0]['username'];

		
				$request=Requests::where('requestuser','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',33)->with('requser')->get();
				$out=$request->toArray();
					
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}

	public function checker_request_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('requestuser','=',$data)->where('requestflag','=',0)->where('conf_flag','=',33)->orderby('id','desc')->with('requser')->with('leafs')->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function accept_request_checker(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',20)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{

				array_push($out,'success');
				$userid=$user[0]['userid'];

				if($data['approval']=='reject')
				{
					$date=new DateTime;
					$request=Requests::where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',33)->orderby('id','desc')->first();
					$request->conf_flag=2;
					$request->requestflag=1;
					$request->remarks=$data['remarks'];
					$request->save();
					array_push($out,'success');
				}
				else
				{
					$date = new DateTime;
					$request=Requests::where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',33)->orderby('id','desc')->first();
					$request->conf_flag=0;
					$request->remarks=$data['remarks'];
					$request->save();
					
					array_push($out,'success');
					
				}

				$mailtext = "A Chequebook request has been forwarded by PD admin checker with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$request['leaves']."</div>";

				$userd = Users::where('userid', '=', $userid)->where('user_role','=',2)->first();

				if($userd->emailid) {

					$to = $userd->emailid;
				} else {

					$to = "garamaiah@gmail.com";
				}
				$subject = "Chequebook request forwarded - PD portal";
				// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
				// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
				// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available Balance</b>: Rs {{pdbalance}}</div>', '', $message);
				// 	$message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available LOC</b>: Rs {{locbalance}}</div>', '', $message);
				
				//sendEmail($to, $subject, $message);
			}

		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);
	}


}

?>