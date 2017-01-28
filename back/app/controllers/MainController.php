<?php

class MainController extends BaseController {

	public function get_areas(){
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
				$out=Areas::whereNotIn('id', [13,14,15,16,17,18,19,20,21,23])->orderby('areaname')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_panchayat_hoas(){
		$data=Input::all();
		$areas = $data['arealist'];
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
				$out=array('8448001090001002000NVN'=>array(),'8448001090001001000NVN'=>array(),'8448001090001005000NVN'=>array());
				$arealist=array();
				foreach ($areas as $key => $area) {
					$out['8448001090001002000NVN'][$area['areacode']] = array();
					$out['8448001090001001000NVN'][$area['areacode']] = array();
					$out['8448001090001005000NVN'][$area['areacode']] = array();

					$x=Pdaccount::select('areacode','accountno','hoa','ddocode','balance','obalance','modify_date')->where('areacode','=',$area['areacode'])->where('activation','=',2)->where('hoa','=','8448001090001002000NVN')->get()->toArray();
					$y=Pdaccount::select('areacode','accountno','hoa','ddocode','balance','obalance','modify_date')->where('areacode','=',$area['areacode'])->where('activation','=',2)->where('hoa','=','8448001090001001000NVN')->get()->toArray();
					$z=Pdaccount::select('areacode','accountno','hoa','ddocode','balance','obalance','modify_date')->where('areacode','=',$area['areacode'])->where('activation','=',2)->where('hoa','=','8448001090001005000NVN')->get()->toArray();
					$totbal_x =0;
					$totobal_x =0;
					foreach ($x as $key => $value) {
						$totbal_x += $value['balance'] ;
						$totobal_x+= $value['obalance'];
					}
					$totbal_y =0;
					$totobal_y =0;
					foreach ($x as $key => $value) {
						$totbal_y += $value['balance'] ;
						$totobal_y+= $value['obalance'];
					}
					$totbal_z =0;
					$totobal_z =0;
					foreach ($x as $key => $value) {
						$totbal_z += $value['balance'] ;
						$totobal_z+= $value['obalance'];
					}
					$out['8448001090001002000NVN'][$area['areacode']]= array('reports'=>$x,'totbal'=>$totbal_x,'totobal'=>$totobal_x);
					$out['8448001090001001000NVN'][$area['areacode']]= array('reports'=>$y,'totbal'=>$totbal_y,'totobal'=>$totobal_y);
					$out['8448001090001005000NVN'][$area['areacode']]= array('reports'=>$z,'totbal'=>$totbal_z,'totobal'=>$totobal_z);
					$arealist[$area['areacode']]=$area['areaname'];
				}
				$out=json_encode(array($out,$arealist));
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;

	}
	public function get_panchayat_abs_loc_rpt(){
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
				$out=Loc::with('requser')->with('schemes')->where('requestdate', '>=', $fdate)->where('requestdate', '<=', $tdate)->orderby('requestdate')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_panchayat_abs_chq_rpt(){
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
				$out=Transactions::where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->where('transdate','>=',$fdate)->where('transdate','<=',$tdate)->orderby('transdate','desc')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_stolist(){
		$area=Input::get('area');
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
				if($area) {
					$out=Users::select('username','userdesc')->where('username','like',$area.'%')->where('user_role','=',8)->orderBy('username')->get();
				} else {

					$out=Users::select('username','userdesc')->where('user_role','=',8)->orderBy('username')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ddolist(){
		$sto=Input::get('sto');
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
				$out=Users::select('username','userdesc')->where('username','like',$sto.'%')->where('user_role','=',2)->orderby('userdesc')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ddolist_panchayathq(){
		$sto=Input::get('sto');
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out1=json_encode(array("invalid"));
			}
			else
			{
				$out1=Users::select('username','userdesc')->where('username','like',$sto.'%')->where('user_role','=',2)->orderby('userdesc')->get();
			}
			$out=array();
			foreach ($out1 as $key => $user) {
				$t=Pdaccount::where('ddocode','=',$user['username'])->where('activation','=',2)->where(function($q){$q->where('hoa','=','8448001090001002000NVN')->orWhere('hoa','=','8448001090001001000NVN')->orWhere('hoa','=','8448001090001005000NVN');})->get();
				if(count($t)>0){
					array_push($out, array('username'=>$user['username'],'userdesc'=>$user['userdesc']));
				}
			}
			$out=json_encode($out);
				file_put_contents('abc', var_export($out,1));
				file_put_contents('def', var_export($out1,1));
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ddotrans(){
		$ddo=Input::get('ddo');
		$sto=Input::get('sto');
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
					$out=Transactions::where('issueuser','like',$sto.'%')->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->orderby('transdate','desc')->get();
				}
				else
				{
					$out=Transactions::where('issueuser','=',$ddo)->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->orderby('transdate','desc')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ddoloc(){
		$ddo=Input::get('ddo');
		$sto=Input::get('sto');
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
					$out=Loc::where('userid','=',$sto)->with('requser')->with('schemes')->orderby('requestdate')->get();
				}
				else
				{
					$out=Loc::where('userid','=',$sto)->where('requestuser','=',$ddo)->with('requser')->with('schemes')->orderby('requestdate')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function loc_report(){
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
				$out=Loc::where('userid','=',$userid)->with('requser')->with('schemes')->orderby('requestdate')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function req_rpt(){
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
				$out=Requests::where('userid','=',$userid)->with('requser')->with('bookdata')->orderby('id')->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function chqrpt(){
		$value = Request::header('X-CSRFToken');
		$ddo=Input::get('ddo');
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
				$request=Transactions::where('issueuser','=',$ddo)->where('transtype','=','1')->where('transstatus','!=',31)->where('chqflag','=','1')->where('transdate','>','2014-10-24')->orderby('transdate','desc')->get();
				$out=$request;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function login(){
		$user=Input::all();
		$userdata=Users::where('username','=',$user['id'])->get();
		$out=[];
		if($userdata->count()==0)
		{
			array_push($out,"nouser");
		}
		elseif($userdata[0]['password']!=md5($user['password']))
		{
			array_push($out,"nopass");
		}
		else
		{
			array_push($out,array($userdata[0]['refreshtoken'],$userdata[0]['user_role']));
		}
		return ")]}',\n".json_encode($out);
	}

	public function getcdate(){
		$date = date('d/m/Y');
		return ")]}',\n".$date;

	}

	public function get_ifsc(){
		$ifsc=Input::get('ifsc');
		$bank=Banks::where('ifsccode','=',$ifsc)->first();
		return ")]}',\n".$bank;
	}

	public function get_user_data(){
		$data=Request::header('X-CSRFToken');
		$out=array();
		$laprec = array();
		$pdacarr = array();
		if($data)
		{
			$userdata=Users::where('refreshtoken','=',$data)->get();
			$pdacdata = Pdaccount::where('ddocode', '=', $userdata[0]['username'])->where('pdacno', '=', null)->where('activation','=', 2)->get();
			$out=array($userdata[0]['user_role'],$userdata[0]['userdesc'],$userdata[0]['chqflag'],$userdata[0]['lapsableflag'],$userdata[0]['username'], $userdata[0]['password'], $userdata[0]['phoneno'], $userdata[0]['emailid']);
			for ($i=0; $i < count($pdacdata); $i++) { 
				array_push($pdacarr,array("hoa"=>$pdacdata[$i]['hoa']));
			}
			array_push($out, $pdacarr);

			// $pdacdata2 = Pdaccount::where('ddocode', '=', $userdata[0]['username'])->first();
			// $bankdet = Users::where('user_role', '=', 4)->where('userid', '=', $pdacdata2->userid)->first();
			// array_push($out, $bankdet);
		}
		return ")]}',\n".json_encode($out);
	}

	public function get_user_lap_data(){
		$data=Request::header('X-CSRFToken');
		$out = array();
		if($data)
		{
			$userdata=Users::where('refreshtoken','=',$data)->get();
			$q = Pdaccount::where('ddocode','=',$userdata[0]['username'])->where('lapsableflag','=','1')->get();
			$txt=array();
			for ($i=0; $i < count($q); $i++) { 
				array_push($txt,$q[$i]->hoa);
			}
			$out = Transactions::whereIn('hoa',$txt)->where('issueuser','=',$userdata[0]['username'])->where('transtype','=','2')->with('laptrans')->where('partyamount','>',0)->get();
			
		}
		return ")]}',\n".$out;
	}


	public function post_lepexp(){
		$data=Request::header('X-CSRFToken');
		$out = array();
		if($data)
		{
			$userdata=Users::where('refreshtoken','=',$data)->get();
			if($userdata->count()==0)
			{
				array_push($out,"invalid");
			}else
			{
				array_push($out,"success");
				$lap = Input::get('dat');
				for ($i=0; $i < count($lap); $i++) { 
					$q1 = Transactions::where('id','=',$lap[$i]['id'])->first();
					$q1->lapexp = $lap[$i]['lappexp'];
					$q1->laprecref = $lap[$i]['lapref'];
					$q1->save();
				}
				$q2 = Users::where('username','=',$userdata[0]['username'])->first();
				
				$q2->chqflag = '1';
				$q2->lapsableflag = '2';
				$q2->save();
			}
			
		}
		return ")]}',\n".json_encode($out);
	}

	public function post_lepexp_empty(){
		$data=Request::header('X-CSRFToken');
		$out = array();
		if($data)
		{
			$userdata=Users::where('refreshtoken','=',$data)->get();
			if($userdata->count()==0)
			{
				array_push($out,"invalid");
			}else
			{
				array_push($out,"success");
				
				$q2 = Users::where('username','=',$userdata[0]['username'])->first();
				
				$q2->chqflag = '1';
				$q2->lapsableflag = '2';
				$q2->save();
			}
			
		}
		return ")]}',\n".json_encode($out);
	}

	public function submit_query(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$date = new DateTime();
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['username'];
				$q=Input::get('dat');
				$nqry=array(
					'userid'=>$userid,
					'subject'=>$q['subject'],
					'query'=>$q['qy'],
					'name'=>$q['name'],
					'query_date'=>$date,
					'phoneno'=>$q['phone']
					);
				$nq=Query::create($nqry);
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function query_list(){
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
				$userid=$user[0]['username'];
				$out=Query::where('userid','=',$userid)->get();
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_statement(){
		$value=Request::header('X-CSRFToken');
		$data=Input::all();
		if($value)
		{
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$userid=$user[0]['username'];
				$account=Pdaccount::where('ddocode','=',$data['ddo'])->where('hoa','=',$data['hoa'])->with('scheme')->with('usernames')->first();
				$ds=$data['y'].'-'.$data['m'].'-01';
				if($data['m']=='12')
				{
					$dey=$data['y']+1;
					$dem='01';
				}
				else
				{
					$dey=$data['y'];
					$dem=$data['m']+1;
				}
				$de=$dey.'-'.$dem.'-01';
				$sum_receipt=Transactions::where('issueuser','=',$data['ddo'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('transtype','=','2')->where('confirmdate','<',$ds)->sum('partyamount');
				$sum_payment=Transactions::where('issueuser','=',$data['ddo'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('transtype','=','1')->where('confirmdate','<',$ds)->sum('partyamount');
				$trans=Transactions::where('issueuser','=',$data['ddo'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('confirmdate','>=',$ds)->where('confirmdate','<',$de)->orderby('confirmdate')->get();
				$mt=$trans->toArray();
				$obal=intval($account->obalance);
				$filename="uploads/AccountStatement".$account->ddocode.'_'.$account->hoa.'.txt';
				$fp=fopen($filename,'w');
				$lb=str_repeat('-',107);
				$wt="";
				$wt=$wt."                                        Account Statement-".$data['m'].'/'.$data['y']."\n";
				$wt=$wt."DDO:".$account->usernames->userdesc."(".$account->ddocode.")                      Head of Account:".$account->scheme->schemename."(".$account->hoa.")\n";
				$wt=$wt.$lb."\n";
				$wt=$wt."S.No        Date            Chq/Trans              Credit            Debit          Balance\n";
				$ob=$obal+$sum_receipt-$sum_payment;
				$cb=$ob;
				$wt=$wt.$lb."\n";
				$count=1;
				fwrite($fp,$wt);
				foreach($mt as $t)
				{
					$txt='';
					$count1=str_pad($count,12,' ',STR_PAD_RIGHT);
					$txt=$txt.$count1;
					$dt12=substr($t['confirmdate'],8,2).'/'.substr($t['confirmdate'],5,2).'/'.substr($t['confirmdate'],0,4);
					$date1=str_pad($dt12,16,' ',STR_PAD_RIGHT);
					$txt=$txt.$date1;
					$chq1=str_pad($t['chequeno'],14,' ',STR_PAD_RIGHT);
					$txt=$txt.$chq1;
					if($t['transtype']=='1')
					{
						$dbt=$t['partyamount'];
						$cdt='0';
						$cb=$cb-$dbt;
					}
					else
					{
						$cdt=$t['partyamount'];
						$dbt="0";
						$cb=$cb+$cdt;
					}
					$cdt1=str_pad($cdt,12,' ',STR_PAD_LEFT);
					$txt=$txt.$cdt1.'.00  ';
					$dbt1=str_pad($dbt,12,' ',STR_PAD_LEFT);
					$txt=$txt.$dbt1.'.00  ';
					$ob1=str_pad($cb,12,' ',STR_PAD_LEFT);
					$txt=$txt.$ob1.'.00  ';
					$count++;
					$wt=$txt."\n";
					fwrite($fp,$wt);
				}
				$wt=$lb."\n";
				$wt=$wt."        Opening Balance: ".$ob.".00                      Closing Balance:".$cb.".00\n";
				$wt=$wt.$lb."\n";
				$wt=$wt.$lb."\n";
				fwrite($fp,$wt);
				fclose($fp);
				$out=$filename;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function change_pass(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$userpass=$user[0]['password'];
				$pass=Input::get('dat');
				if($userpass==md5($pass['old']))
				{
					array_push($out,"success");
					$u=Users::where('refreshtoken','=',$data)->first();
					$u->password=md5($pass['new']);
					$u->save();

				}
				else
				{
					array_push($out,"error");
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}


	public function update_pass(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				$userpass=$user[0]['password'];
				$newpass=Input::get('newpass');
				$oldpass=Input::get('oldpass');
				if($userpass==md5($oldpass))
				{
					array_push($out,"success");
					$u=Users::where('refreshtoken','=',$data)->first();
					$u->password=md5($newpass);
					$u->save();

				}
				else
				{
					array_push($out,"error");
				}
			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($newpass);
	}

	public function get_bank_names(){
		
	}

	public function sample_code(){
//		$q = Pdaccount::where('ddocode','=','27020104001')->where('hoa','=','8448001200002000000NVN')->first();
//		$q->transitamount = 742617;
//		$q->save();
//		$q = Leaves::where('user','=','27022806001')->delete();
/*		echo '<pre>';
		print_r($q);
		// echo '</pre>';	*/
		$s = 29908;
		$e = 29908;
		 while($s<=$e)
		 {
		 	$chq = str_pad($s,6,'0',STR_PAD_LEFT);
		 	// echo $chq.'<br>';
		 	//  $nqry=array(
    //                     'user'=> '27021802001',
    //                     'chequeno' => $chq,
    //                     'usedflag' => '0'
    //                     );
		 	//  $q = Leaves::create($nqry);
		//$q = Leaves::where('chequeno','=',$chq)->where('user','=','27021802001')->first();
		 //	$q->delete();
		 	$s++;
		 }

    }

    public function uploadbulk(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				if(Input::hasFile('file'))
				{
					array_push($out,'success');
					$destination_path="uploads";
					$file = Input::file('file');
					$date = new DateTime();
					$filename = $userid.$date->getTimestamp().'.csv';
					$filesize = $file->getSize();
					$fileext = $file->getClientOriginalExtension();
					if($fileext!='csv')
					{
						array_push($out,"Please check your file format");
					}
					elseif($filesize>2097152)
					{
						array_push($out,"File size too big");
					}
					else
					{
						$up=$file->move($destination_path,$filename);
						if($up)
						{
							array_push($out,"success");
							$fp=fopen($destination_path.'/'.$filename,'r');
							$c=1;
							$partydata=array();
							$tdata=array();
							$fdata=array();
							$hdata=array();
							while($data=fgetcsv($fp)){
								if($c==0)
								{
									if($data[0])
									{
										$no_leaves=intval($data[2])-intval($data[1])+1;
										$fno = str_pad($data[1],6,"0",STR_PAD_LEFT);
										$lno = str_pad($data[2],6,"0",STR_PAD_LEFT);
										if($no_leaves==25)
										{
											$no_leavesdat='a';
											$df='AP/'.$userid.'/'.$no_leavesdat.'/'.$data[0];
											array_push($tdata,array($df,$fno,$lno,$no_leaves));
										}
										elseif($no_leaves==50)
										{
											$no_leavesdat='b';
											$df='AP/'.$userid.'/'.$no_leavesdat.'/'.$data[0];
											array_push($fdata,array($df,$fno,$lno,$no_leaves));
										}
										else
										{
											$no_leavesdat='c';
											$df='AP/'.$userid.'/'.$no_leavesdat.'/'.$data[0];
											array_push($hdata,array($df,$fno,$lno,$no_leaves));
										}
									}
								}
								else
								{
									$c=0;
								}
							}
							$partydata=array($tdata,$fdata,$hdata);
							array_push($out,$partydata);
						}
						else
						{
							array_push($out,"Error uploading file");
						}
						array_push($out,$filename);
					}
				}
				else
				{
					array_push($out,'error');
				}

			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function fetchdata() {

		$data=Request::header('X-CSRFToken');
		$file=Request::input('filename');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{

				array_push($out,'success');
				$userid=$user[0]['userid'];

				if(file_exists("files/files/".$file)) {

					$filename = $userid.time().".csv";

					$up = copy("files/files/".$file,"uploads/".$filename);

					if($up)	{

						array_push($out,'success');

						//unlink("/var/www/html/pd/back/public/files/".$userid.".csv");

						$fp=fopen('uploads/'.$filename,'r');
						$c=1;
						$partydata=array();
						while($data=fgetcsv($fp)){
							if($c==0)
							{
								array_push($partydata,array($data[1],$data[2],$data[3],$data[4],$data[5],$data[6]));
							}
							else
							{
								$c=0;
							}
						}
						if(json_encode($partydata))
						{
							array_push($out,"success");
							array_push($out,$partydata);
						}
						else
						{
							array_push($out,"Special characters detected in the file.Please check.");
						}
					}
					else
					{
						array_push($out,"Error uploading file");
					}
				} else {

					array_push($out,"Something went wrong.");
				}
				array_push($out,$filename);


			}
		}
		else
		{
			array_push($out,'error');
		}


		return ")]}',\n".json_encode($out);


	}

	
	public function uploading(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				if(Input::hasFile('file'))
				{
					array_push($out,'success');
					$destination_path="uploads";
					$file = Input::file('file');
					$date = new DateTime();
					$filename = $userid.$date->getTimestamp().'.xls';
					$filesize = $file->getSize();
					$fileext = $file->getClientOriginalExtension();
					if($fileext!='xls')
					{
						array_push($out,"Please check your file format");
					}
					elseif($filesize>2097152)
					{
						array_push($out,"File size too big");
					}
					else
					{
						$up=$file->move($destination_path,$filename);
						$datafile = new Spreadsheet_Excel_Reader($destination_path.'/'.$filename);
						$mainarr = array();
						for($i=0;$i<count($datafile->sheets);$i++) // Loop to get all sheets in a file.
						{	
							if(count($datafile->sheets[$i]['cells'])>0) // checking sheet not empty
							{
								for($j=1;$j<=count($datafile->sheets[$i]['cells']);$j++) // loop used to get each row of the sheet
								{ 
									$tdarr = array();
									for($k=1;$k<=count($datafile->sheets[$i]['cells'][$j]);$k++) // This loop is created to get data in a table format.
									{

										$tdarr[] = $datafile->sheets[$i]['cells'][$j][$k];
									}

									$mainarr[] = implode(",",$tdarr);
									
								}
							}
						 
						}

						$excelfilename = $filename;

						$filename = str_ireplace(".xls", ".csv", $filename);
						 
						$filee = fopen($destination_path.'/'.$filename,"w");

						foreach ($mainarr as $line)
						{
						  fputcsv($filee,explode(',',$line));
						}

						fclose($filee);

						unlink($destination_path.'/'.$excelfilename);

						if($up)
						{
							$fp=fopen($destination_path.'/'.$filename,'r');
							$c=1;
							$partydata=array();
							while($data=fgetcsv($fp)){
								if($c==0)
								{
									array_push($partydata,array($data[1],$data[2],$data[3],$data[4],$data[5],$data[6]));
								}
								else
								{
									$c=0;
								}
							}
							if(json_encode($partydata))
							{
								array_push($out,"success");
								array_push($out,$partydata);
							}
							else
							{
								array_push($out,"Special characters detected in the file.Please check.");
							}
						}
						else
						{
							array_push($out,"Error uploading file");
						}
						array_push($out,$filename);
					}
				}
				else
				{
					array_push($out,'error');
				}

			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}




	public function uploadingpdtopd(){
		$data=Request::header('X-CSRFToken');
		$out=[];
		if($data)
		{
			$user=Users::where('refreshtoken','=',$data)->get();
			if($user->count()==0)
			{
				array_push($out,"invalid");
			}
			else
			{
				array_push($out,'success');
				$userid=$user[0]['userid'];
				if(Input::hasFile('file'))
				{
					array_push($out,'success');
					$destination_path="uploads";
					$file = Input::file('file');
					$date = new DateTime();
					$filename = $userid.$date->getTimestamp().'.xls';
					$filesize = $file->getSize();
					$fileext = $file->getClientOriginalExtension();
					if($fileext!='xls')
					{
						array_push($out,"Please check your file format");
					}
					elseif($filesize>2097152)
					{
						array_push($out,"File size too big");
					}
					else
					{
						$up=$file->move($destination_path,$filename);

						$datafile = new Spreadsheet_Excel_Reader($destination_path.'/'.$filename);
						$mainarr = array();
						for($i=0;$i<count($datafile->sheets);$i++) // Loop to get all sheets in a file.
						{	
							if(count($datafile->sheets[$i]['cells'])>0) // checking sheet not empty
							{
								for($j=1;$j<=count($datafile->sheets[$i]['cells']);$j++) // loop used to get each row of the sheet
								{ 
									$tdarr = array();
									for($k=1;$k<=count($datafile->sheets[$i]['cells'][$j]);$k++) // This loop is created to get data in a table format.
									{

										$tdarr[] = $datafile->sheets[$i]['cells'][$j][$k];
									}

									$mainarr[] = implode(",",$tdarr);
									
								}
							}
						 
						}

						$excelfilename = $filename;

						$filename = str_ireplace(".xls", ".csv", $filename);
						 
						$filee = fopen($destination_path.'/'.$filename,"w");

						foreach ($mainarr as $line)
						{
						  fputcsv($filee,explode(',',$line));
						}

						fclose($filee);

						unlink($destination_path.'/'.$excelfilename);
						if($up)
						{
							array_push($out,"success");
							$fp=fopen($destination_path.'/'.$filename,'r');
							$c=1;
							$partydata=array();
							while($data=fgetcsv($fp)){
								if($c==0)
								{
									array_push($partydata,array($data[1],$data[2],$data[3],$data[4]));	
								}
								else
								{
									$c=0;
								}
							}
							array_push($out,$partydata);
						}
						else
						{
							array_push($out,"Error uploading file");
						}
						array_push($out,$filename);
					}
				}
				else
				{
					array_push($out,'error');
				}

			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}

	public function update_contactdetails() {

		$phno = Input::get("phno");
		$emailid = Input::get("emailid");
		$data=Request::header('X-CSRFToken');

		$user=Users::where('refreshtoken','=',$data)->first();

		if(!$user)
		{
			$out = 0;
		}
		else
		{
			$pdacdata = Pdaccount::where('ddocode', '=', $user->username)->where('pdacno', '=', null)->get();

			if($pdacdata->count() > 0) {

				$out = 1;
			} else if($user->password=="e10adc3949ba59abbe56e057f20f883e") {

				$out = 2;
			} else {

				$out = 3;
			}

			$user->phoneno=$phno;
			$user->emailid=$emailid;

			$user->save();

		}

		return $out;
	}

	public function update_pdacno() {

		$pdinfo = Input::get("userpdacinfo");
		$data=Request::header('X-CSRFToken');

		$user=Users::where('refreshtoken','=',$data)->first();

		if(!$user)
		{
			$out = 0;
		}
		else
		{

			for($i=0;$i<count($pdinfo);$i++) {

				$pdacdata = Pdaccount::where('ddocode', '=', $user->username)->where('hoa', '=', $pdinfo[$i]['hoa'])->first();
				if($pdacdata) {
					$pdacdata->pdacno= $pdinfo[$i]['acno'];
					$pdacdata->save();
				}
			}

			$out = 1;

			if($user->password == "e10adc3949ba59abbe56e057f20f883e") {

				$out = 2;
			}

		}

		return $out;
	}

	public function forgotpass() {

		$userid = Input::get('userid');
		$user=Users::where('username','=',$userid)->first();
		if($user) {
			if($user->user_role == 2 || $user->user_role == 20) {
				if($user->emailid) {
					
						$rand = substr(md5(microtime()),rand(0,26),6);
						$newpass = md5($rand);
						$user->password =$newpass;
						$user->save();
						$to = $user->emailid;
						$subject = "Password change - PD portal";
						$message = "Hello sir/madam,<br>
						Your new password for userid ".$userid." in PD portal is: ".$rand."<br>
						Please login and change this password from the 'Change password' option in the menu.";
						sendEmail($to, $subject, $message);
						$out = 1;
					
				} else {

					$out = 2;
				}
			} else {

				$to = "yogeshk96@gmail.com";
				$subject = "Password reset request - PD portal";
				$message = "Hello sir/madam,<br>
				A password reset request came from userid ".$userid." in PD portal.
				";
				sendEmail($to, $subject, $message);
				$out = 3;
			}
		} else {

			$out = 0;
		}
		return $out;
	}

	public function readresponsefile() {

		$i=10;
		$x = array();
		foreach(file('testsbi.txt') as $line) {

			$i--;
			
			if($i<0) {

				if(strpos($line, '---------------------') !== false) {

					$i=9;

				} else {

					if(trim(substr($line, 0, 10)) == '') {

						break;
					} else {
						//echo $line;

		   				$x[] = substr($line, 78, 9);
		   			}
		   		}
		   	}
		   	
		}
		return $x;
	}


	public function uploadingresponse(){
		$id=Request::header('transid');
		$type=Request::header('type');
		$out=[];
		if($id)
		{
			$trans=Transactions::where('id','=',$id)->first();
			if(!$trans)
			{
				array_push($out,"invalid");
			}
			else
			{
				$userid=$trans->issueuser;
				$chequeno=$trans->chequeno;
				if(Input::hasFile('file'))
				{
					$destination_path="responses";
					$file = Input::file('file');
					$date = new DateTime();
					if($type=='neft') {

						$filename = "response_neft_".$userid.$chequeno.".csv";
						$filenametxt = str_ireplace(".csv", ".txt", $filename);
					} else {

						$filename = "response_sbi_".$userid.$chequeno.".csv";
						$filenametxt = str_ireplace(".csv", ".txt", $filename);
					}
					$filesize = $file->getSize();
					$fileext = $file->getClientOriginalExtension();
					if($fileext!='txt' && $fileext!='TXT')
					{
						array_push($out,"Please check your file format");
					}
					else
					{
						$mainarr = array();

						$up=$file->move($destination_path,$filenametxt);

						if($type == 'neft') {

							$i=10;

							foreach(file($destination_path.'/'.$filenametxt) as $line) {

								$i--;
								
								if($i<0) {

									if(strpos($line, '---------------------') !== false) {

										$i=9;

									} else {

							   			if(substr($line, 0, 3) == 'TOT') {

											break;
										} else {


											$acno = substr($line, 0, 11);
											$amount = trim(substr($line, 11, 18));
											$ifsccode = substr($line, 53, 11);
											$status = substr($line, 76, 4);
											$desc = trim(substr($line, 82, 50));
											$referenceno = trim(substr($line, 133, 17));

							   				$mainarr[] = $acno.",".$amount.",".$ifsccode.",".$status.",".$desc.",".$referenceno;

							   			}
							   		}
							   	}
							   	
							}

						} else {

							$j=10;
							foreach(file($destination_path.'/'.$filenametxt) as $line) {

								$j--;
								
								if($j<0) {

									if(strpos($line, '---------------------') !== false) {

										$j=9;

									} else {

										if(trim(substr($line, 0, 10)) == '') {

											break;
										} else {

											$comstring = trim(substr($line, 0, 34));
											$comstring = explode("-", $comstring);

											$acno = $comstring[0];
											$transid = $comstring[1];
											$amount = trim(substr($line, 35, 18));
											$crdt = substr($line, 54, 2);

											$date = trim(substr($line, 57, 8));
											$status = substr($line, 78, 9);

							   				$mainarr[] = $acno.",".$amount.",".$transid.",".$status.",".$date.",".$crdt;


							   			}
							   		}
							   	}
							   	
							}


						}
						 
						$filee = fopen($destination_path.'/'.$filename,"w");

						foreach ($mainarr as $linee)
						{
						  fputcsv($filee,explode(',',$linee));
						}

						fclose($filee);

						if($type=='neft') {

							$trans->responsefileneft = $filename;
						} else {

							$trans->responsefile = $filename;
						}
						$trans->save();
						array_push($out,'success');
					}
				}
				else
				{
					array_push($out,'error');
				}

			}
		}
		else
		{
			array_push($out,'invalid');
		}
		return ")]}',\n".json_encode($out);
	}


}
