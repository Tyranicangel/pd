app.controller("SaController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='1')
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

app.controller("SaMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_data'
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

app.controller("SaRequestController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_requests'
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

app.controller("SaLoclistController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_loclist'
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


app.controller("SaChequeController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
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
		url:$scope.requesturl+'/sa_request_data',
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
				$state.go('sa.request');
			}
			else
			{
				$scope.requestdata=result;
			}
		}
	});

	$scope.get_book=function(){
		$scope.showloader=true;
		if($scope.cheques.number)
		{
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sa_book_data',
				params:{book:$scope.cheques.number}
			}).
			success(function(result){
				if(result)
				{
					$scope.cheques.first=result.cstart;
					$scope.cheques.last=result.cend;
					$scope.cheques.size=result.size;
					$scope.showloader=false;
				}else
				{
					$scope.cheques.first='--There is no book with this book no--';
					$scope.cheques.last='--There is no book with this book no--';
					$scope.cheques.size='--There is no book with this book no--';
					$scope.showloader=false;
				}
			});
		}else
		{
			$scope.showloader=false;
		}
	}

	$scope.getleaves=function(dat)
	{
		if(!dat)
		{
			return 0;
		}
		else
		{
			var count=0;
			angular.forEach(dat,function(d){
				if(d.usedflag=='0')
				{
					count++;
				}
			});
			return count;
		}
	}

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_invent_data'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.allinv=result;
			angular.forEach($scope.allinv,function(inv){
				if(inv.size=='25')
				{
					if($scope.book25==0)
					{
						$scope.bookno25=inv.bookno;
						$scope.first25=inv.cstart;
						$scope.last25=inv.cend;
					}
					$scope.book25++;
				}
				if(inv.size=='50')
				{
					if($scope.book50==0)
					{
						$scope.bookno50=inv.bookno;
						$scope.first50=inv.cstart;
						$scope.last50=inv.cend;
					}
					$scope.book50++;
				}
				if(inv.size=='100')
				{
					if($scope.book100==0)
					{
						$scope.bookno100=inv.bookno;
						$scope.first100=inv.cstart;
						$scope.last100=inv.cend;
					}
					$scope.book100++;
				}
			});
		}
	});	

	$scope.no_leaves=function(){
		if(!$scope.cheques.first)
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if($scope.cheques.first=="")
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if(!$scope.cheques.last)
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if($scope.cheques.last=="")
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if(!$scope.regex.test($scope.cheques.last))
		{
			return "--Please correct first and last cheque numbers--";
		}
		else if(!$scope.regex.test($scope.cheques.first))
		{
			return "--Please correct first and last cheque numbers--";	
		}
		else if(parseInt($scope.cheques.last)<parseInt($scope.cheques.first))
		{
			return "--Please correct first and last cheque numbers--";
		}
		else
		{
			return (parseInt($scope.cheques.last)-parseInt($scope.cheques.first)+1)
		}
	}

	$scope.req_accept=function(){
		if(!$scope.remarks)
		{
			$scope.remarks="";
		}
		if($scope.approval=='reject')
		{
			if($scope.remarks=="")
			{
				alert("Please enter Remarks");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_request',
					data:{
						user:$scope.requestdata.requestuser,
						approval:$scope.approval,
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						if(result[1]=='success')
						{
							alert('Cheque book Request Rejected and forwarded to your Treasury officer for final approval!');
							$state.go('sa.request');
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
		else
		{
			if(!$scope.cheques.first)
			{
				alert("Please enter First Cheque No");
			}
			else if(!$scope.cheques.last)
			{
				alert("Please enter Last Cheque No");
			}
			else if(!$scope.regex.test($scope.cheques.last))
			{
				alert("Please enter correct Last Cheque No");
			}
			else if(!$scope.regex.test($scope.cheques.first))
			{
				alert("Please enter correct First Cheque No");
			}
			else if(parseInt($scope.cheques.last)<parseInt($scope.cheques.first))
			{
				alert("Please enter correct First and Last Cheque No");
			}
			else if(!$scope.cheques.number)
			{
				alert("Please enter Cheque Book No");
			}
			else if($scope.approval=='reject'&& $scope.remarks=="")
			{
				alert("Please enter Remarks");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_request',
					data:{
						user:$scope.requestdata.requestuser,
						chequedata:$scope.cheques,
						approval:$scope.approval,
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						if(result[1] == 'success')
						{
							alert('Cheque book request approved and forwarded to your treasury officer for final approval!');
							$state.go('sa.request');
						}else
						{
							alert(result[1]);
						}
					}
					else
					{
						Logging.logout();
					}
				});
			}
		}
	}
});

app.controller("SaCreateController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.account={};
	$scope.account.actype='2';
	$scope.account.cat='A';
	$scope.account.balance=0;
	$scope.hoa8="NVN";

	$scope.obal_words=function(){
		if(!$scope.account.balance)
		{
			return "---Please enter Opening balance to display relavant data---";
		}
		else if($scope.account.balance=='')
		{
			return "---Please enter Opening balance to display relavant data---";
		}
		else if($scope.regex.test($scope.account.balance))
		{
			return getwords($scope.account.balance);
		}
		else
		{
			$scope.account.balance="";
		}
	}

	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}

	$scope.create_account=function(){
		$scope.account.hoa=$scope.hoa1+$scope.hoa2+$scope.hoa3+$scope.hoa4+$scope.hoa5+$scope.hoa6+$scope.hoa7+$scope.hoa8;
		if(!$scope.account)
		{
			alert("Please fill in the data");
		}
		else if(!$scope.account.ddo)
		{
			alert("Please enter ddocode");
		}
		else if(!$scope.account.ddoname)
		{
			alert("Please enter the name of ddo");
		}
		else if(!$scope.account.hoa)
		{
			alert("Please enter head of account");
		}
		else if($scope.account.hoa.length!=22)
		{
			alert("Please enter head of account in 7 tier format");
		}
		else if(!$scope.account.hoaname)
		{
			alert("Please enter the head of account name");
		}
		else if(!$scope.account.actype)
		{
			alert("Please enter type of account");
		}
		else if(!$scope.account.cat)
		{
			alert("Please select account category");
		}
		else if($scope.account.balance=='')
		{
			alert("Please enter opening balance");
		}
		else if(!$scope.account.remarks)
		{
			alert("Please enter reference/authority");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/create_account',
				data:{acdata:$scope.account}
			}).success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='created')
					{
						alert('Account Created! User Id is the DDO code that you just entered, and password is 123456');
					}
					else if(result[1]=='exists')
					{
						alert('This account already exists');
					}
					else if(result[1]=='activate')
					{
						alert('This account needs to be confirmed by correspoding DTO/STO');
					}
					else
					{
						alert('Error in creating account.Please try again.')
					}
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


app.controller("SaLocController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.approval='approve';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_loc_data',
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
				$state.go('sa.loclist');
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

	$scope.req_accept=function(){
		if(!$scope.remarks)
		{
			$scope.remarks="";
		}
		if($scope.approval=='reject')
		{
			if($scope.remarks=="")
			{
				alert("Please enter remarks");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_loc',
					data:{
						user:$scope.requestdata.requestuser,
						hoa:$scope.requestdata.hoa,
						amt:$scope.issue_amt,
						refno:$scope.refno,
						approval:$scope.approval,
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						alert('Loc Rejected and Forwarded to your Treasury Officer for approval');
						$state.go('sa.loclist');
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
			if(!$scope.issue_amt)
			{
				alert("Please enter Issue Amount towards LOC");
			}
			else if(!$scope.regex.test($scope.issue_amt))
			{
				alert("Please enter correct amount to be issued");
			}
			else if(parseInt($scope.issue_amt)>(parseInt($scope.requestdata.accounts.balance)+parseInt($scope.requestdata.accounts.loc)))
			{
				alert("Issued Loc is more than the existing Balance");
			}
			else
			{
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/accept_loc',
					data:{
						user:$scope.requestdata.requestuser,
						hoa:$scope.requestdata.hoa,
						amt:$scope.issue_amt,
						refno:$scope.refno,
						approval:$scope.approval,
						remarks:$scope.remarks
						}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						alert('LOC Approved and has been forwarded to your treasury Officer for final approval!');
						$state.go('sa.loclist');
					}
					else
					{
						Logging.logout();
					}
				});
			}
		}
	}
});

app.controller("SaTransController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_trans'
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
				url:$scope.requesturl+'/sa_chqlist_confirm',
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
				url:$scope.requesturl+'/sa_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Cheques Rejected and sent to your Treasury Officer for approval");
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

app.controller("SaConfirmController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates,$stateParams){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.approval='approve';	
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/sa_chq_data',
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
			$scope.dat.acinfo.balance = parseInt($scope.dat.acinfo.balance) + parseInt($scope.dat.acinfo.transitamount);
			$scope.dat.acinfo.balance=Commas.getcomma($scope.dat.acinfo.balance);
			$scope.dat.laprecinfo.balamt =  Commas.getcomma(parseInt($scope.dat.laprecinfo.partyamount) - parseInt($scope.dat.laprecinfo.lapexp));
			$scope.dat.laprecinfo.partyamount = Commas.getcomma($scope.dat.laprecinfo.partyamount);
			$scope.dat.laprecinfo.dtransdate = Dates.getDate($scope.dat.laprecinfo.transdate);


		}
	});


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
	
	$scope.chq_generate=function(){
		$scope.showloader=true;
		$http({
			method:'GET',
			url:'../front/get_transid.php',
			params:{dat:$scope.dat}
		}).success(function(result){
			$scope.dat.transid=result;
			if(result!='Fail')
			{
				$http({
					method:'GET',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/update_transid',
					params:{chq:$scope.dat.chequeno,trans:$scope.dat.transid,ddocode:$scope.dat.issueuser}
				}).success(function(result){
					$scope.showloader=false;
					if(result[0]=='success')
					{
						
					}
					else
					{
						Logging.logout();
					}
				});

			}else{

				alert('Sorry cant generate trans id!Please try later');
				window.location.reload();
			}
				
				
		});
	}

	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}

	$scope.chq_confirm=function(){
		var chqlist=[$scope.transno];
		$scope.showloader=true;
		if($scope.approval=='approve')
		{
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sa_chqlist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheque Passed and sent to your treasury officer for authorization.");
					$state.go('sa.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
		else
		{
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sa_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheque Rejected and sent to your Treasury Officer for approval");
					$state.go('sa.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}

	$scope.chq_reject=function(){
		var chqlist=[$scope.transno];
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
				url:$scope.requesturl+'/sa_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheque Rejected and sent to your Treasury Officer for approval");
					$state.go('sa.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});

app.controller("SaLedgerController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sa_admins'
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

	$scope.admin_change=function(){
		$scope.hoalist=[];
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_hoas',
			params:{ddo:$scope.pdadmin.ddocode}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.hoalist=result;
			}
		});
	}

	$scope.hoa_change=function(){
		$scope.showloader=true;
		$scope.maintrans=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_ledger',
			params:{ddo:$scope.pdadmin.ddocode,hoa:$scope.hoa.hoa}
		}).
		success(function(result){
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.maintrans=result;
			}
			$scope.showloader=false;
		});
	}
});

app.controller("SaStatementController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
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
		url:$scope.requesturl+'/get_sa_admins'
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
				url:$scope.requesturl+'/get_sa_hoas',
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

app.controller("LocRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.hoa="select";
	$scope.loc="";
	$scope.bal="";
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/loc_report',
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.loclist=result;
			var counter=1;
			angular.forEach($scope.loclist,function(l){
				l.sno=counter;
				counter++;
				l.reqamount=Commas.getcomma(l.reqamount);
				if(!(l.remarks))
				{
					l.remarks='None';
				}
				if(l.conf_flag=='0')
				{
					l.rems='Pending';
					if(l.requestflag=='0')
					{
						l.grantamount='-';
						l.refno='-';
					}
					else
					{
						l.grantamount=Commas.getcomma(l.grantamount);
					}
				}
				else if(l.conf_flag=='1')
				{
					if(l.requestflag=='0')
					{
						l.rems='Pending';
						l.grantamount='-';
						l.refno='-';
					}
					else
					{
						l.rems='Granted';
						l.grantamount=Commas.getcomma(l.grantamount);
					}
				}
				else if(l.conf_flag=='3' || l.conf_flag=='4' || l.conf_flag=='5' || l.conf_flag=='6')
				{
					if(l.requestflag=='0')
					{
						l.rems='Pending';
						l.grantamount='-';
						l.refno='-';
					}
					else
					{
						l.rems='Granted';
						l.grantamount=Commas.getcomma(l.grantamount);
					}
				}
				else
				{
					l.rems='Rejected';
					l.grantamount=Commas.getcomma(l.grantamount);
				}
			});
		}		
	});
	
});

app.controller("ReqRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/req_rpt'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.allrequests=result;
			var counter=1;
			angular.forEach($scope.allrequests,function(l){
				l.sno=counter;
				counter++;
				if(!(l.remarks))
				{
					l.remarks='None';
				}
				l.issuedate='-';
				l.bookno='-';
				l.cstart='-';
				l.cend='-';
				l.gleaf='-';
				if(l.conf_flag=='0')
				{
					l.rems='Pending';
				}
				else if(l.conf_flag=='6' ||   l.conf_flag=='4' || l.conf_flag=='5')
				{
					if(l.requestflag=='0')
					{
						l.rems='Pending';
					}
					else
					{
						l.rems='Granted';
						l.issuedate=Dates.getDate(l.bookdata.issuedate);
						l.bookno=l.bookdata.bookno;
						l.cstart=l.bookdata.chequestart;
						l.cend=l.bookdata.chequeend;
						l.gleaf=(parseInt(l.bookdata.chequeend)-parseInt(l.bookdata.chequestart)+1);
					}
				}
				else
				{
					l.rems='Rejected';
				}
			});
		}
	});
});

app.controller("ChqRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
        $scope.pdadmin="select";
        $http({
                method:'GET',
                headers:{'X-CSRFToken':localStorage.token},
                url:$scope.requesturl+'/get_sa_admins'
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
	
	$scope.admin_change=function(){
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		params:{ddo:$scope.pdadmin},
		url:$scope.requesturl+'/chqrpt'
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
				if(!(tr.rejects))
				{
					tr.rejects='None';
				}
				if(tr.transstatus=='0')
				{
					tr.rems="Cheque pending with Senior Accountant";
				}
				else if(tr.transstatus=='1')
				{
					tr.rems="Cheque pending with government";
				}
				else if(tr.transstatus=='2')
				{
					tr.rems="Cheque sent to bank.Waiting for payment";
				}
				else if(tr.transstatus=='4')
				{
					tr.rems="Cheque sent to bank.Waiting for payment";
				}
				else if(tr.transstatus=='5')
				{
					tr.rems="Cheque pending with DTO/STO";
				}
				else if(tr.transstatus=='21')
				{
					tr.rems="Cheque Rejected";
				}
				else if(tr.transstatus=='3')
				{
					tr.rems="Payment Done";
				}
				else if(tr.transstatus=='65')
				{
					tr.rems="Cheque pending with Deputy Director";
				}
				else if(tr.transstatus=='64')
				{
					tr.rems="Cheque pending with ATO";
				}
				else if(tr.transstatus=='63')
				{
					tr.rems="Cheque pending with STO";
				}
				else if(tr.transstatus=='62')
				{
					tr.rems="Cheque pending with SA";
				}
				else if(tr.transstatus=='61')
				{
					tr.rems="Cheque pending with PD Admin Checker Login";
				}
				else if(!tr.hoa)
				{
					tr.rems="Cheque Cancelled";
					tr.rejects=angular.copy(tr.purpose);
					tr.purpose="-";
					tr.hoa='-';
					tr.amount='-';
					tr.multiflag=1;
					tr.partyname='-';
				}
			});
		}
	});
	}
});

app.controller("SaPageController",function($scope,$http,$state,$stateParams,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.acdat=$stateParams.account
	$scope.page=$stateParams.page;
	$scope.maintrans=[];
	$scope.trans=[];
	$scope.first=false;
	$scope.second=false;
	$scope.endbut=false;
	$scope.end=false;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_ledgerdata',
		params:{account:$stateParams.account}
	}).
	success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.accountinfo=result;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ledgerpage',
				params:{hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode,page:$scope.page}
			}).
			success(function(data){
				$scope.maintrans=data;
			});
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ledgerpagelist',
				params:{hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode}
			}).success(function(data){
				$scope.showloader=false;
				$scope.totpages=data[0];
				if($scope.totpages==0)
				{
					$scope.page=0;
					$scope.pagehide=false;	
				}
				else
				{
					$scope.pagehide=true;
				}
				if($scope.totpages%10==0)
				{
					$scope.pages=$scope.totpages/10;
				}
				else
				{
					$scope.pages=parseInt($scope.totpages/10)+1;
				}
				if($scope.page=='last')
				{
					$scope.page=$scope.pages;
				}
				else if($scope.page>$scope.pages)
				{
					$scope.page=$scope.pages;
				}
				else if(!parseInt($scope.page))
				{
					$scope.page=$scope.pages;	
				}
				else
				{
					$scope.page=parseInt($scope.page);
				}
				if($scope.page<$scope.pages-1)
				{
					$scope.end=true;
				}
				if($scope.page!=$scope.pages)
				{
					$scope.endbut=true;	
				}
				if($scope.page>2)
				{
					$scope.first=true;
				}
				if($scope.page!=1)
				{
					$scope.second=true;
				}
			});
		}
	});
});

app.controller("SaActivateController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sa_admins'
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

	$scope.admin_change=function(){
		$scope.hoalist=[];
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_hoas',
			params:{ddo:$scope.pdadmin.ddocode}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.hoalist=result;
			}
		});
	}

	$scope.delete_trans=function(t){
		if($scope.hoa.activation==2)
		{
			alert('You cannot delete from this account');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/delete_sa_trans',
				params:{chq:t.chequeno}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					$scope.showloader=false;
					for(var j=0;j<$scope.maintrans.length;j++)
					{
						if($scope.maintrans[j]['id']==t.id)
						{
							$scope.maintrans.splice(j,1);
						}
					}
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}

	$scope.hoa_change=function(){
		$scope.showloader=true;
		$scope.maintrans=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_ledger',
			params:{ddo:$scope.pdadmin.ddocode,hoa:$scope.hoa.hoa}
		}).
		success(function(result){
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.maintrans=result;
			}
			$scope.showloader=false;
		});
	}

	$scope.show_data=function(){
		if($scope.hoa)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	$scope.confirm=function()
	{
		if($scope.hoa.activation=='0')
		{
			alert('PD Administrator must confirm the balance first.');
		}
		else if($scope.hoa.activation=='2')
		{
			alert('You have alreaday confirmed this account');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/confirm_sa_account',
				params:{hoa:$scope.hoa.hoa,ddo:$scope.pdadmin.ddocode}
			}).
			success(function(result){
				if(result[0]=='invalid')
				{
					$scope.showloader=false;
					Logging.logout();
				}
				else
				{
					alert('Confirmed');
					window.location.reload();
				}
			});
		}
	}

	$scope.reject=function()
	{
		if($scope.hoa.activation=='0')
		{
			alert('PD Administrator must confirm the balance first.');
		}
		else if($scope.hoa.activation=='2')
		{
			alert('You have alreaday confirmed this account');
		}
		else
		{
			alert('Rejected');
			window.location.reload();
		}
	}
});

app.controller("SaAdjustController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$(document).ready(function(){
		$('#d_from').datepicker({dateFormat: 'dd-mm-yy'});
	});

	$scope.showloader=true;
	$scope.party_type='single';
	$scope.party={};
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sa_admins'
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

	$scope.admin_change=function(){
		$scope.hoalist=[];
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_hoas',
			params:{ddo:$scope.pdadmin.ddocode}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.hoalist=result;
			}
		});
	}

	$scope.partychange=function(){
		if(!$scope.party.acno)
		{
			alert('Please enter account no')
		}
		else if($scope.party.acno!=$scope.party.cacno)
		{
			alert('Both account numbers do not match');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/admin_get_party',
				params:{partyac:$scope.party.acno}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					if(result)
					{
						$scope.party.name=result[0]['partyname'];
						$scope.party.bank=result[0]['partybank'];
						$scope.party.branch=result[0]['partybranch'];
						$scope.party.ifsc=result[0]['partyifsc'];
					}
				}
			});
		}
	}

	$scope.transType=function(type){
		return type===$scope.trans_type;
	}

	$scope.isShown=function(type){
		return type===$scope.party_type;
	}

	$scope.upload = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploading',
			data:formdata,
			headers:{'Content-Type': undefined,
			'X-CSRFToken':localStorage.token},
			transformRequest: function(data) {return data;}
		}).
		success(function(data){
			if(data[0]=='success')
			{
				if(data[1]=='success')
				{
					if(data[2]=='success')
					{
						$scope.filepath=data[3];
					}
					else
					{
						alert(data[2]);
					}
				}
				else
				{
					alert('Please select a file');
				}
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.recipt_submit=function(){
		if($scope.hoa.activation==2)
		{
			alert('You cannot make adjustment to this account');
		}
		else if($('#d_from').val()=='')
		{
			alert('Please enter date');
		}
		else if(!$scope.pdadmin)
		{
			alert('Please select PD Administrator');
		}
		else if(!$scope.hoa)
		{
			alert('Please select Head of Account');
		}
		else if(!$scope.party.purpose)
		{
			alert('Please enter the purpose of the recipt');
		}
		else if(!$scope.party.amount)
		{
			alert('Please enter the Amount');
		}
		else if(!$scope.party.name)
		{
			alert('Please enter the Recipt details');
		}
		else if(!$scope.party.cheque)
		{
			alert('Please enter the Recipt/Challan No');
		}
		else
		{
			$scope.party.hoa=$scope.hoa.hoa;
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/adjust_recipt',
				params:{
					ddo:$scope.pdadmin.ddocode,
					hoa:$scope.party.hoa,
					cheque:$scope.party.cheque,
					amount:$scope.party.amount,
					purpose:$scope.party.purpose,
					name:$scope.party.name,
					dates:$('#d_from').val()
				}
			}).success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='success')
					{
						alert("Recipt Adjusted");
						window.location.reload();
					}
					else
					{
						$scope.showloader=false;
						alert("Please select correct HOA");
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

	$scope.chq_submit=function(){
		if($scope.isShown('single'))
		{
			if($scope.hoa.activation==2)
			{
				alert('You cannot make adjustment to this account');
			}
			else if(!$scope.party.acno)
			{
				alert('Please enter Party Account No');
			}
			else if($scope.party.acno!=$scope.party.cacno)
			{
				alert('Both account numbers do not match');
			}
			else if(!$scope.party.name)
			{
				alert('Please enter Party Name');
			}
			else if(!$scope.party.bank)
			{
				alert("Please enter Party's Bank Name");
			}
			else if(!$scope.party.branch)
			{
				alert("Please enter Party's Bank Branch");
			}
			else if(!$scope.party.ifsc)
			{
				alert("Please enter Party's Bank IFSC Code");
			}
			else if($('#d_from').val()=='')
			{
				alert('Please enter date');
			}
			else if(!$scope.pdadmin)
			{
				alert('Please select PD Administrator');
			}
			else if(!$scope.hoa)
			{
				alert('Please select Head of Account');
			}
			else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else
			{
				$scope.party.hoa=$scope.hoa.hoa;
				$scope.party.dates=$('#d_from').val();
				$scope.party.pdadmin=$scope.pdadmin.ddocode;
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/adjust_single_party',
					data:{partydets:$scope.party}
				}).success(function(result){
					if(result[0]=='success')
					{
						if(result[1]=='success')
						{
							alert("Cheque Adjusted");
							window.location.reload();
						}
						else
						{
							$scope.showloader=false;
							alert("Please select correct HOA");
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
		else if($scope.isShown('multiple'))
		{
			if($scope.hoa.activation==2)
			{
				alert('You cannot make adjustment to this account');
			}
			else if($('#d_from').val()=='')
			{
				alert('Please enter date');
			}
			else if(!$scope.pdadmin)
			{
				alert('Please select PD Administrator');
			}
			else if(!$scope.hoa)
			{
				alert('Please select Head of Account');
			}
			else if(!$scope.filepath)
			{
				alert('Please upload the file');
			}
			else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else
			{
				$scope.party.hoa=$scope.hoa.hoa;
				$scope.showloader=true;
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/adjust_multiple_party',
					data:{
						hoa:$scope.party.hoa,
						partyfile:$scope.filepath,
						cheque:$scope.party.cheque,
						amount:$scope.party.amount,
						purpose:$scope.party.purpose,
						ddo:$scope.pdadmin.ddocode,
						dates:$('#d_from').val()
					}
				}).success(function(result){
					if(result[0]=='success')
					{
						if(result[1]=='success')
						{
							alert("Cheque Adjusted");
							window.location.reload();
						}
						else
						{
							$scope.showloader=false;
							alert("Please select correct HOA");
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
	}
});
