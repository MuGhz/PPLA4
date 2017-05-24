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
 * Class FinanceController
 * Acts as a Controller for finance to view and process all Claims sent to him.
 *
 * @package App\Http\Controllers
 */
class FinanceController extends Controller
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
     * Show the list of received claim.
     *
     * @return view page
     * View received claim page
     */
    public function showReceived()
    {
        $allClaim = Claim::where('finance_id', '=', Auth::id())->where('claim_status', '=', '2')->get();
        return view('adminlte::home', compact('allClaim'));
    }
    /**
     * Show the list of approved claim by finance.
     *
     * @return view
     * View approved claim page
     */
    public function showApproved()
    {
        $allClaim = Claim::where('finance_id', '=', Auth::id())->where('claim_status', '=', '3')->get();
        return view('adminlte::home', compact('allClaim'));
    }
    /**
     * Show the list of rejected claim.
     *
     * @return view
     * View rejected claim page
     */
    public function showRejected()
    {
        $allClaim = Claim::where('finance_id', '=', Auth::id())->where('claim_status', '=', '6')->get();
        return view('adminlte::home', compact('allClaim'));
    }
    /**
     * Show the list of reported claim.
     *
     * @return view
     * View rejected claim page
     */
    public function showReported()
    {
        $allClaim = Claim::where('finance_id', '=', Auth::id())->where('claim_status', '=', '4')->get();
        return view('adminlte::home', compact('allClaim'));
    }
}
