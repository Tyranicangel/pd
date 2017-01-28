var app=angular.module("PdApp",["ui.router"]);

app.service('Uploader', function($http) {
	this.upload = function(url, file) {
		//var deferred = $q.defer();
		var formdata = new FormData();
		formdata.append('file', file);
		$http({
			method: 'POST',
			url: url,
			data: formdata,
			headers: {'Content-Type': undefined,
			'X-CSRFToken':localStorage.token},
			transformRequest: function(data) {return data;}
		}).
		success(function(data,status,headers,config){

		});
	};
});

app.factory('Dates',function(){
	return{
		getDate:function(str1)
		{
			if(!str1)
			{
				return "";
			}
			else
			{
				var dt1=str1.substring(8,10);
				var mon1=str1.substring(5,7);
				var yr1=str1.substring(0,4);
				return dt1+'/'+mon1+'/'+yr1;
			}
		}
	}
});

app.service('Commas',function(){
	this.getcomma=function(nums){
		if(nums)
		{
		var num1=nums.toString();
		if(num1.length>7)
		{
			numstart=num1.substr(0,num1.length-7);
			numstart=numstart+',';
			num=num1.substr(-7);
		}
		else
		{
			num=num1;
			numstart="";
		}
		if(num.length>4)
		{
			num1=num.substr(0,num.length-3);
			if(num1.length%2==0)
			{
				var num2 = num1.match(/(.{1,2})/g);
				num2.push(num.substr(-3));
				fin=num2.join();
			}
			else
			{
				var num2=num1.substr(1);
				var num3 = num2.match(/(.{1,2})/g);
				num3.push(num.substr(-3));
				fin=num3.join();
				fin=num.substr(0,1)+','+fin;
			}
		}
		else
		{
			fin=num;
		}
		return numstart+fin;
		}
		else
		{
			return " ";
		}
	}
});

app.service('Logging',function($state){
	this.logout=function(){
		localStorage.token="";
		$state.go('home.main');
	}
});

app.directive('fileChange', function() {
	return {
		restrict: 'A',
		link: function(scope, element, attrs) {
			element.bind('change', function() {
				scope.$apply(function() {
				scope[attrs['fileChange']](element[0].files);
				});
			});
		},
	}
});

app.config(function($stateProvider,$urlRouterProvider){
	//alert(123);
	$urlRouterProvider.otherwise("/home");

	$stateProvider.
		state('home',{
			views:{
				"main":{
					templateUrl:"partials/home.html"
				}
			}
		}).
		state('home.main',{
			url: '/home',
			views:{
				"content":{
					templateUrl:"partials/home/main.html",
					data:{title:'Home'},
					controller:'HomeController'
				}
			}
		}).
		state('home.faq',{
			url: '/faq',
			views:{
				"content":{
					templateUrl:"partials/home/faq.html",
					data:{title:'LATEST NEWS'},
					controller:'HomeController'
				}
			}
		}).
		state('home.contact',{
			url: '/contact',
			views:{
				"content":{
					templateUrl:"partials/home/contact.html",
					data:{title:'Contact'},
					controller:'HomeController'
				}
			}
		}).
		state('opening',{
			url: '/pdadmin/start',
			views:{
				"main":{
					templateUrl:"partials/opening.html",
					controller:'AdminStartController'
				}
			}
		}).
		state('lapopening',{
			url: '/pdadmin/lapstart',
			views:{
				"main":{
					templateUrl:"partials/lapopening.html",
					controller:'AdminLapStartController'
				}
			}
		}).
		state('admin',{
			views:{
				"main":{
					templateUrl:"partials/pdadmin.html",
					data:{title:'Home'},
					controller:'AdminController'
				}
			}
		}).
		state('adminchecker',{
			views:{
				"main":{
					templateUrl:"partials/pdadminchecker.html",
					data:{title:'Home'},
					controller:'AdminCheckerController'
				}
			}
		}).
		state('contactdet',{
			url: '/pdadmin/contactdetails',
			views:{
				"main":{
					templateUrl:"partials/contactdetails.html",
					controller:'AdminContactController'
				}
			}
		}).
		state('admin.main',{
			url: '/pdadmin/main',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/main.html",
					data:{title:'Home'},
					controller:'AdminMainController'
				}
			}
		}).
		state('adminchecker.main',{
			url: '/pdadminchecker/main',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/main.html",
					data:{title:'Home'},
					controller:'AdminCheckerMainController'
				}
			}
		}).
		state('admin.query',{
			url: '/pdadmin/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('adminchecker.query',{
			url: '/adminchecker/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('admin.request',{
			url: '/pdadmin/request',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/request.html",
					data:{title:'Request'},
					controller:'AdminRequestController'
				}
			}
		}).
		state('admin.loc',{
			url: '/pdadmin/loc',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/loc.html",
					data:{title:'LOC'},
					controller:'AdminLocController'
				}
			}
		}).
		state('admin.locrpt',{
			url: '/pdadmin/locreport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/issuedloc.html",
					data:{title:'LOC Report'},
					controller:'AdminLocRptController'
				}
			}
		}).
		state('adminchecker.locrpt',{
			url: '/adminchecker/locreport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/issuedloc.html",
					data:{title:'LOC Report'},
					controller:'AdminLocRptController'
				}
			}
		}).
		state('admin.reqrpt',{
			url: '/pdadmin/requestreport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/reqrpt.html",
					data:{title:'Cheque Book Request Report'},
					controller:'AdminReqRptController'
				}
			}
		}).
		state('adminchecker.reqrpt',{
			url: '/adminchecker/requestreport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/reqrpt.html",
					data:{title:'Cheque Book Request Report'},
					controller:'AdminReqRptController'
				}
			}
		}).
		state('admin.chqrpt',{
			url: '/pdadmin/chequereport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'AdminChqRptController'
				}
			}
		}).
		state('adminchecker.chqrpt',{
			url: '/adminchecker/chequereport',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'AdminChqRptController'
				}
			}
		}).
		state('admin.cancel',{
			url: '/pdadmin/cancel',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/cancel.html",
					data:{title:'Cancel Cheque'},
					controller:'AdminCancelController'
				}
			}
		}).
		state('admin.cheque',{
			url: '/pdadmin/cheque',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/cheque.html",
					data:{title:'Issue Cheque'},
					controller:'AdminChequeController'
				}
			}
		}).
		state('admin.booklist',{
			url: '/pdadmin/passbooklist',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/booklist.html",
					data:{title:'My PassBooks'},
					controller:'AdminBookController'
				}
			}
		}).
		state('admin.aclist',{
			url: '/pdadmin/accountlist',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/aclist.html",
					data:{title:'Accounts List'},
					controller:'AdminBookController'
				}
			}
		}).
		state('adminchecker.aclist',{
			url: '/adminchecker/accountlist',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/aclist.html",
					data:{title:'Accounts List'},
					controller:'AdminBookController'
				}
			}
		}).
		state('admin.activate',{
			url: '/pdadmin/confirmaccount',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/activate.html",
					data:{title:'Confirm Accounts'},
					controller:'AdminActivateController'
				}
			}
		}).
		state('admin.book',{
			url: '/pdadmin/passbook/:account/page/:page',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/book.html",
					data:{title:'My PassBook'},
					controller:'AdminPageController'
				}
			}
		}).
		state('admin.statement',{
			url: '/pdadmin/statement/:account',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/statement.html",
					data:{title:'Account Statement'},
					controller:'AdminStatementController'
				}
			}
		}).
		state('adminchecker.statement',{
			url: '/adminchecker/statement/:account',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/statement.html",
					data:{title:'Account Statement'},
					controller:'AdminStatementController'
				}
			}
		}).
		state('admin.password',{
			url: '/pdadmin/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('adminchecker.password',{
			url: '/adminchecker/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('sa',{
			views:{
				"main":{
					templateUrl:"partials/sa.html",
					data:{title:'Home'},
					controller:'SaController'
				}
			}
		}).
		state('dsa',{
			views:{
				"main":{
					templateUrl:"partials/dsa.html",
					data:{title:'Home'},
					controller:'DsaController'
				}
			}
		}).
		state('sa.query',{
			url: '/sa/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('dsa.query',{
			url: '/dsa/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('sa.locrpt',{
			url: '/sa/locreport',
			views:{
				"content":{
					templateUrl:"partials/sa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('dsa.locrpt',{
			url: '/dsa/locreport',
			views:{
				"content":{
					templateUrl:"partials/dsa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('dsa.reqrpt',{
			url: '/dsa/requestreport',
			views:{
				"content":{
					templateUrl:"partials/dsa/reqrpt.html",
					data:{title:'Cheque Book Request Report'},
					controller:'ReqRptController'
				}
			}
		}).
		state('sa.chqrpt',{
			url: '/sa/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('dsa.chqrpt',{
			url: '/dsa/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('sa.main',{
			url: '/sa/main',
			views:{
				"content":{
					templateUrl:"partials/sa/main.html",
					data:{title:'Home'},
					controller:'SaMainController'
				}
			}
		}).
		state('dsa.main',{
			url: '/dsa/main',
			views:{
				"content":{
					templateUrl:"partials/dsa/main.html",
					data:{title:'Home'},
					controller:'DsaMainController'
				}
			}
		}).
		state('sa.request',{
			url: '/sa/request',
			views:{
				"content":{
					templateUrl:"partials/sa/request.html",
					data:{title:'Requests'},
					controller:'SaRequestController'
				}
			}
		}).
		state('dsa.request',{
			url: '/dsa/request',
			views:{
				"content":{
					templateUrl:"partials/dsa/request.html",
					data:{title:'Requests'},
					controller:'DsaRequestController'
				}
			}
		}).
		state('adminchecker.request',{
			url: '/adminchecker/request',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/request.html",
					data:{title:'Requests'},
					controller:'AdminCheckerRequestController'
				}
			}
		}).
		state('sa.loclist',{
			url: '/sa/loclist',
			views:{
				"content":{
					templateUrl:"partials/sa/loclist.html",
					data:{title:'Requests'},
					controller:'SaLoclistController'
				}
			}
		}).
		state('dsa.loclist',{
			url: '/dsa/loclist',
			views:{
				"content":{
					templateUrl:"partials/dsa/loclist.html",
					data:{title:'LOC Requests'},
					controller:'DsaLoclistController'
				}
			}
		}).
		state('adminchecker.loclist',{
			url: '/adminchecker/loclist',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/loclist.html",
					data:{title:'LOC Requests'},
					controller:'AdminCheckerLoclistController'
				}
			}
		}).
		state('sa.create',{
			url: '/sa/create',
			views:{
				"content":{
					templateUrl:"partials/sa/create.html",
					data:{title:'Create Account'},
					controller:'SaCreateController'
				}
			}
		}).
		state('dsa.create',{
			url: '/dsa/create',
			views:{
				"content":{
					templateUrl:"partials/dsa/create.html",
					data:{title:'Create Account'},
					controller:'DsaCreateController'
				}
			}
		}).
		state('sa.adjust',{
			url: '/sa/adjust',
			views:{
				"content":{
					templateUrl:"partials/sa/adjust.html",
					data:{title:'Adjustment'},
					controller:'SaAdjustController'
				}
			}
		}).
		state('sa.statement',{
			url: '/sa/statement',
			views:{
				"content":{
					templateUrl:"partials/sa/statement.html",
					data:{title:'Statement'},
					controller:'SaStatementController'
				}
			}
		}).
		state('dsa.statement',{
			url: '/dsa/statement',
			views:{
				"content":{
					templateUrl:"partials/dsa/statement.html",
					data:{title:'Statement'},
					controller:'DsaStatementController'
				}
			}
		}).
		state('sa.ledger',{
			url: '/sa/ledger',
			views:{
				"content":{
					templateUrl:"partials/sa/ledger.html",
					data:{title:'Ledgers'},
					controller:'SaLedgerController'
				}
			}
		}).
		state('sa.book',{
			url: '/sa/ledgerbook/:account/page/:page',
			views:{
				"content":{
					templateUrl:"partials/sa/ledgerpage.html",
					data:{title:'My Ledger Book'},
					controller:'SaPageController'
				}
			}
		}).
		state('sa.activate',{
			url: '/sa/confirmaccount',
			views:{
				"content":{
					templateUrl:"partials/sa/activate.html",
					data:{title:'Confirm Accounts'},
					controller:'SaActivateController'
				}
			}
		}).
		state('sa.confirm',{
			url: '/sa/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/sa/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'SaConfirmController'
				}
			}
		}).
		state('dsa.confirm',{
			url: '/dsa/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/dsa/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'DsaConfirmController'
				}
			}
		}).
		state('adminchecker.confirm',{
			url: '/adminchecker/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'AdminCheckerConfirmController'
				}
			}
		}).
		state('sa.trans',{
			url: '/sa/lists',
			views:{
				"content":{
					templateUrl:"partials/sa/trans.html",
					data:{title:'Cheque List'},
					controller:'SaTransController'
				}
			}
		}).
		state('dsa.trans',{
			url: '/dsa/lists',
			views:{
				"content":{
					templateUrl:"partials/dsa/trans.html",
					data:{title:'Cheque List'},
					controller:'DsaTransController'
				}
			}
		}).
		state('adminchecker.trans',{
			url: '/adminchecker/lists',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/trans.html",
					data:{title:'Cheque List'},
					controller:'AdminCheckerTransController'
				}
			}
		}).
		state('sa.cheque',{
			url: '/sa/cheque/:requester',
			views:{
				"content":{
					templateUrl:"partials/sa/cheque.html",
					data:{title:'Issue Cheque'},
					controller:'SaChequeController'
				}
			}
		}).
		state('dsa.cheque',{
			url: '/dsa/cheque/:requester',
			views:{
				"content":{
					templateUrl:"partials/dsa/cheque.html",
					data:{title:'Issue Cheque Book'},
					controller:'DsaChequeController'
				}
			}
		}).
		state('adminchecker.cheque',{
			url: '/adminchecker/cheque/:requester',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/cheque.html",
					data:{title:'Approve Cheque Book Request'},
					controller:'AdminCheckerChequeController'
				}
			}
		}).
		state('sa.loc',{
			url: '/sa/loc/:requester/:requesthoa',
			views:{
				"content":{
					templateUrl:"partials/sa/loc.html",
					data:{title:'Issue LOC'},
					controller:'SaLocController'
				}
			}
		}).
		state('dsa.loc',{
			url: '/dsa/loc/:requester/:requesthoa',
			views:{
				"content":{
					templateUrl:"partials/dsa/loc.html",
					data:{title:'Issue LOC'},
					controller:'DsaLocController'
				}
			}
		}).
		state('adminchecker.loc',{
			url: '/adminchecker/loc/:requester/:requesthoa',
			views:{
				"content":{
					templateUrl:"partials/pdadminchecker/loc.html",
					data:{title:'Issue LOC'},
					controller:'AdminCheckerLocController'
				}
			}
		}).
		state('sa.password',{
			url: '/sa/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('dsa.password',{
			url: '/dsa/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('dto',{
			views:{
				"main":{
					templateUrl:"partials/dto.html",
					data:{title:'Home'},
					controller:'DtoController'
				}
			}
		}).
		state('ato',{
			views:{
				"main":{
					templateUrl:"partials/ato.html",
					data:{title:'Home'},
					controller:'AtoController'
				}
			}
		}).
		state('sto',{
			views:{
				"main":{
					templateUrl:"partials/sto.html",
					data:{title:'Home'},
					controller:'StoController'
				}
			}
		}).
		state('dto.query',{
			url: '/dto/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('sto.query',{
			url: '/sto/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('ato.query',{
			url: '/ato/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('dto.editddo',{
                        url: '/dto/editddo',
                        views:{
                                "content":{
                                        templateUrl:"partials/dto/editddo.html",
                                        data:{title:'Edit DDO'},
                                        controller:'EditDDoController'
                                }
                        }
                }).
		state('dto.statement',{
			url: '/dto/statement',
			views:{
				"content":{
					templateUrl:"partials/sa/statement.html",
					data:{title:'Statement'},
					controller:'DtoStatementController'
				}
			}
		}).
		state('sto.statement',{
			url: '/sto/statement',
			views:{
				"content":{
					templateUrl:"partials/sa/statement.html",
					data:{title:'Statement'},
					controller:'StoStatementController'
				}
			}
		}).
		state('ato.statement',{
			url: '/ato/statement',
			views:{
				"content":{
					templateUrl:"partials/sa/statement.html",
					data:{title:'Statement'},
					controller:'AtoStatementController'
				}
			}
		}).
		state('dto.locrpt',{
			url: '/dto/locreport',
			views:{
				"content":{
					templateUrl:"partials/sa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('sto.locrpt',{
			url: '/sto/locreport',
			views:{
				"content":{
					templateUrl:"partials/sa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('ato.locrpt',{
			url: '/ato/locreport',
			views:{
				"content":{
					templateUrl:"partials/sa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('dto.reqrpt',{
			url: '/dto/requestreport',
			views:{
				"content":{
					templateUrl:"partials/sa/reqrpt.html",
					data:{title:'Request Report'},
					controller:'ReqRptController'
				}
			}
		}).
		state('sto.reqrpt',{
			url: '/sto/requestreport',
			views:{
				"content":{
					templateUrl:"partials/sa/reqrpt.html",
					data:{title:'Request Report'},
					controller:'ReqRptController'
				}
			}
		}).
		state('ato.reqrpt',{
			url: '/ato/requestreport',
			views:{
				"content":{
					templateUrl:"partials/sa/reqrpt.html",
					data:{title:'Request Report'},
					controller:'ReqRptController'
				}
			}
		}).
		state('dto.chqrpt',{
			url: '/dto/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('sto.chqrpt',{
			url: '/sto/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('ato.chqrpt',{
			url: '/ato/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('dto.main',{
			url: '/dto/main',
			views:{
				"content":{
					templateUrl:"partials/dto/main.html",
					data:{title:'Home'},
					controller:'DtoMainController'
				}
			}
		}).
		state('sto.main',{
			url: '/sto/main',
			views:{
				"content":{
					templateUrl:"partials/sto/main.html",
					data:{title:'Home'},
					controller:'StoMainController'
				}
			}
		}).
		state('ato.main',{
			url: '/ato/main',
			views:{
				"content":{
					templateUrl:"partials/ato/main.html",
					data:{title:'Home'},
					controller:'AtoMainController'
				}
			}
		}).
		state('dto.request',{
			url: '/dto/request',
			views:{
				"content":{
					templateUrl:"partials/dto/request.html",
					data:{title:'Requests'},
					controller:'DtoRequestController'
				}
			}
		}).
		state('sto.request',{
			url: '/sto/request',
			views:{
				"content":{
					templateUrl:"partials/sto/request.html",
					data:{title:'Requests'},
					controller:'StoRequestController'
				}
			}
		}).
		state('ato.request',{
			url: '/ato/request',
			views:{
				"content":{
					templateUrl:"partials/ato/request.html",
					data:{title:'Requests'},
					controller:'AtoRequestController'
				}
			}
		}).
		state('dto.loclist',{
			url: '/dto/loclist',
			views:{
				"content":{
					templateUrl:"partials/dto/loclist.html",
					data:{title:'Requests'},
					controller:'DtoLoclistController'
				}
			}
		}).
		state('sto.loclist',{
			url: '/sto/loclist',
			views:{
				"content":{
					templateUrl:"partials/sto/loclist.html",
					data:{title:'Requests'},
					controller:'StoLoclistController'
				}
			}
		}).
		state('ato.loclist',{
			url: '/ato/loclist',
			views:{
				"content":{
					templateUrl:"partials/ato/loclist.html",
					data:{title:'Requests'},
					controller:'AtoLoclistController'
				}
			}
		}).
		state('dto.create',{
			url: '/dto/create',
			views:{
				"content":{
					templateUrl:"partials/dto/create.html",
					data:{title:'Create Account'},
					controller:'DtoCreateController'
				}
			}
		}).
		state('dto.adjust',{
			url: '/dto/adjust',
			views:{
				"content":{
					templateUrl:"partials/dto/adjust.html",
					data:{title:'Adjustment'},
					controller:'DtoAdjustController'
				}
			}
		}).
		state('dto.ledger',{
			url: '/dto/ledger',
			views:{
				"content":{
					templateUrl:"partials/dto/ledger.html",
					data:{title:'Ledgers'},
					controller:'DtoLedgerController'
				}
			}
		}).
		state('dto.book',{
			url: '/dto/ledgerbook/:account/page/:page',
			views:{
				"content":{
					templateUrl:"partials/dto/ledgerpage.html",
					data:{title:'My Ledger Book'},
					controller:'DtoPageController'
				}
			}
		}).
		state('dto.activate',{
			url: '/dto/confirmaccount',
			views:{
				"content":{
					templateUrl:"partials/dto/activate.html",
					data:{title:'Confirm Accounts'},
					controller:'DtoActivateController'
				}
			}
		}).
		state('dto.confirm',{
			url: '/dto/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/dto/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'DtoConfirmController'
				}
			}
		}).
		state('dto.trans',{
			url: '/dto/lists',
			views:{
				"content":{
					templateUrl:"partials/dto/trans.html",
					data:{title:'Cheque List'},
					controller:'DtoTransController'
				}
			}
		}).
		state('sto.trans',{
			url: '/sto/lists',
			views:{
				"content":{
					templateUrl:"partials/sto/trans.html",
					data:{title:'Cheque List'},
					controller:'StoTransController'
				}
			}
		}).
		state('ato.trans',{
			url: '/ato/lists',
			views:{
				"content":{
					templateUrl:"partials/ato/trans.html",
					data:{title:'Cheque List'},
					controller:'AtoTransController'
				}
			}
		}).
		state('dto.cheque',{
			url: '/dto/cheque/:requester',
			views:{
				"content":{
					templateUrl:"partials/dto/cheque.html",
					data:{title:'Issue Cheque'},
					controller:'DtoChequeController'
				}
			}
		}).
		state('dto.loc',{
			url: '/dto/loc/:requester/:requesthoa',
			views:{
				"content":{
					templateUrl:"partials/dto/loc.html",
					data:{title:'Issue LOC'},
					controller:'DtoLocController'
				}
			}
		}).
		state('dto.addinventory',{
			url: '/dto/addinventory',
			views:{
				"content":{
					templateUrl:"partials/dto/addinventory.html",
					data:{title:'Add Inventory'},
					controller:'DtoaddinventController'
				}
			}
		}).
		state('dto.viewinventory',{
			url: '/dto/viewinventory',
			views:{
				"content":{
					templateUrl:"partials/dto/viewinventory.html",
					data:{title:'View Inventory'},
					controller:'DtoviewinventController'
				}
			}
		}).
		state('dto.password',{
			url: '/dto/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('sto.password',{
			url: '/sto/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('ato.password',{
			url: '/ato/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('bank',{
			views:{
				"main":{
					templateUrl:"partials/bank.html",
					data:{title:'Home'},
					controller:'BankController'
				}
			}
		}).
		state('bank.chqrpt',{
			url: '/bank/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('bank.conflist',{
			url: '/bank/confirmedchequesreport',
			views:{
				"content":{
					templateUrl:"partials/bank/confirmed_cheques.html",
					data:{title:'Confirmed Cheques Report'},
					controller:'ConfirmedChqController'
				}
			}
		}).
		state('bank.chqstatus',{
			url: '/bank/chequestatusreport',
			views:{
				"content":{
					templateUrl:"partials/bank/cheque_status_report.html",
					data:{title:'Cheque status Report'},
					controller:'ChqStatusController'
				}
			}
		}).
		state('bank.manualchq',{
			url: '/bank/manualchq',
			views:{
				"content":{
					templateUrl:"partials/bank/manual_chq.html",
					data:{title:'Cheque status Report'},
					controller:'ManualChqController'
				}
			}
		}).
		state('bank.query',{
			url: '/bank/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('bank.main',{
			url: '/bank/main',
			views:{
				"content":{
					templateUrl:"partials/bank/main.html",
					data:{title:'Home'},
					controller:'BankMainController'
				}
			}
		}).
		state('bank.trans',{
			url: '/bank/lists',
			views:{
				"content":{
					templateUrl:"partials/bank/trans.html",
					data:{title:'Cheque List'},
					controller:'BankTransController'
				}
			}
		}).
		state('bank.confirm',{
			url: '/bank/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/bank/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'BankConfirmController'
				}
			}
		}).
		state('bank.recipts',{
			url: '/bank/recipts', 
			views:{
				"content":{
					templateUrl:"partials/bank/reclist.html",
					data:{title:'Cheque List'},
					controller:'BankRecptController'
				}
			}
		}).
		state('bank.accept',{
			url: '/bank/accept/:transaction',
			views:{
				"content":{
					templateUrl:"partials/bank/recconfirm.html",
					data:{title:'Confirm Cheque'},
					controller:'BankAcceptController'
				}
			}
		}).
		state('bank.password',{
			url: '/bank/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('govt',{
			views:{
				"main":{
					templateUrl:"partials/govt.html",
					data:{title:'Home'},
					controller:'GovtController'
				}
			}
		}).
		state('govt.main',{
			url: '/govt/main',
			views:{
				"content":{
					templateUrl:"partials/govt/main.html",
					data:{title:'Home'},
					controller:'GovtMainController'
				}
			}
		}).
		state('govt.trans',{
			url: '/govt/lists',
			views:{
				"content":{
					templateUrl:"partials/govt/trans.html",
					data:{title:'Cheque List'},
					controller:'GovtTransController'
				}
			}
		}).
		state('govt.chqconflist',{
			url: '/govt/confirmedchequeslist',
			views:{
				"content":{
					templateUrl:"partials/govt/conflist.html",
					data:{title:'Confirmed Cheques List'},
					controller:'GovtConfController'
				}
			}
		}).
		state('govt.confirm',{
			url: '/govt/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/govt/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'GovtConfirmController'
				}
			}
		}).
		state('govtif.confirm',{
			url: '/govtif/confirm/:transaction',
			views:{
				"content":{
					templateUrl:"partials/govt/confirm.html",
					data:{title:'Confirm Cheque'},
					controller:'GovtIfConfirmController'
				}
			}
		}).
		state('govtif.chqconflist',{
			url: '/govtif/confirmedchequeslist',
			views:{
				"content":{
					templateUrl:"partials/govtif/conflist.html",
					data:{title:'Confirmed Cheques List'},
					controller:'GovtConfController'
				}
			}
		}).
		state('govt.password',{
			url: '/govt/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('ag',{
			views:{
				"main":{
					templateUrl:"partials/ag.html",
					data:{title:'Home'},
					controller:'AgController'
				}
			}
		}).
		state('ag.main',{
			url: '/ag/main',
			views:{
				"content":{
					templateUrl:"partials/ag/main.html",
					data:{title:'Home'},
					controller:'AgMainController'
				}
			}
		}).
		state('ag.hoa',{
			url: '/ag/hoa',
			views:{
				"content":{
					templateUrl:"partials/ag/hoawise.html",
					data:{title:'Transactions List'},
					controller:'AgHoaController'
				}
			}
		}).
		state('ag.ddo',{
			url: '/ag/pdadmin',
			views:{
				"content":{
					templateUrl:"partials/ag/ddowise.html",
					data:{title:'Transactions List'},
					controller:'AgDDoController'
				}
			}
		}).
		state('ag.password',{
			url: '/ag/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('govtif',{
			views:{
				"main":{
					templateUrl:"partials/govtif.html",
					data:{title:'Home'},
					controller:'GovtIfController'
				}
			}
		}).
		state('govtif.main',{
			url: '/govtif/main',
			views:{
				"content":{
					templateUrl:"partials/govtif/main.html",
					data:{title:'Home'},
					controller:'GovtIfMainController'
				}
			}
		}).
		state('govtif.allacslist',{
			url: '/govtif/listofallaccounts',
			views:{
				"content":{
					templateUrl:"partials/govtif/allaccounts.html",
					data:{title:'All Accounts Report'},
					controller:'GovtIfMainController'
				}
			}
		}).
		state('govt.allacslist',{
			url: '/govt/listofallaccounts',
			views:{
				"content":{
					templateUrl:"partials/govtif/allaccounts.html",
					data:{title:'All Accounts Report'},
					controller:'GovtIfMainController'
				}
			}
		}).
		state('govtif.cheques',{
			url: '/govtif/chequestoapprove',
			views:{
				"content":{
					templateUrl:"partials/govtif/cheques.html",
					data:{title:'Cheques to be confirmed'},
					controller:'GovtIfChequesController'
				}
			}
		}).
		state('govtif.alltrans',{
			url: '/govtif/alltransactions',
			views:{
				"content":{
					templateUrl:"partials/govtif/alltransactions.html",
					data:{title:'All Transactions Report'},
					controller:'GovtIFTransactionsController'
				}
			}
		}).
		state('govtif.password',{
			url: '/govtif/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('govtif.chqrpt',{
			url: '/govtif/chqrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:"AdminBackChqRptController"
				}
			}
		}).
		state('govtif.locrpt',{
			url: '/govtif/locrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/locrpt.html",
					data:{title:'LOC Report'},
					controller:"AdminBackLocRptController"
				}
			}
		}).
		state('dd',{
			views:{
				"main":{
					templateUrl:"partials/dd.html",
					//data:{title:'Home'},
					controller:'DdController'
				}
			}
		}).
		state('dd.main',{
			url: '/dd/main',
			views:{
				"content":{
					templateUrl:"partials/dd/main.html",
					data:{title:'Home'},
					controller:'DdMainController'
				}
			}
		}).
		state('dd.validateuser',{
			url: '/dd/validateuser',
			views:{
				"content":{
					templateUrl:"partials/dd/validateuser.html",
					data:{title:'Validate Accounts'},
					controller:'DdValidateUserController'
				}
			}
		}).
		state('dd.validateuserapproved',{
			url: '/dd/validateuserapproved',
			views:{
				"content":{
					templateUrl:"partials/dd/validateuserapproved.html",
					data:{title:'Approved Accounts'},
					controller:'DdValidateApprovedController'
				}
			}
		}).
		state('dd.validateuserrejected',{
			url: '/dd/validateuserrejected',
			views:{
				"content":{
					templateUrl:"partials/dd/validateuserrejected.html",
					data:{title:'Rejected Accounts'},
					controller:'DdValidateRejectedController'
				}
			}
		}).
		state('dd.request',{
			url: '/dd/request',
			views:{
				"content":{
					templateUrl:"partials/dd/request.html",
					data:{title:'Requests'},
					controller:'DdRequestController'
				}
			}
		}).
		state('dd.loclist',{
			url: '/dd/loclist',
			views:{
				"content":{
					templateUrl:"partials/dd/loclist.html",
					data:{title:'Requests'},
					controller:'DdLoclistController'
				}
			}
		}).
		state('dd.trans',{
			url: '/dd/lists',
			views:{
				"content":{
					templateUrl:"partials/dd/trans.html",
					data:{title:'Cheque List'},
					controller:'DdTransController'
				}
			}
		}).
		state('dd.statement',{
			url: '/dd/statement',
			views:{
				"content":{
					templateUrl:"partials/sa/statement.html",
					data:{title:'Statement'},
					controller:'DdStatementController'
				}
			}
		}).
		state('dd.locrpt',{
			url: '/dd/locreport',
			views:{
				"content":{
					templateUrl:"partials/dsa/locrpt.html",
					data:{title:'LOC Report'},
					controller:'LocRptController'
				}
			}
		}).
		state('dd.reqrpt',{
			url: '/dd/requestreport',
			views:{
				"content":{
					templateUrl:"partials/sa/reqrpt.html",
					data:{title:'Request Report'},
					controller:'ReqRptController'
				}
			}
		}).
		state('dd.chqrpt',{
			url: '/dd/chequereport',
			views:{
				"content":{
					templateUrl:"partials/sa/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:'ChqRptController'
				}
			}
		}).
		state('dd.addinventory',{
			url: '/dd/addinventory',
			views:{
				"content":{
					templateUrl:"partials/dd/addinventory.html",
					data:{title:'Add Inventory'},
					controller:'DdaddinventController'
				}
			}
		}).
		state('dd.viewinventory',{
			url: '/dd/viewinventory',
			views:{
				"content":{
					templateUrl:"partials/dd/viewinventory.html",
					data:{title:'View Inventory'},
					controller:'DdviewinventController'
				}
			}
		}).
		state('dd.query',{
			url: '/dd/query',
			views:{
				"content":{
					templateUrl:"partials/query.html",
					data:{title:'OQS'},
					controller:'QueryController'
				}
			}
		}).
		state('dd.password',{
			url: '/dd/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('dd.editddo',{
            url: '/dd/editddo',
            views:{
                    "content":{
                            templateUrl:"partials/dd/editddo.html",
                            data:{title:'Edit DDO'},
                            controller:'EditDDoController'
                    }
            }
   		}).
		state('dd.create',{
			url: '/dd/create',
			views:{
				"content":{
					templateUrl:"partials/dd/create.html",
					data:{title:'Create Account'},
					controller:'DdCreateController'
				}
			}
		}).
		state('dd.mapchquser',{
			url: '/dd/mapchequebookuser',
			views:{
				"content":{
					templateUrl:"partials/dd/mapchq.html",
					data:{title:'Map Cheque Book User Account'},
					controller:'DdMapChqController'
				}
			}
		}).
		state('dd.mapauthuser',{
			url: '/dd/mapauthuser',
			views:{
				"content":{
					templateUrl:"partials/dd/chqlocmap.html",
					data:{title:'Map Auth user'},
					controller:'DdMapAuthController'
				}
			}
		}).
		state('dd.createuac',{
			url: '/dd/createuseraccounts',
			views:{
				"content":{
					templateUrl:"partials/dd/createusers.html",
					data:{title:'Create User Account'},
					controller:'DdCreateUsersController'
				}
			}
		}).
		state('dd.edituser',{
			url: '/dd/editusers',
			views:{
				"content":{
					templateUrl:"partials/dd/edit_users.html",
					data:{title:'Edit Users'},
					controller:'DdEditUsersController'
				}
			}
		}).
		state('dd.manageuac',{
			views:{
				"content":{
					templateUrl:"partials/dd/manageusers.html",
					controller:'DdManageUsersController'
				}
			}
		}).
		state('dd.manageuac.hoatosa',{
			url: '/dd/maphoatosa',
			views:{
				"manageusers":{
					templateUrl:"partials/dd/hoatosa.html",
					data:{title:'Map Users'},
					controller:'DdManageUsersHoatoSaController'
				}
			}
		}).
		state('dd.manageuac.samap',{
			url: '/dd/samapping',
			views:{
				"manageusers":{
					templateUrl:"partials/dd/samap.html",
					data:{title:'Map Users'},
					controller:'DdManageUsersSamapController'
				}
			}
		}).
		state('dd.manageuac.stomap',{
			url: '/dd/stomapping',
			views:{
				"manageusers":{
					templateUrl:"partials/dd/stomap.html",
					data:{title:'Map Users'},
					controller:'DdManageUsersStomapController'
				}
			}
		}).
		//state for query admin panel
		state('backadmin',{
			views:{
				"main":{

					templateUrl:"partials/admin/admin_menu.html",
					controller:"AdminBackController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.adminhome',{
			url: '/admins/',
			views:{
				"content":{
					templateUrl:"partials/admin/admin_home.html",
					data:{title:'Admin Home'},
					controller:"AdminBackMainController"
				}
			}
		}).
		state('backadmin.chqrpt',{
			url: '/admins/chqrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/chqrpt.html",
					data:{title:'Cheque Report'},
					controller:"AdminBackChqRptController"
				}
			}
		}).
		state('backadmin.statement',{
			url: '/admins/statement/:account',
			views:{
				"content":{
					templateUrl:"partials/pdadmin/statement.html",
					data:{title:'Account Statement'},
					controller:'AdminBackStatementController'
				}
			}
		}).
		state('backadmin.accstmt',{
			url: '/admins/accountstatement',
			views:{
				"content":{
					templateUrl:"partials/admin/accountstatement.html",
					data:{title:'Account statement'},
					controller:"AdminBackAccStmtController"
				}
			}
		}).
		state('backadmin.currentacnochange',{
			url: '/admins/currentacnochange',
			views:{
				"content":{
					templateUrl:"partials/admin/change_current_acno.html",
					data:{title:'Change Current A/c Number'},
					controller:"AdminBackCurrentAcnoController"
				}
			}
		}).
		state('backadmin.locactivity',{
			url: '/admins/locactivity',
			views:{
				"content":{
					templateUrl:"partials/admin/locactivity.html",
					data:{title:'LOC Activity'},
					controller:"AdminBackActLocController"
				}
			}
		}).
		state('backadmin.chqactivity',{
			url: '/admins/chqactivity',
			views:{
				"content":{
					templateUrl:"partials/admin/chqactivity.html",
					data:{title:'LOC Activity'},
					controller:"AdminBackActChqController"
				}
			}
		}).
		state('backadmin.accrpt',{
			url: '/admins/accrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/approvedrejpenlist.html",
					data:{title:'PD Account status Report'},
					controller:"AdminBackAccRptController"
				}
			}
		}).
		state('backadmin.password',{
			url: '/admins/password',
			views:{
				"content":{
					templateUrl:"partials/change_password.html",
					data:{title:'ChangePassword'},
					controller:'PasswordController'
				}
			}
		}).
		state('backadmin.allacslist',{
			url: '/admins/listofallaccounts',
			views:{
				"content":{
					templateUrl:"partials/admin/allaccounts.html",
					data:{title:'All accounts Report'},
					controller:"AdminBackAllAccountController"
				}
			}
		}).
		state('backadmin.confaclist',{
			url: '/admins/rejectedaccounts',
			views:{
				"content":{
					templateUrl:"partials/admin/rejectaccounts.html",
					data:{title:'Rejected Accounts'},
					controller:"AdminBackRejectedAccountController"
				}
			}
		}).
		state('backadmin.alltrans',{
			url: '/admins/alltransactions',
			views:{
				"content":{
					templateUrl:"partials/admin/alltransactions.html",
					data:{title:'All Transactions Report'},
					controller:"AdminBackAllTransController"
				}
			}
		}).
		state('backadmin.allfaultyacnts',{
			url: '/admins/allfaultyacnts',
			views:{
				"content":{
					templateUrl:"partials/admin/allfaultyacnts.html",
					data:{title:'All Faulty Account Report'},
					controller:"AdminBackAllFaultController"
				}
			}
		}).
		state('backadmin.locrpt',{
			url: '/admins/locrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/locrpt.html",
					data:{title:'LOC Report'},
					controller:"AdminBackLocRptController"
				}
			}
		}).
		state('backadmin.totrpt',{
			url: '/admins/totrpt',
			views:{
				"content":{
					templateUrl:"partials/admin/totrpt.html",
					data:{title:'Total Report'},
					controller:"AdminBackTotRptController"
				}
			}
		}).
		//state for bank add in admin panel
		state('backadmin.addbank',{
			url: '/admins/addbank',
			views:{
				"content":{
					templateUrl:"partials/admin/add_bank.html",
					data:{title:'Add Bank'},
					controller:"AdminBackAddBankController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.adminquery',{
			url: '/admins/querysystem',
			views:{
				"content":{
					templateUrl:"partials/admin/admin_query.html",
					data:{title:'Query System'},
					controller:"AdminBackController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.resetpass',{
			url: '/admins/resetpass',
			views:{
				"content":{
					templateUrl:"partials/admin/admin_reset_pass.html",
					data:{title:'Rest password'},
					controller:"AdminBackController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.singlechq',{
			url: '/admins/singlechq',
			views:{
				"content":{
					templateUrl:"partials/admin/add_delete_singlechq.html",
					data:{title:'Add/delete single cheque'},
					controller:"AdminBackController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.multiplechq',{
			url: '/admins/multiplechq',
			views:{
				"content":{
					templateUrl:"partials/admin/add_delete_multiplechq.html",
					data:{title:'Add/delete multiple cheque'},
					controller:"AdminBackController"
				}
			}
		}).
		state('backadmin.multiplebook',{
			url: '/admins/multiplebook',
			views:{
				"content":{
					templateUrl:"partials/admin/add_del_inventory.html",
					data:{title:'Add/delete Inventory'},
					controller:"AdminBackInventory"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.dbquery',{
			url: '/admins/dbquery',
			views:{
				"content":{
					templateUrl:"partials/admin/dbquery.html",
					data:{title:'Database query'},
					controller:"AdminBackController"
				}
			}
		}).
		//state for query admin panel
		state('backadmin.sapendingloc',{
			url: '/admins/sapendingloc',
			views:{
				"content":{
					templateUrl:"partials/admin/sapendingloc.html",
					data:{title:'LOCs pending with SA'},
					controller:"AdminBackSaPendingLocController"
				}
			}
		}).
		state('backadmin.pendingchqs',{
			url: '/admins/pendingchqs',
			views:{
				"content":{
					templateUrl:"partials/admin/pendingchqs.html",
					data:{title:'Cheques Pending for Approval'},
					controller:"AdminBackChqPendingLocController"
				}
			}
		}).
		state('panchayathq',{
			views:{
				"main":{
					templateUrl:"partials/panchayat.html",
					data:{title:'Home'},
					controller:'PanchayatHQController'
				}
			}
		}).
		state('panchayathq.main',{
			url: '/panchayathq/main',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqmain.html",
					data:{title:'Home'},
					controller:'PanchayatHQMainController'
				}
			}
		}).
		state('panchayathq.statement',{
			url: '/panchayathq/statement',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqstatement.html",
					data:{title:'PR Account Statement'},
					controller:'PanchayatHQStatementController'
				}
			}
		}).
		state('panchayathq.locrpt',{
			url: '/panchayathq/locrpt',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqlocrpt.html",
					data:{title:'PR LOC Report'},
					controller:'PanchayatHQLOCRptController'
				}
			}
		}).
		state('panchayathq.chqrpt',{
			url: '/panchayathq/chqrpt',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqchqrpt.html",
					data:{title:'PR Cheque Report'},
					controller:'PanchayatHQChqRptController'
				}
			}
		}).
		state('panchayathq.allacslist',{
			url: '/panchayathq/listofallaccounts',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqallaccounts.html",
					data:{title:'All accounts Report'},
					controller:"PanchayatHQAllAccountController"
				}
			}
		}).
		state('panchayathq.absacc',{
			url: '/panchayathq/abstractstatement',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqabsaccstatement.html",
					data:{title:'Abstract Account Statement Report'},
					controller:"PanchayatHQAbsStatementController"
				}
			}
		}).
		state('panchayathq.absloc',{
			url: '/panchayathq/abstractlocreport',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqabslocrpt.html",
					data:{title:'Abstract LOC Report'},
					controller:"PanchayatHQAbsLOCController"
				}
			}
		}).
		state('panchayathq.abschq',{
			url: '/panchayathq/abstractchequereport',
			views:{
				"content":{
					templateUrl:"partials/panchayat/hqabschqrpt.html",
					data:{title:'Abstract Cheque Report'},
					controller:"PanchayatHQAbsChqController"
				}
			}
		})
		;
});
