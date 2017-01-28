<?php

class AtoController extends BaseController {

	public function ao_booklist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
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
				$date=new DateTime;
				foreach($data as $key)
				{
					$trans=Requests::where('id','=',$key)->with('bookdata')->first();
					$trans->requestflag=1;
					$trans->conf_flag=2;
					$trans->remarks=$rems;
					$trans->atotime=$date;
					$trans->save();
					if($trans->bookdata)
					{
						$inv=Inventory::where('bookno','=',$trans->bookdata->bookno)->first();
						$inv->used=0;
						$inv->save();
						$chqs=Cheques::where('bookno','=',$trans->bookdata->bookno)->first();
						$chqs->delete();
					}

					$mailtext = "A Chequebook request has been rejected by DTO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$trans['leaves']."</div>";

					$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Chequebook request rejected - PD portal";
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
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}
	

	public function ao_booklist_returntosa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
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
				$date=new DateTime;
				foreach($data as $key)
				{
					$trans=Requests::where('id','=',$key)->with('bookdata')->first();
					$trans->requestflag=0;
					$trans->conf_flag=0;
					$trans->remarks=$rems;
					$trans->atotime=$date;
					$trans->save();
					if($trans->bookdata)
					{
						$inv=Inventory::where('bookno','=',$trans->bookdata->bookno)->first();
						$inv->used=0;
						$inv->save();
						$chqs=Cheques::where('bookno','=',$trans->bookdata->bookno)->first();
						$chqs->delete();
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
	
	public function ao_booklist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
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
				$date=new DateTime;
				foreach ($data as $key) {
					$trans=Requests::where('id','=',$key)->first();
					$trans->requestflag=1;
					$trans->remarks=$rems;
					$trans->atotime=$date;
					$trans->save();
					$book=Cheques::where('requestid','=',$key)->first();
					$cstart=intval($book->chequestart);
					$cend=intval($book->chequeend);
					while($cstart<=$cend)
					{
						$csmain = str_pad($cstart,6,"0",STR_PAD_LEFT);
						$nlf=array('user'=>$book->reciptuser,'chequeno'=>$csmain,'usedflag'=>0);
						$nl=Leaves::create($nlf);
						$cstart++;
					}

					$mailtext = "A Chequebook request has been approved by DTO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$trans['leaves']."</div>";

					$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Chequebook request approved - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/locchequetemplate.html");
					// $message = str_ireplace("{{maincontent}}", $mailtext, $message);
					// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available Balance</b>: Rs {{pdbalance}}</div>', '', $message);
					// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;"><b>Available LOC</b>: Rs {{locbalance}}</div>', '', $message);
					
					//sendEmail($to, $subject, $message);
				//	$nlf=array('user'=>$book->reciptuser,'chequeno'=>$cend,'usedflag'=>0);
				//	$nl=Leaves::create($nlf);
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_ato_admins(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Pdaccount::where('userid','=',$userid)->where('activation','=',2)->groupBy('ddocode')->with('usernames')->get(array('ddocode'));
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ato_hoas(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
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

	

	public function ato_requests(){
		$value = Request::header('X-CSRFToken');
		$sas=array();
		$stos = array();
		$out = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$username = $user[0]['username'];

				$q1 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'STO%')->get()->toArray();
				$q4 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'SA%')->get()->toArray();

				if(count($q1)==0 && count($q4)==0)
				{
				}
				else
				{
					for ($z=0; $z < count($q4); $z++) { //put sas into sas array
						array_push($sas, $q4[$z]['currentuser']);
					}

					for ($i=0; $i < count($q1); $i++) {  //get stos list
						array_push($stos, $q1[$i]['currentuser']);
					}

					if(count($stos)!=0)
					{
						$q3 = Maptable::whereIn('mappeduser',$stos)->get()->toArray();

						for ($y=0; $y < count($q3) ; $y++) { //put sas into sas array
							
							array_push($sas,$q3[$y]['currentuser']);
						}
					}

					$q5 = ChequeBookUsers::where('userid','=',$userid)->get();
					if($q5->count()==0)
					{
					}
					else
					{
						$sa = $q5[0]['sauser'];
						if(in_array($sa,$sas))
						{
							$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',5)->with('requser')->with('bookdata')->get();
							$out=$request->toArray();
						}
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

	public function ato_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$hoas = array();
		$sas = array();
		$stos = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$username = $user[0]['username'];

				$q1 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'STO%')->get()->toArray();
				$q4 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'SA%')->get()->toArray();

				if(count($q1)==0 && count($q4)==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					for ($z=0; $z < count($q4); $z++) { //put sas into sas array
						array_push($sas, $q4[$z]['currentuser']);
					}

					for ($i=0; $i < count($q1); $i++) {  //get stos list
						array_push($stos, $q1[$i]['currentuser']);
					}

					if(count($stos)!=0)
					{
						$q3 = Maptable::whereIn('mappeduser',$stos)->get()->toArray();

						for ($y=0; $y < count($q3) ; $y++) { //put sas into sas array
							
							array_push($sas,$q3[$y]['currentuser']);
						}
					}

					if(count($sas)!=0)
					{
						$q2 = Pdaccount::select('hoa')->whereIn('mapto',$sas)->distinct()->get()->toArray(); 
						
						for ($j=0; $j <count($q2) ; $j++) { //get hoas
							array_push($hoas,$q2[$j]['hoa']);
						}


						$q5 = ChequeBookUsers::where('userid','=',$userid)->get();
						if($q5->count()==0)
						{
							array_push($out,"0");
						}
						else
						{
							$sa = $q5[0]['sauser'];
							if(in_array($sa,$sas))
							{
								$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',5)->get();
								array_push($out,$request->count());
							}
							else
							{
								array_push($out,"0");
							}
							
						}
						if(count($hoas)==0)
						{
							array_push($out,"0");
							array_push($out,"0");
						}
						else
						{
							$trans=Transactions::where('issueuser','like',$userid.'%')->whereIn('hoa',$hoas)->where('transstatus','=',64)->get();
							array_push($out,$trans->count());
							$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->whereIn('hoa',$hoas)->where('conf_flag','=',5)->get();
							array_push($out,$request->count());
						}
					}
					else
					{
						array_push($out,'nomap');
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


	public function ato_trans(){
		$value = Request::header('X-CSRFToken');
		$sas = array();
		$hoas = array();
		$stos = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];

				$username = $user[0]['username'];
				$q1 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'STO%')->get()->toArray();
				$q4 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'SA%')->get()->toArray();

				if(count($q1)==0 && count($q4)==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					for ($z=0; $z < count($q4); $z++) { //put sas into sas array
						array_push($sas, $q4[$z]['currentuser']);
					}

					for ($i=0; $i < count($q1); $i++) {  //get stos list
						array_push($stos, $q1[$i]['currentuser']);
					}

					if(count($stos)!=0)
					{
						$q3 = Maptable::whereIn('mappeduser',$stos)->get()->toArray();
						for ($y=0; $y < count($q3) ; $y++) { //put sas into sas array
							
							array_push($sas,$q3[$y]['currentuser']);
						}
					}

					$q2 = Pdaccount::select('hoa')->whereIn('mapto',$sas)->distinct()->get()->toArray(); 
					
					for ($j=0; $j <count($q2) ; $j++) { //get hoas
						array_push($hoas,$q2[$j]['hoa']);
					}

					if(count($hoas)==0)
					{
						$out=json_encode([]);
					}
					else
					{
						$request=Transactions::where('issueuser','like',$userid.'%')->whereIn('hoa',$hoas)->where('transstatus','=',64)->with('requser')->with('accountdet')->orderby('id')->get();

						for ($i=0; $i < count($request); $i++) { 

							$q = Pdaccount::where('hoa','=',$request[$i]['hoa'])->where('ddocode','=',$request[$i]['issueuser'])->first();
							if($q['lapsableflag']=='1')
							{
								$recid = $request[$i]['laprecid'];
								$q10 = Transactions::where('id','=',$recid)->with('laptrans')->first()->toArray();
								$request[$i]['laprecinfo'] = $q10;
							}else
							{
								$request[$i]['laprecinfo'] = '';
							}

							$request[$i]['thisbalance'] = $q['balance'] + $q['transitamount'];

						}
						
						$out=$request;
					}
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}


	public function ato_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				
				$userid=$user[0]['userid'];

				$date = new DateTime;

				$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->get();

				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)
				->update(array('transstatus'=>65,'atouser'=>$user[0]['username'],'atotime'=>$date,'atoremarks'=>$rems,'conf_flag'=>0));

				for($i=0;$i<count($thistrans);$i++) {

						$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans[$i]['hoa'])->where('ddocode','=',$thistrans[$i]['issueuser'])->where('activation','=',2)->first();

							$partytext = "";
							$tableheader = "";

							if($thistrans[$i]['multiflag'] == 2) {

								if($thistrans[$i]['pdtopdflag'] == 1) {

									$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
								} else {

									$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
								}

								$partyfile = "uploads/".$thistrans[$i]['partyfile'];

								$fp=fopen($partyfile,'r');

								$c=1;
								$x =1;

								while($datafile=fgetcsv($fp)){
									if($c==0)
									{

										if($thistrans[$i]['pdtopdflag'] == 1) {

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

								$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyamount'].'</td></tr>';

							}

						$userd = Users::where('userid', '=', $thistrans[$i]['issueuser'])->where('user_role','=',2)->first();

						if($userd->emailid) {

							$to = $userd->emailid;
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque forwarded - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
						// $message = str_ireplace("{{transtype}}", "forwarded", $message);
						// $message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
						// $message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
						// $message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
						// $message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
						// $message = str_ireplace("{{byname}}", 'by ATO', $message);
						// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
						//sendEmail($to, $subject, $message);
					}

				
				array_push($out,'success');
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}


	public function ato_return_sa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				
				$userid=$user[0]['userid'];

				$date = new DateTime;

				$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->get();

				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)
				->update(array('transstatus'=>62,'atouser'=>$user[0]['username'],'atotime'=>$date,'atoremarks'=>$rems,'conf_flag'=>0));

				for($i=0;$i<count($thistrans);$i++) {

						$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans[$i]['hoa'])->where('ddocode','=',$thistrans[$i]['issueuser'])->where('activation','=',2)->first();

							$partytext = "";
							$tableheader = "";

							if($thistrans[$i]['multiflag'] == 2) {

								if($thistrans[$i]['pdtopdflag'] == 1) {

									$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
								} else {

									$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
								}

								$partyfile = "uploads/".$thistrans[$i]['partyfile'];

								$fp=fopen($partyfile,'r');

								$c=1;
								$x =1;

								while($datafile=fgetcsv($fp)){
									if($c==0)
									{

										if($thistrans[$i]['pdtopdflag'] == 1) {

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

								$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyamount'].'</td></tr>';

							}

						$userd = Users::where('userid', '=', $thistrans[$i]['issueuser'])->where('user_role','=',2)->first();

						if($userd->emailid) {

							$to = $userd->emailid;
						} else {

							$to = "garamaiah@gmail.com";
						}
						$subject = "Cheque returned - PD portal";
						// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
						// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
						// $message = str_ireplace("{{transtype}}", "returned", $message);
						// $message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
						// $message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
						// $message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
						// $message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
						// $message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
						// $message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
						// $message = str_ireplace("{{partydetails}}", $partytext, $message);
						// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
						// $message = str_ireplace("{{byname}}", 'by ATO', $message);
						// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
						//sendEmail($to, $subject, $message);
					}

				
				array_push($out,'success');
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}
	
	

	public function ato_booklist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
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

				$date = new DateTime;
				$username = $user[0]['username'];

				foreach ($data as $key) {
						$trans=Requests::where('id','=',$key)->first();
						$trans->atotime=$date;
						$trans->atoremarks=$rems;
						$trans->atouser=$username;
						$trans->conf_flag = '6';
						$trans->save();

						$mailtext = "A Chequebook request has been forwarded by ATO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>No of leaves:</b> ".$trans['leaves']."</div>";

						$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

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
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function ato_loclist_approve(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');

				array_push($out,'success');
				$date=new DateTime;
				$userid=$user[0]['userid'];
				foreach ($data as $key) {

					$trans=Loc::where('id','=',$key)->first();
					$trans->requestflag=1;
					$trans->remarks=$rems;
					$trans->atotime=$date;
					$trans->save();

					$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
					$loc=$account['loc'];
					$newloc=$loc+$trans->grantamount;
					$account->loc=$newloc;
					$account->save();

					$mailtext = "A LOC request has been approved by ATO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$trans['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$trans['grantamount']."</div>";

					$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Loc request approved - PD portal";
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
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}



	

	public function ato_loclist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$ids = Input::get('list');
		$rems = Input::get('rems');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$date = new DateTime;

				$request=Loc::whereIn('id',$ids)->get();
				for ($i=0; $i < count($request); $i++) { 

					$request[$i]->requestflag=0;
					$request[$i]->conf_flag = 6;
					$request[$i]->atoremarks=$rems;
					$request[$i]->atouser=$user[0]['username'];
					$request[$i]->atotime=$date;
					$request[$i]->save();

					$account = Pdaccount::where('ddocode','=',$request[$i]['requestuser'])->where('hoa','=',$request[$i]['hoa'])->first();

					$mailtext = "A LOC request has been forwarded by ATO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$request[$i]['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$request[$i]['grantamount']."</div>";

					$userd = Users::where('userid', '=', $request[$i]['requestuser'])->where('user_role','=',2)->first();

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
	// public function dto_loclist_confirm(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 			foreach ($data as $key) {
	// 				$trans=Loc::where('id','=',$key)->first();
	// 				$trans->requestflag=1;
	// 				$trans->remarks=$rems;
	// 				$trans->save();
	// 				$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
	// 				$loc=$account['loc'];
	// 				$newloc=$loc+$trans->grantamount;
	// 				$account->loc=$newloc;
	// 				$account->save();
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	public function ato_loclist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
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
				$trans=Loc::query()->whereIn('id',$data)->update(array('requestflag'=>'1','conf_flag'=>'2','remarks'=>$rems));

				$thislocs = Loc::whereIn('id',$data)->get();
				for($i=0;$i<count($thislocs);$i++) {
					$mailtext = "A LOC request has been rejected by ATO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$thislocs[$i]['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Requested Amount:</b> Rs. ".$thislocs[$i]['reqamount']."</div>";

					$userd = Users::where('userid', '=', $thislocs[$i]['requestuser'])->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}

					$account = Pdaccount::where('ddocode', '=', $thislocs[$i]['requestuser'])->where('hoa','=', $thislocs[$i]['hoa'])->first();
					// $subject = "Loc request rejected - PD portal";
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
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function ato_loclist_return(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
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
				$trans=Loc::query()->whereIn('id',$data)->update(array('requestflag'=>'0','conf_flag'=>'3','atoremarks'=>$rems));

				$thislocs = Loc::whereIn('id',$data)->get();
				for($i=0;$i<count($thislocs);$i++) {
					$mailtext = "A LOC request has been returned to SA by ATO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$thislocs[$i]['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Requested Amount:</b> Rs. ".$thislocs[$i]['reqamount']."</div>";

					$userd = Users::where('userid', '=', $thislocs[$i]['requestuser'])->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}

					$account = Pdaccount::where('ddocode', '=', $thislocs[$i]['requestuser'])->where('hoa','=', $thislocs[$i]['hoa'])->first();
					// $subject = "Loc request returned to SA - PD portal";
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
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function ato_chqlist_approve(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
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
				$date=new DateTime;

				$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->get();

				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->update(array('transstatus'=>2,'rejects'=>$rems,'atotime'=>$date));

				//$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->update(array('transstatus'=>55,'rejects'=>$rems,'atotime'=>$date));

				for($i=0;$i<count($thistrans);$i++) {
					$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans[$i]['hoa'])->where('ddocode','=',$thistrans[$i]['issueuser'])->where('activation','=',2)->first();
					$partytext = "";
					$tableheader = "";
					if($thistrans[$i]['multiflag'] == 2) {
						if($thistrans[$i]['pdtopdflag'] == 1) {
							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						} else {
							$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
						}
						$partyfile = "uploads/".$thistrans[$i]['partyfile'];
						$fp=fopen($partyfile,'r');
						$c=1;
						$x =1;
						while($datafile=fgetcsv($fp)){
							if($c==0)
							{
								if($thistrans[$i]['pdtopdflag'] == 1) {
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
						$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyamount'].'</td></tr>';
					}
					$userd = Users::where('userid', '=', $thistrans[$i]['issueuser'])->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque authorized - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
					// $message = str_ireplace("{{transtype}}", "authorized", $message);
					// $message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
					// $message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
					// $message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
					// $message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
					// $message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
					// $message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
					// $message = str_ireplace("{{partydetails}}", $partytext, $message);
					// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
					// $message = str_ireplace("{{byname}}", 'by ATO', $message);
					// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
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

	public function ato_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				array_push($out,'success');
				$date=new DateTime;
				$userid=$user[0]['userid'];
				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',64)->update(array('transstatus'=>21,'rejects'=>$rems,'atotime'=>$date));
				
				foreach ($data as $x) {
					$transnew = Transactions::where('id','=',$x)->where('issueuser','like',$userid.'%')->first();
					$pamt = $transnew['partyamount'];
					$hoa = $transnew['hoa'];
					$ddocode = $transnew['issueuser'];
					$account = Pdaccount::where('ddocode','=',$ddocode)->where('hoa','=',$hoa)->first();
					$transit = $account['transitamount'];
					$bal = $account['balance'];
					$loc = $account['loc'];
					
					$lapflag = $account['lapsableflag'];

					if($lapflag=='1')
					{
						$laprecid = $transnew['laprecid'];
						$q1 = Transactions::where('id','=',$laprecid)->first();
						$lapexp = $q1->lapexp;
						$nlapexp = $lapexp - $pamt;
						$q1->lapexp = $nlapexp;
						$q1->save();
					}

					$newbal = $bal + $pamt;
					$newtransit = $transit - $pamt;
					$newloc = $loc + $pamt;

					$accounttype = $account['account_type'];
					if($accounttype == 2)
					{
						$account->balance=$newbal;
						$account->transitamount=$newtransit;
						$account->loc=$newloc;
						$account->save();
					}else
					{
						$account->balance=$newbal;
						$account->transitamount=$newtransit;
						$account->save();
					}



						$partytext = "";
						$tableheader = "";

						if($transnew['multiflag'] == 2) {

							if($transnew['pdtopdflag'] == 1) {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							} else {

								$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
							}

							$partyfile = "uploads/".$transnew['partyfile'];

							$fp=fopen($partyfile,'r');

							$c=1;
							$x =1;

							while($datafile=fgetcsv($fp)){
								if($c==0)
								{

									if($transnew['pdtopdflag'] == 1) {

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

							$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$transnew['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$transnew['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$transnew['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$transnew['partyamount'].'</td></tr>';

						}

					$userd = Users::where('userid', '=', $ddocode)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque rejected - PD portal";
					// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($transnew['transdate'])), $message);
					// $message = str_ireplace("{{transtype}}", "rejected", $message);
					// $message = str_ireplace("{{chequeno}}", $transnew['chequeno'], $message);
					// $message = str_ireplace("{{ddocode}}", $transnew['issueuser'], $message);
					// $message = str_ireplace("{{hoa}}", $transnew['hoa'], $message);
					// $message = str_ireplace("{{amount}}", $transnew['partyamount'], $message);
					// $message = str_ireplace("{{pdbalance}}", $newbal, $message);
					// $message = str_ireplace("{{locbalance}}", $newloc, $message);
					// $message = str_ireplace("{{partydetails}}", $partytext, $message);
					// $message = str_ireplace('{{dedtype}}', 'credited', $message);
					// $message = str_ireplace("{{byname}}", 'by ATO', $message);
					// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
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

	// public function dto_requests(){
	// 	$value = Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',1)->with('requser')->with('bookdata')->get();
	// 			$out=$request;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	public function ato_loclist(){
		$value = Request::header('X-CSRFToken');
		$hoas = array();
		$sas = array();
		$stos = array();

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',9)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$username = $user[0]['username'];

				$q1 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'STO%')->get()->toArray();
				$q4 = Maptable::where('mappeduser','=',$username)->where('currentuser','LIKE',$userid.'SA%')->get()->toArray();

				if(count($q1)==0 && count($q4)==0)
				{
					array_push($out,'nomap');
				}
				else
				{
					for ($z=0; $z < count($q4); $z++) { //put sas into sas array
						array_push($sas, $q4[$z]['currentuser']);
					}

					for ($i=0; $i < count($q1); $i++) {  //get stos list
						array_push($stos, $q1[$i]['currentuser']);
					}

					if(count($stos)!=0)
					{
						$q3 = Maptable::whereIn('mappeduser',$stos)->get()->toArray();

						for ($y=0; $y < count($q3) ; $y++) { //put sas into sas array
							
							array_push($sas,$q3[$y]['currentuser']);
						}
					}

					$q2 = Pdaccount::select('hoa')->whereIn('mapto',$sas)->distinct()->get()->toArray(); 
					
					for ($j=0; $j <count($q2) ; $j++) { //get hoas
						array_push($hoas,$q2[$j]['hoa']);
					}
					if(count($hoas)==0)
					{
						$out=json_encode([]);
					}
					else
					{
						$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->whereIn('hoa',$hoas)->where('conf_flag','=',5)->with('requser')->with('schemes')->get();

						$r=$request->toArray();
						for($i=0;$i<count($r);$i++)
						{
							$ac=Pdaccount::where('hoa','=',$r[$i]['hoa'])->where('ddocode','=',$r[$i]['requestuser'])->first();
							$r[$i]['balance']=$ac->balance;
							$r[$i]['exloc']=$ac->loc;
						}
						$out=json_encode($r);
					}
				}



				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	// public function create_account()
	// {
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	$data=Input::all();
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,"invalid");
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->where('activation','=',2)->get();
	// 			$oac=Pdaccount::orderby('accountno','desc')->first();
	// 			$acno=intval($oac->accountno)+1;
	// 			if($ac->count()!=0)
	// 			{
	// 				array_push($out,"exists");
	// 			}
	// 			else
	// 			{
	// 				array_push($out,"created");
	// 				$date = new DateTime;
	// 				$area=substr($userid,0,2);
	// 				$nac=array('accountno'=>$acno,'hoa'=>$data['acdata']['hoa'],'ddocode'=>$data['acdata']['ddo'],'balance'=>$data['acdata']['balance'],'modify_date'=>$date,'account_type'=>$data['acdata']['actype'],'userid'=>$userid,'obalance'=>0,'areacode'=>$area,'activation'=>2,'loc'=>0);
	// 				$cac=Pdaccount::create($nac);
	// 				$sc=Schemes::where('hoa','=',$data['acdata']['hoa'])->get();
	// 				if($sc->count()==0)
	// 				{
	// 					$nsc=array('schemename'=>$data['acdata']['hoaname'],'modified'=>$date,'hoa'=>$data['acdata']['hoa']);
	// 					$csc=Schemes::create($nsc);
	// 				}
	// 				$us=Users::where('username','=',$data['acdata']['ddo'])->get();
	// 				$tkn=md5($data['acdata']['ddo'].microtime());
	// 				if($us->count()==0)
	// 				{
	// 					$nus=array('username'=>$data['acdata']['ddo'],'password'=>'e10adc3949ba59abbe56e057f20f883e','userid'=>$data['acdata']['ddo'],'user_role'=>2,'modify_date'=>$date,'refreshtoken'=>$tkn,'userdesc'=>$data['acdata']['ddoname']);
	// 					$cus=Users::create($nus);
	// 				}
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"error");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	// public function accept_request(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	$data=Input::all();
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,"invalid");
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('requestflag','=',0)->orderby('id','desc')->first();
	// 			$request->requestflag=1;
	// 			$request->save();
	// 			$date = new DateTime;
	// 			$nchq=array('issueuser'=>$userid,'reciptuser'=>$data['user'],'chequestart'=>$data['chequedata']['first'],'chequeend'=>$data['chequedata']['last'],'bookno'=>$data['chequedata']['number'],'issuedate'=>$date,'complete'=>0);
	// 			$nc=Cheques::create($nchq);
	// 			$cstart=intval($data['chequedata']['first']);
	// 			$cend=intval($data['chequedata']['last']);
	// 			while($cstart!=$cend)
	// 			{
	// 				$nlf=array('user'=>$data['user'],'chequeno'=>$cstart,'usedflag'=>0);
	// 				$nl=Leaves::create($nlf);
	// 				$cstart++;
	// 			}
	// 			$nlf=array('user'=>$data['user'],'chequeno'=>$cend,'usedflag'=>0);
	// 			$nl=Leaves::create($nlf);
	// 			if($nc)
	// 			{
	// 				array_push($out,'success');
	// 			}
	// 			else
	// 			{
	// 				array_push($out,'error');
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"error");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	// public function accept_loc(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	$data=Input::all();
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
	// 		if($user->count()==0)
	// 		{
	// 			array_push($out,"invalid");
	// 		}
	// 		else
	// 		{
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->orderby('id','desc')->first();
	// 			$request->requestflag=1;
	// 			$request->refno=$data['refno'];
	// 			$request->grantamount=$data['amt'];
	// 			$request->save();
	// 			$account=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['user'])->where('activation','=',2)->first();
	// 			$loc=$account['loc'];
	// 			$newloc=$loc+$data['amt'];
	// 			$account->loc=$newloc;
	// 			$account->save();
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"error");
	// 	}
	// 	return ")]}',\n".json_encode($out);		
	// }

	// public function adjust_single_party(){
	// 	$value=Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
