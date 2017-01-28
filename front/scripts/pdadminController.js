app.controller("AdminController",function($scope,$http,$state,Uploader,Logging,Commas){
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
				url:$scope.requesturl+'/get_user_data'	
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='2' || result[0] == '20')
				{

					if(result[7] == null || result[5] == "e10adc3949ba59abbe56e057f20f883e" || result[8].length != 0){

						$state.go("contactdet");
					}
					
					$scope.userdata=result;
					$scope.username=result[1];
					
					$scope.showloader=false;
					if(result[3]==null || result[3]=='')	//not lapsable
					{

							if(result[2]=='0' && result[4].indexOf("2702") != 0)
							{
								$state.go('opening');
							}
					}
					else if(result[3]=='0')	// go to cheques screen even lapsable yet to be done
					{
							if(result[2]=='0' && result[4].indexOf("2702") != 0)
							{
								$state.go('opening');
							}
					}
					else if(result[3]=='1') // go to lapsable screen . cheques are over
					{
							if(result[2]=='0' && result[4].indexOf("2702") != 0)
							{
								$state.go('lapopening');
							}
					}
					
				}
				else if(result[0]==20) {
					
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
app.controller("AdminContactController",function($scope,$http,$state,Uploader,Logging,Commas){

	$scope.showloader=true;
	$scope.contactthis = false;
	$scope.passthis = false;
	$scope.pdacnos = false;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_user_data'
			}).
			success(function(result){
				$scope.userpdacinfo = result[8];
				if(result[0]=='2' || result[0] == '20')
				{

					$scope.showloader=false;

					if(result[6] == null) {

						$scope.contactthis = true;
						$scope.passthis = false;
						$scope.pdacnos = false;

					} else if(result[8].length != 0 && result[0] ==2) {

						$scope.contactthis = false;
						$scope.passthis = false;
						$scope.pdacnos = true;
					} else if(result[5] == "e10adc3949ba59abbe56e057f20f883e") {

						$scope.contactthis = false;
						$scope.pdacnos = false;
						$scope.passthis = true;
					}  else{

						if(result[0] == '20') {

							window.location.href="/pd/front/#/pdadminchecker/main";
						} else {

							window.location.href="/pd/front/#/pdadmin/main";
						}
					}
				}

			});

			$scope.update_details = function(phno, emailid) {

				var isnum = /^\d+$/.test(phno);

				if(phno =="" || !phno){

					alert("Please enter your phone number.");

				} else if(isnum == false) {

					alert("Please enter a valid phone number.");


				} else if(phno.length < 10 ){

					alert("Phone number should be of 10 digits.");

				} else if(!$scope.regex.test(phno)){

					alert("Please enter correct phone number.");
				}else if(emailid=="" || !emailid){

					alert("Please enter your email id.");
				} else if($scope.emailid.indexOf('@') === -1) {

					alert("Please enter correct email id.")
				}else {

					$http({
					method:'GET',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/update_contactdetails',
					params:{phno:phno, emailid:emailid}
					}).
					success(function(result){

						if(result == 1) {

							$scope.contactthis = false;
							$scope.passthis = false;
							$scope.pdacnos = true;
						} else if(result == 2){

							$scope.contactthis = false;
							$scope.passthis = true;
							$scope.pdacnos = false;

						} else {

							window.location.href="/pd/front/#/pdadmin/contatdetails";
						}
						
					});
				}
			}

			$scope.update_pass = function(newpass, newpassconf) {

				$scope.pass = [];

				if(newpass =="" || !newpass){

					alert("Please enter new password.");

				}else if(newpassconf=="" || !newpassconf){

					alert("Please confirm new password.");
				} else if(newpass != newpassconf) {

					alert("Passwords do not match.");
				} else if(newpass == "123456") {

					alert("Please enter password other than default password.");
				}else {

					
					$scope.showloader=true;
					$http({
					method:'GET',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/update_pass',
					params:{newpass:newpass, oldpass:'123456'}
					}).
					success(function(result){
						window.location.reload();
						
					});
				}


			}

			$scope.update_pdacno = function() {

				var emptycount = 0;
				var wrongformat = 0;
				var confirmfail = 0;
				angular.forEach($scope.userpdacinfo,function(pdinfo){
						
						if(pdinfo['acno']==""|| !pdinfo['acno']) {

							emptycount = 1;
						}
						if(pdinfo['acno'] && !$scope.regex.test(pdinfo['acno'])) {

							wrongformat = 1;
						}
						if(pdinfo['acno'] != pdinfo['acnoconf']) {

							confirmfail = 1;
						}
				});

				if(emptycount == 1) {

					alert("Please enter all PD Account numbers.");
				} else if(wrongformat == 1) {

					alert("Please enter all PD Account number in correct format.");
				} else if(confirmfail == 1) {

					alert("One of your PD account number and confirm pd acount number doesnot match. Please check and enter again.");
				} else {

					$scope.showloader=true;
					$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/update_pdacno',
					data:{userpdacinfo:$scope.userpdacinfo}
					}).
					success(function(result){

					  if(result == 2) {
					  		$scope.showloader=false;
					  		$scope.contactthis = false;
							$scope.passthis = true;
							$scope.pdacnos = false;

					  } else {

					  	window.location.reload();
					  }
											
					});
				}
			}

});

app.controller("AdminStartController",function($scope,$http,$state,Uploader,Logging,Dates,Commas){
	$scope.Dates = Dates;
	$scope.$emit("changeTitle",'Start');
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
				url:$scope.requesturl+'/get_user_data'
			}).
			success(function(result){
				$scope.showloader=true;
				if(result[0]=='2')
				{
					$scope.userdata=result;
					$scope.username=result[1];

					$scope.laprecdet = result[4];
					$scope.showloader=false;

					angular.forEach($scope.laprecdet,function(x){
						x.camt = Commas.getcomma(x.partyamount);
						x.date = Dates.getDate(x.transdate);
					});

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

	$scope.no_leaves=function(){
		if(!$scope.first)
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if($scope.first=="")
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if(!$scope.last)
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if($scope.last=="")
		{
			return "--Please enter first and last cheque numbers--";
		}
		else if(!$scope.regex.test($scope.last))
		{
			return "--Please correct first and last cheque numbers--";
		}
		else if(!$scope.regex.test($scope.first))
		{
			return "--Please correct first and last cheque numbers--";	
		}
		else if(parseInt($scope.last)<parseInt($scope.first))
		{
			return "--Please correct first and last cheque numbers--";
		}
		else
		{
			return (parseInt($scope.last)-parseInt($scope.first)+1)
		}
	}

	$scope.add_book=function(){
		$scope.showloader=true;
		if(!$scope.first)
		{
			alert("Please enter First Cheque No");
			$scope.showloader=false;
		}
		else if(!$scope.last)
		{
			alert("Please enter Last Cheque No");
			$scope.showloader=false;
		}
		else if(!$scope.regex.test($scope.last))
		{
			alert("Please enter correct Last Cheque No");
			$scope.showloader=false;
		}
		else if(!$scope.regex.test($scope.first))
		{
			alert("Please enter correct First Cheque No");
			$scope.showloader=false;
		}
		else if(parseInt($scope.last)<parseInt($scope.first))
		{
			alert("Please enter correct First and Last Cheque No");
			$scope.showloader=false;
		}
		else if(!$scope.number)
		{
			alert("Please enter Cheque Book No");
		}
		else
		{
			
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/start',
				data:{book:$scope.number,first:$scope.first,last:$scope.last}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					if(result[0]=='success')
					{
						alert('Thank You! Your account has been activated!Please read the user manual to understand all the features better! You can find the user manual in GENERAL>User Manual');
						$state.go('admin.main');
					}
					else if(result[0]=='forward')
					{
						alert('Thank You! Your cheque leaves details have been captured, you have Lapsable HOAs among your PD Accounts you will now be forwarded to the lapsable details screen please enter those details in order to activate your account!');
						$state.go('lapopening');
					}
					else
					{
						alert(result[0]);
					}
				}
			});
		}
	}
});

app.controller("AdminLapStartController",function($scope,$http,$state,Uploader,Logging,Dates,Commas){
	$scope.Dates = Dates;
	$scope.$emit("changeTitle",'Start');
	
	$scope.showloader=false;
	if(localStorage.token)
	{
		if(!$scope.token)
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_user_lap_data'
			}).
			success(function(result){
				$scope.showloader=false;
				

				if(result.length==0)
				{
					$scope.showloader=true;
					$http({
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/post_lepexp_empty',
					}).
					success(function(result){
						$scope.showloader=false;
						if(result=='invalid')
						{
							Logging.logout();
						}
						else if(result=='success')
						{
							alert('You do not have any receipts under your lapsable HOA, your account will now be activated! Please wait!');
							$state.go('admin.main');
						}
					});
				}
				else
				{
					$scope.laprecdet = result;
					angular.forEach($scope.laprecdet,function(x){
						x.camt = Commas.getcomma(x.partyamount);
						x.date = Dates.getDate(x.transdate);
						x.lappexp = 0;
						x.lapref ='';
					});
				}
				

			});
		}
	}
	else
	{
		Logging.logout();
	}

	$scope.enter_lap = function()
	{
		if(localStorage.token)
		{
			if(!$scope.token)
			{
				var flag = 0;

				angular.forEach($scope.laprecdet,function(x){
					// if(x.lapref=='')
					// {
					// 	flag = 1;
					// }
					if(parseInt(x.partyamount)<parseInt(x.lappexp))
					{
						flag = 2;
					}
					if(x.lappexp==='')
					{
						flag = 3;
					}
				});

				
				if(flag==1)
				{
					alert('Please fill in all the reference fields for all the receipts');
				}
				else if(flag==2)
				{
					alert('Please check the expenditures that you have entered, expenditure cannot be more than your receipt amount!');
				}
				else if(flag==3)
				{
					alert('Please enter expenditures in all the expenditure columns provided!');
				}
				else
				{
					$scope.showloader=true;
					$http({
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/post_lepexp',
						data:{dat:$scope.laprecdet}
					}).
					success(function(result){
						$scope.showloader=false;
						if(result=='invalid')
						{
							Logging.logout();
						}
						else if(result=='success')
						{
							$state.go('admin.main');
						}
					});
				}
				
			}
		}
		else
		{
			Logging.logout();
		}
	}

	
});

app.controller("AdminMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_cheq'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.chequeno=result.length;
			if($scope.chequeno<=10)
			{
				$scope.placerequest=true;
			}
			else
			{
				$scope.placerequest=false;
			}
		}
	});
});

app.controller("AdminRequestController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.leaf='25';
	$scope.otype='y';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_req'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.reqs=result;
		}
	});
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_cheq'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.chequeno=result.length;
			if($scope.chequeno<=10)
			{
				$scope.placerequest=true;
			}
			else
			{
				$scope.placerequest=false;
			}
		}
	});

	$scope.showchqs=function(){
		if(parseInt($scope.chequeno)<=10)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	$scope.request=function(){
		if($scope.placerequest)
		{
			if($scope.otype=='y')
			{
				$scope.challan='exempted';
				$scope.rmtamt='exempted';
			}
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/place_request',
				data:{leaves:$scope.leaf,challan:$scope.challan,rmtamt:$scope.rmtamt}
			}).success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='success')
					{
						if(result[2]=='success')
						{
							alert('Your request has been placed!You can view the status of your request in REPORTS > Cheque book req. report');
							window.location.reload();
						}
						else
						{
							$scope.showloader=false;
							alert("Error while placing request.Please try later");
						}
					}
					else if(result[1]=='invalid')
					{
						$scope.showloader=false;
						alert('You already have a pending request for a cheque book!');
					}
					else if(result[1]=='nomap')
					{
						$scope.showloader=false;
						alert('Your Treasury office has not mapped any SA to cheque book requests! Please contact your Treasury Office and try again later!');
					}

				}
				else if(result[0]=='invalid')
				{
					Logging.logout();
				}
			});
		}
		else{
			$scope.showloader=false;
			alert("You have enough cheques in your current cheque book");
		}
	}
});

app.controller("AdminLocController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_loc_hoa'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.schemelist=result;
		}
	});
	$scope.hoa="select";
	$scope.loc="";
	$scope.bal="";

	$scope.loc_words=function(){
		if(!$scope.new_loc)
		{
			return "---Please enter LOC amount to display relavant data---";
		}
		else if($scope.new_loc=='')
		{
			return "---Please enter LOC amount to display relavant data---";
		}
		else if($scope.regex.test($scope.new_loc))
		{
			return getwords($scope.new_loc);
		}
		else
		{
			$scope.new_loc="";
		}
	}

	function getwords(e){if(parseInt(e) == 0) { return "ZERO";} else {e = e.replace(/^0+/, ''); var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t} }

	$scope.hoa_balance=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else
		{
			var bal;
			angular.forEach($scope.schemelist, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					bal=scheme.balance;
					$scope.bal=bal;
				}
			});
			return Commas.getcomma(bal);
		}
	}
	
	$scope.hoa_loc=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else
		{
			var bal;
			angular.forEach($scope.schemelist, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					if(scheme.status!=1)
					{
						alert('This account needs to be reconciled by your treasury officer');
						$scope.hoa='select';
					}
					else
					{
						bal=scheme.loc;
						$scope.loc=bal;
						return Commas.getcomma(bal);
					}
				}
			});
			return Commas.getcomma(bal);
		}
	}
 
	$scope.request=function(){
		if($scope.hoa=='select')
		{
			alert('Please select Head of Account');
		}
		else if(!$scope.new_loc)
		{
			alert('Please enter the LOC required');
		}
		else if(!$scope.purpose) {

			alert('Please enter Loc request purpose.');
		}
		else if(parseInt($scope.new_loc)>(parseInt($scope.bal)-parseInt($scope.loc)) && $scope.hoa !="8011001050001000000NVN")
		{
			alert('You do not have sufficient balance to process this LOC!');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/place_loc',
				data:{hoa:$scope.hoa,amt:$scope.new_loc, purpose:$scope.purpose}
			}).success(function(result){
				if(result[0]=='success')
				{
					if(result[1]=='success')
					{
						if(result[2]=='success')
						{
							alert('LOC Request placed! you can view the status of your request in REPORTS>LOC Report');
							window.location.reload();
						}
						else
						{
							$scope.showloader=false;
							alert("Error while placing request.Please try later");
						}
					}
					else if(result[1]=='invalid')
					{
						alert('You already have a pending request for a LOC! You cannot request for another LOC before the first one is processed by the DTO/STO');
						window.location.reload();
					}
					else if(result[1]=='nomap')
					{
						alert('Your HOA is not mapped to any Senior Accountant in the Treasury Office! Please contact the Treasury Office and kindly ask them to map your HOA to any senior accountant in their Office!');
						window.location.reload();
					}

				}
				else if(result[0]=='invalid')
				{
					Logging.logout();
				}
				
			});
		}
	}
});

app.controller("AdminLocRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_loc_hoa'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.schemelist=result;
			
		}
	});
	$scope.hoa="select";
	$scope.loc="";
	$scope.bal="";

	$scope.show_loc_rpt=function(){
		if($scope.hoa=='select')
		{
			$scope.loclist={};
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/admin_loc_report',
				params:{hoa:$scope.hoa}
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
						else if(l.conf_flag=='1' || l.conf_flag=='3' || l.conf_flag=='4' || l.conf_flag=='5' || l.conf_flag=='6' || l.conf_flag=='33')
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
		}
	}

	$scope.hoa_balance=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else
		{
			var bal;
			angular.forEach($scope.schemelist, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					bal=scheme.balance;
					$scope.bal=bal;
				}
			});
			return Commas.getcomma(bal);
		}
	}
	
	$scope.hoa_loc=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else
		{
			var bal;
			angular.forEach($scope.schemelist, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					bal=scheme.loc;
					$scope.loc=bal;
				}
			});
			return Commas.getcomma(bal);
		}
	}
 
	
});

app.controller("AdminReqRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_req_rpt'
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
				else if(l.conf_flag=='1' || l.conf_flag=='4' || l.conf_flag=='5' || l.conf_flag=='6' || l.conf_flag=='3' || l.conf_flag=='33')
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

app.controller('AdminCancelController',function($scope,$http,$state,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_cheq'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.chequeno=result.length;
			if($scope.chequeno==0)
			{
				alert("You do not have any cheques to issue.Please place a request for a new cheque book");
				$state.go('admin.request');
			}
			else
			{
				$scope.cheque=result[0]['chequeno'];
			}
		}
	});

	$scope.cancel=function(){
		if(!$scope.reason)
		{
			alert('Please enter the reason for cancelling the cheque');
		}
		else if($scope.reason=='')
		{
			alert('Please enter the reason for cancelling the cheque');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/cancel_chq',
				data:{chq:$scope.cheque,reason:$scope.reason}
			}).
			success(function(result){
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					alert('Cheque is cancelled');
					window.location.reload();
				}
			});
		}
	}
});

app.controller("AdminChequeController",function($scope,$http,$state,$rootScope,Uploader,Logging,Commas,Dates){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.lapmonth = "select";
	$scope.lapyear = "select";
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.party_type='single';
	$scope.showloader=true;
	$scope.confchq=false;
	$scope.hoa='select';
	$scope.party={};
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_cheq'
	}).success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.chequeno=result.length;
			if($scope.chequeno==0)
			{
				alert("You do not have any cheques to issue.Please place a request for a new cheque book");
				$state.go('admin.request');
			}
			else
			{
				$scope.party.cheque=result[0]['chequeno'];
				$scope.get_hoa();
			}
		}
	});

	$scope.ifsc_search=function(){
		if(!$scope.party.ifsc)
		{
			alert('Please enter IFSC Code');
		}
		else if($scope.party.ifsc=='')
		{
			alert('Please enter IFSC Code');	
		}
		else if($scope.party.ifsc.length!=11)
		{
			alert('Wrong IFSC Code');
		}
		else if($scope.party.ifsc.substr(4,1)!='0')
		{
			alert('Wrong IFSC Code');
		}
		else
		{
			
			$scope.party.ifsc = $scope.party.ifsc.toUpperCase();
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ifsc',
				params:{ifsc:$scope.party.ifsc}
			}).success(function(result){
				$scope.showloader=false;
				if(result=="")
				{
					alert("Please check your ifsc code and place a query");
				}
				else
				{
					$scope.party.bank=result['bankname'];
					$scope.party.branch=result['branch'];
				}
			});
		}
	}

	

	$scope.amount_words=function(){
		if(!$scope.party.amount)
		{
			return "---Please enter Party amount to display relavant data---";
		}
		else if($scope.party.amount=='')
		{
			return "---Please enter Party amount to display relavant data---";
		}
		else if($scope.regex.test($scope.party.amount))
		{
			return getwords($scope.party.amount);
		}
		else
		{
			$scope.party.amount="";
		}
	}
	
	$scope.words=function(dat)
	{
		return getwords(dat)+' ONLY';
	}

	function getwords(e){if(parseInt(e) == 0) { return "ZERO";} else {e = e.replace(/^0+/, ''); var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t} }

	$scope.show_bankname=function(){
		if(!$scope.party.bank)
		{
			return "--Please enter IFSC Code and Search to show relevant data--";
		}
		else if($scope.party.bank=='')
		{
			return "--Please enter IFSC Code and Search to show relevant data--";
		}
		else
		{
			return $scope.party.bank;
		}
	}

	$scope.show_bankbranch=function(){
		if(!$scope.party.branch)
		{
			return "--Please enter IFSC Code and Search to show relevant data--";
		}
		else if($scope.party.branch=='')
		{
			return "--Please enter IFSC Code and Search to show relevant data--";
		}
		else
		{
			return $scope.party.branch;
		}
	}

	$scope.get_hoa=function(){
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_hoa'
		}).
		success(function(dat){
			if(dat[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.allaccounts=dat;
			}
			$scope.showloader=false;
		});
	}
	
	$scope.hoa_balance=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else
		{
			var bal;
			angular.forEach($scope.allaccounts, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					if(scheme.status!=1)
					{
						alert('This account needs to be reconciled by your treasury officer');
						$scope.hoa='select';
					}
					else
					{
						bal=scheme.balance;
						$scope.bal=bal;
						$scope.actype=scheme.account_type;
						return Commas.getcomma(bal);
					}
				}
			});
			return Commas.getcomma(bal);
		}
	}



	$scope.valid_till=function(){
		
		if(!$scope.laprec)
		{
			delete $scope.valdate;
			return "---Please select a receipt to display relavant data---";
		}
		else
		{
			
			recdate= $scope.laprec.transdate;

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
			
			return Dates.getDate($scope.valdate);
		}
	}
	
	$scope.lapcheck = function()
	{
		if($scope.hoa=="select")
		{
			return false;
		}
		else
		{
			var dat;
			
			angular.forEach($scope.allaccounts, function(x){
				if(x.hoa==$scope.hoa)
				{
					if(x.lapsableflag=='1')
					{
						dat = true;
						
					}else
					{
						dat = false;
					}
				}
			});
			
			return dat;
		}
	}


	$scope.get_receipts=function(){

		delete $scope.laprec;
		
		var yy=parseInt($scope.maindate.substr(6,4));
		var mm=parseInt($scope.maindate.substr(3,2));
		var flg=0;

		if($scope.lapmonth!='select' && $scope.lapyear!='select')
		{
			// if($scope.lapyear=='2014')
			// {
			// 	if(parseInt($scope.lapmonth)<5)
			// 	{
			// 		alert('Sorry this data is not available');
			// 		flg=1;
			// 		$scope.allrec = [];
			// 	}
			// }
			// if(parseInt($scope.lapmonth)>mm)
			// {
			// 	alert('Sorry this data is not available');
			// 	flg=1;
			// 	$scope.allrec =[];
			// }
			if(flg==0)
			{
				$scope.showloader=true;
				
				$http({
                        method:'GET',
                        headers:{'X-CSRFToken':localStorage.token},
                      	url:$scope.requesturl+'/get_receipts_data',
                        params:{y:$scope.lapyear,m:$scope.lapmonth,hoa:$scope.hoa}
                }).
                success(function(result){
                        if(result[0]=='invalid')
                        {
                                Logging.logout();
                        }
                        else
                        {
                        	if(result.length!=0)
                        	{
                        		 $scope.allrec=result;
                        	}
                        	else
                        	{
                        		$scope.allrec=result;
                        		alert('Sorry there are no receipts in this month of the selected year!');

                        	}
                               
                            $scope.showloader=false;

                        }
                });
			}

		}

		
	}




	$scope.hoa_loc=function(){
		if($scope.hoa=="select")
		{
			return "---Please select Head of Account to display relavant data---";
		}
		else if($scope.actype=='1')
		{
			return 'Not Applicable';
		}
		else
		{
			var bal;
			angular.forEach($scope.allaccounts, function(scheme){
				if(scheme.hoa==$scope.hoa)
				{
					bal=scheme.loc;
					$scope.loc=bal;
				}
			});
			return Commas.getcomma(bal);
		}
	}

	
	$scope.isShown=function(type){
		return type===$scope.party_type;
	}



	$scope.partychange=function(){
		if(!$scope.party.cacno || $scope.party.cacno=="")
		{
			if($scope.party.acno)
			{
				alert('Please enter account no')
			}
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
					if(result.length!=0)
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

	$scope.totals=0;

	$scope.upload = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$scope.allplist={};
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploading',
			data:formdata,
			headers:{'Content-Type': undefined,
			'X-CSRFToken':localStorage.token},
			transformRequest: function(data) {return data;}
		}).
		success(function(data){
			$scope.showloader=false;
			if(data[0]=='success')
			{
				if(data[1]=='success')
				{
					if(data[2]=='success')
					{
						if(data[3].length==0)
						{
							alert('Please enter the party details and check your file format');
						}
						else
						{
							$scope.allplist=data[3];
							$scope.tamount=0;
							angular.forEach($scope.allplist,function(pl){
								$scope.tamount=$scope.tamount+parseInt(pl[5]);
								pl[5]=Commas.getcomma(pl[5]);
							});
							$scope.tamount = Commas.getcomma($scope.tamount);
							$scope.filepath=data[4];
						}
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


	$scope.getfilelist = function() {

		$scope.showloader=true;
		$http({
			method:'GET',
			url:$scope.requesturl+'/getfilelist',
			headers:{'X-CSRFToken':localStorage.token}
		}).
		success(function(data){
			$scope.showloader=false;

			console.log(data);
			
			$scope.filelist = data;
		});

	}



	$scope.fetch = function(filename) {
		
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/fetchdata',
			data:{filename:filename},
			headers:{'X-CSRFToken':localStorage.token}
		}).
		success(function(data){
			console.log(data);
			$scope.showloader=false;
			if(data[0]=='success')
			{
				if(data[1]=='success')
				{
					if(data[2]=='success')
					{
						if(data[3].length==0)
						{
							alert('Please upload file through SFTP first.');
						}
						else
						{
							$scope.allplist=data[3];
							$scope.tamount=0;
							angular.forEach($scope.allplist,function(pl){
								$scope.tamount=$scope.tamount+parseInt(pl[5]);
								pl[5]=Commas.getcomma(pl[5]);
							});
							$scope.tamount = Commas.getcomma($scope.tamount);
							$scope.filepath=data[4];

							$scope.filelist = "";
							$scope.hidefetch = true;
						}
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




	$scope.uploadpdtopd = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$scope.allplist={};
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploadingpdtopd',
			data:formdata,
			headers:{'Content-Type': undefined,
			'X-CSRFToken':localStorage.token},
			transformRequest: function(data) {return data;}
		}).
		success(function(data){
			$scope.showloader=false;
			if(data[0]=='success')
			{
				if(data[1]=='success')
				{
					if(data[2]=='success')
					{
						if(data[3].length==0)
						{
							alert('Please enter the PD Account details and check your file format');
						}
						else
						{
							$scope.allpdtopdlist=data[3];
							$scope.pdtamount=0;
							angular.forEach($scope.allpdtopdlist,function(pl){
								$scope.pdtamount=$scope.pdtamount+parseInt(pl[3]);
								pl[3]=Commas.getcomma(pl[3]);
							});
							$scope.pdtamount = Commas.getcomma($scope.pdtamount);
							$scope.pdfilepath=data[4];
						}
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



	$scope.chq_issue=function(){
		if($scope.isShown('single'))
		{

			if(!$scope.party.acno)
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
			else if(!$scope.party.ifsc)
			{
				alert("Please enter Party's Bank IFSC Code");
			}
			else if(!$scope.party.bank)
			{
				alert("Please enter valid IFSC code and search");
			}
			else if(!$scope.party.branch)
			{
				alert("Please enter valid IFSC code and search");
			}
			else if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient LOC');
			}
			else
			{
				$scope.party.hoa=$scope.hoa;
				$scope.showloader=true;

				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						accountdat = x;
					}
				});


				if(accountdat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						//set some more variables
						$scope.party.lapid = $scope.laprec.id;
						if(!$scope.lapremarks)
						{
							$scope.party.lapremarks = '';
						}
						else
						{
							$scope.party.lapremarks = $scope.lapremarks;
						}
						
						

						$http({///lapsable hoa Ajax call
							method:'POST',
							headers:{'X-CSRFToken':localStorage.token},
							url:$scope.requesturl+'/issue_single_party_lapsable',
							data:{partydets:$scope.party,bookno:$scope.bookno}
						}).success(function(result){
							if(result[0]=='success')
							{
								if(result[1]=='success')
								{
									if(result[2]=='success')
									{
											alert("Cheque Issued and has been forwarded!");
											window.location.reload();
									}
									else
									{
										$scope.showloader=false;
										alert(result[2]);
									}
								}
								else if(result[1]=='nomap')
								{
									alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
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
								Logging.logout();
							}
						});
					}

				}
				else
				{
					
					$http({ //non lapsable hoa Ajax call
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/issue_single_party',
						data:{partydets:$scope.party,bookno:$scope.bookno}
					}).success(function(result){
						if(result[0]=='success')
						{
							if(result[1]=='success')
							{
								if(result[2]=='success')
								{
										alert("Cheque Issued and has been forwarded!");
										window.location.reload();
								}
								else
								{
									$scope.showloader=false;
									alert(result[2]);
								}
							}
							else if(result[1]=='nomap')
							{
								alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
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
							Logging.logout();
						}
					});

				}
			}
		}
		else if($scope.isShown('multiple'))
		{
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient Loc');
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
			else if(Commas.getcomma($scope.party.amount)!=$scope.tamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else
			{
				$scope.party.hoa=$scope.hoa;
				$scope.showloader=true;

				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						accountdat = x;
					}
				});


				if(accountdat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						//set some more variables
						$scope.party.lapid = $scope.laprec.id;

						if(!$scope.lapremarks)
						{
							$scope.lapremarks = '';
						}
						else
						{
							$scope.lapremarks = $scope.lapremarks;
						}

						$http({//lapsable multiple party ajax
							method:'POST',
							headers:{'X-CSRFToken':localStorage.token},
							url:$scope.requesturl+'/issue_multiple_party_lapsable',
							data:{hoa:$scope.party.hoa,partyfile:$scope.filepath,cheque:$scope.party.cheque,amount:$scope.party.amount,purpose:$scope.party.purpose,bookno:$scope.bookno,lapid:$scope.party.lapid,lapremarks:$scope.lapremarks}
						}).success(function(result){
							if(result[0]=='success')
							{
								if(result[1]=='success')
								{
									alert("Cheque Issued and Forwarded");
									window.location.reload();
								}
								else
								{
									$scope.showloader=false;
									alert("Please select correct HOA");
								}
							}
							else if(result[1]=='nomap')
							{
								alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
								window.location.reload();
							}
							else
							{
								Logging.logout();
							}
						});

					}

				}else
				{
					$http({//non lapsable multiple 
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/issue_multiple_party',
						data:{hoa:$scope.party.hoa,partyfile:$scope.filepath,cheque:$scope.party.cheque,amount:$scope.party.amount,purpose:$scope.party.purpose,bookno:$scope.bookno}
					}).success(function(result){
						if(result[0]=='success')
						{
							if(result[1]=='success')
							{
								alert("Cheque Issued and Forwarded");
								window.location.reload();
							}
							else if(result[1]=='nomap')
							{
								alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
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
							Logging.logout();
						}
					});
				}


				
			}
		}
		else if($scope.isShown('pdtopd'))
		{

			console.log($scope.hoa);
			
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2 && parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient Loc');
			}
			else if(!$scope.pdfilepath)
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
			else if(Commas.getcomma($scope.party.amount)!=($scope.pdtamount))
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else
			{
				$scope.party.hoa=$scope.hoa;
				$scope.showloader=true;

				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						accountdat = x;
					}
				});

				if(accountdat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						//set some more variables
						$scope.party.lapid = $scope.laprec.id;

						if(!$scope.lapremarks)
						{
							$scope.lapremarks = '';
						}
						else
						{
							$scope.lapremarks = $scope.lapremarks;
						}

						$http({
							method:'POST',
							headers:{'X-CSRFToken':localStorage.token},
							url:$scope.requesturl+'/issue_pdtopd_cheque_lapsable',
							data:{hoa:$scope.party.hoa,partyfile:$scope.pdfilepath,cheque:$scope.party.cheque,amount:$scope.party.amount,purpose:$scope.party.purpose,bookno:$scope.bookno,lapid:$scope.party.lapid,lapremarks:$scope.lapremarks}
						}).success(function(result){
							if(result[0]=='success')
							{
								if(result[1]=='success')
								{
									alert("Cheque Issued and Forwarded");
									window.location.reload();
								}
								else if(result[1]=='nomap')
								{
									alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
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
								Logging.logout();
							}
						});

					}

				}else
				{

					$http({
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/issue_pdtopd_cheque',
						data:{hoa:$scope.party.hoa,partyfile:$scope.pdfilepath,cheque:$scope.party.cheque,amount:$scope.party.amount,purpose:$scope.party.purpose,bookno:$scope.bookno}
					}).success(function(result){
						if(result[0]=='success')
						{
							if(result[1]=='success')
							{
								alert("Cheque Issued and Forwarded");
								window.location.reload();
							}
							else if(result[1]=='nomap')
							{
								alert('The HOA that you have selected has not been mapped to any Senior Accountant! You cannot issue any cheque under this HOA until the Deputy Director Maps your HOA to any SA, Please contact the Treasury Office and get your HOA mapped! ');
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
							Logging.logout();
						}
					});

				}

				
			}
		}
	}

	$scope.chq_cancel=function(){
		window.location.reload();
	}

	$scope.chq_submit=function(){

		if($scope.isShown('single'))
		{
			if(!$scope.party.acno)
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
			else if($scope.hoa=='select')
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
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient Loc');
			}
			else
			{
				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						maindat = x;
					}
				});
						
				if(maindat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						$scope.confchq=true;
					}

				}
				else
				{
					$scope.confchq=true;

				}
				
			}
		}
		else if($scope.isShown('multiple'))
		{
			console.log(Commas.getcomma($scope.party.amount));
			console.log($scope.tamount);
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN'&& $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient Loc');
			}
			else if(!$scope.filepath)
			{
				alert('Please upload the file');
			}else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else if(Commas.getcomma($scope.party.amount)!=($scope.tamount))
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else
			{
				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						maindat = x;
					}
				});
						
				if(maindat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						$scope.confchq=true;
					}

				}
				else
				{
					$scope.confchq=true;
				}
			}
		}
		else if($scope.isShown('pdtopd'))
		{
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient Loc');
			}
			else if(!$scope.pdfilepath)
			{
				alert('Please upload the file');
			}else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else if(Commas.getcomma($scope.party.amount)!=$scope.pdtamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN' && $scope.allaccounts[0]['ddocode'] != '05010307005' && $scope.hoa != '8443001060001000000NVN')
			{
				alert('Insufficient funds');
			}
			else
			{
				angular.forEach($scope.allaccounts, function(x){
					if(x.hoa==$scope.hoa)
					{
						maindat = x;
					}
				});
						
				if(maindat.lapsableflag=='1')
				{
					if($scope.lapmonth=='select')
					{
						alert('This is a lapsable HOA, please select a month and then proceed!');
					}
					else if($scope.lapyear=='select')
					{
						alert('This is a lapsable HOA, please select a year and then proceed!');
					}
					else if(!$scope.laprec)
					{
						alert('This is a lapsable HOA, please select a receipt and then proceed!');
					}
					else if($scope.maindate>$scope.valdate && (!$scope.lapremarks))
					{
						
						alert('This is a lapsable HOA and the validity of your receipt has been over! If you want to still use these funds then please enter remarks and then proceed!');
						
					}
					else if(parseInt($scope.party.amount)>(parseInt($scope.laprec.partyamount) - parseInt($scope.laprec.lapexp)))
					{
						alert('This is a lapsable HOA! The entered amount is greater than the existing balance of your selected receipt! Please check again and proceed!');
					}
					else
					{
						$scope.confchq=true;
					}

				}
				else
				{
					$scope.confchq=true;
				}
			}
		}
	}
});

app.controller("AdminBookController",function($scope,$http,$state,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_booklist'
	}).
	success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.mybooks=result;
		}
		$scope.showloader=false;
	});
});

app.controller("AdminActivateController",function($scope,$http,$state,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_booklist'
	}).
	success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.hoalist=result;
		}
		$scope.showloader=false;
	});

	$scope.hoa_change=function(){
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_hoa_trans',
			params:{hoa:$scope.hoa.hoa}
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
		if($scope.hoa.activation!='0')
		{
			alert('You have already confirmed this account');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/confirm_account',
				params:{hoa:$scope.hoa.hoa}
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
		if($scope.hoa.activation!='0')
		{
			alert('You have already confirmed this account');
		}
		else if(!$scope.reasons)
		{
			alert('Please enter the re  ason for rejecting');
		}
		else if($scope.reasons=='')
		{
			alert('Please enter the reason for rejecting');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/reject_account',
				params:{hoa:$scope.hoa.hoa,reason:$scope.reasons}
			}).
			success(function(result){
				if(result[0]=='invalid')
				{
					$scope.showloader=false;
					Logging.logout();
				}
				else
				{
					alert('Rejected');
					window.location.reload();
				}
			});
		}
	}
});

app.controller("AdminChqRptController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/admin_chqrpt'
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
			var totamount = 0;
			var totdoneamount = 0;
			var totpenamount = 0;
			var totrejamount = 0;
			angular.forEach($scope.alltrans,function(tr){
				tr.sno=counter;
				counter++;
				totamount = totamount+parseInt(tr.partyamount);
				if(!(tr.rejects))
				{
					tr.rejects='None';
				}
				if(tr.transstatus=='0')
				{
					tr.rems="Cheque pending with Senior Accountant";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='1')
				{
					tr.rems="Cheque pending with government";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='2')
				{
					tr.rems="Cheque forwarded to bank waiting for confirmation!";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='4')
				{
					tr.rems="Cheque reciept confirmed by bank! waiting for payment ";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='5')
				{
					tr.rems="Cheque pending with DTO/STO";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='21')
				{
					tr.rems="Cheque Rejected";
					totrejamount = totrejamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='3')
				{
					tr.rems="Payment Done";
					totdoneamount = totdoneamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='61')
				{
					tr.rems="Cheque with PD admin checker!";

					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='62')
				{
					tr.rems="Cheque with SA!";
					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='63')
				{
					tr.rems="Cheque with STO!";
					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='64')
				{
					tr.rems="Cheque with ATO!";
					totpenamount = totpenamount+parseInt(tr.partyamount);
				}
				else if(tr.transstatus=='65')
				{
					tr.rems="Cheque with DD/STO/DTO!";
					totpenamount = totpenamount+parseInt(tr.partyamount);
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

			$scope.chqlistall = $scope.alltrans.length;
			if(totrejamount == 0 || totrejamount == "") 
			{
				totrejamount = "0";
			}
			if(totamount != 0) {
				totamount = Commas.getcomma(totamount);
			}
			if(totdoneamount != 0) {
				totdoneamount = Commas.getcomma(totdoneamount);
			}
			if(totpenamount != 0) {
				totpenamount = Commas.getcomma(totpenamount);
			}
			if(totrejamount != 0) {
				totrejamount = Commas.getcomma(totrejamount);
			}
			


			$(".totamount").text(totamount);
			$(".totdoneamount").text(totdoneamount);
			$(".totpenamount").text(totpenamount);
			$(".totrejamount").text(totrejamount);

		}
	});
});

app.controller("AdminPageController",function($scope,$http,$state,$stateParams,Dates,Logging,Commas){
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
		url:$scope.requesturl+'/get_bookdata',
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
				url:$scope.requesturl+'/get_pagedata',
				params:{hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode,page:$scope.page}
			}).
			success(function(data){
				$scope.maintrans=data;
			});
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_pagelist',
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

app.controller("AdminStatementController",function($scope,$http,$state,$stateParams,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.acdat=$stateParams.account;
	$scope.month='select';
	$scope.year='select';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_bookdata',
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
			$scope.showloader=false;
		}
	});

	$scope.submit=function(){
		var yy=parseInt($scope.maindate.substr(6,4));
		var mm=parseInt($scope.maindate.substr(3,2));
		var flg=0;
		if($scope.month=='select')
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
			alert('Sorry this data is not available for this month');
			flg=1;
		}
		else if(parseInt($scope.year)==yy)
		{
			if($scope.year=='2014')
			{
				if(parseInt($scope.month)<5)
				{
					alert('Sorry this data is not available for this month');
					flg=1;
				}
			}
			if(parseInt($scope.month)>mm)
			{
				alert('Sorry this data is not available for this month');
				flg=1;
			}
		}
		else
		{
			if($scope.year=='2014')
			{
				if(parseInt($scope.month)<5)
				{
					alert('Sorry this data is not available for this month');
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
                                        params:{y:$scope.year,m:$scope.month,hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode}
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

