<?php

class DsaController extends BaseController {

	public function update_transid_dsa(){
		$value = Request::header('X-CSRFToken');
		$chq=Input::get('chq');
		$trans=Input::get('trans');
		$ddocode = Input::get('ddocode');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$account=Transactions::where('chequeno','=',$chq)->where('issueuser','=',$ddocode)->first();
				$account['transid'] = $trans;
				$account->save();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out); 
	}

	

	public function get_dsa_admins(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Pdaccount::where('userid','=',$userid)->groupBy('ddocode')->where('activation','=',2)->with('usernames')->get(array('ddocode'));
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_dsa_hoas(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Pdaccount::where('userid','=',$userid)->where('ddocode','=',$ddo)->where('activation','=',2)->with('scheme')->get();
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	// public function get_sa_ledger(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$ddo=Input::get('ddo');
	// 	$hoa=Input::get('hoa');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$ddos=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->get();
	// 			$out=$ddos;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	public function dsa_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$hoas = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$username = $user[0]['username'];
				//$q1 = Pdaccount::where('mapto','=',$username)->get()->toArray();
				$f9 = Pdaccount::where('mapto','=',$username)->get();

				if($f9->count()==0)
				{
					$q4 = ChequeBookUsers::where('userid','=',$userid)->get();
					if($q4->count()==0)
					{
						array_push($out,"nomap");
					}
					else
					{					
						$sa = $q4[0]['sauser'];
						if($sa==$username)
						{
								$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->get();
								array_push($out,$request->count());
						}
						else
						{
								array_push($out,"0");
						}
						
						array_push($out,'0');
						array_push($out,'0');
					}
				}
				else
				{

					$q1 = Pdaccount::select('hoa')->where('mapto','=',$username)->distinct()->get()->toArray();
					$q4 = ChequeBookUsers::where('userid','=',$userid)->get();
					if(count($q1)==0 && $q4->count()==0)
					{
						array_push($out,"nomap");
					}
					else
					{					
						if($q4->count()==0)
						{
							array_push($out,"0");
						}
						else
						{
							$sa = $q4[0]['sauser'];
							if($sa==$username)
							{
									$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->get();
									array_push($out,$request->count());
							}
							else
							{
									array_push($out,"0");
							}

						}

						for ($i=0; $i < count($q1); $i++) { 
							array_push($hoas, $q1[$i]['hoa']);
						}
						
						$trans=Transactions::where('issueuser','like',$userid.'%')->whereIn('hoa',$hoas)->where('transstatus','=',62)->get();
						array_push($out,$trans->count());
						$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->whereIn('hoa',$hoas)->where('conf_flag','=',3)->get();
						array_push($out,$request->count());
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

	public function dsa_request_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data)->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->with('requser')->with('leafs')->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dsa_invent_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Inventory::where('userid','=',$userid)->where('used','=',0)->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dsa_loc_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];

				$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['requser'])->where('hoa','=',$data['reqhoa'])->where('requestflag','=',0)->where('conf_flag','=',3)->with('requser')->with('schemes')->orderby('id','desc')->first();

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

	public function dsa_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$username=$user[0]['username'];
				$userid=$user[0]['userid'];
				$q1 = Pdaccount::select('hoa')->where('mapto','=',$username)->distinct()->get()->toArray();
				$hoas=[];
				if(count($q1)==0)
				{
					$out=json_encode([]);
				}
				else
				{					
					for ($i=0; $i < count($q1); $i++) { 
						array_push($hoas, $q1[$i]['hoa']);
					}
					$trans=Transactions::where('issueuser','like',$userid.'%')->whereIn('hoa',$hoas)->where('transstatus','=',62)->with('requser')->orderby('id')->get();
					$out=$trans;
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dsa_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$transid=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('id','=',$transid)->where('issueuser','like',$userid.'%')->where('transstatus','=',62)->with('requser')->orderby('id')->get();
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

	public function dsa_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				$type=Input::get('type');
				
				$userid=$user[0]['userid'];

				$q1 = Maptable::where('currentuser','=',$user[0]['username'])->get();
				if($q1->count()==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					$map = $q1[0]['mappeduser'];
					$var = substr($map,4,3);

					if($var == 'ATO')
					{
						$ntransstatus = '64';
					}
					else if($var == 'STO')
					{
						$ntransstatus = '63';
					}
					else if($var == '')
					{
						$ntransstatus = '65';
					}
					$date=new DateTime;
					if($type == "approve") {

						$cflag = 0;
					} else {

						$cflag = 1;
					}

					$thistrans = Transactions::where('id','=',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',62)->first();

					$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans['hoa'])->where('ddocode','=',$thistrans['issueuser'])->where('activation','=',2)->first();

					$trans=Transactions::query()->where('id','=',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',62)->update(array('transstatus'=>$ntransstatus,'rejects'=>$rems,'conf_flag'=>$cflag,'sauser'=>$user[0]['username'],'satime'=>$date));
					array_push($out,'success');

					$partytext = "";

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

				$userd = Users::where('userid', '=', $thistrans['issueuser'])->where('user_role','=',2)->first();

				if($userd->emailid) {

					$to = $userd->emailid;
				} else {

					$to = "garamaiah@gmail.com";
				}
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
				// $message = str_ireplace("{{byname}}", 'by SA', $message);
				// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
				//sendEmail($to, $subject, $message);

					
				}
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

	// public function sa_chqlist_reject(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,"invalid");
	// 		}
	// 		else
	// 		{
	// 			$data=Input::get('list');
	// 			$rems=Input::get('rems');
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$trans=Transactions::query()->whereIn('chequeno',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>1));
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	public function dsa_requests(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$username = $user[0]['username'];

				$q4 = ChequeBookUsers::where('userid','=',$userid)->get();
					
				if($q4->count()==0)
				{	
					
				}
				else
				{
					$sa = $q4[0]['sauser'];
					if($sa==$username)
					{
						$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->with('requser')->get();
						$out=$request->toArray();
					}
				}		
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}

	public function dsa_book_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$data=Input::all();
				$request=Inventory::where('userid','=',$userid)->where('bookno','=',$data['book'])->where('used','=',0)->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}	

	public function dsa_loclist(){
		$value = Request::header('X-CSRFToken');
		$hoas = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$username = $user[0]['username'];
				$q1 = Pdaccount::where('mapto','=',$username)->get()->toArray();
				if(count($q1)==0)
				{
					$out=json_encode([]);
				}
				else
				{
					for ($i=0; $i < count($q1); $i++) { 
						array_push($hoas, $q1[$i]['hoa']);
					}
					$request=Loc::whereIn('hoa',$hoas)->where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',3)->with('requser')->with('schemes')->get();
					$out=$request;
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function create_account_dsa()
	{
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				//$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->get();
				$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->first();
				$oac=Pdaccount::orderby('accountno','desc')->first();
				$acno=intval($oac->accountno)+1;
				if($ac)
				{
					if($ac->activation=='2')
					{
						array_push($out,"exists");
					}
					elseif($ac->activation=='0')
					{
						array_push($out,"activate");
					}
					elseif($ac->activation=='3')
					{
						array_push($out,"created");
						$ac->activation=='0';
						$ac->save();
					}
				}
				else
				{
					array_push($out,"created");
					$date = new DateTime;
					$area=substr($userid,0,2);
					$nac=array('accountno'=>$acno,'hoa'=>$data['acdata']['hoa'],'ddocode'=>$data['acdata']['ddo'],'balance'=>$data['acdata']['balance'],'modify_date'=>$date,'account_type'=>$data['acdata']['actype'],'userid'=>$userid,'obalance'=>0,'areacode'=>$area,'activation'=>0,'loc'=>0,'category'=>$data['acdata']['cat'],'reference'=>$data['acdata']['remarks'],'lapsableflag'=>$data['acdata']['lap']);
					$cac=Pdaccount::create($nac);
					$sc=Schemes::where('hoa','=',$data['acdata']['hoa'])->get();
					if($sc->count()==0)
					{
						$nsc=array('schemename'=>$data['acdata']['hoaname'],'modified'=>$date,'hoa'=>$data['acdata']['hoa']);
						$csc=Schemes::create($nsc);
					}
					$us=Users::where('username','=',$data['acdata']['ddo'])->get();
					$tkn=md5($data['acdata']['ddo'].microtime());
					$tkn1=md5($data['acdata']['ddo'].'auth'.microtime());
					if($us->count()==0)
					{
						$nus=array('username'=>$data['acdata']['ddo'],'password'=>'e10adc3949ba59abbe56e057f20f883e','userid'=>$data['acdata']['ddo'],'user_role'=>2,'modify_date'=>$date,'refreshtoken'=>$tkn,'userdesc'=>$data['acdata']['ddoname']);
						$cus=Users::create($nus);
						$nus1=array('username'=>$data['acdata']['ddo'].'auth','password'=>'e10adc3949ba59abbe56e057f20f883e','userid'=>$data['acdata']['ddo'],'user_role'=>20,'modify_date'=>$date,'refreshtoken'=>$tkn1,'userdesc'=>$data['acdata']['ddoname']);
						$cus1=Users::create($nus1);
					}
				}
			}
		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);
	}

	public function accept_request_dsa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$q1 = Maptable::where('currentuser','=',$user[0]['username'])->get();
				if($q1->count()==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					$map = $q1[0]['mappeduser'];
					$var = substr($map,4,3);

					if($var == 'ATO')
					{
						$conf_flag = '5';
					}
					else if($var == 'STO')
					{
						$conf_flag = '4';
					}
					else if($var == '')
					{
						$conf_flag = '6';
					}


					array_push($out,'success');
					$userid=$user[0]['userid'];

					if($data['approval']=='reject')
					{
						$date=new DateTime;
						$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->first();
						$request->conf_flag=$conf_flag;
						$request->saflag=1;
						$request->sauser=$user[0]['username'];
						$request->satime=$date;
						$request->remarks=$data['remarks'];
						$request->save();
						array_push($out,'success');
					}
					else
					{
						$inv=Inventory::where('bookno','=',$data['chequedata']['number'])->where('used','=',0)->first();
						if($inv)
						{
							$date = new DateTime;
							$inv->used=1;
							$inv->save();
							$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->first();
							$request->conf_flag=$conf_flag;
							$request->saflag=0;
							$request->remarks=$data['remarks'];
							$request->sauser=$user[0]['username'];
							$request->satime=$date;
							$request->save();
							$nchq=array('issueuser'=>$userid,'reciptuser'=>$data['user'],'chequestart'=>$data['chequedata']['first'],'chequeend'=>$data['chequedata']['last'],'bookno'=>$data['chequedata']['number'],'issuedate'=>$date,'complete'=>0,'requestid'=>$request->id);
							$nc=Cheques::create($nchq);
							array_push($out,'success');
						}
						else
						{
							array_push($out,'This book is not available in your inventory.Please recheck your inventory and try again.');
						}
					}

					$mailtext = "A Chequebook request has been forwarded by SA with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$request['leaves']."</div>";

					$userd = Users::where('userid', '=', $data['user'])->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Chequebook request forwarded - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
					// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
					// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available Balance</b>: Rs {{pdbalance}}</div>', '', $message);
					// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available LOC</b>: Rs {{locbalance}}</div>', '', $message);
					
					//sendEmail($to, $subject, $message);
				}

				
			}
		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);
	}

	public function accept_loc_dsa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',11)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$q1 = Maptable::where('currentuser','=',$user[0]['username'])->get();

				if($q1->count()==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					$map = $q1[0]['mappeduser'];
					$var = substr($map,4,3);

					if($var == 'ATO')
					{
						$conflag = '5';
					}
					else if($var == 'STO')
					{
						$conflag = '4';
					}
					else if($var == '')
					{
						$conflag = '6';
					}

					if($data['approval']=='reject')
					{
						$date=new DateTime;
						$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->where('conf_flag','=',3)->orderby('id','desc')->first();
						$request->requestflag=0;
						$request->conf_flag=$conflag;
						$request->saflag=1;
						$request->sauser=$user[0]['username'];
						$request->satime=$date;
						$request->remarks=$data['remarks'];
						$request->save();
					}
					else
					{
						$date=new DateTime;
						$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->where('conf_flag','=',3)->orderby('id','desc')->first();
						$request->requestflag=0;
						$request->refno=$data['refno'];
						$request->grantamount=$data['amt'];
						$request->conf_flag = $conflag;
						$request->saflag=0;
						$request->sauser=$user[0]['username'];
						$request->satime=$date;
						$request->remarks=$data['remarks'];
						$request->save();
						//$out = $request->toArray();
					}

					$account = Pdaccount::where('ddocode','=',$data['user'])->where('hoa','=',$data['hoa'])->first();

					$mailtext = "A LOC request has been forwarded by SA with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$data['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$data['amt']."</div>";

						$userd = Users::where('userid', '=', $data['user'])->where('user_role','=',2)->first();

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
		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);		
	}

	// public function adjust_single_party(){
	// 	$value=Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,'invalid');
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$data=Input::get('partydets');
	// 			//var_dump($data);
	// 			$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['pdadmin'])->where('activation','=',2)->get();
	// 			if($pdac->count()==0)
	// 			{
	// 				array_push($out,"error");
	// 			}
	// 			else
	// 			{
	// 				array_push($out,'success');
	// 				$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
	// 				$ntrans=array(
	// 					'transtype'=>1,
	// 					'transdate'=>$date,
	// 					'chequeno'=>$data['cheque'],
	// 					'partyname'=>$data['name'],
	// 					'partyacno'=>$data['acno'],
	// 					'partybank'=>$data['bank'],
	// 					'partyifsc'=>$data['ifsc'],
	// 					'partyamount'=>$data['amount'],
	// 					'issueuser'=>$data['pdadmin'],
	// 					'hoa'=>$data['hoa'],
	// 					'multiflag'=>1,
	// 					'partybranch'=>$data['branch'],
	// 					'transstatus'=>3,
	// 					'purpose'=>$data['purpose'],
	// 					'confirmdate'=>$date
	// 				);
	// 				$nt=Transactions::create($ntrans);
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,'invalid');
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }


	// public function adjust_multiple_party(){
	// 	$value=Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,'invalid');
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$data=Input::all();
	// 			$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['ddo'])->where('activation','=',2)->get();
	// 			if($pdac->count()==0)
	// 			{
	// 				array_push($out,"error");
	// 			}
	// 			else
	// 			{
	// 				array_push($out,'success');
	// 				$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
	// 				$ntrans=array(
	// 					'transtype'=>1,
	// 					'transdate'=>$date,
	// 					'chequeno'=>$data['cheque'],
	// 					'issueuser'=>$data['ddo'],
	// 					'partyamount'=>intval($data['amount']),
	// 					'hoa'=>$data['hoa'],
	// 					'multiflag'=>2,
	// 					'partyfile'=>$data['partyfile'],
	// 					'transstatus'=>3,
	// 					'purpose'=>$data['purpose'],
	// 					'confirmdate'=>$date
	// 				);
	// 				$nt=Transactions::create($ntrans);
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,'invalid');
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	// public function adjust_recipt(){
	// 	$value=Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,'invalid');
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$data=Input::all();
	// 			$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['ddo'])->where('activation','=',2)->get();
	// 			if($pdac->count()==0)
	// 			{
	// 				array_push($out,"error");
	// 			}
	// 			else
	// 			{
	// 				array_push($out,'success');
	// 				$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
	// 				$ntrans=array(
	// 					'transtype'=>2,
	// 					'transdate'=>$date,
	// 					'chequeno'=>$data['cheque'],
	// 					'issueuser'=>$data['ddo'],
	// 					'partyamount'=>intval($data['amount']),
	// 					'hoa'=>$data['hoa'],
	// 					'multiflag'=>1,
	// 					'transstatus'=>3,
	// 					'purpose'=>$data['purpose'],
	// 					'confirmdate'=>$date
	// 				);
	// 				$nt=Transactions::create($ntrans);
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,'invalid');
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	// public function get_ledgerdata(){
	// 	$value=Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$data=Input::get('account');
	// 			$account=Pdaccount::where('accountno','=',$data)->where('activation','=',2)->with('scheme')->first();
	// 			$out=$account;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	// public function get_ledgerpage(){
	// 	$value=Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$hoa=Input::get('hoa');
	// 			$ddo=Input::get('ddo');
	// 			$page=Input::get('page');
	// 			$count=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->count();
	// 			if($page=='last')
	// 			{
	// 				$pages=$count/10;
	// 				if($count%10==0)
	// 				{
	// 					$pageno=intval($pages);
	// 				}
	// 				else
	// 				{
	// 					$pageno=intval($pages)+1;
	// 				}
	// 			}
	// 			else
	// 			{
	// 				$pageno=intval($page);
	// 			}
	// 			if($count<=10)
	// 			{
	// 				$trans=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->orderby('id')->get();
	// 			}
	// 			else
	// 			{
	// 				$skip=($pageno-1)*10;
	// 				$trans=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->orderby('id')->skip($skip)->take(10)->get();
	// 			}
	// 			$out=$trans;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	// public function get_ledgerpagelist(){
	// 	$value=Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$hoa=Input::get('hoa');
	// 			$ddo=Input::get('ddo');
	// 			$count=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->count();
	// 			$out=json_encode(array($count));;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }
}
