<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;

class ScrappingController extends Controller
{
    public function index($url) {
    	$company_url = 'http://www.mycorporateinfo.com/business/'.$url;
    	$export = new DataExport();
    	$export->export($company_url);
    }
}