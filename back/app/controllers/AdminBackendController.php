<?php

class AdminBackendController extends BaseController {

	public function gettryinventory(){
		$try=Input::get('treasury');
		return Inventory::where('userid','=',$try)->orderBy('id')->get();
	}

	public function deleteinv(){
		$try=Input::get('treasury');
		$fid=Input::get('fromid');
		$tid=Input::get('toid');
		Inventory::where('userid','=',$try)->where('id','>=',$fid)->where('id','<=',$tid)->delete();
	}

	public function useinv(){
		$try=Input::get('treasury');
		$fid=Input::get('fromid');
		$tid=Input::get('toid');
		Inventory::where('userid','=',$try)->where('id','>=',$fid)->where('id','<=',$tid)->update(array('used'=>1));
	}

	public function getchqpending(){
		$tlist=Transactions::where('transstatus','=',55)->where('issueuser','not like','2702%')->where('transdate','>=','2015-06-01')->orderBy('transdate')->orderBy('issueuser')->get();
		$trans=$tlist->toArray();
		$out=array();
		for($i=0;$i<count($trans);$i++)
		{
			$ddo=$trans[$i]['issueuser'];
			$hoa=$trans[$i]['hoa'];
			$acc=Pdaccount::where('ddocode','=',$ddo)->where('hoa','=',$hoa)->first();
			if($acc->account_type==1)
			{
				array_push($out,array(
					'issueuser'=>$ddo,
					'hoa'=>$hoa,
					'transdate'=>$trans[$i]['transdate'],
					'id'=>$trans[$i]['id'],
					'transid'=>$trans[$i]['transid'],
					'chequeno'=>$trans[$i]['chequeno'],
					'partyamount'=>$trans[$i]['partyamount'],
					'purpose'=>$trans[$i]['purpose'],
					'balance'=>$acc->balance
					));
			}
		}
		return $out;
	}

	public function get_queries_pending(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{
				$querysys=Query::where('resolveflag','=','0')->get();
				if($querysys->count()==0)
				{
					$out=0;
				}
				else
				{
					$out=$querysys;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}

	public function get_booklistadmin()
	{

		$username = Input::get('username');
		$user=Users::where('username','=',$username)->first();
		if(!$user)
		{
			$out=json_encode(array("invalid"));
		}
		else
		{
			$userid=$user[0]['userid'];
			$out=Pdaccount::where('ddocode','=',$username)->where('activation','=',2)->with('usernames')->with('scheme')->get();
			
		}
		
		return ")]}',\n".$out;
	}


	public function get_queries_resolved(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{
				$querysys=Query::where('resolveflag','=','1')->get();
				if($querysys->count()==0)
				{
					$out=0;
				}
				else
				{
					$out=$querysys;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}

	public function get_queries_forwarded(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{
				$querysys=Query::where('resolveflag','=','2')->get();
				if($querysys->count()==0)
				{
					$out=0;
				}
				else
				{
					$out=$querysys;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}

	public function update_query(){
		$value = Request::header('X-CSRFToken');
		$tdate = date("Y-m-d");
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$queries = Input::All();

				$queryid = $queries['id'];
				if($queries['status'] == "Pending") {

					$queries['status'] = 0;
				} else if($queries['status'] == "Resolved") {

					$queries['status'] = 1;
				} else if($queries['status'] == "Forward") {

					$queries['status'] = 2;
				}

				$querysys=Query::where('id','=',$queryid)->first();
				if($querysys->count()==0)
				{
					$out=0;
				}
				else
				{
					$querysys->remarks= $queries['remarks'];
					$querysys->resolveflag = $queries['status'];
					$querysys->resolve_date = $tdate;

					$querysys->save();
					$out = 1;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}

	public function reset_pass(){
		$value = Request::header('X-CSRFToken');
		$tdate = date("Y-m-d");
		$pass = md5('123456');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('userid');

				$usersys=Users::where('username','=',$userid)->first();
				if($usersys->count()==0)
				{
					$out=0;
				}
				else
				{
					$usersys->password= $pass;
					
					$usersys->save();
					$out = 1;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}
	public function rejectedaccountslist(){
		$currpage=Input::get('currpage');
		$reslim=Input::get('reslim');
		$stocode = Input::get('stocode');

		if($currpage == 1) {

		$out=Pdaccount::where('status','=',2)->where('ddocode', 'LIKE', $stocode.'%')->orderBy('ddocode')->get();
		} else{
		$skip = ($currpage-1) * $reslim;
		$out=Pdaccount::where('status','=',2)->where('ddocode', 'LIKE', $stocode.'%')->orderBy('ddocode')->take($reslim)->skip($skip)->get();
		}
		return $out;
	}

	public function deleterejectedaccountslist(){ 
		$value = Request::header('X-CSRFToken');
		$id = Input::get("id");
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$acc=Pdaccount::where('id','=',$id)->first();
				$acc->delete();
				$out=json_encode(array("success"));
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function updaterejectedaccountslist(){ 
		$value = Request::header('X-CSRFToken');
		$id = Input::get("id");
		$dat=Input::get('dat');
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$acc=Pdaccount::where('id','=',$id)->first();
				$acc->obalance=$dat;
				$acc->status=1;
				$acc->save();
				$out=json_encode(array("success"));
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function sendrejectedaccountslist(){ 
		$value = Request::header('X-CSRFToken');
		$id = Input::get("id");
		$dat=Input::get('dat');
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$acc=Pdaccount::where('id','=',$id)->first();
				$acc->remarks=$dat;
				$acc->status=4;
				$acc->save();
				$out=json_encode(array("success"));
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	// public function add_singlechq(){
	// 	$value = Request::header('X-CSRFToken');
	// 	if($value)
	// 	{
	// 		$user=Users::where('refreshtoken','=',$value)->where('user_role','=',10)->get();
	// 		if($user->count()>0)
	// 		{

	// 			$userid = Input::get('user_id');
	// 			$chqno = Input::get('chqno');

	// 			$usersys=Chequeleaves::where('user','=',$userid)->where('chequeno','=',$chqno)->get();
	// 			if($usersys->count()==0)
	// 			{
					
	// 				$chqmax=Chequeleaves::orderBy('id', 'desc')->where('user','=',$userid)->first();
	// 				$chqmin=Chequeleaves::orderBy('id', 'asc')->where('user','=',$userid)->first();
	// 				if(intval($chqno) < intval($chqmax->chequeno)) {

	// 					$chqlist=Chequeleaves::where('user','=',$userid)->where('usedflag','!=',1)->get();
	// 					$i=0;
	// 					foreach ($chqlist as $key => $value) {
	// 						$chqArr[] = $value->chequeno;
	// 					}
	// 					foreach ($chqArr as $singlechq) {


	// 						$chqlistdel=Chequeleaves::where('user','=',$userid)->where('usedflag','!=',1)->where('chequeno','=',$singlechq)->first();
	// 						$chqlistdel->delete();
	// 					}
						
	// 					$chqArr[] = $chqno;
	// 					sort($chqArr);

	// 					foreach ($chqArr as $singlechq) {

	// 						$chqarrsingle=array(
	// 							'user'=>$userid,
	// 							'chequeno'=>$singlechq,
	// 							'usedflag'=>'0'
	// 							);
	// 						$insertchq=Chequeleaves::create($chqarrsingle);

	// 					}
	// 					$out = 1;

						
	// 				} else if(intval($chqno) > intval($chqmax->chequeno)) {

	// 					$chqarrsingle=array(
	// 							'user'=>$userid,
	// 							'chequeno'=>$chqno,
	// 							'usedflag'=>'0'
	// 							);
	// 					$insertchq=Chequeleaves::create($chqarrsingle);

	// 					$out = 1;
	// 				}
					
	// 			}
	// 			else
	// 			{
					
	// 				$out = 2;
	// 			}
	// 		}
	// 		else
	// 		{
	// 			$out=0;
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$out=0;
	// 	}
		
	// 	return $out;
	// }
	public function add_singlechq(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('user_id');
				$chqno = Input::get('chqno');

				$usersys=Chequeleaves::where('user','=',$userid)->where('chequeno','=',$chqno)->get();
				if($usersys->count()==0)
				{
					
				

					$chqarrsingle=array(
						'user'=>$userid,
						'chequeno'=>$chqno,
						'usedflag'=>'0'
						);
					$insertchq=Chequeleaves::create($chqarrsingle);

					$out = 1;
					
				}
				else
				{
					
					$out = 2;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		
		return $out;
	}
	public function delete_singlechq(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('user_id');
				$chqno = Input::get('chqno');

				$usersys=Chequeleaves::where('user','=',$userid)->where('chequeno','=',$chqno)->first();
				if($usersys->count()==0)
				{
					$out=0;
				}
				else
				{
					if($usersys->usedflag != 1) {

						$usersys->delete();
						$out = 1;
					} else {

						$out = 2;
					}
					
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		return $out;
	}
	public function get_chqlist(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('user_id');
				$usersys=Chequeleaves::where('user','=',$userid)->orderBy('id')->get();
				if($usersys->count()==0)
				{
					$out=0;
				}
				else
				{
					$out = $usersys;
					
				}
			}
			else
			{
				$out=2;
			}
		}
		else
		{
			$out=2;
		}
		return $out;
	}

	public function add_multiplechq(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('user_id');
				$chqnofrm = Input::get('chqnofrm');
				$chqnoto = Input::get('chqnoto');

				if(intval($chqnofrm) < intval($chqnoto)) {

					$chqnofrmint = intval($chqnofrm);

					while($chqnofrmint <= intval($chqnoto)) {

						$chqnothis = str_pad($chqnofrmint,6,'0',STR_PAD_LEFT);
							
						$usersys=Chequeleaves::where('user','=',$userid)->where('chequeno','=',$chqnothis)->get();
						if($usersys->count()==0)
						{
							
							$chqarrsingle=array(
								'user'=>$userid,
								'chequeno'=>$chqnothis,
								'usedflag'=>'0'
								);
							$insertchq=Chequeleaves::create($chqarrsingle);

							$chqnofrmint++;
							$out = 1;
						}
						
					}

				} else {

					$out = 2;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		
		return $out;
	}
	public function delete_multiplechq(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('user_id');
				$chqnofrm = Input::get('chqnofrm');
				$chqnoto = Input::get('chqnoto');

				if(intval($chqnofrm) < intval($chqnoto)) {

					$chqnofrmint = intval($chqnofrm);
					$chqnotoint = intval($chqnoto);

					while($chqnofrmint <= $chqnotoint) {


					    echo $chqnothis = str_pad($chqnofrmint,6,'0',STR_PAD_LEFT);

							
						$usersys=Chequeleaves::where('user','=',$userid)->where('chequeno','=',$chqnothis)->first();
						if($usersys) {
							if($usersys->usedflag != '1') {
							
								$usersys->delete();
							}

					 	$chqnofrmint++;
							$out = 1;
							
						} else {

							$out = 3;
						}
						
					}

				}
				else {

					$out = 2;
				}
			}
			else
			{
				$out=0;
			}
		}
		else
		{
			$out=0;
		}
		
		return $out;
	}

	public function get_queryresult(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->where('user_role','=',50)->get();
			if($user->count()>0)
			{

				$userid = Input::get('data');
				$usersys=Chequeleaves::where('user','=',$userid)->orderBy('id')->get();
				if($usersys->count()==0)
				{
					$out=0;
				}
				else
				{
					$out = $usersys;
					
				}
			}
			else
			{
				$out=2;
			}
		}
		else
		{
			$out=2;
		}
		return $out;
	}

	public function get_ddotransadmin(){
		$ddo=Input::get('ddo');
		$sto=Input::get('sto');
		$fdate=Input::get('fdate');
		$tdate=Input::get('tdate');
		$currpage=Input::get('currpage');
		$reslim=Input::get('reslim');


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

				if($currpage == 1) {

					if($ddo=='all')
					{
						$out=Transactions::where('issueuser','like',$sto.'%')->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->where('transdate','>=',$fdate)->where('transdate','<=',$tdate)->orderby('transdate','desc')->get();
					}
					else
					{
						$out=Transactions::where('issueuser','=',$ddo)->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->where('transdate','>=',$fdate)->where('transdate','<=',$tdate)->orderby('transdate','desc')->get();
					}
				} else {

					$skip = ($currpage-1) * $reslim;
					if($ddo=='all')
					{
						$out=Transactions::where('issueuser','like',$sto.'%')->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->where('transdate','>=',$fdate)->where('transdate','<=',$tdate)->orderby('transdate','desc')->take($reslim)->skip($skip)->get();
					}
					else
					{
						$out=Transactions::where('issueuser','=',$ddo)->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->where('transdate','>=',$fdate)->where('transdate','<=',$tdate)->orderby('transdate','desc')->take($reslim)->skip($skip)->get();
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

	public function get_ddolocadmin(){
		$ddo=Input::get('ddo');
		$sto=Input::get('sto');
		$fdateexp=explode("-",Input::get('fdate'));
		$fdate = $fdateexp[2]."-".$fdateexp[1]."-".$fdateexp[0];
		$tdateexp=explode("-",Input::get('tdate'));
		$tdate = $tdateexp[2]."-".$tdateexp[1]."-".$tdateexp[0];
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
				if($ddo=='all')
				{
					$out=Loc::where('userid','=',$sto)->with('requser')->with('schemes')->where('requestdate', '>=', $fdate)->where('requestdate', '<=', $tdate)->orderby('requestdate')->get();
				}
				else
				{
					$out=Loc::where('userid','=',$sto)->where('requestuser','=',$ddo)->with('requser')->with('schemes')->where('requestdate', '>=', $fdate)->where('requestdate', '<=', $tdate)->orderby('requestdate')->orderby('requestdate')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_if_acnt_dataadmin(){ 
		$value = Request::header('X-CSRFToken');
		$stocode = Input::get("stocode");
		
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Pdaccount::where('ddocode','LIKE',$stocode.'%')->where('ddocode','!=','27022002002')->where('ddocode','!=','27029009008')->with('usernames')->where('activation','=', '2')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_faulty_acnt_dataadmin(){ 
		$value = Request::header('X-CSRFToken');
		
		if($value)
		{
			
			$user=Users::where('refreshtoken','=',$value)->orWhere('user_role','=',50)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['userid'];
				$out = Pdaccount::where('ddocode','!=','27022002002')->where('ddocode','!=','27029009008')->with('usernames')->where('activation','=', '2')->where('balance', '<', '0')
				->orWhere( function ( $query )
			    {
			        $query->where( 'loc', '<', '0' )
			            ->where( 'account_type', '!=', '1' )
			            ->where('ddocode','!=','27029009008')
			            ->where('ddocode','!=','27022002002');
			    })
			    ->orWhere( function ( $query )
			    {
			        $query->whereRaw( 'loc > balance' )
			            ->where('ddocode','!=','27029009008')
			            ->where('ddocode','!=','27022002002');
			    })
				->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function govt_confirmed_cheques_for_govtifadmin(){
		$value = Request::header('X-CSRFToken');

		$fdateexp=explode("-",Input::get('fdate'));
		$fdate = $fdateexp[2]."-".$fdateexp[1]."-".$fdateexp[0];
		$tdateexp=explode("-",Input::get('tdate'));
		$tdate = $tdateexp[2]."-".$tdateexp[1]."-".$tdateexp[0];

		$ddo = Input::get('ddo');

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
				if($ddo == 'all') {

					$out = Transactions::where('chqflag','=','1')->where('purpose','!=','n/a')->where('transtype','=','1')->where('transdate','<=',$tdate)->where('transdate','>=',$fdate)->with('requser')->orderBy('transdate')->where('issueuser','!=','27029009008')->where('issueuser','!=','27022002002')->where('transstatus','!=','31')->get();
				} else {
				
					$out = Transactions::where('issueuser','=',$ddo)->where('chqflag','=','1')->where('purpose','!=','n/a')->where('transtype','=','1')->where('transdate','<=',$tdate)->where('transdate','>=',$fdate)->with('requser')->orderBy('transdate')->where('issueuser','!=','27029009008')->where('issueuser','!=','27022002002')->where('transstatus','!=','31')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function add_bank() {

		$bankname = Input::get('bankname');
		$bankbranch = Input::get('bankbranch');
		$bankifsc = Input::get('bankifsc');
		$bankmicr = Input::get('bankmicr');
		$bankaddress = Input::get('bankaddress');
		$bankcontact = Input::get('bankcontact');
		$bankcenter = Input::get('bankcenter');
		$bankdistrict = Input::get('bankdistrict');
		$bankstate = Input::get('bankstate');

		$banks = Banks::where('ifsccode', '=', $bankifsc)->get();
		if($banks->count() == 0) {

			$bankifscadd=array(
				'bankname'=>$bankname,
				'ifsccode'=>$bankifsc,
				'microcode'=>$bankmicr,
				'branch'=>$bankbranch,
				'address'=>$bankaddress,
				'contact'=>$bankcontact,
				'center'=>$bankcenter,
				'district'=>$bankdistrict,
				'state'=>$bankstate
				);
			$addifsc=Banks::create($bankifscadd);

			return 1;


		} else {

			return 0;
		}
		

	}

	public function getaccrpt() {

		$value = Request::header('X-CSRFToken');


		$user=Users::where('refreshtoken','=',$value)->first();
		$countarr = array();
		if($user) {

			$uniqueuser = OfcType::orderBy('code')->get();	

			for($i=0;$i<count($uniqueuser);$i++) {

				$areathis = substr($uniqueuser[$i]['code'], 0, 2);

				$areanamethis = Areas::where('areacode', '=', $areathis)->first();

				if($areanamethis) {
					$x = $areanamethis->areaname;
				
			    } else {

			    	$x = "-";
			    }


				if($uniqueuser[$i]['type'] == 2) {

					$actype = "DTO";
				} else {

					$actype = "STO";
				}

				$uname=Users::where('username','=',$uniqueuser[$i]['code'])->first();
				if($uname) {

					$countarr[$i]['actype'] = $actype;

					$countarr[$i]['areaname'] = $uname->userdesc;

					$countarr[$i]['name'] = $uniqueuser[$i]['code'];

					$getcountapp = Pdaccount::where('status','=', 1)->where('ddocode', 'LIKE', $uniqueuser[$i]['code'].'%')->get();

					$countarr[$i]['app'] = count($getcountapp);

					$getcountrej = Pdaccount::where('status','=', 2)->where('ddocode', 'LIKE', $uniqueuser[$i]['code'].'%')->get();

					$countarr[$i]['rej'] = count($getcountrej);

					$getcountpen = Pdaccount::where('status','=', 0)->where('ddocode', 'LIKE', $uniqueuser[$i]['code'].'%')->get();

					$countarr[$i]['pen'] = count($getcountpen);
				}
			}


		} 

		return $countarr;;
	}

	public function getsapendinglocs() {

		$locs = Loc::where('conf_flag', '=', '3')->where('requestuser', 'not like', '2702%')->with('requser')->where('admin_approve', '=', 0)->where('requestdate','>=','2015-06-01')->orderBy('requestdate')->orderBy('requestuser')->get();
		return $locs;
	}

	public function approvelocadmin() {

		$id = Input::get('locid');

		$locs = Loc::where('id','=', $id)->first();
		if($locs) {

			$locs->admin_approve = 1;
			$locs->save();

			return 1;
		}

	}

	public function rejectlocadmin() {

		$id = Input::get('locid');
		$remarks = Input::get('remarks');

		$locs = Loc::where('id','=', $id)->first();
		if($locs) {

			$locs->admin_approve = 2;
			$locs->admin_remarks = $remarks;
			$locs->save();

			return 1;
		}

	}

	public function approvechqadmin() {

		$id = Input::get('chqid');

		$rems = Input::get('rems');

		$trans = Transactions::where('id','=', $id)->first();
		if($trans) {

			$trans->transstatus = 2;
			$trans->rejects = $rems;
			$trans->save();

			return 1;
		} else {

			return 0;
		}

	}

	public function rejectchqadmin() {

		$id = Input::get('chqid');

		$rems = Input::get('rems');

		$trans = Transactions::where('id','=', $id)->first();
		if($trans) {

			$trans->transstatus = 21;
			$trans->rejects = $rems;
			$trans->save();

			return 1;
		} else {

			return 0;
		}

	}

	public function locactivity(){
		$fdate=new DateTime(Input::get('date'));
		$tdate=new DateTime(Input::get('date'));
		$tdate->modify('+1 day');
		$ofc=OfcType::where('code','=','2702')->orWhere('code','=','2213')->orWhere('code','like','%01')->orderby('code')->get()->toArray();
		$out=array();
		for($i=0;$i<count($ofc);$i++)
		{
			if($ofc[$i]['loc_pass_auth']=='2')
			{
				$passed=Loc::where('userid','=',$ofc[$i]['code'])->where('requestflag','=',1)->where('conf_flag','!=',2)->where('ddtime','>',$fdate)->where('ddtime','<',$tdate)->count();
				$rejected=Loc::where('userid','=',$ofc[$i]['code'])->where('requestflag','=',1)->where('conf_flag','=',2)->where('ddtime','>',$fdate)->where('ddtime','<',$tdate)->count();
			}
			else
			{
				$passed=Loc::where('userid','=',$ofc[$i]['code'])->where('requestflag','=',1)->where('conf_flag','!=',2)->where('atotime','>',$fdate)->where('atotime','<',$tdate)->count();
				$rejected=Loc::where('userid','=',$ofc[$i]['code'])->where('requestflag','=',1)->where('conf_flag','=',2)->where('atotime','>',$fdate)->where('atotime','<',$tdate)->count();
			}
			$pending=Loc::where('userid','=',$ofc[$i]['code'])->where('requestflag','=',0)->where('conf_flag','!=',33)->count();
			array_push($out,array($ofc[$i]['code'],$passed,$rejected,$pending));
		}
		return $out;
	}

	public function chqactivity(){
		$fdate=new DateTime(Input::get('date'));
		$tdate=new DateTime(Input::get('date'));
		$tdate->modify('+1 day');
		$ofc=OfcType::where('code','=','2702')->orWhere('code','=','2213')->orWhere('code','like','%01')->orderby('code')->get()->toArray();
		$out=array();
		for($i=0;$i<count($ofc);$i++)
		{
			if($ofc[$i]['loc_pass_auth']=='2')
			{
				$passed=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',3)->where('ddtime','>',$fdate)->where('ddtime','<',$tdate)->count();
				$passed1=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',2)->where('ddtime','>',$fdate)->where('ddtime','<',$tdate)->count();
			}
			else
			{
				$passed=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',3)->where('atotime','>',$fdate)->where('atotime','<',$tdate)->count();
				$passed1=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',2)->where('atotime','>',$fdate)->where('atotime','<',$tdate)->count();
			}
			$confirmed=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',3)->where('confirmdate','>',$fdate)->where('confirmdate','<',$tdate)->count();
			$pendingbank=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','=',2)->count();
			$pending=Transactions::where('issueuser','like',$ofc[$i]['code'].'%')->where('transtype','=',1)->where('transstatus','!=',2)->where('transstatus','!=',0)->where('transstatus','!=',61)->where('transstatus','!=',5)->where('transstatus','!=',55)->where('transstatus','!=',21)->where('transstatus','!=',31)->where('transstatus','!=',3)->count();
			array_push($out,array($ofc[$i]['code'],($passed+$passed1),$pending,$confirmed,$pendingbank));
		}
		return $out;
	}

	public function getcurrentacno() {

		$ddocode = Input::get('ddocode');
		$hoa = Input::get('hoa');

		$pdaccount = Pdaccount::where('ddocode', '=', $ddocode)->where('hoa', '=', $hoa)->first();
		if($pdaccount) {

			$out = $pdaccount['pdacno'];
		} else {

			$out = "error";
		}

		return $out;
	}

	public function updatecurrentacno() {

		$ddocode = Input::get('ddocode');
		$hoa = Input::get('hoa');
		$pdacno = Input::get('pdacno');

		$pdaccount = Pdaccount::where('ddocode', '=', $ddocode)->where('hoa', '=', $hoa)->first();
		if($pdaccount) {

			$pdaccount->pdacno = $pdacno;
			$pdaccount->save();
			$out = 1;
		} else {

			$out = "error";
		}

		return $out;
	}

}

?>