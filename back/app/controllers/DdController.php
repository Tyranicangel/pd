<?php

class DdController extends BaseController {

	// public function delete_dto_trans(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$chq=Input::get('chq');
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
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$account=Transactions::where('transstatus','=',3)->where('chequeno','=',$chq)->first();
	// 			$account->delete();
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	public function add_invent(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$inv=Inventory::where('bookno','=',$data['book'])->get();
				if($inv->count()>0)
				{
					array_push($out,'This book is already added');
				}
				else
				{
					array_push($out,'success');
					$userid=$user[0]['userid'];
					$date = new DateTime;
					$leaves=intval($data['last'])-intval($data['first'])+1;
					$ni=array(
						'userid'=>$userid,
						'bookno'=>$data['book'],
						'cstart'=>$data['first'],
						'cend'=>$data['last'],
						'used'=>0,
						'size'=>$leaves,
						'createdate'=>$date
						);
					$niv=Inventory::create($ni);
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function add_bulk(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$tbookdata=$data['book'][0];
				$fbookdata=$data['book'][1];
				$hbookdata=$data['book'][2];
				for($i=0;$i<count($tbookdata);$i++)
				{
					$inv=Inventory::where('bookno','=',$tbookdata[$i][0])->get();
					if($inv->count()>0)
					{
					}
					else
					{
						$userid=$user[0]['userid'];
						$date = new DateTime;
						$leaves=intval($tbookdata[$i][3]);
						$ni=array(
							'userid'=>$userid,
							'bookno'=>$tbookdata[$i][0],
							'cstart'=>$tbookdata[$i][1],
							'cend'=>$tbookdata[$i][2],
							'used'=>0,
							'size'=>$leaves,
							'createdate'=>$date
							);
						$niv=Inventory::create($ni);
					}
				}
				for($i=0;$i<count($fbookdata);$i++)
				{
					$inv=Inventory::where('bookno','=',$fbookdata[$i][0])->get();
					if($inv->count()>0)
					{
					}
					else
					{
						$userid=$user[0]['userid'];
						$date = new DateTime;
						$leaves=intval($fbookdata[$i][3]);
						$ni=array(
							'userid'=>$userid,
							'bookno'=>$fbookdata[$i][0],
							'cstart'=>$fbookdata[$i][1],
							'cend'=>$fbookdata[$i][2],
							'used'=>0,
							'size'=>$leaves,
							'createdate'=>$date
							);
						$niv=Inventory::create($ni);
					}
				}
				for($i=0;$i<count($hbookdata);$i++)
				{
					$inv=Inventory::where('bookno','=',$hbookdata[$i][0])->get();
					if($inv->count()>0)
					{
					}
					else
					{
						$userid=$user[0]['userid'];
						$date = new DateTime;
						$leaves=intval($hbookdata[$i][3]);
						$ni=array(
							'userid'=>$userid,
							'bookno'=>$hbookdata[$i][0],
							'cstart'=>$hbookdata[$i][1],
							'cend'=>$hbookdata[$i][2],
							'used'=>0,
							'size'=>$leaves,
							'createdate'=>$date
							);
						$niv=Inventory::create($ni);
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


	public function get_ofctype(){
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
				$q1 = OfcType::where('code','=',$user[0]['userid'])->get();
				if($q1->count()==0)
				{
					
				}
				else
				{
					$q2 = $q1->toArray();
					$out = $q2[0];
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function update_auth_user(){
		$value = Request::header('X-CSRFToken');

		$chqauthuser=Input::get('chqauthuser');
		$locauthuser=Input::get('locauthuser');
		$bookauthuser=Input::get('bookauthuser');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$q1 = OfcType::where('code','=',$user[0]['userid'])->first();
				if($q1)
				{

					$q1->cheque_pass_auth = $chqauthuser;
					$q1->loc_pass_auth = $locauthuser;
					$q1->book_pass_auth=$bookauthuser;
					$q1->save();
					array_push($out, "1");
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dd_booklist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
					$trans->ddtime=$date;
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
	// public function confirm_dto_account(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$ddo=Input::get('ddo');
	// 	$hoa=Input::get('hoa');
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
	// 			array_push($out,'success');
	// 			$userid=$user[0]['userid'];
	// 			$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$ddo)->where('activation','=',2)->first();
	// 			$account->activation=2;
	// 			$account->save();
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	public function edit_dd_user_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$out=json_encode(array('success'));
				$data=Input::get('dat');
				$userid=$user[0]['userid'];
				for($i=0;$i<count($data);$i++)
				{
					$userdat=Users::where('username','=',$data[$i]['username'])->first();
					$userdat->userdesc=$data[$i]['userdesc'];
					$userdat->save();

					$userdat=Users::where('username','=',$data[$i]['username'].'auth')->first();
					$userdat->userdesc=$data[$i]['userdesc'];
					$userdat->save();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_dd_user_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Users::where('userid','=',$userid)->where('user_role','!=',8)->where('user_role','>',8)->orderby('user_role','desc')->orderby('username')->get();
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_dd_admins(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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


	public function get_sa_user_list(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Users::where('user_role','=','11')->where('userid','=',$userid)->with('mappedto')->with('chequemap')->get()->toArray();

			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}


	public function get_sa_chq_map_list(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = ChequeBookUsers::where('userid','=',$userid)->first();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function post_chq_map_sa(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$sauser = Input::get('dat');
				array_push($out,"success");

				$q3 = ChequeBookUsers::where('userid','=',$userid)->get();
				if(count($q3)==0)
				{
					$nq = array('sauser' => $sauser, 
								'userid' => $userid);
					$q1 = ChequeBookUsers::create($nq);
				}
				else
				{
					$q1 = ChequeBookUsers::query()->where('userid','=',$userid)->update(array('sauser' => $sauser));	
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}


	public function get_sto_user_list(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Users::where('user_role','=','10')->where('userid','=',$userid)->with('mappedto')->get()->toArray();

			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}

	// public function get_sa_dets_ants(){
	// 	$value = Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$out = Users::where('user_role','=','11')->where('userid','=',$userid)->get();
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	public function edit_ddo(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
                if($value)
                {
                        $user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
                        if($user->count()==0)
                        {
                                $out=json_encode(array("invalid"));
                        }
                        else
                        {
				$out=json_encode(array("success"));
				$ddo=Users::where('username','=',$data['ddocode'])->first();
				$ddo->userdesc=$data['ddoname'];
				$ddo->save();
                        }
                }
                else
                {
                        $out=json_encode(array("invalid"));
                }
                return ")]}',\n".$out;
	}

	public function get_dd_hoas(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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

	// public function get_dto_ledger(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$ddo=Input::get('ddo');
	// 	$hoa=Input::get('hoa');
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

	public function view_invent(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$accounts=Inventory::where('userid','=',$userid)->orderby('cstart')->get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dd_ac_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$accounts=Pdaccount::where('userid','=',$userid)->where('activation','=',0)->with('usernames')->with('scheme')->get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dd_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',6)->get();
				array_push($out,$request->count());
				$trans=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',65)->get();
				array_push($out,$trans->count());
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',6)->get();
				array_push($out,$request->count());

				$accounts=Pdaccount::where('userid','=',$userid)->where('activation','=',0)->get();
				array_push($out,$accounts->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	// public function dto_request_data(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$data=Input::get('requser');
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
	// 			$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data)->where('requestflag','=',0)->orderby('id','desc')->with('requser')->with('leaves')->first();
	// 			$out=$request;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }


	public function get_hoas_under_sa(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		//$hoas = array();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$usr = $userid.'%';

				$out1 = DB::select(DB::raw("SELECT * FROM (SELECT id,hoa,mapto FROM pdaccountinfo WHERE id IN(
				  SELECT MIN(id)
				    FROM pdaccountinfo WHERE ddocode LIKE '$usr'
				    GROUP BY hoa)) AS p INNER JOIN schemes ON p.hoa = schemes.hoa"));
				
				
				
				array_push($out,$out1);
				
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}


	public function post_hoa_map_dets(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$hoa = Input::get('dat');
				for ($i=0; $i < count($hoa); $i++) { 
					$q = Pdaccount::query()->where('hoa','=',$hoa[$i]['hoa'])->where('ddocode','like',$userid.'%')->update(array('mapto' => $hoa[$i]['sauser']));
				}
				$out=json_encode(array("success"));
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}


	public function post_sa_map_dets(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$sa = Input::get('dat');
				for ($i=0; $i < count($sa); $i++) { 
					$q = Maptable::where('currentuser','=',$sa[$i]['username'])->get();
					if($q->count()==0)
					{
						$map = array('currentuser' => $sa[$i]['username'],
									 'mappeduser' => $sa[$i]['mapuser']);
						$q1 = Maptable::create($map);
					}
					else
					{
						$q = Maptable::query()->where('currentuser','=',$sa[$i]['username'])->update(array('mappeduser' => $sa[$i]['mapuser']));
					}
					
				}

				array_push($out,'success');

			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}


	public function post_sto_map_dets(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$sa = Input::get('dat');
				for ($i=0; $i < count($sa); $i++) { 
					$q = Maptable::where('currentuser','=',$sa[$i]['username'])->get();
					if($q->count()==0)
					{
						$map = array('currentuser' => $sa[$i]['username'],
									 'mappeduser' => $sa[$i]['mapuser']);
						$q1 = Maptable::create($map);
					}
					else
					{
						$q = Maptable::query()->where('currentuser','=',$sa[$i]['username'])->update(array('mappeduser' => $sa[$i]['mapuser']));
					}
					
				}

				array_push($out,'success');

			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".json_encode($out);
	}


	public function get_users_above_sa(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Users::whereIn('user_role',array('8','9','10'))->where('userid','=',$userid)->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_users_above_sto(){
		$value = Request::header('X-CSRFToken');
		$out = array();
		
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Users::whereIn('user_role',array('8','9'))->where('userid','=',$userid)->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	// public function dto_loc_data(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$data=Input::all();
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
	// 			$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['requser'])->where('hoa','=',$data['reqhoa'])->where('requestflag','=',0)->with('requser')->with('schemes')->with('accounts')->orderby('id','desc')->first();
	// 			$out=$request;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	public function dd_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',65)->with('requser')->with('accountdet')->orderby('id')->get();

				for ($i=0; $i < count($request); $i++) { 

					$q = Pdaccount::where('hoa','=',$request[$i]['hoa'])->where('ddocode','=',$request[$i]['issueuser'])->first();
					if($q['lapsableflag']=='1')
					{
						$recid = $request[$i]['laprecid'];
						$q2 = Transactions::where('id','=',$recid)->with('laptrans')->first()->toArray();
						$request[$i]['laprecinfo'] = $q2;
					}else
					{
						$request[$i]['laprecinfo'] = '';
					}
					$request[$i]['thisbalance'] = $q['balance'] + $q['transitamount'];
				}
				
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	// public function dto_chq_data(){
	// 	$value = Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$chq=Input::get('chqno');
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
	// 		if($user->count()==0)
	// 		{
	// 			$out=json_encode(array("invalid"));
	// 		}
	// 		else
	// 		{
	// 			$userid=$user[0]['userid'];
	// 			$request=Transactions::where('chequeno','=',$chq)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->with('requser')->orderby('id')->get();
	// 			$out=$request;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=json_encode(array("invalid"));
	// 	}
	// 	return ")]}',\n".$out;
	// }

	public function dd_aclist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
				$trans=Pdaccount::query()->whereIn('id',$data)->where('userid','=',$userid)->update(array('activation'=>'2','reason'=>$rems));
				for($i=0;$i<count($data);$i++)
				{
					$id = $data[$i];
					$nq = Pdaccount::where('id','=',$id)->first()->toArray();
					$ddocode = $nq['ddocode'];
					$q1 = Users::where('userid','=',$ddocode)->first();
					$q1->chqflag = '1';
					$q1->save();
					
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dd_aclist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
				
				// for($i=0;$i<count($data);$i++)
				// {
				// 	$id = $data[$i];
				// 	$nq = Pdaccount::where('id','=',$id)->first()->toArray();
				// 	$ddocode = $nq['ddocode'];
				// 	$q1 = Users::where('userid','=',$ddocode)->delete();
				// }

				$trans=Pdaccount::query()->whereIn('id',$data)->where('userid','=',$userid)->delete();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dd_return_sa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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

				$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->get();

				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)
				->update(array('transstatus'=>62,'ddtime'=>$date,'rejects'=>$rems,'conf_flag'=>0));

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
						// $message = str_ireplace("{{byname}}", 'by DTO', $message);
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
	

	public function get_account_dets(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$dat = Input::get('dat');

				if($dat == 'sa')
				{
					$role = '11';
				}
				else if($dat == 'sto')
				{
					$role = '10';
				}
				else if($dat == 'ato')
				{
					$role = '9';
				}

				$userid=$user[0]['userid'];
				$out = Users::where('user_role','=',$role)->where('userid','=',$userid)->get();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".$out;
	}

	public function crt_new_acnt_dd(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$userid=$user[0]['userid'];
				$dat = Input::get('dat');
				$desc = Input::get('desc');
				if($dat=='sa')
				{
					$role = '11';
					$uname = $userid.'SA01';
				}
				else if($dat == 'sto')
				{
					$role = '10';
					$uname = $userid.'STO01';
				}
				else if($dat == 'ato')
				{
					$role = '9';
					$uname = $userid.'ATO01';
				}

				$q1 = Users::where('user_role','=',$role)->where('userid','=',$userid)->get();

				$date = new DateTime;
				$defpass = md5('123456');
				

				
				if($q1->count()==0) //not there
				{
					$tkn = md5($uname.microtime());

					$nq = array('username' => $uname, 
								'password' => $defpass,
								'userid' => $userid,
								'user_role' => $role,
								'modify_date' => $date,
								'refreshtoken' => $tkn,
								'userdesc' => $desc,
								'chqflag' => '0');
					
					$q2 = Users::create($nq);
					$out = $uname;
				}
				else
				{
					$maxu = Users::where('user_role','=',$role)->where('userid','=',$userid)->max('username');
					$num = substr($maxu,'-2');
					$rest = substr($maxu,'0','-2');
					$num = $num + 1;
					$num = str_pad($num,2,"0",STR_PAD_LEFT);

					$fusername = $rest.$num;
					$tkn = md5($fusername.microtime());

					$nq = array('username' => $fusername, 
								'password' => $defpass,
								'userid' => $userid,
								'user_role' => $role,
								'modify_date' => $date,
								'refreshtoken' => $tkn,
								'userdesc' => $desc,
								'chqflag' => '0');

					$q2 = Users::create($nq);
					$out = $fusername;
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	// public function dto_booklist_confirm(){
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
	// 				$trans=Requests::where('id','=',$key)->first();
	// 				$trans->requestflag=1;
	// 				$trans->remarks=$rems;
	// 				$trans->save();
	// 				$book=Cheques::where('requestid','=',$key)->first();
	// 				$cstart=intval($book->chequestart);
	// 				$cend=intval($book->chequeend);
	// 				while($cstart<=$cend)
	// 				{
	// 					$csmain = str_pad($cstart,6,"0",STR_PAD_LEFT);
	// 					$nlf=array('user'=>$book->reciptuser,'chequeno'=>$csmain,'usedflag'=>0);
	// 					$nl=Leaves::create($nlf);
	// 					$cstart++;
	// 				}
	// 			//	$nlf=array('user'=>$book->reciptuser,'chequeno'=>$cend,'usedflag'=>0);
	// 			//	$nl=Leaves::create($nlf);
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	public function dd_booklist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
					$trans->ddtime=$date;
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

	public function dd_booklist_returntosa(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
					$trans->ddtime=$date;
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

	public function dd_loclist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
					$trans->ddtime=$date;
					$trans->save();

					$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
					$loc=$account['loc'];
					$newloc=$loc+$trans->grantamount;
					$account->loc=$newloc;
					$account->save();

					$mailtext = "A LOC request has been approved by DTO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$trans['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$trans['grantamount']."</div>";

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

	public function dd_loclist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				$date=new DateTime;
				array_push($out,'success');
				$userid=$user[0]['userid'];

				$trans=Loc::query()->whereIn('id',$data)->update(array('requestflag'=>'1','conf_flag'=>'2','remarks'=>$rems,'ddtime'=>$date));

				foreach ($data as $key) {

					$trans=Loc::where('id','=',$key)->first();
					
					$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
					

					$mailtext = "A LOC request has been rejected by DTO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$trans['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$trans['grantamount']."</div>";

					$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Loc request rejected - PD portal";
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

	public function dd_loclist_return(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				$data=Input::get('list');
				$rems=Input::get('rems');
				$date=new DateTime;
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$trans=Loc::query()->whereIn('id',$data)->update(array('requestflag'=>'0','conf_flag'=>'3','remarks'=>$rems,'ddtime'=>$date));

				foreach ($data as $key) {

					$trans=Loc::where('id','=',$key)->first();
					
					$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
					

					$mailtext = "A LOC request has been returned to SA by DTO with following details:<br><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>HOA:</b> ".$trans['hoa']."</div><div style='float: left;width: 50%;height: 25px;line-height: 25px;'><b>Granted Amount:</b> Rs. ".$trans['grantamount']."</div>";

					$userd = Users::where('userid', '=', $trans->requestuser)->where('user_role','=',2)->first();

					if($userd->emailid) {

						$to = $userd->emailid;
					} else {

						$to = "garamaiah@gmail.com";
					}
					$subject = "Loc request returned - PD portal";
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

	// dta authorization section---------------------------------------------------------------------------------------------------

	// public function dd_chqlist_confirm(){
	// 	$value = Request::header('X-CSRFToken');
	// 	$out=[];
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
	// 			$date=new DateTime;

	// 			$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->get();

	// 			$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->update(array('transstatus'=>55,'rejects'=>$rems,'ddtime'=>$date));

	// 			for($i=0;$i<count($thistrans);$i++) {
	// 				$pdaccountinfo = Pdaccount::where('hoa','=',$thistrans[$i]['hoa'])->where('ddocode','=',$thistrans[$i]['issueuser'])->where('activation','=',2)->first();
	// 				$partytext = "";
	// 				$tableheader = "";
	// 				if($thistrans[$i]['multiflag'] == 2) {
	// 					if($thistrans[$i]['pdtopdflag'] == 1) {
	// 						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">DDOCODE</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">HOA</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
	// 					} else {
	// 						$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
	// 					}
	// 					$partyfile = "uploads/".$thistrans[$i]['partyfile'];
	// 					$fp=fopen($partyfile,'r');
	// 					$c=1;
	// 					$x =1;
	// 					while($datafile=fgetcsv($fp)){
	// 						if($c==0)
	// 						{
	// 							if($thistrans[$i]['pdtopdflag'] == 1) {
	// 								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[3].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[4].'</td></tr>';
	// 							} else {
	// 								$partytext .= '<tr><td style="border:1px solid #bababa;text-align:center;">'.$x.'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[1].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[2].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[5].'</td><td style="border:1px solid #bababa;text-align:center;">'.$datafile[6].'</td></tr>';
	// 							}
	// 							$x++;
	// 						}
	// 						else
	// 						{
	// 							$c=0;
	// 						}
	// 					}
	// 				} else {
	// 					$tableheader = '<tr><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Sno</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Name</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Account No</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">IFSC Code</th><th style="background:#474646;color:#bababa;border:1px solid #bababa;">Amount (in Rs)</th></tr>';
	// 					$partytext = '<tr><td style="border:1px solid #bababa;text-align:center;">1</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyname'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyacno'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyifsc'].'</td><td style="border:1px solid #bababa;text-align:center;">'.$thistrans[$i]['partyamount'].'</td></tr>';
	// 				}
	// 				$userd = Users::where('userid', '=', $thistrans[$i]['issueuser'])->where('user_role','=',2)->first();

	// 				if($userd->emailid) {

	// 					$to = $userd->emailid;
	// 				} else {

	// 					$to = "garamaiah@gmail.com";
	// 				}
	// 				$subject = "Cheque authorized - PD portal";
	// 				// $message = file_get_contents("http://www.money-line.in/pd/mailtemplate.html");
	// 				// $message = str_ireplace("{{chequedate}}", date("d/m/Y", strtotime($thistrans[$i]['transdate'])), $message);
	// 				// $message = str_ireplace("{{transtype}}", "authorized", $message);
	// 				// $message = str_ireplace("{{chequeno}}", $thistrans[$i]['chequeno'], $message);
	// 				// $message = str_ireplace("{{ddocode}}", $thistrans[$i]['issueuser'], $message);
	// 				// $message = str_ireplace("{{hoa}}", $thistrans[$i]['hoa'], $message);
	// 				// $message = str_ireplace("{{amount}}", $thistrans[$i]['partyamount'], $message);
	// 				// $message = str_ireplace("{{pdbalance}}", $pdaccountinfo['balance'], $message);
	// 				// $message = str_ireplace("{{locbalance}}", $pdaccountinfo['loc'], $message);
	// 				// $message = str_ireplace("{{partydetails}}", $partytext, $message);
	// 				// $message = str_ireplace('<div style="float: left;width: 100%;height: 25px;line-height: 25px;">Your PD account has been {{dedtype}} for the above amount.</div>', '', $message);
	// 				// $message = str_ireplace("{{byname}}", 'by DTO', $message);
	// 				// $message = str_ireplace("{{tableheader}}", $tableheader, $message);
	// 				//sendEmail($to, $subject, $message);
	// 			}
	// 		}
	// 	}
	// 	else
	// 	{
	// 		array_push($out,"invalid");
	// 	}
	// 	return ")]}',\n".json_encode($out);
	// }

	// dd authorization section ends ------------------------------------------------------------------------------------------------

	public function dd_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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

				$thistrans = Transactions::whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->get();

				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->update(array('transstatus'=>2,'rejects'=>$rems,'ddtime'=>$date));

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
					// $message = str_ireplace("{{byname}}", 'by DTO', $message);
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

	public function dd_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
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
				$trans=Transactions::query()->whereIn('id',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',65)->update(array('transstatus'=>21,'rejects'=>$rems,'ddtime'=>$date));
				
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
					// $message = str_ireplace("{{byname}}", 'by DTO', $message);
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

	public function dd_requests(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',6)->with('requser')->with('bookdata')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dd_loclist(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',6)->with('requser')->with('schemes')->get();
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

	public function get_dd_all_accounts(){
		$value = Request::header('X-CSRFToken');
		$status = Input::get("status");
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				if($status==0)
				{
					$ddos=Pdaccount::where('userid','=',$userid)->where('activation','=',2)->where('status', '=', $status)->with('usernames')->get();
				}
				else if($status==1)
				{
					$ddos=Pdaccount::where('userid','=',$userid)->where('activation','=',2)->where('status', '=', $status)->with('usernames')->get();
				}
				else
				{
					$ddos=Pdaccount::where('userid','=',$userid)->where('activation','=',2)->where('status', '=', $status)->with('usernames')->get();
				}
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function update_pd_status(){
		$value = Request::header('X-CSRFToken');
		$hoa = Input::get('hoa');
		$ddocode = Input::get('ddocode');
		$status = Input::get('status');
		$remark = Input::get('remarks');

		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',8)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$ddos=Pdaccount::where('ddocode','=',$ddocode)->where('hoa','=',$hoa)->first();
				 if($ddos) {

					$ddos->status = $status;
					$ddos->remarks = $remark;
					if($status=='1')
					{
						$ddos->activation=2;
					}
					$ddos->save();

					if($status == 1) {

						$userac = Users::where('userid', '=', $ddocode)->update(array('active_flag' => 1));
					}
				 }
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$ddos;
	}

}
