<?php

class SaController extends BaseController {

	public function update_transid(){
		$value = Request::header('X-CSRFToken');
		$chq=Input::get('chq');
		$trans=Input::get('trans');
		$ddocode = Input::get('ddocode');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function delete_sa_trans(){
		$value = Request::header('X-CSRFToken');
		$chq=Input::get('chq');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function confirm_sa_account(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		$hoa=Input::get('hoa');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function get_sa_admins(){
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

	public function get_sa_hoas(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function get_sa_ledger(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
		$hoa=Input::get('hoa');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function sa_data(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->get();
				array_push($out,$request->count());
				$trans=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',0)->get();
				array_push($out,$trans->count());
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->get();
				array_push($out,$request->count());
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function sa_request_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function sa_invent_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::get('requser');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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

	public function sa_loc_data(){
		$value = Request::header('X-CSRFToken');
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['requser'])->where('hoa','=',$data['reqhoa'])->where('requestflag','=',0)->where('conf_flag','=',0)->with('requser')->with('schemes')->orderby('id','desc')->first();
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

	public function sa_trans(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('issueuser','like',$userid.'%')->where('transstatus','=',0)->with('requser')->orderby('id')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function sa_chq_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$chq=Input::get('chqno');
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Transactions::where('chequeno','=',$chq)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->with('requser')->orderby('id')->get();
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

	public function sa_chqlist_confirm(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','>',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>0));
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('partyamount','<=',10000000)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>0));
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('multiflag','=',2)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>0));
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function sa_chqlist_reject(){
		$value = Request::header('X-CSRFToken');
		$out=[];
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
				$trans=Transactions::query()->whereIn('chequeno',$data)->where('issueuser','like',$userid.'%')->where('transstatus','=',0)->update(array('transstatus'=>5,'rejects'=>$rems,'conf_flag'=>1));
			}
		}
		else
		{
			array_push($out,"invalid");
		}
		return ")]}',\n".json_encode($out);
	}

	public function sa_requests(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Requests::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->with('requser')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function sa_book_data(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$data=Input::all();
				$request=Inventory::where('bookno','=',$data['book'])->where('used','=',0)->first();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}	

	public function sa_loclist(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$request=Loc::where('userid','=',$userid)->where('requestflag','=',0)->where('conf_flag','=',0)->with('requser')->with('schemes')->get();
				$out=$request;
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->get();
				$ac=Pdaccount::where('hoa','=',$data['acdata']['hoa'])->where('ddocode','=',$data['acdata']['ddo'])->where('activation','=',2)->first();
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
					$nac=array('accountno'=>$acno,'hoa'=>$data['acdata']['hoa'],'ddocode'=>$data['acdata']['ddo'],'balance'=>$data['acdata']['balance'],'modify_date'=>$date,'account_type'=>$data['acdata']['actype'],'userid'=>$userid,'obalance'=>0,'areacode'=>$area,'activation'=>0,'loc'=>0,'category'=>$data['acdata']['cat'],'reference'=>$data['acdata']['remarks']);
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
					$date = new DateTime;
					$request=Requests::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->first();
					$request->conf_flag=1;
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
						$request->conf_flag=1;
						$request->saflag=0;
						$request->sauser=$user[0]['username'];
						$request->satime=$date;
						$request->remarks=$data['remarks'];
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
					$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->first();
					$request->requestflag=0;
					$request->conf_flag=1;
					$request->saflag=1;
					$request->sauser=$user[0]['username'];
					$request->satime=$date;
					$request->remarks=$data['remarks'];
					$request->save();
				}
				else
				{
					$date=new DateTime;
					$request=Loc::where('userid','=',$userid)->where('requestuser','=',$data['user'])->where('hoa','=',$data['hoa'])->where('requestflag','=',0)->where('conf_flag','=',0)->orderby('id','desc')->first();
					$request->requestflag=0;
					$request->refno=$data['refno'];
					$request->grantamount=$data['amt'];
					$request->conf_flag=1;
					$request->saflag=0;
					$request->sauser=$user[0]['username'];
					$request->satime=$date;
					$request->remarks=$data['remarks'];
					$request->save();
				}
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',1)->get();
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
