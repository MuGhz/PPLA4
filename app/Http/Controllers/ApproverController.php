<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.3/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Claim;
use App\User;
use App\Company;


/**
 * Class HomeController
 * @package App\Http\Controllers
 */
class ApproverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function showReceived()
    {
        $allClaim = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '=', '1')->get();
        return view('adminlte::home', compact('allClaim'));
    }

    public function showApproved()
    {
        $allClaim = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '!=', '1')->where('claim_status', '!=', '6')->get();
        return view('adminlte::home', compact('allClaim'));
    }

    public function showRejected()
    {
        $allClaim = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '=', '6')->get();
        return view('adminlte::home', compact('allClaim'));
    }
}