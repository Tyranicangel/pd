app.controller("StoController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.role=2;
	$scope.showloader=false;
	if(localStorage.token)
	{
		if(!$scope.token)
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_user_data',
			}).
			success(function(result){
				if(result[0]=='10')
				{
					$scope.username=result[1];
					$scope.showloader=false;
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
	else
	{
		Logging.logout();
	}
});

app.controller("StoMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sto_data'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='success')
		{
			if(result[1]=='nomap')
			{
				alert('Sorry your account is not mapped to any of your STOs/SAs, your account will be activated only when it is mapped to them, please contact your DD/DTO to map your account!');
				Logging.logout();
			}
			else
			{
				$scope.reqno=result[1];
				$scope.transno=result[2];
				$scope.locno=result[3];
			}
		}
		else
		{
			Logging.logout();
		}
	});
});

app.controller("StoRequestController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sto_requests'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.allrequests=result;
			counter=1;
			angular.forEach($scope.allrequests, function(req){
				req.sno=counter;
				counter++;
				if(req.saflag=='1')
				{
					req.bookdata={};
					req.bookdata.bookno='-';
					req.bookdata.chequestart="-";
					req.bookdata.chequeend="-";
					req.rec="Reject";
				}
				else
				{
					req.rec="Approve";
				}
			});
		}
	});

	$scope.get_grant_leafs=function(a,b){
		if(a=='-')
		{
			return "-"
		}
		else
		{
			return (parseInt(a)-parseInt(b)+1);
		}
	}

	$scope.checkall=function(){
		var val=!$scope.allchecked();
		angular.forEach($scope.allrequests,function(trans){
			trans.check=val;
		});
	}


	$scope.allchecked=function(){
		var flag=1;
		count=0;
		if($scope.allrequests)
		{
			angular.forEach($scope.allrequests,function(trans){
				if(trans.check)
				{
					count++;
				}
			});
			return count===$scope.allrequests.length;
		}
		else
		{
			return false;
		}
	}

	$scope.chq_confirm=function(){
		var chqlist=[];
		flg=0;
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
				if(trans.saflag=='1')
				{
					flg=1;
				}
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheque books to confirm");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sto_booklist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='nomap')
					{
						alert('Sorry you are not mapped to anyone!Cannot process the request now! Please contact your DTO/STO/DD to map your account and try again later!');
						window.location.reload();
					}
					else
					{
						alert("Chequebooks Confirmed and forwarded!");
						window.location.reload();
					}
				}
				else
				{
					$scope.showloader=false;
					Logging.logout();
				}
			});
		}
	}
});

app.controller("StoLoclistController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sto_loclist'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.allrequests=result;
			counter=1;
			angular.forEach($scope.allrequests, function(req){
				req.sno=counter;
				counter++;
				req.reqamount=Commas.getcomma(req.reqamount);
				if(!req.remarks)
				{
					req.remarks="None";
				}
				if(req.saflag=='1')
				{
					req.refno="-";
					req.grantamount="-";
					req.rec='Reject';
				}
				else
				{
					req.rec='Approve';
					req.grantamount=Commas.getcomma(req.grantamount);
				}
			});
		}
	});

	$scope.get_grant_leafs=function(a,b){
		return (parseInt(a)-parseInt(b)+1);
	}

	$scope.checkall=function(){
		var val=!$scope.allchecked();
		angular.forEach($scope.allrequests,function(trans){
			trans.check=val;
		});
	}


	$scope.allchecked=function(){
		var flag=1;
		count=0;
		if($scope.allrequests)
		{
			angular.forEach($scope.allrequests,function(trans){
				if(trans.check)
				{
					count++;
				}
			});
			return count===$scope.allrequests.length;
		}
		else
		{
			return false;
		}
	}

	$scope.chq_confirm=function(){
		var chqlist=[];
		var data_to_impact=[];
		flg=0;
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				
				chqlist.push(trans.id);
				data_to_impact.push(trans);
				if(trans.saflag=='1')
				{
					flg=1;
				}
			}
		});

		if(!$scope.remarks)
		{
			$scope.remarks = '';
		}


		if(chqlist.length==0)
		{
			alert("Please select the LOCs to confirm");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sto_loclist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='nomap')
					{
						$scope.showloader=false;
						alert('Sorry you are not mapped to any ATO/DD, please contact your DD/DTO to map your account and then try again later!');
					}else
					{
						alert('LOC Forwarded to your next highest authority!');
						window.location.reload();
					}
					
				}
				else
				{
					$scope.showloader=false;
					Logging.logout();
				}
			});
		}
	}
});

app.controller("StoTransController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sto_trans'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.alltrans=result;
			var counter=1;
			angular.forEach($scope.alltrans,function(tr){
				tr.sno=counter;
				counter++;

				tr.bal = parseInt(tr.accountdet.balance) + parseInt(tr.accountdet.transitamount);
				if(tr.bal==0)
				{
					
				}else
				{
					tr.bal = Commas.getcomma(tr.bal);
				}

				if(!tr.rejects)
				{
					tr.rejects = 'None';
				}
				
			});
			
		}
	});

	$scope.view_lap_details = function(x)
	{
		$scope.dtolap = x;
		$scope.lapshow = true;

		var laprealexp = 0;

		angular.forEach($scope.dtolap.laptrans,function(x){
			if(x.transstatus=='3')
			{
				laprealexp += parseInt(x.partyamount);
				x.stat = 'Paid';
			}
			else if(x.transstatus=='21')
			{
				x.stat = 'Cheque Rejected!';
			}
			else
			{
				x.stat = 'In Transit!';
			}
		});
		$scope.laprealexp = laprealexp;
		$scope.totrecleft =  Commas.getcomma(parseInt(x.partyamount) - laprealexp);

		
		x.dtransdate = Dates.getDate(x.transdate);

		recdate= x.transdate;

		month = parseInt(recdate.substr(5,2));
		year = parseInt(recdate.substr(0,4));

		if(month>=1 && month<=3)
		{
			year = parseInt(year) + 1;
		}
		else
		{
			year = parseInt(year) + 2;
		}
		$scope.valdate = year+'-'+'03'+'-'+'31';
		
		$scope.dtovaldate = Dates.getDate($scope.valdate);
	}

	$scope.close_lap_dv = function()
	{
		delete $scope.dtolap;
		$scope.lapshow = false;
	}

	$scope.checkall=function(){
		var val=!$scope.allchecked();
		angular.forEach($scope.alltrans,function(trans){
			trans.check=val;
		});
	}

	$scope.allchecked=function(){
		var flag=1;
		count=0;
		if($scope.alltrans)
		{
			angular.forEach($scope.alltrans,function(trans){
				if(trans.check)
				{
					count++;
				}
			});
			return count===$scope.alltrans.length;
		}
		else
		{
			return false;
		}
	}

	$scope.chq_confirm=function(){
		var chqlist=[];
		var data_to_impact=[];
		angular.forEach($scope.alltrans,function(trans){
			if(trans.check)
			{
				data_to_impact.push([trans.issueuser,trans.transid]);
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to confirm");
		}
		else
		{
			if(!$scope.remarks)
			{
				$scope.remarks = '';
			}
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sto_chqlist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Cheques Authorized and forwarded");
					window.location.reload();
				}
				else if(result[0]=='nomap')
				{
					alert('Sorry you are not mapped to anyone!please contact your DD/DTO and get your account mapped and then try again later!');
					window.location.reload();
				}
				else
				{
					$scope.showloader=false;
					Logging.logout();
				}
			});
		}
	}
});

app.controller("StoStatementController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.pdadmin="select";
	$scope.hoa='select';
	$scope.month='select';
	$scope.year='select';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sto_admins'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.ddolist=result;
		}
	});

	$scope.get_c=function(){
		if($scope.pdadmin=="select")
		{
			return "--Please select PD Admin and HOA--";
		}
		else if($scope.hoa=='select')
		{
			return "--Please select PD Admin and HOA--";
		}
		else
		{
			var bal;
			angular.forEach($scope.hoalist,function(h){
				if(h.hoa==$scope.hoa)
				{
					bal=h.balance;
				}
			});
			return Commas.getcomma(bal);
		}
	}

	$scope.admin_change=function(){
		$scope.hoa='select';
		if($scope.pdamin!='select')
		{
			$scope.hoalist=[];
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_sto_hoas',
				params:{ddo:$scope.pdadmin}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					$scope.hoa='select';
					$scope.hoalist=result;
				}
			});
		}
		else
		{
			$scope.hoalist={};
			$scope.hoa='select';
		}
	}

	$scope.submit=function(){
		var yy=parseInt($scope.maindate.substr(6,4));
		var mm=parseInt($scope.maindate.substr(3,2));
		var flg=0;
		if($scope.pdadmin=='select')
		{
			alert('Please select PD Admin');
			flg=1;
		}
		else if($scope.hoa=='select')
		{
			alert('Please select HOA');
			flg=1;
		}
		else if($scope.month=='select')
		{
			alert('Please select month');
			flg=1;
		}
		else if($scope.year=='select')
		{
			alert('Please select year');
			flg=1;
		}
		else if(parseInt($scope.year)>yy)
		{
			alert('Sorry this data is not available');
			flg=1;
		}
		else if(parseInt($scope.year)==yy)
		{
			if($scope.year=='2014')
			{
				if(parseInt($scope.month)<5)
				{
					alert('Sorry this data is not available');
					flg=1;
				}
			}
			if(parseInt($scope.month)>mm)
			{
				alert('Sorry this data is not available');
				flg=1;
			}
		}
		else
		{
			if($scope.year=='2014')
			{
				if(parseInt($scope.month)<5)
				{
					alert('Sorry this data is not available');
					flg=1;
				}
			}
		}
		if(flg==0)
		{
			$scope.showloader=true;
				$http({
					method:'GET',
					headers:{'X-CSRFToken':localStorage.token},
					url:'getstatement.php',
					params:{y:$scope.year,m:$scope.month,hoa:$scope.hoa,ddo:$scope.pdadmin}
				}).
				success(function(result){
					if(result[0]=='invalid')
					{
						Logging.logout();
					}
					else
					{
						$scope.fname=$scope.requesturl+'/'+result;
						$scope.showloader=false;
					}
				});
		}
	}
});
