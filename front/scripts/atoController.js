app.controller("AtoController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
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
				if(result[0]=='9')
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



app.controller("AtoMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/ato_data'
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

// app.controller("EditDDoController",function($scope,$http,$state,$rootScope,Logging,Commas){
//         $scope.$emit("changeTitle",$state.current.views.content.data.title);
//         $scope.showloader=true;
//         $scope.pdadmin="select";
//         $http({
//                 method:'GET',
//                 headers:{'X-CSRFToken':localStorage.token},
//                 url:$scope.requesturl+'/get_sa_admins'
//         }).
//         success(function(result){
//                 $scope.showloader=false;
//                 if(result[0]=='invalid')
//                 {
//                       Logging.logout();
//                 }
//                 else
//                 {
//                         $scope.ddolist=result;
//                 }
//         });
	
// 	$scope.admin_change=function(){
// 		angular.forEach($scope.ddolist,function(ddo){
// 			if(ddo.ddocode==$scope.pdadmin)
// 			{
// 				$scope.ddoname=angular.copy(ddo.usernames.userdesc);
// 			}
// 		});
// 	}

// 	$scope.edit_ddo=function(){
// 		$scope.showloader=true;
// 		$http({
// 	                  	method:'POST',
//                                 headers:{'X-CSRFToken':localStorage.token},
//                                 url:$scope.requesturl+'/edit_ddo',
//                                 data:{ddocode:$scope.pdadmin,ddoname:$scope.ddoname}
//                         }).
//                         success(function(result){
//                                 if(result[0]=='success')
//                                 {
// 					window.location.reload();	
//                                 }
//                                 else
//                                 {
//                                         Logging.logout();
//                                 }
//                         });
// 	}
// });


app.controller("AtoRequestController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_ofctype',
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.bookauthuser = result['book_pass_auth'];
		}
	});
	$scope.chq_approve=function(){
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
		else if(flg==1)
		{
			alert("You cannot confirm the Cheque Book request which SA has rejected");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/ao_booklist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Chequebooks Confirmed");
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
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheque books to reject");
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
				url:$scope.requesturl+'/ao_booklist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Chequebooks Rejected");
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

	$scope.chq_returntosa=function(){
		var chqlist=[];
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheque books to return to SA.");
		}
		else if(!$scope.remarks)
		{
			alert("Please enter remarks.");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/ao_booklist_returntosa',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Chequebooks Returned to SA.");
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
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/ato_requests'
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

				if(!req.storemarks)
				{
					req.storemarks == 'None';
				}

				if(!req.remarks)
				{
					req.remarks == 'None';
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
				url:$scope.requesturl+'/ato_booklist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Chequebook request forwarded to your next level authority for final approval!");
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

app.controller("AtoLoclistController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/ato_loclist'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else if(result[0]=='nomap')
		{
			alert('No Mapping!');
			Logging.logout();
		}
		else
		{

			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ofctype',
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					$scope.chqauthuser = result['cheque_pass_auth'];
					$scope.locauthuser = result['loc_pass_auth'];
				}
			});
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

				if(!req.storemarks)
				{
					req.storemarks = "None";
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
				url:$scope.requesturl+'/ato_loclist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					
					alert('LOC Forwarded to your next highest authority!');
					window.location.reload();
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}

	$scope.chq_approve=function(){
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
		if(chqlist.length==0)
		{
			alert("Please select the LOCs to confirm");
		}
		else if(flg==1)
		{
			alert("You cannot confirm the LOC which SA has rejected");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/ato_loclist_approve',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					var dat=JSON.stringify(data_to_impact);
					$http({
						method:'GET',
						headers:{'X-CSRFToken':localStorage.token},
						url:'../front/save_loc.php',
						params:{list:dat}
					}).
					success(function(result){
						alert("LOCs Confirmed");
						window.location.reload();
					});
					//window.location.reload();
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}

	$scope.chq_reject=function(){
		var chqlist=[];
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the LOCs to reject");
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
				url:$scope.requesturl+'/ato_loclist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("LOCs Rejected");
					window.location.reload();
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}

	$scope.chq_return=function(){
		var chqlist=[];
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the LOCs to return to SA");
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
				url:$scope.requesturl+'/ato_loclist_return',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("LOCs Returned back to Senior Accountant");
					window.location.reload();
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}


});


// app.controller("DtoChequeController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$scope.showloader=true;
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/dto_request_data',
// 		params:{requser:$stateParams.requester}
// 	}).
// 	success(function(result){
// 		$scope.showloader=false;
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			if(result=='')
// 			{
// 				alert("There are no pending requests from this user");
// 				$state.go('dto.request');
// 			}
// 			else
// 			{
// 				$scope.requestdata=result;
// 			}
// 		}
// 	});

// 	$scope.cheques={};

// 	$scope.req_accept=function(){
// 		if(!$scope.cheques.first)
// 		{
// 			alert("Please enter First Cheque No");
// 		}
// 		else if(!$scope.cheques.last)
// 		{
// 			alert("Please enter First Cheque No");
// 		}
// 		else if(!$scope.cheques.number)
// 		{
// 			alert("Please enter Cheque Book No");
// 		}
// 		else
// 		{
// 			$scope.showloader=true;
// 			$http({
// 				method:'POST',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/accept_request',
// 				data:{
// 					user:$scope.requestdata.requestuser,
// 					chequedata:$scope.cheques
// 					}
// 			}).success(function(result){
// 				$scope.showloader=false;
// 				if(result[0]=='success')
// 				{
// 					if(result[1]=='success')
// 					{
// 						alert('Cheque book Issued');
// 					}
// 					else
// 					{
// 						alert('Error in issuing cheque book.Please try again.')
// 					}
// 					$state.go('dto.request');
// 				}
// 				else
// 				{
// 					Logging.logout();
// 				}
// 			});
// 		}
// 	}
// });

// app.controller("DtoCreateController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/dto_ac_data',
// 	}).
// 	success(function(result){
// 		$scope.showloader=false;
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			$scope.allrequests=result;
// 			counter=1;
// 			angular.forEach($scope.allrequests, function(req){
// 				req.sno=counter;
// 				counter++;
// 				req.balance=Commas.getcomma(req.balance);
// 				if(req.account_type=='2')
// 				{
// 					req.act='LOC';
// 				}
// 				else
// 				{
// 					req.act='NON-LOC';
// 					req.loc='N/A';
// 				}
// 			});
// 		}
// 	});

// 	$scope.checkall=function(){
// 		var val=!$scope.allchecked();
// 		angular.forEach($scope.allrequests,function(trans){
// 			trans.check=val;
// 		});
// 	}

// 	$scope.allchecked=function(){
// 		var flag=1;
// 		count=0;
// 		if($scope.allrequests)
// 		{
// 			angular.forEach($scope.allrequests,function(trans){
// 				if(trans.check)
// 				{
// 					count++;
// 				}
// 			});
// 			return count===$scope.allrequests.length;
// 		}
// 		else
// 		{
// 			return false;
// 		}
// 	}

// 	$scope.chq_confirm=function(){
// 		var chqlist=[];
// 		angular.forEach($scope.allrequests,function(trans){
// 			if(trans.check)
// 			{
// 				chqlist.push(trans.id);
// 			}
// 		});
// 		if(chqlist.length==0)
// 		{
// 			alert("Please select the accounts to confirm");
// 		}
// 		else
// 		{
// 			$scope.showloader=true;
// 			$http({
// 				method:'POST',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/dto_aclist_confirm',
// 				data:{list:chqlist,rems:$scope.remarks}
// 			}).
// 			success(function(result){
// 				$scope.showloader=false;
// 				if(result[0]=='success')
// 				{
// 					alert("Accounts Confirmed");
// 					window.location.reload();
// 				}
// 				else
// 				{
// 					Logging.logout();
// 				}
// 			});
// 		}
// 	}

// 	$scope.chq_reject=function(){
// 		var chqlist=[];
// 		angular.forEach($scope.allrequests,function(trans){
// 			if(trans.check)
// 			{
// 				chqlist.push(trans.id);
// 			}
// 		});
// 		if(chqlist.length==0)
// 		{
// 			alert("Please select the accounts to reject");
// 		}
// 		else if(!$scope.remarks)
// 		{
// 			alert("Please enter remarks");
// 		}
// 		else
// 		{
// 			$scope.showloader=true;
// 			$http({
// 				method:'POST',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/dto_aclist_reject',
// 				data:{list:chqlist,rems:$scope.remarks}
// 			}).
// 			success(function(result){
// 				$scope.showloader=false;
// 				if(result[0]=='success')
// 				{
// 					alert("Accounts Rejected");
// 					window.location.reload();
// 				}
// 				else
// 				{
// 					Logging.logout();
// 				}
// 			});
// 		}
// 	}
// });


// app.controller("DtoLocController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$scope.showloader=true;
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/dto_loc_data',
// 		params:{requser:$stateParams.requester,reqhoa:$stateParams.requesthoa}
// 	}).
// 	success(function(result){
// 		$scope.showloader=false;
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			if(result=='')
// 			{
// 				alert("There are no pending requests from this user");
// 				$state.go('dto.loclist');
// 			}
// 			else
// 			{
// 				$scope.requestdata=result;
// 				$scope.requestdata.reamount=Commas.getcomma($scope.requestdata.reqamount);
// 				$scope.issue_amt=result.reqamount;
// 			}
// 		}
// 	});

// 	$scope.req_accept=function(){
// 		if(!$scope.issue_amt)
// 		{
// 			alert("Please enter Issue Amount towards LOC");
// 		}
// 		else if(parseInt($scope.issue_amt)>(parseInt($scope.requestdata.accounts.balance)+parseInt($scope.requestdata.accounts.loc)))
// 		{
// 			alert("Issued Loc is more than the existing Balance");
// 		}
// 		else
// 		{
// 			$scope.showloader=true;
// 			$http({
// 				method:'POST',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/accept_loc',
// 				data:{
// 					user:$scope.requestdata.requestuser,
// 					hoa:$scope.requestdata.hoa,
// 					amt:$scope.issue_amt,
// 					refno:$scope.refno
// 					}
// 			}).success(function(result){
// 				$scope.showloader=false;
// 				if(result[0]=='success')
// 				{
// 					alert('Loc Issued');
// 					$state.go('dto.loclist');
// 				}
// 				else
// 				{
// 					Logging.logout();
// 				}
// 			});
// 		}
// 	}
// });

app.controller("AtoTransController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){

	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}

	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/ato_trans'
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
				url:$scope.requesturl+'/get_ofctype',
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					$scope.chqauthuser = result['cheque_pass_auth'];
					$scope.locauthuser = result['loc_pass_auth'];
				}
			});
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
					tr.rejects = "None";
				}

				if(!tr.storemarks)
				{
					tr.storemarks = "None";
				}
				
			});
			
		}
	});

	$scope.view_lap_details = function(x)
	{
		$scope.dtolap = x;
		
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


		$scope.lapshow = true;
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
				url:$scope.requesturl+'/ato_chqlist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{

					alert("Cheques Authorized and forwarded to the next highest authority!");
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

	$scope.chq_approve=function(){
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
			alert("Please select the cheques to approve.");
		}
		else
		{
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
						url:$scope.requesturl+'/ato_chqlist_approve',
						data:{list:chqlist,rems:$scope.remarks}
					}).
					success(function(result){
						if(result[0]=='success')
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
	}

	$scope.chq_returnsa=function(){
		var chqlist=[];
		angular.forEach($scope.alltrans,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the cheques to return.");
		} else if(!$scope.remarks) {

			alert("Please enter remark.");
		}
		else
		{
			
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/ato_return_sa',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{

					alert("Cheques return to SA!");
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
			alert("Please select the cheques to reject.");
		}
		else if(!$scope.remarks)
		{
			alert("Please enter remarks");
		}
		else
		{
			
			$scope.showloader=true;
		        var dat=JSON.stringify(data_to_impact);

			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:'../front/reject_payment.php', // reject payment script
				params:{list:dat}
			}).
			success(function(result){
				if(result=='success')
				{
					$http({
						method:'POST',
						headers:{'X-CSRFToken':localStorage.token},
						url:$scope.requesturl+'/ato_chqlist_reject',
						data:{list:chqlist,rems:$scope.remarks}
					}).
					success(function(result){
						if(result=='success')
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
				else
				{
					alert('Error in rejecting');
				}
			});
		}
	}

});

// app.controller("DtoConfirmController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates,$stateParams){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$scope.transno=$stateParams.transaction;
// 	$scope.showloader=true;
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/dto_chq_data',
// 		params:{chqno:$scope.transno}
// 	}).success(function(result){
// 		$scope.showloader=false;
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			$scope.dat=result[0];
// 		}
// 	});

// 	$scope.words=function(dat)
// 	{
// 		if(dat)
// 		{
// 			return getwords(dat)+' ONLY';
// 		}
// 		else
// 		{
// 			return "";
// 		}
// 	}

// 	function getwords(e){var t="";if(e.length==2){}else if(e.length==1){e=0+e}else if(e.length%2===0){e=0+e}var n=e.substr(-2,2);t=t+getnum(n);if(e.length>=3){var r="0"+e.substr(-3,1);if(r=="00"){}else{t=getnum(r)+" HUNDRED"+t}}if(e.length>=5){var i=e.substr(-5,2);if(i=="00"){}else{t=getnum(i)+" THOUSAND"+t}}if(e.length>=7){var s=e.substr(-7,2);if(s=="00"){}else{t=getnum(s)+" LAKH"+t}}if(e.length>7){var o=e.substr(0,e.length-7);t=getwords(o)+" CRORE"+t}return t}function getnum(e){var t="";ones=e.substr(1,1);tens=e.substr(0,1);if(tens=="0"){switch(ones){case"0":t="";break;case"1":t=" ONE";break;case"2":t=" TWO";break;case"3":t=" THREE";break;case"4":t=" FOUR";break;case"5":t=" FIVE";break;case"6":t=" SIX";break;case"7":t=" SEVEN";break;case"8":t=" EIGHT";break;case"9":t=" NINE";break}}else if(tens=="1"){switch(ones){case"0":t=" TEN";break;case"1":t=" ELEVEN";break;case"2":t=" TWELVE";break;case"3":t=" THIRTEEN";break;case"4":t=" FOURTEEN";break;case"5":t=" FIFTEEN";break;case"6":t=" SIXTEEN";break;case"7":t=" SEVENTEEN";break;case"8":t=" EIGHTEEN";break;case"9":t="NINETEEN";break}}else{switch(tens){case"2":t=" TWENTY";break;case"3":t=" THIRTY";break;case"4":t=" FORTY";break;case"5":t=" FIFTY";break;case"6":t=" SIXTY";break;case"7":t=" SEVENTY";break;case"8":t=" EIGHTY";break;case"9":t=" NINTY";break}switch(ones){case"0":t=t+"";break;case"1":t=t+" ONE";break;case"2":t=t+" TWO";break;case"3":t=t+" THREE";break;case"4":t=t+" FOUR";break;case"5":t=t+" FIVE";break;case"6":t=t+" SIX";break;case"7":t=t+" SEVEN";break;case"8":t=t+" EIGHT";break;case"9":t=t+" NINE";break}}return t}

// 	$scope.chq_confirm=function(){
// 		var chqlist=[$scope.transno];
// 		$scope.showloader=true;
// 		$http({
// 			method:'POST',
// 			headers:{'X-CSRFToken':localStorage.token},
// 			url:$scope.requesturl+'/dto_chqlist_confirm',
// 			data:{list:chqlist,rems:$scope.remarks}
// 		}).
// 		success(function(result){
// 			$scope.showloader=false;
// 			if(result[0]=='success')
// 			{
// 				alert("Cheques Authorized and sent to bank for final payment");
// 				$state.go('dto.trans');
// 			}
// 			else
// 			{
// 				Logging.logout();
// 			}
// 		});
// 	}

// 	$scope.chq_reject=function(){
// 		var chqlist=[$scope.transno];
// 		if(!$scope.remarks)
// 		{
// 			alert("Please enter remarks");
// 		}
// 		else
// 		{
// 			$scope.showloader=true;
// 			$http({
// 				method:'POST',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/dto_chqlist_reject',
// 				data:{list:chqlist,rems:$scope.remarks}
// 			}).
// 			success(function(result){
// 				$scope.showloader=false;
// 				if(result[0]=='success')
// 				{
// 					alert("Cheques Rejected");
// 					$state.go('dto.trans');
// 				}
// 				else
// 				{
// 					Logging.logout();
// 				}
// 			});
// 		}
// 	}
// });

// app.controller("DtoLedgerController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$scope.showloader=true;
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/get_dto_admins'
// 	}).
// 	success(function(result){
// 		$scope.showloader=false;
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			$scope.ddolist=result;
// 		}
// 	});

// 	$scope.admin_change=function(){
// 		$scope.hoalist=[];
// 		$scope.showloader=true;
// 		$http({
// 			method:'GET',
// 			headers:{'X-CSRFToken':localStorage.token},
// 			url:$scope.requesturl+'/get_dto_hoas',
// 			params:{ddo:$scope.pdadmin.ddocode}
// 		}).
// 		success(function(result){
// 			$scope.showloader=false;
// 			if(result[0]=='invalid')
// 			{
// 				Logging.logout();
// 			}
// 			else
// 			{
// 				$scope.hoalist=result;
// 			}
// 		});
// 	}

// 	$scope.hoa_change=function(){
// 		$scope.showloader=true;
// 		$scope.maintrans=[];
// 		$http({
// 			method:'GET',
// 			headers:{'X-CSRFToken':localStorage.token},
// 			url:$scope.requesturl+'/get_dto_ledger',
// 			params:{ddo:$scope.pdadmin.ddocode,hoa:$scope.hoa.hoa}
// 		}).
// 		success(function(result){
// 			if(result[0]=='invalid')
// 			{
// 				Logging.logout();
// 			}
// 			else
// 			{
// 				$scope.maintrans=result;
// 			}
// 			$scope.showloader=false;
// 		});
// 	}
// });

// app.controller("DtoPageController",function($scope,$http,$state,$stateParams,Dates,Logging,Commas){
// 	$scope.Dates=Dates;
// 	$scope.$emit("changeTitle",$state.current.views.content.data.title);
// 	$scope.showloader=true;
// 	$scope.acdat=$stateParams.account
// 	$scope.page=$stateParams.page;
// 	$scope.maintrans=[];
// 	$scope.trans=[];
// 	$scope.first=false;
// 	$scope.second=false;
// 	$scope.endbut=false;
// 	$scope.end=false;
// 	$http({
// 		method:'GET',
// 		headers:{'X-CSRFToken':localStorage.token},
// 		url:$scope.requesturl+'/get_ledgerdata',
// 		params:{account:$stateParams.account}
// 	}).
// 	success(function(result){
// 		if(result[0]=='invalid')
// 		{
// 			Logging.logout();
// 		}
// 		else
// 		{
// 			$scope.accountinfo=result;
// 			$http({
// 				method:'GET',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/get_ledgerpage',
// 				params:{hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode,page:$scope.page}
// 			}).
// 			success(function(data){
// 				$scope.maintrans=data;
// 			});
// 			$http({
// 				method:'GET',
// 				headers:{'X-CSRFToken':localStorage.token},
// 				url:$scope.requesturl+'/get_ledgerpagelist',
// 				params:{hoa:$scope.accountinfo.hoa,ddo:$scope.accountinfo.ddocode}
// 			}).success(function(data){
// 				$scope.showloader=false;
// 				$scope.totpages=data[0];
// 				if($scope.totpages==0)
// 				{
// 					$scope.page=0;
// 					$scope.pagehide=false;	
// 				}
// 				else
// 				{
// 					$scope.pagehide=true;
// 				}
// 				if($scope.totpages%10==0)
// 				{
// 					$scope.pages=$scope.totpages/10;
// 				}
// 				else
// 				{
// 					$scope.pages=parseInt($scope.totpages/10)+1;
// 				}
// 				if($scope.page=='last')
// 				{
// 					$scope.page=$scope.pages;
// 				}
// 				else if($scope.page>$scope.pages)
// 				{
// 					$scope.page=$scope.pages;
// 				}
// 				else if(!parseInt($scope.page))
// 				{
// 					$scope.page=$scope.pages;	
// 				}
// 				else
// 				{
// 					$scope.page=parseInt($scope.page);
// 				}
// 				if($scope.page<$scope.pages-1)
// 				{
// 					$scope.end=true;
// 				}
// 				if($scope.page!=$scope.pages)
// 				{
// 					$scope.endbut=true;	
// 				}
// 				if($scope.page>2)
// 				{
// 					$scope.first=true;
// 				}
// 				if($scope.page!=1)
// 				{
// 					$scope.second=true;
// 				}
// 			});
// 		}
// 	});
// });

app.controller("AtoStatementController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){

	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
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
		url:$scope.requesturl+'/get_ato_admins'
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
				url:$scope.requesturl+'/get_ato_hoas',
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
