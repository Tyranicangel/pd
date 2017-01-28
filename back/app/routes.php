<?php
Route::get('/', function()
{
	$data = DB::table("pdaccountinfo")->get();
	return $data;
});

Route::get('/getcdate',"MainController@getcdate");

Route::post('/logins',"MainController@login");

Route::post('/forgotpass',"MainController@forgotpass");

Route::get('/get_ifsc',"MainController@get_ifsc");

Route::get('/loc_report',"MainController@loc_report");

Route::get('/req_rpt',"MainController@req_rpt");

Route::get('/chqrpt',"MainController@chqrpt");

Route::get('/get_user_data',"MainController@get_user_data");

Route::get('/govt_data_if',"GovtController@govt_data_if");

Route::post('/govt_chqlist_confirm_if',"GovtController@govt_chqlist_confirm_if");

Route::post('/govt_chqlist_reject_if',"GovtController@govt_chqlist_reject_if");

Route::get('/govt_trans_if',"GovtController@govt_trans_if");

Route::get('/govt_chq_data_if',"GovtController@govt_chq_data_if");

Route::get('/get_user_lap_data',"MainController@get_user_lap_data");

Route::get('/get_statement',"MainController@get_statement");

Route::get('/update_contactdetails',"MainController@update_contactdetails");

Route::post('/update_pdacno',"MainController@update_pdacno");

Route::post('/change_pass',"MainController@change_pass");

Route::get('/update_pass',"MainController@update_pass");

Route::post('/submit_query',"MainController@submit_query");

Route::get('/query_list',"MainController@query_list");

Route::get('/get_types',"MainController@gettype");

Route::post('/uploading',"MainController@uploading");

Route::post('/uploadingresponse',"MainController@uploadingresponse");

Route::post('/fetchdata',"MainController@fetchdata");

Route::post('/uploadbulk',"MainController@uploadbulk");

Route::post('/post_lepexp',"MainController@post_lepexp");

Route::get('/get_areas',"MainController@get_areas");

Route::post('/get_panchayat_hoas',"MainController@get_panchayat_hoas");
Route::post('/get_panchayat_abs_loc_rpt',"MainController@get_panchayat_abs_loc_rpt");
Route::post('/get_panchayat_abs_chq_rpt',"MainController@get_panchayat_abs_chq_rpt");

Route::get('/get_stolist',"MainController@get_stolist");

Route::get('/get_ddolist',"MainController@get_ddolist");

Route::get('/get_ddolist_panchayathq',"MainController@get_ddolist_panchayathq");

Route::get('/get_ddotrans',"MainController@get_ddotrans");

Route::get('/get_ddotransadmin',"AdminBackendController@get_ddotransadmin");

Route::get('/rejectedaccountslist',"AdminBackendController@rejectedaccountslist");

Route::post('/deleterejectedaccountslist',"AdminBackendController@deleterejectedaccountslist");

Route::post('/updaterejectedaccountslist',"AdminBackendController@updaterejectedaccountslist");

Route::post('/sendrejectedaccountslist',"AdminBackendController@sendrejectedaccountslist");

Route::get('/govt_faulty_acnt_dataadmin',"AdminBackendController@govt_faulty_acnt_dataadmin");

Route::get('/getchqpending',"AdminBackendController@getchqpending");

Route::get('/gettryinventory',"AdminBackendController@gettryinventory");

Route::post('/deleteinv',"AdminBackendController@deleteinv");

Route::post('/useinv',"AdminBackendController@useinv");

Route::get('/get_ddoloc',"MainController@get_ddoloc");

Route::get('/get_ddolocadmin',"AdminBackendController@get_ddolocadmin");

Route::post('/post_lepexp_empty',"MainController@post_lepexp_empty");

Route::post('/uploadingpdtopd',"MainController@uploadingpdtopd");

Route::get('/sa_data',"SaController@sa_data");

Route::get('/dsa_data',"DsaController@dsa_data");

Route::get('/get_sa_admins',"SaController@get_sa_admins");

Route::get('/get_dsa_admins',"DsaController@get_dsa_admins");

Route::post('/adjust_single_party',"SaController@adjust_single_party");

Route::post('/adjust_multiple_party',"SaController@adjust_multiple_party");

Route::post('/adjust_recipt',"SaController@adjust_recipt");

Route::get('/get_sa_hoas',"SaController@get_sa_hoas");

Route::get('/get_dsa_hoas',"DsaController@get_dsa_hoas");

Route::get('/get_sa_ledger',"SaController@get_sa_ledger");

Route::get('/get_ledgerdata',"SaController@get_ledgerdata");

Route::get('/get_ledgerpage',"SaController@get_ledgerpage");

Route::get('/get_ledgerpagelist',"SaController@get_ledgerpagelist");

Route::get('/delete_sa_trans',"SaController@delete_sa_trans");

Route::get('/sa_request_data',"SaController@sa_request_data");

Route::get('/dsa_request_data',"DsaController@dsa_request_data");

Route::get('/sa_invent_data',"SaController@sa_invent_data");

Route::get('/dsa_invent_data',"DsaController@dsa_invent_data");

Route::get('/sa_loc_data',"SaController@sa_loc_data");

Route::get('/dsa_loc_data',"DsaController@dsa_loc_data");

Route::get('/confirm_sa_account',"SaController@confirm_sa_account");

Route::post('/accept_request',"SaController@accept_request");

Route::post('/accept_request_dsa',"DsaController@accept_request_dsa");

Route::post('/create_account',"SaController@create_account");

Route::post('/create_account_dsa',"DsaController@create_account_dsa");

Route::post('/accept_loc',"SaController@accept_loc");

Route::post('/accept_loc_dsa',"DsaController@accept_loc_dsa");

Route::get('/sa_requests',"SaController@sa_requests");

Route::get('/dsa_requests',"DsaController@dsa_requests");


Route::get('/sa_book_data',"SaController@sa_book_data");

Route::get('/dsa_book_data',"DsaController@dsa_book_data");

Route::get('/sa_loclist',"SaController@sa_loclist");

Route::get('/dsa_loclist',"DsaController@dsa_loclist");

Route::get('/update_transid',"SaController@update_transid");


Route::get('/update_transid_dsa',"DsaController@update_transid_dsa");

Route::post('/sa_chqlist_confirm',"SaController@sa_chqlist_confirm");

Route::post('/dsa_chqlist_confirm',"DsaController@dsa_chqlist_confirm");

Route::post('/sa_chqlist_reject',"SaController@sa_chqlist_reject");

Route::get('/sa_chq_data',"SaController@sa_chq_data");

Route::get('/dsa_chq_data',"DsaController@dsa_chq_data");

Route::get('/sa_trans',"SaController@sa_trans");

Route::get('/dsa_trans',"DsaController@dsa_trans");

Route::get('/dto_data',"DtoController@dto_data");

Route::get('/sto_data',"StoController@sto_data");

Route::get('/ato_data',"AtoController@ato_data");

Route::get('/dd_data',"DdController@dd_data");

Route::get('/get_dd_user_data',"DdController@get_dd_user_data");

Route::post('/edit_dd_user_data',"DdController@edit_dd_user_data");

Route::get('/get_users_above_sa',"DdController@get_users_above_sa");

Route::get('/get_users_above_sto',"DdController@get_users_above_sto");

Route::post('/post_hoa_map_dets',"DdController@post_hoa_map_dets");

Route::post('/post_sa_map_dets',"DdController@post_sa_map_dets");

Route::post('/post_sto_map_dets',"DdController@post_sto_map_dets");

Route::get('/get_sa_user_list',"DdController@get_sa_user_list");

Route::get('/get_sa_chq_map_list',"DdController@get_sa_chq_map_list");

Route::post('/post_chq_map_sa',"DdController@post_chq_map_sa");

Route::get('/get_sto_user_list',"DdController@get_sto_user_list");

// Route::post('/edit_ddo',"DtoController@edit_ddo");

Route::post('/edit_ddo',"DdController@edit_ddo");

// Route::post('/add_invent',"DtoController@add_invent");

Route::post('/add_invent',"DdController@add_invent");

Route::post('/add_bulk',"DdController@add_bulk");

// Route::get('/view_invent',"DtoController@view_invent");

Route::get('/view_invent',"DdController@view_invent");

Route::get('/dto_ac_data',"DtoController@dto_ac_data");

Route::get('/dd_ac_data',"DdController@dd_ac_data");

Route::get('/get_dto_admins',"DtoController@get_dto_admins");

Route::get('/get_sto_admins',"StoController@get_sto_admins");

Route::get('/get_ato_admins',"AtoController@get_ato_admins");

Route::get('/get_dd_admins',"DdController@get_dd_admins");

Route::get('/get_dto_hoas',"DtoController@get_dto_hoas");

Route::get('/get_sto_hoas',"StoController@get_sto_hoas");

Route::get('/get_ato_hoas',"AtoController@get_ato_hoas");

Route::get('/get_dd_hoas',"DdController@get_dd_hoas");

Route::get('/get_dto_ledger',"DtoController@get_dto_ledger");

Route::get('/get_ledgerdata_dto',"DtoController@get_ledgerdata");

Route::get('/get_ledgerpage_dto',"DtoController@get_ledgerpage");

Route::get('/get_ledgerpagelist_dto',"DtoController@get_ledgerpagelist");

Route::get('/delete_dto_trans',"DtoController@delete_dto_trans");

Route::get('/dto_request_data',"DtoController@dto_request_data");

Route::get('/dto_loc_data',"DtoController@dto_loc_data");

Route::get('/confirm_dto_account',"DtoController@confirm_dto_account");

Route::post('/accept_request_dto',"DtoController@accept_request");

Route::post('/create_account_dto',"DtoController@create_account");

Route::post('/accept_loc_dto',"DtoController@accept_loc");

Route::get('/dto_requests',"DtoController@dto_requests");

Route::get('/sto_requests',"StoController@sto_requests");

Route::get('/ato_requests',"AtoController@ato_requests");

Route::get('/dd_requests',"DdController@dd_requests");

Route::get('/get_sa_dets_ants',"DdController@get_sa_dets_ants");

Route::get('/get_hoas_under_sa',"DdController@get_hoas_under_sa");

Route::get('/dto_loclist',"DtoController@dto_loclist");

Route::get('/sto_loclist',"StoController@sto_loclist");

Route::get('/ato_loclist',"AtoController@ato_loclist");

Route::get('/dd_loclist',"DdController@dd_loclist");

Route::post('/dto_chqlist_confirm',"DtoController@dto_chqlist_confirm");

Route::post('/dd_chqlist_confirm',"DdController@dd_chqlist_confirm");

Route::post('/sto_chqlist_confirm',"StoController@sto_chqlist_confirm");

Route::post('/ato_chqlist_confirm',"AtoController@ato_chqlist_confirm");

Route::post('/ato_return_sa',"AtoController@ato_return_sa");

Route::post('/dd_return_sa',"AtoController@dd_return_sa");

Route::post('/dto_chqlist_reject',"DtoController@dto_chqlist_reject");

Route::post('/dd_chqlist_reject',"DdController@dd_chqlist_reject");

Route::post('/ato_chqlist_reject',"AtoController@ato_chqlist_reject");

Route::post('/ato_chqlist_approve',"AtoController@ato_chqlist_approve");

Route::post('/ato_loclist_approve',"AtoController@ato_loclist_approve");

Route::post('/dto_aclist_confirm',"DtoController@dto_aclist_confirm");

Route::post('/dd_aclist_confirm',"DdController@dd_aclist_confirm");

Route::post('/dd_aclist_confirm',"DdController@dd_aclist_confirm");

Route::post('/dto_aclist_reject',"DtoController@dto_aclist_reject");

Route::post('/dd_aclist_reject',"DdController@dd_aclist_reject");

Route::get('/get_ofctype',"DdController@get_ofctype");

Route::get('/get_account_dets',"DdController@get_account_dets");

Route::get('/get_account_dets_dto',"DtoController@get_account_dets_dto");

Route::get('/crt_new_acnt_dd',"DdController@crt_new_acnt_dd");

Route::post('/dto_booklist_confirm',"DtoController@dto_booklist_confirm");

Route::post('/dd_booklist_confirm',"DdController@dd_booklist_confirm");

Route::post('/ao_booklist_confirm',"AtoController@ao_booklist_confirm");

Route::post('/sto_booklist_confirm',"StoController@sto_booklist_confirm");

Route::post('/ato_booklist_confirm',"AtoController@ato_booklist_confirm");

Route::post('/dto_booklist_reject',"DtoController@dto_booklist_reject");

Route::post('/dd_booklist_reject',"DdController@dd_booklist_reject");

Route::post('/dd_booklist_returntosa',"DdController@dd_booklist_returntosa");

Route::post('/ao_booklist_reject',"AtoController@ao_booklist_reject");

Route::post('/ao_booklist_returntosa',"AtoController@ao_booklist_returntosa");

Route::post('/dto_loclist_confirm',"DtoController@dto_loclist_confirm");

Route::post('/dd_loclist_confirm',"DdController@dd_loclist_confirm");

Route::post('/sto_loclist_confirm',"StoController@sto_loclist_confirm");

Route::post('/ato_loclist_confirm',"AtoController@ato_loclist_confirm");

Route::post('/dto_loclist_reject',"DtoController@dto_loclist_reject");

Route::post('/ato_loclist_reject',"AtoController@ato_loclist_reject");

Route::post('/dd_loclist_reject',"DdController@dd_loclist_reject");

Route::post('/dto_loclist_return',"DtoController@dto_loclist_return");

Route::post('/ato_loclist_return',"AtoController@ato_loclist_return");

Route::post('/dd_loclist_return',"DdController@dd_loclist_return");

Route::get('/dto_chq_data',"DtoController@dto_chq_data");

Route::get('/dto_trans',"DtoController@dto_trans");

Route::get('/sto_trans',"StoController@sto_trans");

Route::get('/ato_trans',"AtoController@ato_trans");

Route::get('/dd_trans',"DdController@dd_trans");

Route::get('/govt_data',"GovtController@govt_data");

Route::post('/govt_chqlist_confirm',"GovtController@govt_chqlist_confirm");

Route::post('/govt_chqlist_reject',"GovtController@govt_chqlist_reject");

Route::get('/govt_chq_data',"GovtController@govt_chq_data");

Route::get('/govt_trans',"GovtController@govt_trans");

Route::get('/bank_data',"BankController@bank_data");

Route::post('/bank_chqlist_accept',"BankController@bank_chqlist_accept");

Route::post('/bank_chqlist_confirm',"BankController@bank_chqlist_confirm");

Route::post('/bank_chqlist_reject',"BankController@bank_chqlist_reject");

Route::get('/bank_chq_data',"BankController@bank_chq_data");

Route::get('/bank_tran_data',"BankController@bank_tran_data");

Route::get('/bank_trans',"BankController@bank_trans");

Route::get('/bank_tran',"BankController@bank_tran");

Route::post('/cancel_chq',"AdminController@cancel_chq");

Route::get('/admin_req',"AdminController@admin_req");

Route::post('/place_request',"AdminController@place_request");

Route::post('/place_loc',"AdminController@place_loc");

Route::get('/admin_cheq',"AdminController@admin_cheq");

Route::get('/admin_trans',"AdminController@admin_trans");

Route::get('/admin_get_party',"AdminController@admin_get_party");

Route::get('/get_bookdata',"AdminController@get_bookdata");

Route::get('/get_pagedata',"AdminController@get_pagedata");

Route::get('/get_pagelist',"AdminController@get_pagelist");

Route::post('/start',"AdminController@start");

Route::post('/issue_single_party',"AdminController@issue_single_party");

Route::post('/bank_issue_single_party',"BankController@bank_issue_single_party");

Route::post('/issue_single_party_lapsable',"AdminController@issue_single_party_lapsable");

Route::post('/bank_issue_single_party_lapsable',"AdminController@bank_issue_single_party_lapsable");

Route::post('/issue_multiple_party',"AdminController@issue_multiple_party");

Route::post('/issue_multiple_party_lapsable',"AdminController@issue_multiple_party_lapsable");

Route::post('/issue_pdtopd_cheque',"AdminController@issue_pdtopd_cheque");

Route::post('/issue_pdtopd_cheque_lapsable',"AdminController@issue_pdtopd_cheque_lapsable");

Route::get('/get_hoa','AdminController@get_hoa');

Route::get('/get_hoa_trans','AdminController@get_hoa_trans');

Route::get('/get_booklist',"AdminController@get_booklist");

Route::get('/confirm_account',"AdminController@confirm_account");

Route::get('/reject_account',"AdminController@reject_account");

Route::get('/admin_loc_hoa',"AdminController@admin_loc_hoa");

Route::get('/admin_loc_report',"AdminController@admin_loc_report");

Route::get('/admin_req_rpt',"AdminController@admin_req_rpt");

Route::get('/admin_chqrpt',"AdminController@admin_chqrpt");

Route::get('/get_accounts',"AgController@get_accounts");

Route::get('/get_ac_trans',"AgController@get_ac_trans");

Route::get('/get_schemelist',"AgController@get_schemelist");

Route::get('/get_hoalist',"AgController@get_hoalist");

Route::get('/get_arealist',"AgController@get_arealist");

Route::get('/get_ddos',"AgController@get_ddos");

Route::get('/sample_code',"MainController@sample_code");

Route::get('/govt_confirmed_cheques',"GovtController@govt_confirmed_cheques");

Route::get('/get_receipts_data',"AdminController@get_receipts_data");

Route::get('/bank_confirmed_cheques',"BankController@bank_confirmed_cheques");

Route::get('/govt_if_acnt_data',"GovtController@govt_if_acnt_data");

Route::get('/govt_if_acnt_dataadmin',"AdminBackendController@govt_if_acnt_dataadmin");

Route::get('/add_bank',"AdminBackendController@add_bank");

Route::get('/govt_confirmed_cheques_for_govtif',"GovtController@govt_confirmed_cheques_for_govtif");

Route::get('/govt_confirmed_cheques_for_govtifadmin',"AdminBackendController@govt_confirmed_cheques_for_govtifadmin");

//route starts for admin query system

Route::get('/get_queries_pending',"AdminBackendController@get_queries_pending");

Route::get('/get_queries_resolved',"AdminBackendController@get_queries_resolved");

Route::get('/get_queries_forwarded',"AdminBackendController@get_queries_forwarded");

Route::post('/update_query',"AdminBackendController@update_query");

Route::post('/reset_pass',"AdminBackendController@reset_pass");

Route::post('/add_singlechq',"AdminBackendController@add_singlechq");

Route::post('/delete_singlechq',"AdminBackendController@delete_singlechq");

Route::post('/get_chqlist',"AdminBackendController@get_chqlist");

Route::post('/add_multiplechq',"AdminBackendController@add_multiplechq");

Route::post('/delete_multiplechq',"AdminBackendController@delete_multiplechq");

Route::post('/get_queryresult',"AdminBackendController@get_queryresult");

Route::get('/getaccrpt',"AdminBackendController@getaccrpt");

Route::get('/getsapendinglocs',"AdminBackendController@getsapendinglocs");

//route ends for admin query system

Route::post('/get_bank_list',"BankController@get_bank_list");

Route::get('/get_dd_all_accounts',"DdController@get_dd_all_accounts");

Route::get('/update_pd_status',"DdController@update_pd_status");

Route::get('/checker_data',"AdminCheckerController@checker_data");

Route::get('/checker_trans',"AdminCheckerController@checker_trans");

Route::get('/checker_chq_data',"AdminCheckerController@checker_chq_data");

Route::post('/checker_chqlist_confirm',"AdminCheckerController@checker_chqlist_confirm");

Route::post('/checker_chqlist_reject',"AdminCheckerController@checker_chqlist_reject");

Route::get('/checker_loclist',"AdminCheckerController@checker_loclist");

Route::get('/checker_loc_data',"AdminCheckerController@checker_loc_data");

Route::post('/accept_loc_checker',"AdminCheckerController@accept_loc_checker");

Route::get('/checker_requests',"AdminCheckerController@checker_requests");

Route::get('/checker_request_data',"AdminCheckerController@checker_request_data");

Route::post('/accept_request_checker',"AdminCheckerController@accept_request_checker");

Route::get('/bank_cheques_status',"BankController@bank_cheques_status");

Route::get('/get_user_hoa',"BankController@get_user_hoa");

Route::get('/update_auth_user',"DdController@update_auth_user");

Route::get('/get_booklistadmin',"AdminBackendController@get_booklistadmin");

Route::post('/approvelocadmin',"AdminBackendController@approvelocadmin");

Route::post('/rejectlocadmin',"AdminBackendController@rejectlocadmin");

Route::post('/approvechqadmin',"AdminBackendController@approvechqadmin");

Route::post('/rejectchqadmin',"AdminBackendController@rejectchqadmin");

Route::get('/getcurrentacno',"AdminBackendController@getcurrentacno");

Route::post('/updatecurrentacno',"AdminBackendController@updatecurrentacno");

Route::get('/locactivity',"AdminBackendController@locactivity");

Route::get('/chqactivity',"AdminBackendController@chqactivity");

Route::get('/getfilelist',"AdminController@getfilelist");

Route::get('/readresponsefile',"MainController@readresponsefile");