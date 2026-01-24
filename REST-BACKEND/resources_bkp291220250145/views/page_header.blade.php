<?php
//define('CUSTOM_SIDEBAR_TEMPLATE', Auth::user()->user_type.'.theme.sidebar_nav');
//define('HEADER', Auth::user()->user_type.'.theme.header');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>{{ ucfirst(Auth::user()->user_type) }} Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('assets/css/sb-admin-2.css') }}" rel="stylesheet">

	@if(
		basename(url()->full()) == 'dashboard'
		||
		basename(url()->full()) == 'list_affiliates'
		||
		basename(url()->full()) == 'list_advertisers'
		||
		basename(url()->full()) == 'all_offers'
		||
		str_contains(url()->full(), 'offer_clicks')
		||
		basename(url()->full()) == 'offers_approved'
		||
		basename(url()->full()) == 'all_payouts'
		||
		str_contains(url()->full(), 'search_all_payouts')
		||
		str_contains(url()->full(), 'offer_payouts')
		||
		str_contains(url()->full(), 'payouts')
        ||
        str_contains(url()->full(), 'summary')  		
		||
		basename(url()->full()) == 'search_offer_payouts'
		||
		str_contains(url()->full(), 'affiliate_payouts')
		||
		basename(url()->full()) == 'search_affiliate_payouts'
		||
		basename(url()->full()) == 'logs'
		||
		basename(url()->full()) == 'list_transactions'
		||
		basename(url()->full()) == 'show_subscription'
	)

	<link href="{{ asset('assets/css/dropdown.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/arrow.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

	@endif

	<link href="{{ asset('assets/css/tab.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet">

</head>
<body id="page-top">
