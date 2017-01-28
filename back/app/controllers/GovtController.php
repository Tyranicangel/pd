<?php

class GovtController extends BaseController {
	public function govt_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$trans=Transactions::where('transstatus','=',1)->get();
				array_push($out,$trans->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function govt_data_if(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$trans=Transactions::where('transstatus','=',1)->get();
				array_push($out,$trans->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function govt_trans_if(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('transstatus','=',1)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_chq_data_if(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$transid=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('id','=',$transid)->where('transstatus','=',1)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}
	public function govt_chqlist_confirm_if(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$date = new DateTime;
				$data=Input::get('list');
				$rems=Input::get('rems');
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$thistrans = Transactions::whereIn('id',$data)->where('transstatus','=',1)->get();
				$trans=Transactions::query()->whereIn('id',$data)->where('transstatus','=',1)->update(array('transstatus'=>2,'govtdate'=>$date));
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
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "authorized", $message);
					$message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
					$message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
					$message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
					$message = str_ireplace("{{byname}}", 'by Govt', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					// sendEmail($to, $subject, $message);
				}
				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}


	public function govt_chqlist_reject_if(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				array_push($out,'success');
				$userid=$user[0]['userid'];
				foreach($data as $x)
				{
					$trans=Transactions::where('id','=',$x['id'])->where('transstatus','=',1)->first();
					$date = new DateTime;
					if($trans) {
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

						$trans->govtdate=$date;
						$trans->rejects=$x['remarks'];
						$trans->transstatus=21;
						$trans->save();

						//new lines
						$account->transitamount = $newtransit;
						$account->balance = $newbal;
						$account->loc = $newloc;
						$account->save();
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

					$userd = Users::where('userid', '=', $ddo)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque rejected - PD portal";
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($transnew['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "rejected", $message);
					$message = str_ireplace("{{chequeno}}", $transnew['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $transnew['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $transnew['hoa'], $message);
					$message = str_ireplace("{{amount}}", $transnew['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $newbal, $message);
					$message = str_ireplace("{{locbalance}}", $newloc, $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('{{dedtype}}', 'credited', $message);
					$message = str_ireplace("{{byname}}", 'by Govt', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					// sendEmail($to, $subject, $message);
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

	public function govt_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid='govt';//$user[0]['userid'];
				$request=Transactions::where('transstatus','=',1)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$transid=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('id','=',$transid)->where('transstatus','=',1)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$date = new DateTime;
				$data=Input::get('list');
				$rems=Input::get('rems');
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$thistrans = Transactions::whereIn('id',$data)->where('transstatus','=',1)->get();
				$trans=Transactions::query()->whereIn('id',$data)->where('transstatus','=',1)
				->update(array('transstatus'=>2,'govtdate'=>$date));
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
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "authorized", $message);
					$message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
					$message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
					$message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
					$message = str_ireplace("{{byname}}", 'by Govt', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					// sendEmail($to, $subject, $message);
				}
				
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function govt_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				array_push($out,'success');
				$userid=$user[0]['userid'];
				foreach($data as $x)
				{
					$trans=Transactions::where('id','=',$x['id'])->where('transstatus','=',1)->first();
					$date = new DateTime;
					if($trans) {
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

						$trans->govtdate=$date;
						$trans->rejects=$x['remarks'];
						$trans->transstatus=21;
						$trans->save();

						//new lines
						$account->transitamount = $newtransit;
						$account->balance = $newbal;
						$account->loc = $newloc;
						$account->save();
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

					$userd = Users::where('userid', '=', $ddo)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Cheque rejected - PD portal";
					$message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
					$message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($transnew['transdate'])), $message);
					$message = str_ireplace("{{transtype}}", "rejected", $message);
					$message = str_ireplace("{{chequeno}}", $transnew['chequeno'], $message);
					$message = str_ireplace("{{ddocode}}", $transnew['issueuser'], $message);
					$message = str_ireplace("{{hoa}}", $transnew['hoa'], $message);
					$message = str_ireplace("{{amount}}", $transnew['partyamount'], $message);
					$message = str_ireplace("{{pdbalance}}", $newbal, $message);
					$message = str_ireplace("{{locbalance}}", $newloc, $message);
					$message = str_ireplace("{{partydetails}}", $partytext, $message);
					$message = str_ireplace('{{dedtype}}', 'credited', $message);
					$message = str_ireplace("{{byname}}", 'by Govt', $message);
					$message = str_ireplace("{{tableheader}}", $tableheader, $message);
					// sendEmail($to, $subject, $message);
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
	
	public function govt_confirmed_cheques(){
		$value = Request::header('X-CSRFToken');

		$fdateexp=explode("-",Input::get('fdate'));
		$fdate = $fdateexp[2]."-".$fdateexp[1]."-".$fdateexp[0];
		$tdateexp=explode("-",Input::get('tdate'));
		$tdate = $tdateexp[2]."-".$tdateexp[1]."-".$tdateexp[0];

		

		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',3)->orWhere('user_role','=',7)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Transactions::where('govtdate','<=',$tdate)->where('govtdate','>=',$fdate)->where('transtype','=','1')->where('partyamount','>=','10000000')->where('issueuser','LIKE','2702%')->with('requser')->orderBy('govtdate')->get();
				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}


	public function govt_if_acnt_data(){ // sec IF
		$value = Request::header('X-CSRFToken');
		
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Pdaccount::where('ddocode','LIKE','2702%')->where('ddocode','!=','27022002002')->where('ddocode','!=','27029009008')->with('usernames')->where('activation','=', '2')->get();

				// create a file pointer connected to the output stream
				$thisdate = date("Y-m-d-H-i-A");
				$filename = "Total-Outstanding-Balances-and-LOCs-as-of-".$thisdate."-PD-Account-Wise";

				$thisUrl = "uploads/".$filename.".csv";
				$output = fopen($thisUrl, 'w');

				// output the column headings
				fputcsv($output, array('S.No', 'DDOCODE', 'HOA', 'ACCOUNT TYPE', 'BALANCE(in Rs.)', 'LOC(in Rs.)'));

				$i=1;

				foreach ($out as $value) {

					if($value->account_type == 2) {

						$atype = "LOC";
					} else {

						$atype = "NON LOC";
					}
					
					fputcsv($output, array($i,$value->ddocode,$value->hoa, $atype, $value->balance, $value->loc));
					$i++;
				}

				fclose($output);

				$out[0]->fileloc = $thisUrl;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}


	public function govt_confirmed_cheques_for_govtif(){
		$value = Request::header('X-CSRFToken');

		$fdateexp=explode("-",Input::get('fdate'));
		$fdate = $fdateexp[2]."-".$fdateexp[1]."-".$fdateexp[0];
		$tdateexp=explode("-",Input::get('tdate'));
		$tdate = $tdateexp[2]."-".$tdateexp[1]."-".$tdateexp[0];

		$fdate = $fdate.' 00:00:00';
		$tdate = $tdate.' 23:59:59';

		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',7)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Transactions::where('chqflag','=','1')->where('purpose','!=','n/a')->where('transtype','=','1')->where('transdate','<=',$tdate)->where('transdate','>=',$fdate)->where('issueuser','LIKE','2702%')->with('requser')->orderBy('transdate')->where('issueuser','!=','27029009008')->where('issueuser','!=','27022002002')->where('transstatus','!=','31')->get();
				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

}
