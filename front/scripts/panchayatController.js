app.controller("PanchayatHQController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.role=26;
	$scope.showloader=false;
	if(localStorage.token)
	{
		if(!$scope.token)
		{
			$scope.username="Panchayat Raj HQ";
			$scope.showloader=false;
		}
	}
	else
	{
		Logging.logout();
	}
});
app.controller("PanchayatHQMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
});
app.controller("PanchayatHQStatementController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	// $scope.Dates=Dates;
	$scope.showloader=true;
	$scope.areamain='select';
	$scope.stomain='select';
	$scope.ddomain='select';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_areas',
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.arealist=result;
		}
	});

	$scope.area_change=function(){
		$scope.showloader=true;
		$scope.stolist=[];
		$scope.stomain='select';
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_stolist',
			params:{area:$scope.areamain}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.stolist=result;
			}
		});
	}

	$scope.sto_change=function(){
		$scope.showloader=true;
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ddolist_panchayathq',
			params:{sto:$scope.stomain}
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
	}

	$scope.ddomainchange = function(ddomain) {

		$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_booklistadmin',
		params:{username:ddomain}
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
	}

});
app.controller("PanchayatHQLOCRptController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.areamain='select';
	$scope.stomain='select';
	$scope.ddomain='select';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_areas',
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.arealist=result;
		}
	});

	$scope.area_change=function(){
		$scope.showloader=true;
		$scope.stolist=[];
		$scope.stomain='select';
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_stolist',
			params:{area:$scope.areamain}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.stolist=result;
			}
		});
	}

	$scope.sto_change=function(){
		$scope.showloader=true;
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ddolist',
			params:{sto:$scope.stomain}
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
	}

	$scope.get_loc_rpt=function(pageno){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		var tdate = $('#tdate').val();
		var tdatesplit = tdate.split("-");
		var tdatec = tdatesplit[2]+"-"+tdatesplit[1]+"-"+tdatesplit[0];

		if(fdate == "") {

			alert("Please select 'From' date.");
		} else if(tdate == "") {

			alert("Please select 'To' date.");
		} else if(fdatec > tdatec) {

			alert("'From' date cannot be greater than 'To' date.");
		} else if($scope.ddomain == 'select') {

			alert("'Please select a PD Admin.");
		} else if($scope.stomain == 'select') {

			alert("'Please select a STO/DTO.");
		} else {
			$scope.showloader=true;
			$scope.chqlist=[];
			var reslim=30;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ddolocadmin',
				params:{ddo:$scope.ddomain,sto:$scope.stomain, fdate:$("#fdate").val(), tdate:$("#tdate").val(), currpage:pageno, reslim:reslim}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{

					$(".indipageno").removeClass("activepage");

					$( "div[thispageno*='"+pageno+"']" ).addClass("activepage");

					var firstpageno = ((pageno-1) * reslim)+1;

					$scope.loclist=result;
					var grantedamt = 0;
					var requestedamt = 0;
					var pendingamt = 0;
					var rejectedamt = 0;
					angular.forEach($scope.loclist,function(l){

					l.pagenumber = firstpageno;
					if(l.grantamount) {
						grantedamt = grantedamt+parseInt(l.grantamount);
					} 
					if(l.reqamount) {
						requestedamt = requestedamt+parseInt(l.reqamount);
					}

					
					if(!(l.remarks))
					{
						l.remarks='None';
					}
					if(l.conf_flag=='0')
					{
						l.rems='Pending';
						if(l.reqamount) {
							pendingamt = pendingamt+parseInt(l.reqamount);
						}
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
							if(l.reqamount) {
								pendingamt = pendingamt+parseInt(l.reqamount);
							}
						}
						else
						{
							l.rems='Granted';
							l.grantamount=Commas.getcomma(l.grantamount);
						}
					}
					else if(l.conf_flag=='3' || l.conf_flag=='4' || l.conf_flag=='5' || l.conf_flag=='6' || l.conf_flag=='33')
					{
						if(l.requestflag=='0')
						{
							l.rems='Pending';
							l.grantamount='-';
							l.refno='-';
							if(l.reqamount) {
								pendingamt = pendingamt+parseInt(l.reqamount);
							}
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
						if(l.reqamount) {
							rejectedamt = rejectedamt+parseInt(l.reqamount);
						}
					}
					l.reqamount=Commas.getcomma(l.reqamount);
					firstpageno = firstpageno+1;
				});
				 $(".totamounttext").show();
				 $(".totamounttext").css("display","inline-block");
				 

				 	if(pageno == "1") {

				 	 if(requestedamt != 0) {
				 	 	requestedamt = Commas.getcomma(requestedamt);
				 	 }
				 	 if(grantedamt != 0) {
					 	grantedamt = Commas.getcomma(grantedamt);
					 }
					 if(pendingamt != 0) {
					 	pendingamt = Commas.getcomma(pendingamt);
					 }
					 if(rejectedamt != 0) {
					 	rejectedamt = Commas.getcomma(rejectedamt);
					 }
					 $(".totreqamount").text(requestedamt);
					 $(".totgrantamount").text(grantedamt);
					 $(".totpendingamount").text(pendingamt);
					 $(".totrejectedamount").text(rejectedamt);

						$scope.loclistall = result.length;
						$scope.pagination = [];
						$scope.totalpage = Math.ceil($scope.loclistall/reslim);
						for(var i=1;i<=$scope.totalpage;i++) {

							$scope.pagination[i] = i; 
						}
					}
				}
			});
		}
	}
});
app.controller("PanchayatHQChqRptController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.areamain='select';
	$scope.stomain='select';
	$scope.ddomain='select';
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_areas',
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.arealist=result;
		}
	});

	$scope.area_change=function(){
		$scope.showloader=true;
		$scope.stolist=[];
		$scope.stomain='select';
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_stolist',
			params:{area:$scope.areamain}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.stolist=result;
			}
		});
	}

	$scope.ddomainchange = function() {

		$(".each_desc").hide();
	}

	$scope.sto_change=function(){
		$scope.showloader=true;
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ddolist',
			params:{sto:$scope.stomain}
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
	}

	$scope.get_chq_rpt=function(pageno){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		var tdate = $('#tdate').val();
		var tdatesplit = tdate.split("-");
		var tdatec = tdatesplit[2]+"-"+tdatesplit[1]+"-"+tdatesplit[0];

		if($("#fdate").val() == "") {

			alert("Please select 'From' date.");
		} else if($("#tdate").val() == "") {

			alert("Please select 'To' date.");
		} else if($("#fdate").val() > $("#tdate").val()) {

			alert("'From' date cannot be greater than 'To' date.");
		} else if($scope.ddomain == 'select') {

			alert("'Please select a PD Admin.");
		} else if($scope.stomain == 'select') {

			alert("'Please select a STO/DTO.");
		} else {

			$scope.showloader=true;
			$scope.chqlist=[];
			var reslim=30;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ddotransadmin',
				params:{ddo:$scope.ddomain,sto:$scope.stomain, fdate:fdatec, tdate:tdatec, currpage:pageno, reslim:reslim}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{

					$(".indipageno").removeClass("activepage");

					$( "div[thispageno='"+pageno+"']" ).addClass("activepage");

					var firstpageno = ((pageno-1) * reslim)+1;

					$scope.chqlist=result;
					var totalchqamount = 0;
					var totalrejamount = 0;
					var totaldoneamount = 0;
					angular.forEach($scope.chqlist,function(tr){
						tr.pagenumber = firstpageno;

						totalchqamount = totalchqamount+parseInt(tr.partyamount);
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
							tr.rems="Cheque forwarded to bank waiting for confirmation!";
						}
						else if(tr.transstatus=='4')
						{
							tr.rems="Cheque reciept confirmed by bank! waiting for payment ";
						}
						else if(tr.transstatus=='5')
						{
							tr.rems="Cheque pending with DTO/STO";
						}
						else if(tr.transstatus=='21')
						{
							tr.rems="Cheque Rejected";
							totalrejamount = totalrejamount+parseInt(tr.partyamount);
						}
						else if(tr.transstatus=='3')
						{
							tr.rems="Payment Done";
							totaldoneamount = totaldoneamount+parseInt(tr.partyamount);
						}
						else if(tr.transstatus=='62')
						{
							tr.rems="Cheque with SA!";
						}
						else if(tr.transstatus=='63')
						{
							tr.rems="Cheque with STO!";
						}
						else if(tr.transstatus=='64')
						{
							tr.rems="Cheque with ATO!";
						}
						else if(tr.transstatus=='65')
						{
							tr.rems="Cheque with DD/STO/DTO!";
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
						tr.partyamount = Commas.getcomma(tr.partyamount);
						firstpageno = firstpageno+1;

				});
					$(".totamounttext").show();
					$(".totamounttext").css("display","inline-block");
					

					if(pageno == "1") {
						var totalpending = totalchqamount - (totaldoneamount+totalrejamount);
						totalchqamount = Commas.getcomma(totalchqamount);
						totalrejamount = Commas.getcomma(totalrejamount);
						totaldoneamount = Commas.getcomma(totaldoneamount);
						totalpending = Commas.getcomma(totalpending);
						$(".totamount").text(totalchqamount);
						$(".totdoneamount").text(totaldoneamount);
						$(".totrejamount").text(totalrejamount);
						$(".totpenamount").text(totalpending);

						$scope.chqlistall = result.length;
						$scope.pagination = [];
						$scope.totalpage = Math.ceil($scope.chqlistall/reslim);
						for(var i=1;i<=$scope.totalpage;i++) {

							$scope.pagination[i] = i; 
						}
					}
				}
			});

				
		}
	}
});
app.controller("PanchayatHQAllAccountController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.stocode = "2702";

	$scope.onloadactn = function() {

		$scope.showloader=true;
	
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/govt_if_acnt_dataadmin',
			params:{stocode:$scope.stocode}
		}).success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
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
	}

	$scope.onloadactn();

	function getwords(e){
		if(e=='0')
			return 'ZERO';
		else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}
});
app.controller("PanchayatHQAbsStatementController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_areas',
	}).
	success(function(result){
		// $scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			// $scope.arealist=result;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_panchayat_hoas',
				data:{arealist:result}
			}).
			success(function(result1){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					console.log(result1)
					$scope.accreports=result1[0];
					$scope.arealist = result1[1];
				}
			});
		}
	});

});
app.controller("PanchayatHQAbsLOCController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.get_abs_loc_rpt = function(){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		var tdate = $('#tdate').val();
		var tdatesplit = tdate.split("-");
		var tdatec = tdatesplit[2]+"-"+tdatesplit[1]+"-"+tdatesplit[0];

		if(fdate == "") {
			alert("Please select 'From' date.");
		} else if(tdate == "") {
			alert("Please select 'To' date.");
		} else if(fdatec > tdatec) {
			alert("'From' date cannot be greater than 'To' date.");
		}else{
			$scope.showloader=true;
			$scope.chqlist=[];
			var reslim=30;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_panchayat_abs_loc_rpt',
				data:{fdate:$("#fdate").val(), tdate:$("#tdate").val()}
			}).
			success(function(result){
				console.log(result);

			});
		}
	};
});
app.controller("PanchayatHQAbsChqController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.get_abs_chq_rpt = function(){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		var tdate = $('#tdate').val();
		var tdatesplit = tdate.split("-");
		var tdatec = tdatesplit[2]+"-"+tdatesplit[1]+"-"+tdatesplit[0];

		if(fdate == "") {
			alert("Please select 'From' date.");
		} else if(tdate == "") {
			alert("Please select 'To' date.");
		} else if(fdatec > tdatec) {
			alert("'From' date cannot be greater than 'To' date.");
		}else{
			$scope.showloader=true;
			$scope.chqlist=[];
			var reslim=30;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_panchayat_abs_chq_rpt',
				data:{fdate:$("#fdate").val(), tdate:$("#tdate").val()}
			}).
			success(function(result){
				console.log(result);

			});
		}
	};
});
