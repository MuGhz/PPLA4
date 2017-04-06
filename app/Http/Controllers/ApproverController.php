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
     public function show($id)
     {
        $detailClaim =  Claim::where('id',$id)->get();
           if(empty($detailClaim) || !isset($detailClaim[0]))
             return abort('404','404 - Page not found');
        return view('approve.viewclaim',compact('detailClaim'));
     }
     public function approve($id){
       $updateClaim = Claim::where('id','=',$id)->first();
          if($updateClaim->approver_id != Auth::id())
            abort('403','forbidden access');
          $newStatus = 2;
          $updateClaim->claim_status = $newStatus;
          $newTime = date("Y-m-d H:i:s");
          $updateClaim->updated_at = $newTime;
          $updateClaim->save();
          return redirect('/home/approver/received');
        }
     public function reject($id){
       $updateClaim = Claim::where('id','=',$id)->first();
          if($updateClaim->approver_id != Auth::id())
            abort('403','forbidden access');
          $newStatus = 6;
          $updateClaim->claim_status = $newStatus;
          $newTime = date("Y-m-d H:i:s");
          $updateClaim->updated_at = $newTime;
          $updateClaim->save();
          return redirect('/home/approver/received');
        }
    public function showReceived()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '=', '1')->get();
        return view('approve.list', compact('claims'));
    }

    public function showApproved()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '!=', '1')->where('claim_status', '!=', '6')->get();
        return view('approve.list', compact('claims'));
    }

    public function showRejected()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())->where('claim_status', '=', '6')->get();
        return view('approve.list', compact('claims'));
    }
}
