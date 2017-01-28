app.controller("AgController",function($scope,$http,$state,$rootScope,Logging,Commas){
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
				if(result[0]=='5')
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

app.controller("AgMainController",function($scope,$http,$state,$rootScope,Logging,Commas){
	
});

app.controller("AgHoaController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.alltrans=[];

	$(document).ready(function(){
		$('#d_from').datepicker({dateFormat: 'dd-mm-yy'});
		$('#d_to').datepicker({dateFormat: 'dd-mm-yy'});
	});

	function compareDate(str1){
		var dt1=parseInt(str1.substring(0,2));
		var mon1=parseInt(str1.substring(3,5));
		var yr1=parseInt(str1.substring(6,10));
		var date1=new Date(yr1, mon1-1, dt1);
		return date1;
	}

	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.showloader=true;
	$http({
		method:'GET',
		headers:{'X-CSRFToken':localStorage.token},
		url:$scope.requesturl+'/get_schemelist'
	}).
	success(function(result){
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

	$scope.hoa_change=function(){
		$scope.arealist=[];
		$scope.ddolist=[];
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_arealist',
			params:{hoa:$scope.hoa.hoa}
		}).
		success(function(result){
			$scope.showloader=false;
			$scope.arealist=result;
		});
	}

	$scope.area_change=function(){
		$scope.ddolist=[];
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_ddos',
			params:{hoa:$scope.hoa.hoa,area:$scope.area.areacode}
		}).
		success(function(result){
			$scope.showloader=false;
			$scope.ddolist=result;
		});
	}

	$scope.submit=function(){
		if(!$scope.pdadmin)
		{
			alert('Please select a pdadmin');
		}
		else if(!$scope.hoa)
		{
			alert('Please select Head of Account');
		}
		else if(compareDate($('#d_from').val())>compareDate($('#d_to').val()))
		{
			alert("Please enter correct date");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ac_trans',
				params:{ddocode:$scope.pdadmin.ddocode,hoa:$scope.pdadmin.hoa,dfrom:$('#d_from').val(),dto:$('#d_to').val()}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result.length==0)
				{
					alert('There are no transactions');
				}
				$scope.alltrans=result;
			});
		}
	}	
});


app.controller("AgDDoController",function($scope,$http,$state,$rootScope,Dates,Logging,Commas){
	$scope.Dates=Dates;
	$scope.alltrans=[];
	$(document).ready(function(){
		$('#d_from').datepicker({dateFormat: 'dd-mm-yy'});
		$('#d_to').datepicker({dateFormat: 'dd-mm-yy'});
	});

	function compareDate(str1){
		var dt1=parseInt(str1.substring(0,2));
		var mon1=parseInt(str1.substring(3,5));
		var yr1=parseInt(str1.substring(6,10));
		var date1=new Date(yr1, mon1-1, dt1);
		return date1;
	}

	$scope.$emit("changeTitle",$state.current.views.content.data.title);

	$scope.admin_change=function()
	{
		$scope.showloader=true;
		$http({
			method:'GET',
			headers:{'X-CSRFToken':localStorage.token},
			url:$scope.requesturl+'/get_hoalist',
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
				$scope.schemelist=result;
			}
			if(result.length==0)
			{
				alert("Please enter correct DDO code");
			}
		});
	}
		

	$scope.submit=function(){
		if(!$scope.pdadmin)
		{
			alert('Please select a pdadmin');
		}
		else if(!$scope.hoa)
		{
			alert('Please select Head of Account');
		}
		else if(compareDate($('#d_from').val())>compareDate($('#d_to').val()))
		{
			alert("Please enter correct date");
		}
		else
		{
			$scope.showloader=true;
			$http({
				method:'GET',
				headers:{'X-CSRFToken':localStorage.token},
				url:$scope.requesturl+'/get_ac_trans',
				params:{ddocode:$scope.pdadmin,hoa:$scope.hoa.hoa,dfrom:$('#d_from').val(),dto:$('#d_to').val()}
			}).
			success(function(result){
				$scope.showloader=false;
				if(result.length==0)
				{
					alert('There are no transactions');
				}
				$scope.alltrans=result;
			});
		}
	}	
});