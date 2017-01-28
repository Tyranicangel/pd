<?php

class DtoController extends BaseController {

	public function delete_dto_trans(){
		$value = Request::header('X-CSRFToken');
		$chq=Input::get('chq');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$account=Transactions::where('transstatus','=',3)->where('chequeno','=',$chq)->first();
				$account->delete();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_account_dets_dto(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
	public function add_invent(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function confirm_dto_account(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		$hoa=Input::get('hoa');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$account=Pdaccount::where('hoa','=',$hoa)->where('ddocode','=',$ddo)->where('activation','=',2)->first();
				$account->activation=2;
				$account->save();
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_dto_admins(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function edit_ddo(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
                if($value)
                {
                        $user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function get_dto_hoas(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function get_dto_ledger(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$ddos=Transactions::where('hoa','=',$hoa)->where('issueuser','=',$ddo)->where('transstatus','=',3)->get();
				$out=$ddos;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function view_invent(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$accounts=Inventory::where('userid','=',$userid)->get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dto_ac_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function dto_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',1)->get();
				array_push($out,$request->count());
				$trans=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',5)->get();
				array_push($out,$trans->count());
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',1)->get();
				array_push($out,$request->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_request_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data)->where('requestflag','=',0)->orderby('id','desc')->with('requser')->with('leaves')->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dto_loc_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['requser'])->where('hoa','=',$data['reqhoa'])->where('requestflag','=',0)->with('requser')->with('schemes')->with('accounts')->orderby('id','desc')->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dto_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',5)->with('requser')->with('accountdet')->orderby('id')->get();

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

	public function dto_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$chq=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('chequeno','=',$chq)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dto_aclist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function dto_aclist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				$trans=Pdaccount::query()->whereIn('id',$data)->where('userid','=',$userid)->update(array('activation'=>'3','reason'=>$rems));
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_booklist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				foreach ($data as $key) {
					$trans=Requests::where('id','=',$key)->first();
					$trans->requestflag=1;
					$trans->remarks=$rems;
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

	public function dto_booklist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				foreach($data as $key)
				{
					$trans=Requests::where('id','=',$key)->with('bookdata')->first();
					$trans->requestflag=1;
					$trans->conf_flag=2;
					$trans->remarks=$rems;
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

	public function dto_loclist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				foreach ($data as $key) {
					$trans=Loc::where('id','=',$key)->first();
					$trans->requestflag=1;
					$trans->remarks=$rems;
					$trans->save();
					$account=Pdaccount::where('hoa','=',$trans->hoa)->where('ddocode','=',$trans->requestuser)->where('activation','=',2)->first();
					$loc=$account['loc'];
					$newloc=$loc+$trans->grantamount;
					$account->loc=$newloc;
					$account->save();
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_loclist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_loclist_return(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				$trans=Loc::query()->whereIn('id',$data)->update(array('requestflag'=>'0','conf_flag'=>'0','remarks'=>$rems));
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','>',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->update(array('transstatus'=>2,'rejects'=>$rems));	
				
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','<=',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->update(array('transstatus'=>2,'rejects'=>$rems));

				$trans=Transactions::query()->whereIn('chequeno',$data)->where('multiflag','=',2)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->update(array('transstatus'=>2,'rejects'=>$rems));
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',5)->update(array('transstatus'=>21,'rejects'=>$rems));
				
				foreach ($data as $x) {
					$transnew = Transactions::where('chequeno','=',$x)->first();
					$pamt = $transnew['partyamount'];
					$hoa = $transnew['hoa'];
					$ddocode = $transnew['issueuser'];
					$account = Pdaccount::where('ddocode','=',$ddocode)->where('hoa','=',$hoa)->first();
					$transit = $account['transitamount'];
					$bal = $account['balance'];
					$loc = $account['loc'];

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
				}
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function dto_requests(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',1)->with('requser')->with('bookdata')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function dto_loclist(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',1)->with('requser')->with('schemes')->get();
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

	public function create_account()
	{
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->where('activation','=',2)->get();
				$oac=Pdaccount::orderby('accountno','desc')->first();
				$acno=intval($oac->accountno)+1;
				if($ac->count()!=0)
				{
					array_push($out,"exists");
				}
				else
				{
					array_push($out,"created");
					$date = new DateTime;
					$area=substr($userid,0,2);
					$nac=array('accountno'=>$acno,'hoa'=>$data['acdata']['hoa'],'ddocode'=>$data['acdata']['ddo'],'balance'=>$data['acdata']['balance'],'modify_date'=>$date,'account_type'=>$data['acdata']['actype'],'userid'=>$userid,'obalance'=>0,'areacode'=>$area,'activation'=>2,'loc'=>0);
					$cac=Pdaccount::create($nac);
					$sc=Schemes::where('hoa','=',$data['acdata']['hoa'])->get();
					if($sc->count()==0)
					{
						$nsc=array('schemename'=>$data['acdata']['hoaname'],'modified'=>$date,'hoa'=>$data['acdata']['hoa']);
						$csc=Schemes::create($nsc);
					}
					$us=Users::where('username','=',$data['acdata']['ddo'])->get();
					$tkn=md5($data['acdata']['ddo'].microtime());
					if($us->count()==0)
					{
						$nus=array('username'=>$data['acdata']['ddo'],'password'=>'e10adc3949ba59abbe56e057f20f883e','userid'=>$data['acdata']['ddo'],'user_role'=>2,'modify_date'=>$date,'refreshtoken'=>$tkn,'userdesc'=>$data['acdata']['ddoname']);
						$cus=Users::create($nus);
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

	public function accept_request(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('requestflag','=',0)->orderby('id','desc')->first();
				$request->requestflag=1;
				$request->save();
				$date = new DateTime;
				$nchq=array('issueuser'=>$userid,'reciptuser'=>$data['user'],'chequestart'=>$data['chequedata']['first'],'chequeend'=>$data['chequedata']['last'],'bookno'=>$data['chequedata']['number'],'issuedate'=>$date,'complete'=>0);
				$nc=Cheques::create($nchq);
				$cstart=intval($data['chequedata']['first']);
				$cend=intval($data['chequedata']['last']);
				while($cstart!=$cend)
				{
					$nlf=array('user'=>$data['user'],'chequeno'=>$cstart,'usedflag'=>0);
					$nl=Leaves::create($nlf);
					$cstart++;
				}
				$nlf=array('user'=>$data['user'],'chequeno'=>$cend,'usedflag'=>0);
				$nl=Leaves::create($nlf);
				if($nc)
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
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);
	}

	public function accept_loc(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->orderby('id','desc')->first();
				$request->requestflag=1;
				$request->refno=$data['refno'];
				$request->grantamount=$data['amt'];
				$request->save();
				$account=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['user'])->where('activation','=',2)->first();
				$loc=$account['loc'];
				$newloc=$loc+$data['amt'];
				$account->loc=$newloc;
				$account->save();
			}
		}
		else
		{
			array_push($out,"error");
		}
		return ")]}',\n".json_encode($out);		
	}

	public function adjust_single_party(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$data=Input::get('partydets');
				//var_dump($data);
				$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['pdadmin'])->where('activation','=',2)->get();
				if($pdac->count()==0)
				{
					array_push($out,"error");
				}
				else
				{
					array_push($out,'success');
					$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
					$ntrans=array(
						'transtype'=>1,
						'transdate'=>$date,
						'chequeno'=>$data['cheque'],
						'partyname'=>$data['name'],
						'partyacno'=>$data['acno'],
						'partybank'=>$data['bank'],
						'partyifsc'=>$data['ifsc'],
						'partyamount'=>$data['amount'],
						'issueuser'=>$data['pdadmin'],
						'hoa'=>$data['hoa'],
						'multiflag'=>1,
						'partybranch'=>$data['branch'],
						'transstatus'=>3,
						'purpose'=>$data['purpose'],
						'confirmdate'=>$date
					);
					$nt=Transactions::create($ntrans);
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}


	public function adjust_multiple_party(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$data=Input::all();
				$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['ddo'])->where('activation','=',2)->get();
				if($pdac->count()==0)
				{
					array_push($out,"error");
				}
				else
				{
					array_push($out,'success');
					$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
					$ntrans=array(
						'transtype'=>1,
						'transdate'=>$date,
						'chequeno'=>$data['cheque'],
						'issueuser'=>$data['ddo'],
						'partyamount'=>intval($data['amount']),
						'hoa'=>$data['hoa'],
						'multiflag'=>2,
						'partyfile'=>$data['partyfile'],
						'transstatus'=>3,
						'purpose'=>$data['purpose'],
						'confirmdate'=>$date
					);
					$nt=Transactions::create($ntrans);
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function adjust_recipt(){
		$value=Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				array_push($out,'invalid');
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$data=Input::all();
				$pdac=Pdaccount::where('hoa','=',$data['hoa'])->where('ddocode','=',$data['ddo'])->where('activation','=',2)->get();
				if($pdac->count()==0)
				{
					array_push($out,"error");
				}
				else
				{
					array_push($out,'success');
					$date = DateTime::createFromFormat('d-m-Y', $data['dates']);
					$ntrans=array(
						'transtype'=>2,
						'transdate'=>$date,
						'chequeno'=>$data['cheque'],
						'issueuser'=>$data['ddo'],
						'partyamount'=>intval($data['amount']),
						'hoa'=>$data['hoa'],
						'multiflag'=>1,
						'transstatus'=>3,
						'purpose'=>$data['purpose'],
						'confirmdate'=>$date
					);
					$nt=Transactions::create($ntrans);
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_ledgerdata(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$data=Input::get('account');
				$account=Pdaccount::where('accountno','=',$data)->where('activation','=',2)->with('scheme')->first();
				$out=$account;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ledgerpage(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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

	public function get_ledgerpagelist(){
		$value=Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',6)->get();
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
}
