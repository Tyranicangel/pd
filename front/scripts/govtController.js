app.controller("GovtController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='3')
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

app.controller("GovtMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_data'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='success')
		{
			$scope.transno=result[1];
		}
		else
		{
			Logging.logout();
		}
	});
});


app.controller("GovtConfController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	
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
				url:$scope.requesturl+'/govt_confirmed_cheques',
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
						if(tr.transstatus=='3')
						{
							tr.stat='Payment Done';
						}
						else if(tr.transstatus=='21')
						{
							tr.stat='Cheque Rejected';
						}
						else if(tr.transstatus=='2')
						{
							tr.stat='Cheque sent to bank waiting for payment!';
						}
						
						tr.amtwords = getwords(tr.partyamount);
					});
				}
			});
		}
	}


	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}


});

app.controller("GovtTransController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_trans'
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
			var totalamnt = 0;
			angular.forEach($scope.alltrans,function(tr){
				totalamnt = totalamnt+parseInt(tr.partyamount);
				tr.remarks='';
				tr.amtwords = getwords(tr.partyamount);
				
			});
			$scope.totalchqamt = totalamnt;
			$scope.totalchqamtwords = getwords(String(totalamnt));

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
				chqlist.push(trans.id);
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
				url:$scope.requesturl+'/govt_chqlist_confirm',
				data:{list:chqlist}
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
				chqlist.push(trans);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to reject");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/govt_chqlist_reject',
				data:{list:chqlist}
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

 function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}





});

app.controller("GovtConfirmController",function($scope,$http,$state,$rootScope,$stateParams,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.showloader=true;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_chq_data',
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
		}
	});
	$scope.chq_confirm=function(){
		var chqlist=[$scope.transno];
		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/govt_chqlist_confirm',
			data:{list:chqlist,rems:$scope.remarks}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='success')
			{
				alert("Cheques Confirmed");
				$state.go('govt.trans');
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.chq_reject=function(){
		var chqlist = [];
		chqlist[0]={"id":$scope.transno, "remarks":$scope.remarks};
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
				url:$scope.requesturl+'/govt_chqlist_reject',
				data:{list:chqlist}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheques Rejected");
					$state.go('govt.trans');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});



app.controller("GovtIfController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='7' || result[0]=='50')
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

app.controller("GovtIfMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_if_acnt_data',
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{

			$scope.fileloc = result[0]['fileloc'];
			$scope.allaclist=result;
			$scope.allaclist.totbal = 0;
			$scope.allaclist.totloc = 0;
			$scope.allaclist.totbalwords = '';
			$scope.allaclist.totlocwords = '';
			$scope.allaclist.noofaccounts = result.length;

			angular.forEach($scope.allaclist,function(tr){
				if(tr.account_type=='2')
				{
					tr.actype='LOC';
					$scope.allaclist.totloc += parseInt(tr.loc);

					tr.locwords = getwords(tr.loc);
					tr.loc = Commas.getcomma(tr.loc);
				}
				else if(tr.account_type=='1')
				{
					tr.actype='NON LOC';
					tr.loc = 'Not Applicable';
					
				}
				$scope.allaclist.totbal += parseInt(tr.balance);
				

				tr.balwords = getwords(tr.balance);
				tr.balance = Commas.getcomma(tr.balance);
				
			});

			$scope.allaclist.totbalwords = getwords($scope.allaclist.totbal+'');
			$scope.allaclist.totbal = Commas.getcomma($scope.allaclist.totbal);

			$scope.allaclist.totlocwords = getwords($scope.allaclist.totloc+'');
			$scope.allaclist.totloc = Commas.getcomma($scope.allaclist.totloc);
		}
	});
	
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_data_if'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='success')
		{
			$scope.transno=result[1];
		}
		else
		{
			Logging.logout();
		}
	});

	function getwords(e){
		if(e=='0')
			return 'ZERO';
		else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}

});


app.controller("GovtIfChequesController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_trans_if'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{

			$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:'/pd/create_govt_csv.php'
			}).
			success(function(result){

				$(".downloadpdflink").attr("href", "/pd/"+result);
			});

			$scope.alltrans=result;
			var totalamnt = 0;
			
			angular.forEach($scope.alltrans,function(tr){
				totalamnt = totalamnt+parseInt(tr.partyamount);
				tr.remarks='';
				tr.amtwords = getwords(tr.partyamount);
				tr.partyamount = Commas.getcomma(tr.partyamount);
				
			});			
			$scope.totalchqamt = totalamnt;
			$scope.totalchqamtwords = getwords(String(totalamnt));
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
				chqlist.push(trans.id);
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
				url:$scope.requesturl+'/govt_chqlist_confirm_if',
				data:{list:chqlist}
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
				chqlist.push(trans);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to reject");
		}
		else
		{

			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/govt_chqlist_reject_if',
				data:{list:chqlist}
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

 function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}





});



app.controller("GovtIfConfirmController",function($scope,$http,$state,$rootScope,$stateParams,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.transno=$stateParams.transaction;
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/govt_chq_data_if',
		params:{chqno:$scope.transno}
	}).success(function(result){

		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			result[0]['partyamount'] = Commas.getcomma(result[0]['partyamount']);
			$scope.dat=result[0];
		}
	});
	$scope.chq_confirm=function(){
		var chqlist=[$scope.transno];
		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/govt_chqlist_confirm_if',
			data:{list:chqlist,rems:$scope.remarks}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='success')
			{
				alert("Cheques Confirmed");
				$state.go('govtif.cheques');
			}
			else
			{
				Logging.logout();
			}
		});
	}

	$scope.chq_reject=function(){
		var chqlist = [];
		chqlist[0]={"id":$scope.transno, "remarks":$scope.remarks};
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
				url:$scope.requesturl+'/govt_chqlist_reject_if',
				data:{list:chqlist}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Cheques Rejected");
					$state.go('govtif.cheques');
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});



app.controller("GovtIFTransactionsController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	//
	

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
				url:$scope.requesturl+'/govt_confirmed_cheques_for_govtif',
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
					alert('No Cheques Paid between these dates!');
				}
				else
				{
					$scope.totaltranslist = result;
					angular.forEach($scope.totaltranslist,function(tr){
						if(tr.transstatus=='3')
						{
							tr.stat='Payment Done';
						}
						else if(tr.transstatus=='21')
						{
							tr.stat='Cheque Rejected';
						}
						else if(tr.transstatus=='2')
						{
							tr.stat='Cheque sent to bank waiting for payment!';
						}
						else if(tr.transstatus=='1')
						{
							tr.stat='Cheque with govt, waiting for approval!';
						}
						
						tr.amtwords = getwords(tr.partyamount);
						tr.partyamount = Commas.getcomma(tr.partyamount);

						if(tr.confirmdate=='')
						{
							tr.confirmdate = '--';
						}
					});
				}
			});
		}
	}


	function getwords(e){
		if(e=='0')
			return 'ZERO';
		else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}




});


