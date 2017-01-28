	app.controller("AdminBackChqRptController",function($scope,$http,$state,Logging,Commas){
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


app.controller("AdminBackAccRptController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.showloader=true;

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/getaccrpt'
	}).
	success(function(result){

		$scope.showloader=false;

		console.log(result);

		$scope.alltrans = result;

	});
	
});

app.controller("AdminBackInventory",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.getchqlist=function(trsy){
		$scope.showloader=true;
		$http({
			method:'GET',
			url:$scope.requesturl+'/gettryinventory',
			headers:{'X-CSRFToken':localStorage.token},
			params:{treasury:trsy}
		}).success(function(result){
			$scope.showloader=false;
			$scope.chqlist=result;
		});
	}

	$scope.deletemultiplechq=function(trsy,fid,tid){
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/deleteinv',
			headers:{'X-CSRFToken':localStorage.token},
			data:{treasury:trsy,fromid:fid,toid:tid}
		}).success(function(result){
			$scope.showloader=false;
			alert('deleted');
		});
	}

	$scope.usebooks=function(trsy,fid,tid){
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/useinv',
			headers:{'X-CSRFToken':localStorage.token},
			data:{treasury:trsy,fromid:fid,toid:tid}
		}).success(function(result){
			$scope.showloader=false;
			alert('Made Used');
		});
	}
});

app.controller("AdminBackCurrentAcnoController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.check_pdacno = function() {
		if(!$scope.ddocode) {

			alert("Please enter ddocode.");
		} else if(!$scope.hoa) {

			alert("Please enter hoa.");
		} else {

			$scope.showloader=true;

			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				params:{ddocode:$scope.ddocode, hoa:$scope.hoa},
				url:$scope.requesturl+'/getcurrentacno'
			}).
			success(function(result){

				$scope.showloader=false;

				if(result == "error") {

					alert("PD Account doesnot exist.");
				} else {

					$scope.pdacno = result;
				}

			});
		}
	}

	$scope.update_pdacno = function() {

		$scope.showloader=true;

		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			data:{ddocode:$scope.ddocode, hoa:$scope.hoa, pdacno:$scope.pdacno},
			url:$scope.requesturl+'/updatecurrentacno'
		}).
		success(function(result){

			$scope.showloader=false;

			if(result == "error") {

				alert("PD Account doesnot exist.");
			} else {

				alert("Current account updated.");
			}

		});
	}
	
});

app.controller("AdminBackRejectedAccountController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.stomain = 'select';
	
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_stolist'
	}).
	success(function(result){

		$scope.stolist = result;
	});

	$scope.sto_change=function(pageno){

		if(pageno != "" && !pageno) {

			var pageno = 1;
		}

		$scope.showloader=true;

		var reslim = 50;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/rejectedaccountslist',
			params:{currpage:pageno, reslim:reslim, stocode:$scope.stomain}
		}).
		success(function(result){

			$(".indipageno").removeClass("activepage");

			$( "div[thispageno='"+pageno+"']" ).addClass("activepage");

			var firstpageno = ((pageno-1) * reslim)+1;
			$scope.showloader=false;
			$scope.rejaccts=result;

			angular.forEach($scope.rejaccts,function(tr){

				tr.pagenumber = firstpageno;
				firstpageno = firstpageno+1;
			});

			if(pageno == "1") {

				$scope.chqlistall = result.length;
				$scope.pagination = [];
				$scope.totalpage = Math.ceil($scope.chqlistall/reslim);
				for(var i=1;i<=$scope.totalpage;i++) {

					$scope.pagination[i] = i; 
				}
			}
		});

	}

	$scope.delete_acc=function(acc, event){


		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/deleterejectedaccountslist',
			data:{id:acc.id}
		}).
		success(function(result){
			alert("Account deleted.");
			$(event.target).parent().parent().css('display','none');
		});
	}

	$scope.update_acc=function(acc, event){

		if(!acc.newbal)
		{
			alert('Please enter the Opening balance for this account');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/updaterejectedaccountslist',
				data:{id:acc.id,dat:acc.newbal}
			}).
			success(function(result){
				$scope.showloader=false;
				alert("Account updated.");
				$(event.target).parent().parent().css('display','none');
			});
		}
	}

	$scope.send_acc=function(acc){
		if(!acc.newrems)
		{
			alert('Please enter remarks');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/sendrejectedaccountslist',
				data:{id:acc.id,dat:acc.newrems}
			}).
			success(function(result){
				alert("Sent for modification.");
				$(event.target).parent().parent().css('display','none');
			});
		}
	}
});



app.controller("AdminBackAccStmtController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
	
});


app.controller("AdminBackStatementController",function($scope,$http,$state,$stateParams,Dates,Logging,Commas){
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


app.controller("AdminBackLocRptController",function($scope,$http,$state,Logging,Commas){
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

app.controller("AdminBackController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='50')
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

	$scope.hideallquery = function() {

		$scope.pendingqueries = false;
		$scope.resolvedqueries = false;
		$scope.forwardedqueries = false;
	}

	$scope.pendingquery = function() {

		$scope.hideallquery();

		$scope.status = "Pending";

		$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_queries_pending',
			}).
			success(function(result){

				if(result != 0) {
				
					$scope.pendingqueries = result;
					$scope.showloader=false;
				}
				
			});
	}
	$scope.pendingquery();
	$scope.resolvedquery = function() {

		$scope.hideallquery();
		$scope.status = "Resolved";

		$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_queries_resolved',
			}).
			success(function(result){
				
				if(result != 0) {
				
					$scope.resolvedqueries = result;
					$scope.showloader=false;
				}
				
			});
	}
	$scope.forwardedquery = function() {

		$scope.hideallquery();

		$scope.status = "Forward";

		$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_queries_forwarded',
			}).
			success(function(result){
				
				if(result != 0) {
				
					$scope.forwardedqueries = result;
					$scope.showloader=false;
				}
				
			});
	}
	$scope.updatequery = function(queries, status) {

		queries['status'] = status;

			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				data:queries,
				url:$scope.requesturl+'/update_query',
			}).
			success(function(result){

				
				if(result != 0) {
				
					$scope.showloader=false;
					if(result == 1) {

						queries.updateflag=1;
					}
				}
				
			});
	}
	$scope.resetpass = function(user_id) {

		var userid = user_id;

			if(userid == '' || !userid) {

				alert("Please enter userid.");
			} else {

				$scope.showloader=true;

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{userid:userid},
					url:$scope.requesturl+'/reset_pass',
				}).
				success(function(result){

					$scope.showloader=false;
					console.log(result);
					if(result != 0) {
						if(result == 1) {

							alert("Password is reset");
							window.location.reload();
						}
					}
					
				});
			}
	}
	$scope.addsinglechq = function(user_id, chqno) {

		if(chqno && user_id) {

			if(chqno.length == 6) {

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{user_id:user_id, chqno:chqno},
					url:$scope.requesturl+'/add_singlechq',
				}).
				success(function(result){

					
					if(result != 0) {
					
						$scope.showloader=false;
						if(result == 1) {

							alert("Cheque added");
							
						} else if(result == 2) {

							alert("Cheque already exists");
							
						}

					}  else {

							alert("Unauthorized access");
					}
					
				});
			} else {

				alert("Please enter the 6 digit cheque number");
			}
		} else if (!user_id){

			alert("Please enter the user id");
		} else if (!chqno){

			alert("Please enter the 6 digit cheque number");
		} 
	}
	$scope.deletesinglechq = function(user_id, chqno) {

		if(chqno && user_id) {

			if(chqno.length == 6) {

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{user_id:user_id, chqno:chqno},
					url:$scope.requesturl+'/delete_singlechq',
				}).
				success(function(result){

					
					if(result != 0) {
					
						$scope.showloader=false;
						if(result == 1) {

							alert("Cheque deleted");
							
						} else if(result == 2) {

							alert("Cannot delete. Cheque already used");
							
						}
					}
					
				});
			} else {

				alert("Please enter the 6 digit cheque number");
			}
		} else if (!user_id){

			alert("Please enter the user id");
		} else if (!chqno ){

			alert("Please enter the 6 digit cheque number");
		} 
	}
	$scope.getchqlist = function(user_id) {

		if(user_id) {

			$scope.showloader=true;

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{user_id:user_id},
					url:$scope.requesturl+'/get_chqlist',
				}).
				success(function(result){

					$scope.showloader=false;

					if(result == 0) {

						alert("No cheques for entered user.");
					} else if(result == 2) {

						alert("Unauthorzed user");
					} else {

						$scope.chqlist = result;
					}
					
				});
		} else {

			alert("Please enter the user id");
		} 
	}
	$scope.addmultiplechq = function(user_id, chqnofrm, chqnoto) {

		if(chqnofrm && chqnoto && user_id) {

			if(chqnofrm.length == 6 && chqnoto.length == 6) {

				$scope.showloader=true;

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{user_id:user_id, chqnofrm:chqnofrm, chqnoto:chqnoto},
					url:$scope.requesturl+'/add_multiplechq',
				}).
				success(function(result){

					
					if(result != 0) {
					
						$scope.showloader=false;
						if(result == 1) {

							alert("Cheques added");
							
						} else if(result == 2) {

							alert("'From' cheque number must be less than the 'To' cheque number");
							
						}

					}  else {

							alert("Unauthorized access");
					}
					
				});
			} else {

				alert("Please enter the 6 digit cheque numbers");
			}
		} else if (!user_id){

			alert("Please enter the user id");
		} else if (!chqnofrm){

			alert("Please enter from cheque number");
		} else if (!chqnoto){

			alert("Please enter to cheque number");
		} 
	}
	$scope.deletemultiplechq = function(user_id, chqnofrm, chqnoto) {

		if(chqnofrm && chqnoto && user_id) {

			if(chqnofrm.length == 6 && chqnoto.length == 6) {

				$scope.showloader=true;

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					data:{user_id:user_id, chqnofrm:chqnofrm, chqnoto:chqnoto},
					url:$scope.requesturl+'/delete_multiplechq',
				}).
				success(function(result){

					
					if(result != 0) {
					
						$scope.showloader=false;
						if(result == 1) {

							alert("Cheques deleted");
						} else if(result == 2) {

							alert("'From' cheque number must be less than the 'To' cheque number");
						} else if(result == 3) {

							alert("User with this cheque number doesnot exist");
						}

					}  else {

							alert("Unauthorized access");
					}
					
				});
			} else {

				alert("Please enter the 6 digit cheque numbers");
			}
		} else if (!user_id){

			alert("Please enter the user id");
		} else if (!chqnofrm){

			alert("Please enter from cheque number");
		} else if (!chqnoto){

			alert("Please enter to cheque number");
		} 
	}

	$scope.executequery = function(query) {

		if(query) {

			if(query.toLowerCase.indexOf("update") != "-1" && query.toLowerCase.indexOf("insert") != "-1" && query.toLowerCase.indexOf("delete") != "-1" && query.toLowerCase.indexOf("drop") != "-1" && query.toLowerCase.indexOf("truncate") != "-1") {

					$http({
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						data:query,
						url:$scope.requesturl+'/get_queryresult',
					}).
					success(function(result){

						
						
					});
				} else {

					alert("Only SELECT query");
				}
		} else {

			alert("Please enter query");
		} 
	}
});
app.controller("AdminBackAllAccountController",function($scope,$http,$state,$rootScope,Logging,Commas){
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


app.controller("AdminBackAllTransController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.stocode='select';
	$scope.ddomain='select';
	

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
		} else if($scope.stocode =="select") {

			alert("Please select a STOCODE.");
		}
		else if($scope.ddomain =="select") {

			alert("Please select a PD Admin.");
		}
		else  
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/govt_confirmed_cheques_for_govtifadmin',
				params:{fdate:fdate,tdate:tdate,ddo:$scope.ddomain}
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

	$scope.sto_change=function(){
		$scope.showloader=true;
		$scope.ddolist=[];
		$scope.ddomain='select';
		$scope.chqlist=[];
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ddolist',
			params:{sto:$scope.stocode}
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


	function getwords(e){
		if(e=='0')
			return 'ZERO';
		else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}




});

app.controller("AdminBackAllFaultController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	//
	

	$scope.showloader=true;
	
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/govt_faulty_acnt_dataadmin',
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


	function getwords(e){
		if(e=='0')
			return 'ZERO';
		else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}




});

app.controller("AdminBackMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$scope.stocode="2702";
	$scope.onloadactn = function() {

		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/govt_if_acnt_dataadmin',
			params:{stocode:$scope.stocode},
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

app.controller("AdminBackTotRptController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.stocode = 'select';

	$scope.get_tot_rpt = function() {

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
		} else if($scope.stocode =="select") {

			alert("Please select a STOCODE.");
		}
		else  
		{
			$scope.showloader=true;	

			$http({
				method:'GET',
				url:"gettotalreport.php",
				params:{stocode:$scope.stocode, fdate:fdate, tdate:tdate},
			}).success(function(result){
				$scope.showloader=false;
				
			});
		}
	}

		function getwords(e){
			if(e=='0')
				return 'ZERO';
			else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}

});


app.controller("AdminBackAddBankController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

		$scope.add_bank = function() {

			if(!$scope.bankname) {

				alert("Please enter bank name.");
			} else if(!$scope.bankifsc) {

				alert("Please enter bank ifsc code.");
			} else if(!$scope.bankbranch) {

				alert("Please enter bank branch.");
			} else {

				$scope.showloader=true;	

				$http({
					method:'GET',
					url:$scope.requesturl+'/add_bank',
					params:{bankname:$scope.bankname, bankbranch:$scope.bankbranch, bankifsc:$scope.bankifsc, bankmicr:$scope.bankmicr, bankaddress:$scope.bankaddress, bankcontact:$scope.bankcontact, bankcenter:$scope.bankcenter, bankdistrict:$scope.bankdistrict, bankstate:$scope.bankstate},
				}).success(function(result){
					$scope.showloader=false;

					if(result == 0) {

						alert("IFSC CODE ALREADY EXISTS.");
					}
					
				});

			}
		}

		function getwords(e){
			if(e=='0')
				return 'ZERO';
			else{var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}}

});

app.controller("AdminBackChqPendingLocController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.Dates=Dates;
	$scope.showloader=true;	
	$http({
		method:'GET',
		url:$scope.requesturl+'/getchqpending',
	}).success(function(result){
		$scope.showloader=false;
		var tot=0;
		for(var i=0;i<result.length;i++)
		{
			tot=tot+parseInt(result[i]['partyamount']);
			result[i]['balance']=Commas.getcomma(result[i]['balance']);
			result[i]['partyamount']=Commas.getcomma(result[i]['partyamount']);
		}
		$scope.tot=Commas.getcomma(tot);
		$scope.allpendingchqs = result;
	});

	$scope.approvechq = function(trans) {

		var data_to_impact=[];
		
		data_to_impact.push([trans.issueuser,trans.transid]);
		
		var dat=JSON.stringify(data_to_impact);
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:'../confirm_payment.php', // confirm cheque script
			params:{list:dat}
		}).
		success(function(result){
			$scope.showloader=true;
			if(result=='success')
			{
				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/approvechqadmin',
					data:{chqid:trans.id,rems:$scope.thisremarkchq}
				}).
				success(function(result){
					if(result==1)
					{
						alert("Cheques Authorized and forwarded.");
						window.location.reload();
					}
					else
					{
						$scope.showloader=false;
						Logging.logout();
					}
				});
			}
			else
			{
				$scope.showloader=false;
				alert('Error in confirming');
			}
		});
		
	}
	$scope.rejectchq = function(data) {

		if(!data.thisremarkchq) {

			alert("Please enter remarks.");
		} else {

			$http({
				method:'POST',
				url:$scope.requesturl+'/rejectchqadmin',
				data:{chqid:data.id, rems:data.thisremarkchq}
			}).success(function(result){

				alert("Cheques rejected.");
				window.location.reload();
			});
		}
	}
});

app.controller("AdminBackSaPendingLocController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.Dates=Dates;
	$scope.showloader=true;	

	$http({
		method:'GET',
		url:$scope.requesturl+'/getsapendinglocs',
	}).success(function(result){
		$scope.showloader=false;
		
		for(var i=0;i<result.length;i++)
		{
			result[i]['reqamount']=Commas.getcomma(result[i]['reqamount']);
		}
		$scope.allpendinglocs = result;

	});

	$scope.approveloc = function(data) {
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/approvelocadmin',
			data:{locid:data.id}
		}).success(function(result){
			window.location.reload();
			$scope.thisrowval = data.id;
		});
	}

	$scope.rejectloc = function(data) {
		if(!data.thisremark) {
			alert("Please enter remarks.");
		} else {
			$scope.showloader=true;
			$http({
				method:'POST',
				url:$scope.requesturl+'/rejectlocadmin',
				data:{locid:data.id, remarks:data.thisremark}
			}).success(function(result){
				window.location.reload();
				$scope.thisrowval = data.id;
			});
		}
	}
});

app.controller("AdminBackActLocController",function($scope,$http,$state,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	
	$scope.get_rpt=function(){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		$scope.showloader=true;
		$http({
			method:'GET',
			url:$scope.requesturl+'/locactivity',
			params:{date:fdatec}
		}).success(function(result){
			$scope.showloader=false;
			$scope.rpt=result;
		});
	}

});


app.controller("AdminBackActChqController",function($scope,$http,$state,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	
	$scope.get_rpt=function(){
		var fdate = $('#fdate').val();
		var fdatesplit = fdate.split("-");
		var fdatec = fdatesplit[2]+"-"+fdatesplit[1]+"-"+fdatesplit[0];
		$scope.showloader=true;
		$http({
			method:'GET',
			url:$scope.requesturl+'/chqactivity',
			params:{date:fdatec}
		}).success(function(result){
			$scope.showloader=false;
			$scope.rpt=result;
		});
	}

});