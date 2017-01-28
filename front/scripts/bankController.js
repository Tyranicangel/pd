app.controller("BankController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
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
				if(result[0]=='4')
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

app.controller("BankMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/bank_data'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='success')
		{
			$scope.transno=result[1];
			$scope.tranno=result[2];
		}
		else
		{
			Logging.logout();
		}
	});
});

app.controller("BankTransController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/bank_tran'
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
			angular.forEach($scope.alltrans,function(trans){
				trans.acinfo.balance=Commas.getcomma(trans.acinfo.balance);
				trans.sno=counter;
				counter++;
				if(trans.acinfo.account_type=='1')
				{
					trans.acinfo.loc='Not Applicable';
				}
				else
				{
					trans.acinfo.loc=Commas.getcomma(trans.acinfo.loc);
				}
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
				chqlist.push({id:trans.id,bookno:trans.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_accept',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Cheques Confirmed");
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
				chqlist.push({chqno:trans.chequeno,bookno:trans.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_reject',
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

app.controller("BankRecptController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/bank_trans'
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
			angular.forEach($scope.alltrans,function(trans){
				trans.acinfo.balance=Commas.getcomma(trans.acinfo.balance);
				trans.sno=counter;
				counter++;
				if(trans.acinfo.account_type=='1')
				{
					trans.acinfo.loc='Not Applicable';
				}
				else
				{
					trans.acinfo.loc=Commas.getcomma(trans.acinfo.loc);
				}
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
				chqlist.push({chqno:trans.chequeno,bookno:trans.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Reciept Confirmed!");
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
				chqlist.push({chqno:trans.chequeno,bookno:trans.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_reject',
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

app.controller("BankConfirmController",function($scope,$http,$state,$rootScope,$stateParams,Dates,Logging,Commas){
	$scope.confchq=true;
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.bookno=$stateParams.bookno;
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/bank_tran_data',
		params:{chqno:$scope.transno}
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.dat=result;
			$scope.dat.acinfo.balance=Commas.getcomma($scope.dat.acinfo.balance);
			if($scope.dat.acinfo.account_type=='1')
			{
				$scope.dat.acinfo.loc="Not Applicable";
			}
			else
			{
				$scope.dat.acinfo.loc=Commas.getcomma($scope.dat.acinfo.loc);
			}
			$scope.dat.partyamount=Commas.getcomma($scope.dat.partyamount);
		}
	});

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

	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}
	
	$scope.chq_confirm=function(){
		var chqlist=[];
		chqlist.push({chqno:$scope.transno,bookno:$scope.bookno});
		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/bank_chqlist_accept',
			data:{list:chqlist,rems:$scope.remarks}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='success')
			{
				alert("Cheque Confirmed");
				$state.go('bank.trans');
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.chq_reject=function(){
		var chqlist=[];
		chqlist.push({chqno:$scope.transno,bookno:$scope.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheques Rejected");
					$state.go('bank.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});

app.controller("BankAcceptController",function($scope,$http,$state,$rootScope,$stateParams,Dates,Logging,Commas){
	$scope.confchq=true;
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.bookno=$stateParams.bookno;
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/bank_chq_data',
		params:{chqno:$scope.transno}
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.dat=result;
			//new lines
			$scope.ttlbal = parseInt($scope.dat.acinfo.balance) + parseInt($scope.dat.acinfo.transitamount);
			$scope.ttlbal=Commas.getcomma($scope.ttlbal);
			//new lines
			if($scope.dat.acinfo.account_type=='1')
			{
				$scope.ttlloc="Not Applicable"; //new lines
			}
			else
			{
				//new lines
				$scope.ttlloc = parseInt($scope.dat.acinfo.loc) + parseInt($scope.dat.acinfo.transitamount);
				$scope.ttlloc=Commas.getcomma($scope.ttlloc);
				//new lines
			}
			$scope.dat.commaspartyamount=Commas.getcomma($scope.dat.partyamount);

			if($scope.dat.multiflag==2) {

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/get_bank_list',
					data:{fileloc:'uploads/'+$scope.dat.partyfile,type:'multi', userdesc:$scope.dat.requser.userdesc,chqno:$scope.dat.chequeno}
				}).
				success(function(result){

					if(result[0]['sbilink'] != 0) {
						$scope.sbidownloadlink = result[0]['sbilink'];
					}
					if(result[0]['neftlink'] != 0) {
						$scope.neftdownloadlink = result[0]['neftlink'];
					}
					if(result[0]['rtgslink'] != 0) {
						$scope.rtgsdownloadlink = result[0]['rtgslink'];
					}
					if(result[0]['intralink'] != 0) {
						$scope.intradownloadlink = result[0]['intralink'];
					}
				});
			} else {

				var typ = "";

				if($scope.dat.partyifsc.indexOf('SBIN') !== -1){

					typ = 'sbi';
				} else {

					typ = 'nonsbi';
				}

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/get_bank_list',
					data:{res:$scope.dat,type:typ,chqno:$scope.dat.chequeno}
				}).
				success(function(result){
					if(result[0]['sbilink'] != 0) {
						$scope.sbidownloadlink = result[0]['sbilink'];
					}
					if(result[0]['neftlink'] != 0) {
						$scope.neftdownloadlink = result[0]['neftlink'];
					}
					if(result[0]['rtgslink'] != 0) {
						$scope.rtgsdownloadlink = result[0]['rtgslink'];
					}
					if(result[0]['intralink'] != 0) {
						$scope.intradownloadlink = result[0]['intralink'];
					}
				});
			}
		}
	});

	// $scope.generatebankfile = function(url, type) {

	// 	$http({
	// 		method:'POST',
	// 		headers:{'X-CSRFToken':localStorage.token},
	// 		url:$scope.requesturl+'/get_bank_list',
	// 		data:{fileloc:url,type:type}
	// 	}).
	// 	success(function(result){
	// 	});
	// }

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

	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}
	
	$scope.chq_confirm=function(){
		var chqlist=[];
		chqlist.push({id:$scope.transno,bookno:$scope.bookno});
		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/bank_chqlist_confirm',
			data:{list:chqlist,rems:$scope.remarks,ddocode:$scope.dat.issueuser}
		}).
		success(function(result){
			$scope.showloader=false;

			// $http({
			// 	method:'GET',
			// 	headers:{'X-CSRFToken':localStorage.token},
			// 	url:'../impact_bank_scroll.php',
			// 	data:{list:chqlist}
			// }).
			// success(function(result){


			// });
			if(result[0]=='success')
			{
				alert("Cheque Payment Confirmed!");
				$state.go('bank.recipts');
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.chq_reject=function(){
		var chqlist=[];
		chqlist.push({id:$scope.transno,bookno:$scope.bookno});
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
				url:$scope.requesturl+'/bank_chqlist_reject',
				data:{list:chqlist,rems:$scope.remarks,ddocode:$scope.dat.issueuser}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheques Rejected");
					$state.go('bank.recipts');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});


app.controller("ConfirmedChqController",function($scope,$http,$state,$rootScope,Logging,Commas, Dates){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	
	
	$scope.get_conf_chqs = function()
	{
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		var tdate = $('#tdate').val();
		var tdatesplit = tdate.split("-");
		var tdatec = tdatesplit[2]+"-"+tdatesplit[1]+"-"+tdatesplit[0];

		if (fdate=='') 
		{
			alert('Please select From date');
		}
		else if(tdate=='')
		{
			alert('Please select To date');
		}
		else if(fdatec>tdatec)
		{
			alert('From date cant be greater than to date!');
		}
		else  
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/bank_confirmed_cheques',
				params:{fdate:fdate,tdate:tdate}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					//$scope.transno=result[1];
					Logging.logout();
				}
				else if(result.length=='')
				{
					alert('No Cheques Confirmed between these dates!');
				}
				else
				{
					$scope.confchqlist = result;
					angular.forEach($scope.confchqlist,function(tr){
						tr.stat='Payment Done';
						tr.amtwords = getwords(tr.partyamount);
						tr.transdate = Dates.getDate(tr.transdate);
						tr.confirmdate = Dates.getDate(tr.confirmdate);
					});
				}
			});
		}
	}


	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}


});

app.controller("ManualChqController",function($scope,$http,$state,$rootScope,Logging,Commas, Dates){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.lapmonth = "select";
	$scope.lapyear = "select";
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.party_type='single';
	$scope.confchq=false;
	$scope.hoa='select';
	$scope.party={};

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
					bal=scheme.balance;
					$scope.bal=bal;
					$scope.actype=scheme.account_type;
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
			else if($scope.party.cheque.length > 6)
			{

				alert("Please enter correct cheque number.");
			}
			else if(!$scope.party.cheque)
			{
				alert('Please enter Cheque number.');
			}
			else if(!$scope.party.purpose)
			{
				alert('Please enter the purpose for issuing cheque');
			}
			else if(!$scope.party.amount)
			{
				alert('Please enter the Party Amount');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8011001050001000000NVN')
			{
				alert('Insufficient funds');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN')
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
							url:$scope.requesturl+'/bank_issue_single_party_lapsable',
							data:{partydets:$scope.party,bookno:$scope.bookno}
						}).success(function(result){
							if(result[0]=='success')
							{
								if(result[1]=='success')
								{
									if(result[2]=='success')
									{
											if(result[3] == "used") {

												alert("Cheque no entered is already used.");
												$scope.showloader = false;
											} else if(result[3] == "no cheque") {

												alert("Cheque no entered doesnot exist for the entered issue user.");
												$scope.showloader = false;
											} else {
												alert("Cheque Issued!");
												$state.go('bank.accept',{'transaction':result[3]});
											}
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
						url:$scope.requesturl+'/bank_issue_single_party',
						data:{partydets:$scope.party,bookno:$scope.bookno}
					}).success(function(result){
						if(result[0]=='success')
						{
							if(result[1]=='success')
							{
								if(result[2]=='success')
								{
									if(result[3] == "used") {

										alert("Cheque no entered is already used.");
										$scope.showloader = false;
									} else if(result[3] == "no cheque") {

										alert("Cheque no entered doesnot exist for the entered issue user.");
										$scope.showloader = false;
									} else {
										alert("Cheque Issued!");
										$state.go('bank.accept',{'transaction':result[3]});
									}
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
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc))
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
			else if(parseInt($scope.party.amount)!=$scope.tamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal))
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
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc))
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
			else if(parseInt($scope.party.amount)!=$scope.pdtamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal))
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
			else if(parseInt($scope.party.amount)>parseInt($scope.bal) && $scope.hoa != '8011001050001000000NVN')
			{
				alert('Insufficient funds');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc) && $scope.hoa != '8443001040001000000NVN' && $scope.hoa != '8011001050001000000NVN')
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
			if($scope.hoa=='select')
			{
				alert('Please enter Head of Account');
			}
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc))
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
			else if(parseInt($scope.party.amount)!=$scope.tamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal))
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
			else if($scope.actype==2&&parseInt($scope.party.amount)>parseInt($scope.loc))
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
			else if(parseInt($scope.party.amount)!=$scope.pdtamount)
			{
				alert('The total amount does not tally');
			}
			else if(parseInt($scope.party.amount)>parseInt($scope.bal))
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

	$scope.get_hoa=function(){
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_user_hoa',
			params:{userid:$scope.party.issueuser}
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

	

});

app.controller("ChqStatusController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.responseuploaded = false;
	
	$scope.get_conf_chqs = function()
	{
		var chqno = $('#chqno').val();

		if (chqno=='') 
		{
			alert('Please enter cheque number.');
		}
		else  
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/bank_cheques_status',
				params:{chqno:chqno}
			}).
			success(function(result){
				
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					//$scope.transno=result[1];
					Logging.logout();
				}
				else if(result[0] == 0) {

					alert("Invalid cheque number.");
				}
				else
				{

					$scope.showbankfile = false;
					$scope.confchqlist = result[1];
					angular.forEach($scope.confchqlist,function(tr){

						if(tr.transstatus=='0')
						{
							tr.stat="Cheque pending with Senior Accountant";
						}
						else if(tr.transstatus=='1')
						{
							tr.stat="Cheque pending with government";
						}
						else if(tr.transstatus=='2')
						{
							tr.stat="Cheque forwarded to bank waiting for confirmation!";
						}
						else if(tr.transstatus=='4')
						{
							tr.stat="Cheque reciept confirmed by bank! waiting for payment ";
						}
						else if(tr.transstatus=='5')
						{
							tr.stat="Cheque pending with DTO/STO";
						}
						else if(tr.transstatus=='21')
						{
							tr.stat="Cheque Rejected";
						}
						else if(tr.transstatus=='3')
						{
							tr.stat="Payment Done";
						}
						else if(tr.transstatus=='61')
						{
							tr.rems="Cheque with PD admin checker!";
						}
						else if(tr.transstatus=='62')
						{
							tr.stat="Cheque with SA!";
						}
						else if(tr.transstatus=='63')
						{
							tr.stat="Cheque with STO!";
						}
						else if(tr.transstatus=='64')
						{
							tr.stat="Cheque with ATO!";
						}
						else if(tr.transstatus=='65')
						{
							tr.stat="Cheque with DD/STO/DTO!";
						}
						tr.amtwords = getwords(tr.partyamount);
					});

					var temp = 0;

					if(result[0]['sbilink'] != 0) {

						$scope.sbidownloadlink = result[0]['sbilink'];

						temp++;
					}
					if(result[0]['neftlink'] != 0) {

						$scope.neftdownloadlink = result[0]['neftlink'];
						temp++;
					}
					if(result[0]['rtgslink'] != 0) {

						$scope.rtgsdownloadlink = result[0]['rtgslink'];
						temp++;
					}
					if(result[0]['intralink'] != 0) {

						$scope.intradownloadlink = result[0]['intralink'];
						temp++;
					}

					if(temp > 0) {

						$scope.showbankfile = true;
					}
				}
			});
		}
	}

	$scope.uploadresponseneft = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$scope.allplist={};
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploadingresponse',
			data:formdata,
			headers:{'Content-Type': undefined,
			'type':'neft',
			'transid':$scope.confchqlist[0]['id']},
			transformRequest: function(data) {return data;}
		}).
		success(function(data){
			console.log(data);
			$scope.showloader=false;
			if(data[0]=='success')
			{
				$scope.responseuploaded = true;
				alert("Response file uploaded successfully.")
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.uploadresponsesbi = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$scope.allplist={};
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploadingresponse',
			data:formdata,
			headers:{'Content-Type': undefined,
			'type':'sbi',
			'transid':$scope.confchqlist[0]['id']},
			transformRequest: function(data) {return data;}
		}).
		success(function(data){
			console.log(data);
			$scope.showloader=false;
			if(data[0]=='success')
			{
				$scope.responseuploaded = true;
				alert("Response file uploaded successfully.")
			}
			else
			{
				//Logging.logout();
			}
		});
	}


	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}


});

