<?php
class AdminController extends BaseController {

	public function start(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::all();
				
				$userid=$user[0]['userid'];
				$cstart=intval($data['first']);
				$cend=intval($data['last']);
				$users=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->first();
				if($users->lapsableflag=='') //if it is not lapsable then process complete
				{
					$users->chqflag='1';
					array_push($out,'success');
				}
				elseif($users->lapsableflag=='0') // if it is lapsable then forward to lap screen
				{
					$users->lapsableflag='1';
					array_push($out,'forward');
				}

				$users->save();
				
				while($cstart<=$cend)
				{
					$cmain = str_pad($cstart,6,"0",STR_PAD_LEFT);
					$nlf=array('user'=>$userid,'chequeno'=>$cmain,'usedflag'=>0);
					$nl=Leaves::create($nlf);
					$cstart++;
				}
				

				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function admin_loc_hoa(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out=Pdaccount::where('ddocode','=',$userid)->where('account_type','=',2)->where('activation','=',2)->with('scheme')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_loc_report(){
		$value=Request::header('X-CSRFToken');
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out=Loc::where('requestuser','=',$userid)->where('hoa','=',$hoa)->orderby('requestdate')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_req_rpt(){
		$value=Request::header('X-CSRFToken');
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out=Requests::where('requestuser','=',$userid)->with('bookdata')->orderby('id')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_chqrpt(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('issueuser','=',$userid)->where('transtype','=','1')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function reject_account(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		$hoa=Input::get('hoa');
		$reason=Input::get('reason');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$userid)->where('activation','=',2)->first();
				$account->reason=$reason;
				$account->save();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function confirm_account(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$userid)->where('activation','=',2)->first();
				$account->activation=1;
				$account->save();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_hoa_trans(){
		$value=Request::header('X-CSRFToken');
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$userid)->get();
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_req(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Requests::where('requestuser','=',$userid)->where('requestflag','=',0)->get();
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function cancel_chq(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$date = new DateTime;
				$ntrans=array(
						'transtype'=>1,
						'transdate'=>$date,
						'chequeno'=>$data['chq'],
						'issueuser'=>$userid,
						'transstatus'=>31,
						'purpose'=>$data['reason'],
					);
				$nt=Transactions::create($ntrans);
				$leaf=Leaves::where('chequeno','=',$data['chq'])->where('user','=',$userid)->first();
				$leaf->usedflag='1';
				$leaf->save();
				if($nt)
				{
					array_push($out,'success');
				}
				else
				{
					array_push($out,'error');
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function place_loc(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();
				$map = $q1->mapto;

				$conflag = '33';

				if($map == '')
				{
					array_push($out,'nomap');
				}
				else
				{
					$request=Loc::where('requestuser','=',$userid)->where('requestflag','=',0)->where('hoa','=',$data['hoa'])->get();
					if($request->count()==0)
					{
						array_push($out,'success');
						$date = new DateTime;
						$nreq=array('userid'=>substr($userid, 0, 4),'requestuser'=>$userid,'requestflag'=>0,'reqamount'=>$data['amt'],'hoa'=>$data['hoa'],'requestdate'=>$date,'conf_flag'=>$conflag, 'purpose'=>$data['purpose']);
						$nr=Loc::create($nreq);
						if($nr)
						{
							array_push($out,'success');

							$mailtext = "A LOC request has been placed with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$data['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$data['amt']."</div>";
							if($user[0]['emailid']) {

								$to = $user[0]['emailid'];
							} else {

								$to = "garamaiah@gmail.com";
							}
							
							$subject = "Loc request - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
							// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
							// $message = str_ireplace("{{pdbalance}}", $q1['balance'], $message);
							// $message = str_ireplace("{{locbalance}}", $q1['loc'], $message);
							//sendEmail($to, $subject, $message);
						}
						else
						{
							array_push($out,'error');
						}
					}
					else
					{
						array_push($out,"invalid");
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

	public function place_request(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		$data=Input::get('leaves');
		$challan=Input::get('challan');
		$rmtamt=Input::get('rmtamt');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);

				$q1 = ChequeBookUsers::where('userid','=',$stocode)->get();
				if($q1->count()==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					$request=Requests::where('requestuser','=',$userid)->where('requestflag','=',0)->get();
					if($request->count()==0)
					{
						array_push($out,'success');
						$date = new DateTime;
						$nreq=array('userid'=>substr($userid, 0, 4),'requestuser'=>$userid,'requestflag'=>0,'requestdate'=>$date,'leaves'=>$data,'conf_flag'=>33,'challanno'=>$challan,'remitamount'=>$rmtamt);
						$nr=Requests::create($nreq);
						if($nr)
						{
							array_push($out,'success');

							$mailtext = "A Chequebook request has been placed with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$data."</div>";

							if($user[0]['emailid']) {

								$to = $user[0]['emailid'];
							} else {

								$to = "garamaiah@gmail.com";
							}
							$subject = "Chequebook request - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
							// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
							// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available Balance</b>: Rs {{pdbalance}}</div>', '', $message);
							// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available LOC</b>: Rs {{locbalance}}</div>', '', $message);
							
							//sendEmail($to, $subject, $message);
						}
						else
						{
							array_push($out,'error');
						}
					}
					else
					{
						array_push($out,"invalid");
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

	public function admin_cheq(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$cheques=Leaves::where('user','=',$userid)->where('usedflag','=',0)->orderby('id')->get();
				$out=$cheques;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_trans(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$trans=Transactions::where('issueuser','=',$userid)->where('transtype','=',1)->orderby('id','desc')->first();
				$out=$trans;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function admin_get_party(){
		$value=Request::header('X-CSRFToken');
		$partyac=Input::get('partyac');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$out=Party::where('partyacno','=',$partyac)->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	

	public function issue_single_party(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);
				$data=Input::get('partydets');
				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '61';

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
						if(intval($pdac['balance'])>=intval($data['amount']) or $data['hoa']=='8011001050001000000NVN')
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
							$pdacloc = $pdac['loc'];
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
										'transstatus'=>61, //1
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
										'transstatus'=>61,//2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1'
									);
								}
								
								//new lines
								if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {
									$pdac->balance = $fibal;
									$pdac->transitamount = $ttltransit;
								}
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {
									$pdac->loc = $filoc;
								}
								
								$pdac->save();
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

								// //new lines
								$filoc = 0;
								$pdac->balance = $fibal;
								$pdac->transitamount = $ttltransit;
								$pdac->save();
								// // new lines
							}
							$filetext = "pdaccountlogs.txt";
							$content = "AdminController::Single party::Cheque issue::DDOCODE:$userid::HOA:".$data['hoa']."::Old Balance:$exbal::New Balance:$fibal::Old Loc:$pdacloc::New Loc:$filoc::Date:".date('d-m-Y H:i:s')."\n";
							file_put_contents($filetext, $content, FILE_APPEND);
							$nt=Transactions::create($ntrans);
							$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
							$leaf->usedflag='1';
							$leaf->save();
							$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$data['name'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['acno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['ifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['amount'].'</td></tr>';
							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							if($user[0]['emailid']) {
								$to = $user[0]['emailid'];
							} else {
								$to = "garamaiah@gmail.com";
							}
							$subject = "Cheque issued - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
							// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
							// $message = str_ireplace("{{transtype}}", "issued", $message);
							// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
							// $message = str_ireplace("{{ddocode}}", $userid, $message);
							// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
							// $message = str_ireplace("{{amount}}", $data['amount'], $message);
							// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
							// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
							// $message = str_ireplace("{{partydetails}}", $partytext, $message);
							// $message = str_ireplace("{{dedtype}}", 'debited', $message);
							// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
							// $message = str_ireplace("{{byname}}", "", $message);
							//sendEmail($to, $subject, $message);
						}
						else
						{
							array_push($out,['Insufficient Funds in the Account']);
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

	public function issue_single_party_lapsable(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);
				$data=Input::get('partydets');

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '61';


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
							$pdacloc = $pdac['loc'];
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
										'transstatus'=>61,//1
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
										'transstatus'=>61, //2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'laprecid'=>$data['lapid'],
										'lapremarks'=>$data['lapremarks']
									);
								}
								
								//new lines
								if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {
									$pdac->balance = $fibal;
									$pdac->transitamount = $ttltransit;
								}
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->loc = $filoc;
								}
								$pdac->save();
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

								// //new lines
								$filoc = 0;
								$pdac->balance = $fibal;
								$pdac->transitamount = $ttltransit;
								$pdac->save();
								// // new lines
							}

							$filetext = "pdaccountlogs.txt";
							$content = "AdminController::Single party lapsable::Cheque issue::DDOCODE:$userid::HOA:".$data['hoa']."::Old Balance:$exbal::New Balance:$fibal::Old Loc:$pdacloc::New Loc:$filoc::Date:".date('d-m-Y H:i:s')."\n";

							file_put_contents($filetext, $content, FILE_APPEND);

							$nt=Transactions::create($ntrans);
							$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
							$leaf->usedflag='1';
							$leaf->save();

							$ql = Transactions::where('id','=',$data['lapid'])->first();
							$ql->lapexp = $ql->lapexp + $pamt;
							$ql->save();

							$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$data['name'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['acno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['ifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$data['amount'].'</td></tr>';

							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

							if($user[0]['emailid']) {

								$to = $user[0]['emailid'];
							} else {

								$to = "garamaiah@gmail.com";
							}
							$subject = "Cheque issued - PD portal";
							// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
							// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
							// $message = str_ireplace("{{transtype}}", "issued", $message);
							// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
							// $message = str_ireplace("{{ddocode}}", $userid, $message);
							// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
							// $message = str_ireplace("{{amount}}", $data['amount'], $message);
							// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
							// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
							// $message = str_ireplace("{{partydetails}}", $partytext, $message);
							// $message = str_ireplace("{{dedtype}}", 'debited', $message);
							// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
							// $message = str_ireplace("{{byname}}", "", $message);
							//sendEmail($to, $subject, $message);
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


	public function get_hoa(){
		$value=Request::header('X-CSRFToken');
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',2)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out=Pdaccount::where('ddocode','=',$userid)->where('activation','=',2)->with('scheme')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function issue_multiple_party(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);	
				$data=Input::all();

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;


				$transstatus = '61';

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
						$date = new DateTime;
						$pdacloc = $pdac['loc'];
						$pamt = $data['amount'];
						$exbal = $pdac['balance'];
						$fibal = $exbal - $pamt;
						$ttltransit = $pdac['transitamount'] + $pamt;
						$filoc = $pdac['loc'] - $pamt;

						if($pdac['account_type']==2)
						{
								if($pamt>10000000 && $stocode=='2702')
								{	
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61, //1
										'purpose'=>$data['purpose'],
										'chqflag'=>'1'
									);

								}else
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61, //2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1'
									);
								}

								if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->balance = $fibal;
									$pdac->transitamount = $ttltransit;
								}
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->loc = $filoc;
								}
								$pdac->save();



						}
						else
						{
							$ntrans=array(
								'transtype'=>1,
								'transdate'=>$date,
								'chequeno'=>$data['cheque'],
								'issueuser'=>$userid,
								'partyamount'=>intval($data['amount']),
								'hoa'=>$data['hoa'],
								'multiflag'=>2,
								'partyfile'=>$data['partyfile'],
								'transstatus'=>$transstatus,
								'purpose'=>$data['purpose'],
								'chqflag'=>'1'
							);
							$filoc = 0;
							$pdac->balance = $fibal;
							$pdac->transitamount = $ttltransit;
							$pdac->save();
						}
						$filetext = "pdaccountlogs.txt";
						$content = "AdminController::Multiple party::Cheque issue::DDOCODE:$userid::HOA:".$data['hoa']."::Old Balance:$exbal::New Balance:$fibal::Old Loc:$pdacloc::New Loc:$filoc::Date:".date('d-m-Y H:i:s')."\n";

						file_put_contents($filetext, $content, FILE_APPEND);
						$nt=Transactions::create($ntrans);
						$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
						$leaf->usedflag='1';
						$leaf->save();

						$fp=fopen('uploads/'.$data['partyfile'],'r');

						$c=1;
						$x=1;
						$partytext = "";

						while($datafile=fgetcsv($fp)){
							if($c==0)
							{

								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[5].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[6].'</td></tr>';
								$x++;
								
							}
							else
							{
								$c=0;
							}
						}

						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';

						if($userid == "27029009008" || $userid == "27021802004") {

							//unlink("/var/www/html/pd/back/public/files/files/".$data['partyfile']);
						}

						if($user[0]['emailid']) {

							$to = $user[0]['emailid'];
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque issued - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
						// $message = str_ireplace("{{transtype}}", "issued", $message);
						// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
						// $message = str_ireplace("{{ddocode}}", $userid, $message);
						// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $data['amount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
						// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace("{{dedtype}}", 'debited', $message);
						// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
						// $message = str_ireplace("{{byname}}", "", $message);
						//sendEmail($to, $subject, $message);
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


	public function issue_multiple_party_lapsable(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);	
				$data=Input::all();

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '61';

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
						$date = new DateTime;
						$pdacloc = $pdac['loc'];
						$pamt = $data['amount'];
						$exbal = $pdac['balance'];
						$fibal = $exbal - $pamt;
						$ttltransit = $pdac['transitamount'] + $pamt;
						$filoc = $pdac['loc'] - $pamt;
						$partytext = "";
		
						if($pdac['account_type']==2)
						{
								if($pamt>10000000 && $stocode=='2702')
								{	
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61,//1
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
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61, //2
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
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->loc = $filoc;
								}
								$pdac->save();



						}
						else
						{
							$ntrans=array(
								'transtype'=>1,
								'transdate'=>$date,
								'chequeno'=>$data['cheque'],
								'issueuser'=>$userid,
								'partyamount'=>intval($data['amount']),
								'hoa'=>$data['hoa'],
								'multiflag'=>2,
								'partyfile'=>$data['partyfile'],
								'transstatus'=>$transstatus,
								'purpose'=>$data['purpose'],
								'chqflag'=>'1',
								'laprecid'=>$data['lapid'],
								'lapremarks'=>$data['lapremarks']
							);
							$filoc = 0;
							$pdac->balance = $fibal;
							$pdac->transitamount = $ttltransit;
							$pdac->save();
						}
						$filetext = "pdaccountlogs.txt";
						$content = "AdminController::Multiple party lapsable::Cheque issue::DDOCODE:$userid::HOA:".$data['hoa']."::Old Balance:$exbal::New Balance:$fibal::Old Loc:$pdacloc::New Loc:$filoc::Date:".date('d-m-Y H:i:s')."\n";

						file_put_contents($filetext, $content, FILE_APPEND);

						$nt=Transactions::create($ntrans);
						$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
						$leaf->usedflag='1';
						$leaf->save();

						$ql = Transactions::where('id','=',$data['lapid'])->first();
						$ql->lapexp = $ql->lapexp + $pamt;
						$ql->save();

						$fp=fopen('uploads/'.$data['partyfile'],'r');

						$c=1;
						$x =1;

						while($datafile=fgetcsv($fp)){
							if($c==0)
							{

								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[5].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[6].'</td></tr>';
								$x++;
								
							}
							else
							{
								$c=0;
							}
						}

						if($userid == "27029009008" || $userid == "27021802004") {

							//unlink("/var/www/html/pd/back/public/files/files/".$data['partyfile']);
						}

						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						

						if($user[0]['emailid']) {

							$to = $user[0]['emailid'];
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque issued - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
						// $message = str_ireplace("{{transtype}}", "issued", $message);
						// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
						// $message = str_ireplace("{{ddocode}}", $userid, $message);
						// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $data['amount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
						// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace("{{dedtype}}", 'debited', $message);
						// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
						// $message = str_ireplace("{{byname}}", "", $message);
						//sendEmail($to, $subject, $message);


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


	public function issue_pdtopd_cheque(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);	
				$data=Input::all();

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				$transstatus = '61';
				

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
						$date = new DateTime;
						$pdacloc = $pdac['loc'];
						$pamt = $data['amount'];
						$exbal = $pdac['balance'];
						$fibal = $exbal - $pamt;
						$ttltransit = $pdac['transitamount'] + $pamt;
						$filoc = $pdac['loc'] - $pamt;
		
						if($pdac['account_type']==2)
						{
								if($pamt>10000000 && $stocode=='2702')
								{	
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61,//
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'pdtopdflag'=>'1'
									);

								}else
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61,//
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'pdtopdflag'=>'1'
									);
								}

								if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->balance = $fibal;
									$pdac->transitamount = $ttltransit;
								}
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->loc = $filoc;
								}
								$pdac->save();
						}
						else
						{
							$ntrans=array(
								'transtype'=>1,
								'transdate'=>$date,
								'chequeno'=>$data['cheque'],
								'issueuser'=>$userid,
								'partyamount'=>intval($data['amount']),
								'hoa'=>$data['hoa'],
								'multiflag'=>2,
								'partyfile'=>$data['partyfile'],
								'transstatus'=>$transstatus,
								'purpose'=>$data['purpose'],
								'chqflag'=>'1',
								'pdtopdflag'=>'1'
							);
							$filoc = 0;
							$pdac->balance = $fibal;
							$pdac->transitamount = $ttltransit;
							$pdac->save();
						}

						//logging update

						$filetext = "pdaccountlogs.txt";
						$content = "AdminController::PD to PD::Cheque issue::DDOCODE:$userid::HOA:".$data['hoa']."::Old Balance:$exbal::New Balance:$fibal::Old Loc:$pdacloc::New Loc:$filoc::Date:".date('d-m-Y H:i:s')."\n";

						file_put_contents($filetext, $content, FILE_APPEND);

						$nt=Transactions::create($ntrans);
						$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
						$leaf->usedflag='1';
						$leaf->save();

						$fp=fopen('uploads/'.$data['partyfile'],'r');

						$c=1;
						$x =1;
						$partytext = "";

						while($datafile=fgetcsv($fp)){
							if($c==0)
							{

								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[3].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[4].'</td></tr>';
								$x++;
								
							}
							else
							{
								$c=0;
							}
						}

						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						

						if($user[0]['emailid']) {

							$to = $user[0]['emailid'];
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque issued - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
						// $message = str_ireplace("{{transtype}}", "issued", $message);
						// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
						// $message = str_ireplace("{{ddocode}}", $userid, $message);
						// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $data['amount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
						// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace("{{dedtype}}", 'debited', $message);
						// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
						// $message = str_ireplace("{{byname}}", "", $message);
						//sendEmail($to, $subject, $message);

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


	public function issue_pdtopd_cheque_lapsable(){
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
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$stocode = substr($userid,0,4);	
				$data=Input::all();

				$q1 = Pdaccount::where('ddocode','=',$userid)->where('hoa','=',$data['hoa'])->first();//check map
				$map = $q1->mapto;
				$act = $q1->account_type;

				
				$transstatus = '61';

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
						$date = new DateTime;
						$pamt = $data['amount'];
						$exbal = $pdac['balance'];
						$fibal = $exbal - $pamt;
						$ttltransit = $pdac['transitamount'] + $pamt;
						$filoc = $pdac['loc'] - $pamt;
						$partytext = "";
		
						if($pdac['account_type']==2)
						{
								if($pamt>10000000 && $stocode=='2702')
								{	
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61, //1
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'pdtopdflag'=>'1',
										'laprecid'=>$data['lapid'],
										'lapremarks'=>$data['lapremarks']
									);

								}else
								{
									$ntrans=array(
										'transtype'=>1,
										'transdate'=>$date,
										'chequeno'=>$data['cheque'],
										'issueuser'=>$userid,
										'partyamount'=>intval($data['amount']),
										'hoa'=>$data['hoa'],
										'multiflag'=>2,
										'partyfile'=>$data['partyfile'],
										'transstatus'=>61, //2
										'purpose'=>$data['purpose'],
										'chqflag'=>'1',
										'pdtopdflag'=>'1',
										'laprecid'=>$data['lapid'],
										'lapremarks'=>$data['lapremarks']
									);
								}

								if($data['hoa'] != "8011001050001000000NVN" && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->balance = $fibal;
									$pdac->transitamount = $ttltransit;
								}
								if($data['hoa'] != "8443001040001000000NVN" && $data['hoa'] != '8011001050001000000NVN' && $userid != '05010307005' && $data['hoa'] != '8443001060001000000NVN') {

									$pdac->loc = $filoc;
								}
								$pdac->save();
						}
						else
						{
							$ntrans=array(
								'transtype'=>1,
								'transdate'=>$date,
								'chequeno'=>$data['cheque'],
								'issueuser'=>$userid,
								'partyamount'=>intval($data['amount']),
								'hoa'=>$data['hoa'],
								'multiflag'=>2,
								'partyfile'=>$data['partyfile'],
								'transstatus'=>$transstatus,
								'purpose'=>$data['purpose'],
								'chqflag'=>'1',
								'pdtopdflag'=>'1',
								'laprecid'=>$data['lapid'],
								'lapremarks'=>$data['lapremarks']
							);
							$pdac->balance = $fibal;
							$pdac->transitamount = $ttltransit;
							$pdac->save();
						}
						$nt=Transactions::create($ntrans);
						$leaf=Leaves::where('chequeno','=',$data['cheque'])->where('user','=',$userid)->first();
						$leaf->usedflag='1';
						$leaf->save();

						$ql = Transactions::where('id','=',$data['lapid'])->first();
						$ql->lapexp = $ql->lapexp + $pamt;
						$ql->save();

						$fp=fopen('uploads/'.$data['partyfile'],'r');

						$c=1;
						$x =1;

						while($data=fgetcsv($fp)){
							if($c==0)
							{

								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[3].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[4].'</td></tr>';
								$x++;
								
							}
							else
							{
								$c=0;
							}
						}

						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						

						if($user[0]['emailid']) {

							$to = $user[0]['emailid'];
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque issued - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y"), $message);
						// $message = str_ireplace("{{transtype}}", "issued", $message);
						// $message = str_ireplace("{{chequeno}}", $data['cheque'], $message);
						// $message = str_ireplace("{{ddocode}}", $userid, $message);
						// $message = str_ireplace("{{hoa}}", $data['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $data['amount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $fibal, $message);
						// $message = str_ireplace("{{locbalance}}", $pdac->loc, $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace("{{dedtype}}", 'debited', $message);
						// $message = str_ireplace("{{byname}}", "", $message);
						//sendEmail($to, $subject, $message);
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

	public function get_booklist()
	{
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out=Pdaccount::where('ddocode','=',$userid)->where('activation','=',2)->with('usernames')->with('scheme')->get();
				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_bookdata(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$data=Input::get('account');
				$account=Pdaccount::where('id','=',$data)->where('activation','=',2)->with('scheme')->first();
				$out=$account;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_pagedata(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$hoa=Input::get('hoa');
				$ddo=Input::get('ddo');
				$page=Input::get('page');
				$count=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->count();
				if($page=='last')
				{
					$pages=$count/10;
					if($count%10==0)
					{
						$pageno=intval($pages);
					}
					else
					{
						$pageno=intval($pages)+1;
					}
				}
				else
				{
					$pageno=intval($page);
				}
				if($count<=10)
				{
					$trans=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->orderby('id')->get();
				}
				else
				{
					$skip=($pageno-1)*10;
					$trans=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->orderby('id')->skip($skip)->take(10)->get();
				}
				$out=$trans;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_pagelist(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$hoa=Input::get('hoa');
				$ddo=Input::get('ddo');
				$count=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->count();
				$out=json_encode(array($count));;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}


	public function get_receipts_data(){
		$value = Request::header('X-CSRFToken');
		$hoa = Input::get('hoa');
		$m = Input::get('m');
		$y = Input::get('y');
		

		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ds=$y.'-'.$m.'-01';
				if($m=='12')
				{
					$dey=$y+1;
			                $dem='01';
			        }
			        else
			        {
			        	$dey=$y;
			               	$dem=$m+1;
			        }
				$de=$dey.'-'.$dem.'-01';
				$out = Transactions::where('issueuser','=',$userid)->where('hoa','=',$hoa)->where('partyamount','>',0)->where('transtype','=','2')->where('transdate','>=',$ds)->where('transdate','<',$de)->with('laptrans')->orderBy('transdate')->get();
				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}
	

	public function getfilelist(){
		$value = Request::header('X-CSRFToken');

		$out = array();

		$i = 0;
	
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->first();
			if(!$user)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];

				if ($handle = opendir('files/files')) {
				    while (false !== ($file = readdir($handle)))
				    {

				        if ($file != "." && $file != ".." && strpos($file, $userid."_") !== false)
				        {
				            $out[$i]['filename'] = $file;

				            $filedescarr = explode($userid."_", $file);
				            $filedesc = str_ireplace(".csv", "", $filedescarr[1]);
				            $out[$i]['filedesc'] = $filedesc;
				            $i++;
				        }
				    }
				    closedir($handle);
				}
				
			}

			$out = json_encode($out);
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}
	

}


