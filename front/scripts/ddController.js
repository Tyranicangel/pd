app.controller("DdController",function($scope,$http,$state,$rootScope,Logging,Commas){
	//$scope.$emit("changeTitle",$state.current.views.content.data.title);
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
				if(result[0]=='8')
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

app.controller("DdaddinventController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.uploadtype='normal';
	$scope.tdata=[];
	$scope.fdata=[];
	$scope.hdata=[];

	$scope.upload = function(files) {
		var formdata = new FormData();
		formdata.append('file', files[0]);
		$scope.allplist={};
		$scope.showloader=true;
		$http({
			method:'POST',
			url:$scope.requesturl+'/uploadbulk',
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
							alert('Please enter the cheque book details and check your file format');
						}
						else
						{
							$scope.tdata=data[3][0];
							$scope.fdata=data[3][1];
							$scope.hdata=data[3][2];
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


	$scope.add_bulk=function(){
		$scope.showloader=true;
		dats=[$scope.tdata,$scope.fdata,$scope.hdata];
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/add_bulk',
			data:{book:dats}
		}).
		success(function(result){
			if(result[0]=='invalid')
			{
				$scope.showloader=false;
				Logging.logout();
			}
			else
			{
				if(result[0]=='success')
				{
					alert('Inventory Added');
					window.location.reload();
				}
				else
				{
					$scope.showloader=false;
					alert(result[0]);
				}
			}
		});	
	}
	
	$scope.add_book=function(){
		if(!$scope.first)
		{
			alert("Please enter First Cheque No");
		}
		else if(!$scope.last)
		{
			alert("Please enter Last Cheque No");
		}
		else if(!$scope.regex.test($scope.last))
		{
			alert("Please enter correct Last Cheque No");
		}
		else if(!$scope.regex.test($scope.first))
		{
			alert("Please enter correct First Cheque No");
		}
		else if(parseInt($scope.last)<parseInt($scope.first))
		{
			alert("Please enter correct First and Last Cheque No");
		}
		else if(!$scope.number)
		{
			alert("Please enter Cheque Book No");
		}
		else if($scope.no_leaves()!=25&&$scope.no_leaves()!=50&&$scope.no_leaves()!=100)
		{
			alert("No of leaves can only be 25,50 or 100");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/add_invent',
				data:{book:$scope.number,first:$scope.first,last:$scope.last}
			}).
			success(function(result){
				if(result[0]=='invalid')
				{
					$scope.showloader=false;
					Logging.logout();
				}
				else
				{
					if(result[0]=='success')
					{
						alert('Inventory Added');
						window.location.reload();
					}
					else
					{
						$scope.showloader=false;
						alert(result[0]);
					}
				}
			});
		}
	}
});

app.controller("DdviewinventController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/view_invent'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.allr=result;
		}
	});

	$scope.show_tab=function(n){
		var flg=0;
		angular.forEach($scope.allr,function(r){
			if(r.size==n)
			{
				flg=1;
			}
		});
		if(flg==1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	$scope.get_bookno=function(n){
		var fl=0;
		angular.forEach($scope.allr,function(r){
			if(r.size==n && r.used=='0')
			{
				fl++;
			}
		});
		return fl;
	}
});



app.controller("DdMapChqController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;

	$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_sa_chq_map_list'
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				if(result.length==0)
				{
					$scope.sauser = 'select';
				}
				else
				{	
					$scope.sauser = result['sauser'];
				}

				$http({
					method:'GET',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/get_sa_user_list'
				}).
				success(function(result){
					$scope.showloader=false;
					if(result[0]=='invalid')
					{
						Logging.logout();
					}
					else
					{
						$scope.sausers=result;

						$scope.item	= {chqflag: "0",
									id: 0,
									lapsableflag: "",
									modify_date: "",
									password: "",
									refreshtoken: "",
									user_role: "",
									userdesc: "SELECT",
									userid: "",
									username: "select"};

						result.unshift($scope.item);
					}
				});
			}
		});

		$scope.save_chq_map = function()
		{
			if(!$scope.sauser)
			{
				alert('Please select SA user to map!');
			}
			else if($scope.sauser=='select')
			{
				alert('Please select SA user to map!');
			}
			else
			{
				$scope.showloader=true;

				$http({
					method:'POST',
					headers:{'X-CSRFToken':localStorage.token},
					url:$scope.requesturl+'/post_chq_map_sa',
					data:{dat:$scope.sauser}
				}).
				success(function(result){
					$scope.showloader=false;
					if(result[0]=='invalid')
					{
						Logging.logout();
					}
					else
					{
						alert('Successfully Mapped the SA as the Cheque book User!');
					}
				});
			}
		}



});//end



app.controller("DdManageUsersController",function($scope,$http,$state,$rootScope,Logging,Commas){
	//$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.gotos =function(x)
	{
		if(x=='hoa')
		{
			$state.go('dd.manageuac.hoatosa');
		}
		else if(x=='samap')
		{
			$state.go('dd.manageuac.samap');
		}
		else if(x=='sto')
		{
			$state.go('dd.manageuac.stomap');
		}
	}

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_ofctype'
	}).
	success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			if(result.length==0)
			{
				alert('Your office type is not defined please contact the admin team  and try again later!');
			}
			else
			{
				$scope.ofctype = result['type'];
			}
		}
	});


});


app.controller("DdMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/dd_data'
	}).
	success(function(result){
		$scope.showloader=false;

		if(result[0]=='success')
		{
			$scope.reqno=result[1];
			$scope.transno=result[2];
			$scope.locno=result[3];
			$scope.acno=result[4];
		}
		else
		{
			Logging.logout();
		}

		$scope.showloader=true;

		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ofctype'
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				if(result.length==0)
				{
					alert('Your office type is not defined please contact the admin team  and try again later!');
					Logging.logout();
				}
				
			}
		});



	});
});

app.controller("EditDDoController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
		angular.forEach($scope.ddolist,function(ddo){
			if(ddo.ddocode==$scope.pdadmin)
			{
				$scope.ddoname=angular.copy(ddo.usernames.userdesc);
			}
		});
	}

	$scope.edit_ddo=function(){
		$scope.showloader=true;
		$http({
	      	method:'POST',
	                headers:{'X-CSRFToken':localStorage.token},
	                url:$scope.requesturl+'/edit_ddo',
	                data:{ddocode:$scope.pdadmin,ddoname:$scope.ddoname}
	        }).
	        success(function(result){
	                if(result[0]=='success')
	                {
	                	alert('Successfully changed the name!');
						window.location.reload();	
	                }
	                else
	                {
	                        Logging.logout();
	                }
	        });
	}
});


app.controller("DdRequestController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/dd_requests'
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

			$scope.showloader=true;

			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ofctype'
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					if(result.length==0)
					{
						alert('Your office type is not defined please contact the admin team  and try again later!');
						Logging.logout();
					}
					else
					{
						$scope.ofctype = result['type'];
					}
				}
			});
		}
	});


	$scope.get_grant_leafs=function(a,b){
		if(a=='-')
		{
			return "-";
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
				url:$scope.requesturl+'/dd_booklist_confirm',
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
				url:$scope.requesturl+'/dd_booklist_reject',
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
				url:$scope.requesturl+'/dd_booklist_returntosa',
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

});

app.controller("DdEditUsersController",function($scope,$http,$state,$rootScope,Logging,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_dd_user_data'
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.alluserlist=result;
		}
	});

	$scope.saveusers=function(){
		$scope.showloader=true;
		$http({
			method:'POST',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/edit_dd_user_data',
			data:{dat:$scope.alluserlist}
		}).success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				alert("Edited Users Successfully");
			}
		});		
	}
});


app.controller("DdCreateUsersController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	//$scope.showloader=true;
	$scope.create_type = 'sa';

	$scope.showloader=true;
		
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_account_dets',
		params:{dat:'sa'}
	}).
	success(function(result){
		
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.salist = result;
		}
		$scope.uname = '';

		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ofctype'
		}).
		success(function(result){
			
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				if(result.length==0)
				{
					alert('Sorry your office Type is not defined! please contact the Admin team through the Online Query System and login later!');
					Logging.logout();
				}
				else
				{
					$scope.ofctype = result['type'];
				}
				
			}
		});
	});


	


	$scope.get_acnt_dets = function(x)
	{
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_account_dets',
			params:{dat:x}
		}).
		success(function(result){
			
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				$scope.salist = result;
			}
			$scope.uname = '';
		});
	}

	$scope.crt_new_user = function()
	{

		if(!$scope.nusername)
		{
			alert('please enter the name of the person!');
		}
		else if(!$scope.create_type)
		{
			alert('please select the type of account you want to create!');
		}
		else
		{
			

			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/crt_new_acnt_dd',
				params:{dat:$scope.create_type,desc:$scope.nusername}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					$scope.uname = result;
					$scope.defpass = '123456';
					delete $scope.nusername;
				}
			});
		}
	}




});//end



app.controller("DdManageUsersHoatoSaController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.manageusers.data.title);
	$scope.showloader=true;
	$rootScope.map_type = 'hoa';

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sa_user_list'
	}).
	success(function(result){

		$scope.item	= {chqflag: "0",
						id: 50431,
						lapsableflag: "",
						modify_date: "",
						password: "",
						refreshtoken: "",
						user_role: "",
						userdesc: "SELECT",
						userid: "",
						username: "select"};

		result.unshift($scope.item);

		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.showloader=true;
			$scope.sausers = result;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_hoas_under_sa'
			}).
			success(function(result){

				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					$scope.hoalist = result[0];

					angular.forEach($scope.hoalist,function(x){
						if(!x.mapto)
						{
							x.sauser = 'select';
						}
						else
						{
							x.sauser = x.mapto;
						}
					});	
				}
			});


			
		}
	});	

	$scope.save = function()
	{
		var flag = 0;
		angular.forEach($scope.hoalist,function(x){
			if(x.sauser=="select")
			{
				flag = 1;
			}
		});

		if(flag==1)
		{
			alert('Please assign a SA to every HOA!');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/post_hoa_map_dets',
				data:{dat:$scope.hoalist}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					alert('Successfully Mapped the HOAs to the mentioned Senior Accountants!');
				}
			});
		}
		

	}

});//end


app.controller("DdManageUsersSamapController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.manageusers.data.title);
	$rootScope.map_type = 'sa';
	$scope.showloader=true;


	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_users_above_sa'
	}).
	success(function(result){
		
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.item	= {chqflag: "0",
							id: 0,
							lapsableflag: "",
							modify_date: "",
							password: "",
							refreshtoken: "",
							user_role: "",
							userdesc: "SELECT",
							userid: "",
							username: "select"};

			result.unshift($scope.item);

			$scope.othusers = result;
		}
	});

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sa_user_list'
	}).
	success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.showloader=false;
			$scope.sausers = result;
			angular.forEach($scope.sausers,function(x){
				if(x.mappedto==null)
				{
					x.mapuser = 'select';
				}
				else
				{
					x.mapuser = x.mappedto.mappeduser;
				}
			});	
		}
	});	

	$scope.save_map = function()
	{
		var flag = 0;
		angular.forEach($scope.sausers,function(x){
			if(x.mapuser=="select")
			{
				flag = 1;
			}
		});

		if(flag==1)
		{
			alert('Please map Every SA!');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/post_sa_map_dets',
				data:{dat:$scope.sausers}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					alert('Successfully Mapped the Senior Accountants!');
				}
			});
			
		}
		

	}

});//end




app.controller("DdManageUsersStomapController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.manageusers.data.title);
	$rootScope.map_type = 'sto';
	$scope.showloader=true;


	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_users_above_sto'
	}).
	success(function(result){
		
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.item	= {chqflag: "0",
							id: 0,
							lapsableflag: "",
							modify_date: "",
							password: "",
							refreshtoken: "",
							user_role: "",
							userdesc: "SELECT",
							userid: "",
							username: "select"};

			result.unshift($scope.item);

			$scope.othusers = result;
		}
	});

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_sto_user_list'
	}).
	success(function(result){
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.showloader=false;
			$scope.stousers = result;
			angular.forEach($scope.stousers,function(x){
				if(x.mappedto==null)
				{
					x.mapuser = 'select';
				}
				else
				{
					x.mapuser = x.mappedto.mappeduser;
				}
			});	
		}
	});	

	$scope.save_map = function()
	{
		var flag = 0;
		angular.forEach($scope.stousers,function(x){
			if(x.mapuser=="select")
			{
				flag = 1;
			}
		});

		if(flag==1)
		{
			alert('Please map Every STO!');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/post_sto_map_dets',
				data:{dat:$scope.stousers}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					alert('Successfully Mapped the STOs!');
				}
			});
			
		}
		

	}

});//end

app.controller("DdLoclistController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/dd_loclist'
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

				if(!req.atoremarks)
				{
					req.atoremarks = "None";
				}

				if(!req.storemarks)
				{
					req.storemarks = "None";
				}

			});



			$scope.showloader=true;
			
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ofctype'
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					if(result.length==0)
					{
						alert('Your office type is not defined please contact the admin team  and try again later!');
						Logging.logout();
					}
					else
					{
						$scope.ofctype = result['type'];
					}
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
				url:$scope.requesturl+'/dd_loclist_confirm',
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
				url:$scope.requesturl+'/dd_loclist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("LOCs Rejected");
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
				url:$scope.requesturl+'/dd_loclist_return',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("LOCs Returned back to Senior Accountant");
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

app.controller("DdMapAuthController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);

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
			$scope.bookauthuser=result['book_pass_auth'];
		}
	});

	$scope.save_chq_loc_map = function() {

		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/update_auth_user',
			params:{chqauthuser:$scope.chqauthuser,locauthuser:$scope.locauthuser,bookauthuser:$scope.bookauthuser}
		}).
		success(function(result){
			$scope.showloader=false;
			if(result[0]=='invalid')
			{
				Logging.logout();
			}
			else
			{
				alert("Mapping successful.");
				window.location.reload();
			}
		});
	}



});

app.controller("DdCreateController",function($scope,$http,$state,$rootScope,Logging,Commas,$stateParams,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/dd_ac_data',
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
				req.balance=Commas.getcomma(req.balance);
				if(req.account_type=='2')
				{
					req.act='LOC';
				}
				else
				{
					req.act='NON-LOC';
					req.loc='N/A';
				}
			});
		}
	});

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
		angular.forEach($scope.allrequests,function(trans){
			if(trans.check)
			{
				chqlist.push(trans.id);
			}
		});
		if(chqlist.length==0)
		{
			alert("Please select the accounts to confirm");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/dd_aclist_confirm',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Accounts Confirmed");
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
			alert("Please select the accounts to reject");
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
				url:$scope.requesturl+'/dd_aclist_reject',
				data:{list:chqlist,rems:$scope.remarks}
			}).
			success(function(result){
				if(result[0]=='success')
				{
					alert("Accounts Rejected");
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

app.controller("DdTransController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.getcommas=function(dat){
		return Commas.getcomma(dat);
	}
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/dd_trans'
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


				if(!tr.storemarks)
				{
					tr.storemarks ="None";
				}

				if(!tr.atoremarks)
				{
					tr.atoremarks ="None";
				}

				if(!tr.rejects)
				{
					tr.rejects ="None";
				}
			});



			$scope.showloader=true;
			
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ofctype'
			}).
			success(function(result){
				$scope.showloader=false;
				if(result[0]=='invalid')
				{
					Logging.logout();
				}
				else
				{
					if(result.length==0)
					{
						alert('Your office type is not defined please contact the admin team  and try again later!');
						Logging.logout();
					}
					else
					{
						$scope.ofctype = result['type'];
					}
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

	//temporary (to be removed later) starts
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
						url:$scope.requesturl+'/dd_chqlist_confirm',
						data:{list:chqlist,rems:$scope.remarks}
					}).
					success(function(result){
						if(result[0]=='success')
						{
							alert("Cheques Authorized and forwarded");
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

	//temporary (to be removed later) ends

	// $scope.chq_confirm=function(){
	// 	var chqlist=[];
	// 	var data_to_impact=[];
	// 	angular.forEach($scope.alltrans,function(trans){
	// 		if(trans.check)
	// 		{
	// 			data_to_impact.push([trans.issueuser,trans.transid]);
	// 			chqlist.push(trans.id);
	// 		}
	// 	});
	// 	if(chqlist.length==0)
	// 	{
	// 		alert("Please select the cheques to confirm");
	// 	}
	// 	else
	// 	{
	// 		var dat=JSON.stringify(data_to_impact);
	// 		$scope.showloader=true;
	// 		$http({
	// 			method:'GET',
	// 			headers:{'X-CSRFToken':localStorage.token},
	// 			url:'../confirm_payment.php', // confirm cheque script
	// 			params:{list:dat}
	// 		}).
	// 		success(function(result){
	// 			$scope.showloader=true;
	// 			if(result=='success')
	// 			{
	// 				$http({
	// 					method:'POST',
	// 					headers:{'X-CSRFToken':localStorage.token},
	// 					url:$scope.requesturl+'/dd_chqlist_confirm',
	// 					data:{list:chqlist,rems:$scope.remarks}
	// 				}).
	// 				success(function(result){
	// 					if(result[0]=='success')
	// 					{
	// 						alert("Cheques Authorized and forwarded");
	// 						window.location.reload();
	// 					}
	// 					else
	// 					{
	// 						$scope.showloader=false;
	// 						Logging.logout();
	// 					}
	// 				});
	// 			}
	// 			else
	// 			{
	// 				$scope.showloader=false;
	// 				alert('Error in confirming');
	// 			}
	// 		});
	// 	}
	// }
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
						url:$scope.requesturl+'/dd_chqlist_reject',
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
				url:$scope.requesturl+'/dd_return_sa',
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

app.controller("DdStatementController",function($scope,$http,$state,$rootScope,Logging,Commas,Dates){
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
		url:$scope.requesturl+'/get_dd_admins'
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
				url:$scope.requesturl+'/get_dd_hoas',
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

app.controller("DdValidateUserController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_dd_all_accounts',
		params:{status:"0"}
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

	$scope.submitstatus = function(hoaarr) {

		if(hoaarr['status'] == "2" && !hoaarr['remarks']) {

			alert("Please enter remark.");
		} else {
			$scope.showloader=true;
			$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/update_pd_status',
			params:{remarks:hoaarr['remarks'],hoa:hoaarr['hoa'], ddocode:hoaarr['ddocode'], status:hoaarr['status']}
			}).
			success(function(result){
				if(result[0]=='invalid')
				{
					$scope.showloader=false;
					Logging.logout();
				}
				else
				{
					window.location.reload();
				}
			});
		}
	}
	
});

app.controller("DdValidateApprovedController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_dd_all_accounts',
		params:{status:"1"}
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
	
});

app.controller("DdValidateRejectedController",function($scope,$http,$state,$rootScope,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.showloader=true;

	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_dd_all_accounts',
		params:{status:"2"}
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
	
});
