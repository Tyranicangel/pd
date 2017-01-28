app.controller("MainController",function($scope,$http,$rootScope,$state,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.title="PdAccount";
	$scope.$on("changeTitle",function(event,data){
		$scope.title=data;
	});

	$scope.requesturl="/pdbabu/back/public";

	$scope.regex = /^[0-9]+$/;

	$http({
		method:'GET',
		url:$scope.requesturl+'/getcdate',
	}).
	success(function(result){
		$scope.maindate=result;
		// $scope.comparedate =  Dates.getDate(result);
	});

	$scope.logout=function(){
		Logging.logout();
	}
});


app.controller("HomeController",function($scope,$http,$state,$rootScope){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.errors=false;
	$scope.error_msg="none";
	$scope.showloader=false;
	$scope.showloaderforgotpass = false;
	$scope.login=function(){
		if(!$scope.user)
		{
			$scope.errors=true;
			$scope.error_msg="Please enter User-ID and Password."
		}
		else if(!$scope.user.id)
		{
			$scope.errors=true;
			$scope.error_msg="Please enter User-ID.";
		}
		else if(!$scope.user.password)
		{
			$scope.errors=true;
			$scope.error_msg="Please enter Password.";
		}
		else
		{
			$scope.errors=false;
			$scope.showloader=true;
			$http({
				method:'POST',
				url:$scope.requesturl+'/logins',
				data:$scope.user
			}).
			success(function(result){
				if(result[0]=="nouser")
				{
					$scope.errors=true;
					$scope.error_msg="Please enter correct username.";
				}
				else if(result[0]=="nopass")
				{
					$scope.errors=true;
					$scope.error_msg="Please enter correct password.";
				}
				else
				{
					$scope.errors=false;
					localStorage.token=result[0][0];
					if(result[0][1]=='1')
					{
						$state.go('sa.main');
					}
					else if(result[0][1]=='2')
					{
						$state.go('admin.main');
					}
					else if(result[0][1]=='3')
					{
						$state.go('govt.main');
					}
					else if(result[0][1]=='4')
					{
						$state.go('bank.main');
					}
					else if(result[0][1]=='5')
					{
						$state.go('ag.main');
					}
					else if(result[0][1]=='7')
					{
						$state.go('govtif.main');
					}
					else if(result[0][1]=='8')
					{
						$state.go('dd.main');
					}
					else if(result[0][1]=='11')
					{
						$state.go('dsa.main');
					}
					else if(result[0][1]=='10')
					{
						$state.go('sto.main');
					}
					else if(result[0][1]=='9')
					{
						$state.go('ato.main');
					}
					else if(result[0][1]=='50')
					{
						$state.go('backadmin.adminhome');
					}
					else if(result[0][1]=='20')
					{
						$state.go('adminchecker.main');
					}
					else if(result[0][1]=='26')
					{
						$state.go('panchayathq.main');
					}
					else if(result[0][1]=='27')
					{
						$state.go('panchayatdist.main');
					}
					else
					{
						$state.go('home.main');
					}
				}
				$scope.showloader=false;
			});
		}
	}

	$scope.forgotbox = function() {

		$scope.showloaderforgotpass = true;
	}
	$scope.forgotpasssubmit = function() {

		$scope.errormesg = false;

		if(!$scope.forgotuserid) {

			$scope.errormesg = "Please enter user id.";
		} else {
			$scope.showloader=true;
			$("#forgotsubbtn").val("SUBMITTING...");

			$http({
				method:'POST',
				url:$scope.requesturl+'/forgotpass',
				data:{userid:$scope.forgotuserid}
			}).
			success(function(result){

				if(result == 0) {
					$scope.showloader=false;
					$scope.errormesg = "User id doesnot exists.";
					$("#forgotsubbtn").val("SUBMIT");
				} else if(result == 2) {
					$scope.showloader=false;
					$scope.errormesg = "No email id resgistered for the given user id.";
					$("#forgotsubbtn").val("SUBMIT");
				} else if(result ==3) {

					$(".forgotpassform").hide();
					$(".forgotwraper").append("<p style='text-align:center;'>Email sent to treasury office regarding your request for password reset.</p>");
					alert("Email sent to treasury office regarding your request for password reset.");
					window.location.reload();
				}
				else {

					$(".forgotpassform").hide();
					$(".forgotwraper").append("<p style='text-align:center;'>Email sent!.</p><p style='text-align:center;'>Please check your email.</p>");
					alert("Email sent to your registered email id.Please check your email.");
					window.location.reload();
				}

			});

		}
	}

	$scope.closeforgetbox = function() {

		$scope.showloaderforgotpass = false;
	}

});


app.controller('QueryController',function($scope,$http,$state,Logging,Commas,Dates){
	$scope.Dates=Dates;
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.query={};
	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/query_list',
	}).success(function(result){
		$scope.showloader=false;
		if(result[0]=='invalid')
		{
			Logging.logout();
		}
		else
		{
			$scope.querylist=result;
			var counter=1;
			angular.forEach($scope.querylist,function(qy){
				qy.sno=counter;
				counter++;
				if(qy.resolveflag=='1')
				{
					qy.rems='Resolved';
				}
				else
				{
					qy.rems='Pending';
				}
				if(!(qy.remarks))
				{
					qy.remarks='None';
				}
				if(!(qy.resolve_date))
				{
					qy.resolve_date='None';
				}
				else
				{
					qy.resolve_date=Dates.getDate(qy.resolve_date);
				}
			});	
		}
	});
	$scope.query_submit=function(){
		if(!$scope.query.name||$scope.query.name=='')
		{
			alert('Please enter your name');
		}
		else if(!$scope.query.phone||$scope.query.phone=='')
		{
			alert('Please enter your phone no');
		}
		else if(!$scope.query.subject||$scope.query.subject=='')
		{
			alert('Please enter a subject for your query');
		}
		else if(!$scope.query.qy||$scope.query.qy=='')
		{
			alert('Please enter your query');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/submit_query',
				data:{dat:$scope.query}
			}).success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					alert("Your Query has been Submitted to the Admin Team! We will try to resolve it with in the next 2 working days! Thank You!");
					location.reload();
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});

app.controller('PasswordController',function($scope,$http,$state,Logging,Commas){
	$scope.$emit("changeTitle",$state.current.views.content.data.title);
	$scope.pass_change=function(){
		if(!$scope.pass)
		{
			alert('Please enter your old password');
		}
		else if(!$scope.pass.old)
		{
			alert('Please enter your old password');
		}
		else if(!$scope.pass.new)
		{
			alert('Please enter your new password');
		}
		else if(!$scope.pass.conf)
		{
			alert('Please confirm your new password');
		}
		else if($scope.pass.new!=$scope.pass.conf)
		{
			alert('Your passwords do not match');
		}
		else if($scope.pass.new==$scope.pass.old)
		{
			alert('Your old and new passwords are same');
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'POST',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/change_pass',
				data:{dat:$scope.pass}
			}).success(function(result){
				$scope.showloader=false;
				if(result[0]=='success')
				{
					if(result[1]=='success')
					{
						alert("Password successfully changed");
						location.reload();
					}
					else
					{
						alert("The old password you have entered is wrong");
					}
				}
				else
				{
					Logging.logout();
				}
			});
		}
	}
});
