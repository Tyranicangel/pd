app.controller("AdminCheckerController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='20')
				{

					if(result[7] == null || result[5] == "e10adc3949ba59abbe56e057f20f883e" || result[8].length != 0){

						$state.go("contactdet");
					}
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



app.controller("AdminCheckerMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_data'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='success')
		{
			$scope.reqno=result[1];
			$scope.transno=result[2];
			$scope.locno=result[3];
			
			
		}
		else
		{
			Logging.logout();
		}
	});
});


app.controller("AdminCheckerTransController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_trans'
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
			});
		}
	});

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
		angular.forEach($scope.alltrans,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.chequeno);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to confirm");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/dsa_chqlist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Cheque Passed and sent to your treasury officer for authorization.");
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

	$scope.chq_reject=function(){
		var chqlist=[];
		angular.forEach($scope.alltrans,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.chequeno);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to reject");
		}
		else if(!$scope.remarks)
		{
			alert("Please enter remarks");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/dsa_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Cheques Rejected");
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

app.controller("AdminCheckerConfirmController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates,$stateParams){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.approval='approve';	
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_chq_data',
		params:{chqno:$scope.transno}
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.dat=result[0];
			$scope.laprealexp = 0; 
			angular.forEach($scope.dat.laprecinfo.laptrans,function(x){
				if(x.transstatus=='3')
				{
					$scope.laprealexp += parseInt(x.partyamount);
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

			$scope.dat.acinfo.balance = parseInt($scope.dat.acinfo.balance) + parseInt($scope.dat.acinfo.transitamount);
			$scope.dat.acinfo.balance=Commas.getcomma($scope.dat.acinfo.balance);
			$scope.dat.laprecinfo.balamt =  Commas.getcomma(parseInt($scope.dat.laprecinfo.partyamount) - parseInt($scope.laprealexp)); // new
			$scope.dat.laprecinfo.partyamount = Commas.getcomma($scope.dat.laprecinfo.partyamount);
			$scope.dat.laprecinfo.dtransdate = Dates.getDate($scope.dat.laprecinfo.transdate);

			

			

		}
	});

setTimeout(function(){
	$scope.valid_till=function(){
		
		if(!$scope.dat.laprecinfo.dtransdate)
		{
			return "-----";
		}
		else
		{
			
			recdate= $scope.dat.laprecinfo.transdate;

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
			$scope.lapvaldate = year+'-'+'03'+'-'+'31';
			
			return Dates.getDate($scope.lapvaldate);
		}
	}
}, 2000);

	$scope.words=function(dat)
	{
		if(dat)
		{
			return getwords(dat)+' ONLY';
		}
		else
		{
			return "";
		}
	}
	$scope.salap_check=function()
	{
		var bool; 
		if($scope.dat.acinfo.lapsableflag=='1')
		{
			bool = true;
		}
		else
		{
			bool = false;
		}


		return bool;
	}
	
	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}

	$scope.chq_confirm=function(){
		var chqlist=$scope.transno;
		$scope.showloader=true;
		
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/checker_chqlist_confirm',
			data:{list:chqlist,rems:$scope.remarks}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='success')
			{
				alert("Cheque forwarded.");
				$state.go('adminchecker.trans');
			}
			else
			{
				Logging.logout();
			}
		});
		
	}

	$scope.chq_reject=function(){
		var chqlist=$scope.transno;
		if(!$scope.remarks)
		{
			alert("Please enter remarks");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/checker_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheque Rejected.");
					$state.go('adminchecker.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});

app.controller("AdminCheckerLoclistController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_loclist'
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
			});
		}
	});
});

app.controller("AdminCheckerLocController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.approval='approve';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_loc_data',
		params:{requser:$stateParams.requester,reqhoa:$stateParams.requesthoa}
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			if(result=='')
			{
				alert("There are no pending requests from this user");
				$state.go('adminchecker.loclist');
			}
			else
			{
				$scope.requestdata=result;
				$scope.remarks=result.remarks;
				$scope.requestdata.reamount=Commas.getcomma($scope.requestdata.reqamount);
				$scope.issue_amt=result.reqamount;
				$scope.requestdata.locamt=parseInt(result.accounts.balance)-parseInt(result.accounts.loc);
			}
		}
	});

	$scope.getc=function(dat){
		if(dat)
		{
			return Commas.getcomma(dat);
		}
		else
		{
			return "";
		}
	}

		
		$scope.req_reject=function()
		{
			if($scope.remarks=="" || !$scope.remarks)
			{
				alert("Please enter remarks");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_loc_checker',
					data:{
						locid:$scope.requestdata.id,
						user:$scope.requestdata.requestuser,
						hoa:$scope.requestdata.hoa,
						approval:'reject',
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						if(result[1]=='nomap')
						{
							alert('Sorry you are not mapped to any of the ATO/STOs in your office! Please contact your Deputy DIrector/DTO for mapping!');
							$state.go('adminchecker.loclist');
						}
						else
						{
							alert('Loc Rejected.');
							$state.go('adminchecker.loclist');
						}
						
					}
					else
					{
						Logging.logout();
					}
				});
			}
		}
		
		$scope.req_accept = function ()
		{

			if(!$scope.remarks) {

				$scope.remarks = "";
			}
			
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/accept_loc_checker',
				data:{
					locid:$scope.requestdata.id,
					user:$scope.requestdata.requestuser,
					hoa:$scope.requestdata.hoa,
					approval:'accept',
					remarks:$scope.remarks
					}
			}).success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{

					if(result[1]=='nomap')
					{
						alert('Sorry you are not mapped to any of the ATO/STOs in your office! Please contact your Deputy DIrector/DTO for mapping!');
						$state.go('adminchecker.loclist');
					}
					else
					{
						alert('LOC Approved.');
						$state.go('adminchecker.loclist');
					}
					
				}
				else
				{
					Logging.logout();
				}
			});
		}
});

app.controller("AdminCheckerRequestController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_requests'
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
			});
		}
	});
});

app.controller("AdminCheckerChequeController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.approval='approve';
	$scope.book25=0;
	$scope.book50=0;
	$scope.book100=0;
	$scope.bookno25="-";
	$scope.bookno50="-";
	$scope.bookno100="-";
	$scope.first25="-";
	$scope.first50="-";
	$scope.first100="-";
	$scope.last25="-";
	$scope.last50="-";
	$scope.last100="-";
	$scope.cheques={};
	$scope.cheques.first="--Please enter book no--";
	$scope.cheques.last="--Please enter book no--";
	$scope.cheques.size="--Please enter book no--";

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/checker_request_data',
		params:{requser:$stateParams.requester}
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			if(result=='')
			{
				alert("There are no pending requests from this user");
				$state.go('adminchecker.request');
			}
			else
			{
				$scope.requestdata=result;
			}
		}
	});


		$scope.req_reject=function()
		{
		
			if(!$scope.remarks)
			{
				alert("Please enter Remarks");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_request_checker',
					data:{
						user:$scope.requestdata.requestuser,
						approval:'reject',
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						if(result[1]=='success')
						{
							alert('Cheque book Request Rejected.');
							$state.go('adminchecker.request');
						}
						else if(result[0]=='nomap')
						{
							alert('Sorry you are not mapped to anyone! Please contact your DTO/STO/DD to map your account!');
						}
						else
						{
							alert(result);
						}
					}
					else
					{
						Logging.logout();
					}
				});
			}
		}

		$scope.req_accept = function()
		{

			if(!$scope.remarks) {

				$scope.remarks = "";
			}
			
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/accept_request_checker',
				data:{
					user:$scope.requestdata.requestuser,
					approval:'accept',
					remarks:$scope.remarks
					}
			}).success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					if(result[1] == 'success')
					{
						alert('Cheque book request forwarded.');
						$state.go('adminchecker.request');
					}
					else
					{
						alert(result[1]);
					}
				}
				else if(result[0]=='nomap')
				{
					alert('Sorry you are not mapped to anyone! Please contact your DTO/STO/DD to map your account!');
				}
				else
				{
					Logging.logout();
				}
			});
			
		}
	
});

