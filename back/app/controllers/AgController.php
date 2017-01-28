<?php

class AgController extends BaseController {
	
	public function get_hoalist(){
		$value = Request::header('X-CSRFToken');
		if($value)
		{
			$ddo=Input::get('ddo');
			$user=Users::where('refreshtoken','=',$value)->get();
			if($user->count()==0)
			{
				$out=json_encode(array("invalid"));
			}
			else
			{
				$accounts=Pdaccount::where('ddocode','=',$ddo)->with('scheme')->with('usernames')->get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_schemelist(){
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
				$accounts=Schemes::get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_arealist(){
		$data=Input::get('hoa');
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
				if(!$data)
				{
					$out=json_encode(array("error"));
				}
				else
				{
					$out=Pdaccount::where('hoa','=',$data)->groupBy('areacode')->with('arealist')->get(array('areacode'));
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ddos(){
		$hoa=Input::get('hoa');
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
				if(!$hoa)
				{
					$out=json_encode(array("error"));
				}
				else
				{
					$out=Pdaccount::where('hoa','=',$hoa)->where('areacode','=',$area)->with('usernames')->get();
				}
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_accounts(){
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
				$accounts=Pdaccount::with('usernames')->get();
				$out=$accounts;
			}
		}
		else
		{
			$out=json_encode(array("invalid"));
		}
		return ")]}',\n".$out;
	}

	public function get_ac_trans(){
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
				$data=Input::all();
				$dt=substr($data['dto'],6,4).'-'.substr($data['dto'],3,2).'-'.substr($data['dto'],0,2);
				$df=substr($data['dfrom'],6,4).'-'.substr($data['dfrom'],3,2).'-'.substr($data['dfrom'],0,2);
				if($data['dfrom']=='' && $data['dto']=='')
				{
					$request=Transactions::where('issueuser','=',$data['ddocode'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->with('requser')->orderby('confirmdate')->get();
				}
				elseif($data['dfrom']=='')
				{
					
					$request=Transactions::where('issueuser','=',$data['ddocode'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('confirmdate','<',$dt)->with('requser')->orderby('confirmdate')->get();
				}
				elseif($data['dto']=='')
				{
					$df=substr($data['dfrom'],6,4).'-'.substr($data['dfrom'],3,2).'-'.substr($data['dfrom'],0,2);
					$request=Transactions::where('issueuser','=',$data['ddocode'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('confirmdate','>',$df)->with('requser')->orderby('confirmdate')->get();
				}
				else
				{
					
					$dt=substr($data['dto'],6,4).'-'.substr($data['dto'],3,2).'-'.substr($data['dto'],0,2);
					$request=Transactions::where('issueuser','=',$data['ddocode'])->where('hoa','=',$data['hoa'])->where('transstatus','=',3)->where('confirmdate','<',$dt)->where('confirmdate','>',$df)->with('requser')->orderby('confirmdate')->get();
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

}