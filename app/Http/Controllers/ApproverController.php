<?php

/*
 * Taken from
 * https://github.com/laravel/framework/blob/5.3/src/Illuminate/Auth/Console/stubs/make/controllers/HomeController.stub
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Requests;
use App\Claim;
use App\User;
use App\Company;


/**
 * Class ApproverController
 * Acts as a Controller for Approver to view and process all Claims sent to him, such as rejecting & approving Claims.
 * @package App\Http\Controllers
 */
class ApproverController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Shows the detail of a Claim based on the inputted ID.
     *
     * @param $id
     *	ID of the designated Claim.
     * @return View
     *	Shows the detail of the Claim, approver may also reject or approve the Claim at this point.
     */
    public function show($id)
    {
        $detailClaim =  Claim::where('id',$id)->get();
        if(empty($detailClaim) || !isset($detailClaim[0]))
            return abort('404','404 - Page not found');
        return view('approve.viewclaim',compact('detailClaim'));
    }

    /**
     * Change the status of a Claim from "Sent" into "Approved", so Finance may process the transaction. The "claim_status"
     * value of a Sent, Approved and Rejected Claim is 1, 2 and 6 respectively.
     * @param $id
     *	ID of the designated Claim which claim_status will be changed.
     */
    public function approve($id)
    {
        $updateClaim = Claim::where('id','=',$id)->first();
        if($updateClaim->approver_id != Auth::id() || $updateClaim->claim_status != 1) {
            Log::alert('user '.(Auth::user()->id).' trying to access forbidden route',['claim'=>$updateClaim, 'user'=>Auth::user()]);
            abort('403','forbidden access');
        }
        $newStatus = 2;
        $updateClaim->claim_status = $newStatus;
        $newTime = date("Y-m-d H:i:s");
        $updateClaim->updated_at = $newTime;
        $updateClaim->save();
        Log::info('user ('.Auth::id().") ".(Auth::user()->name)." approve claim ".$updateClaim->id);
        return redirect('/home/approver/received');
    }

    /**
     * Change the status of a Claim from "Sent" into "Approved", so Finance may process the transaction. The "claim_status"
     * value of a Sent, Approved and Rejected Claim is 1, 2 and 6 respectively.
     * @param $id
     *	ID of the designated Claim which claim_status will be changed.
     */
    public function reject($id)
    {
        $updateClaim = Claim::where('id','=',$id)->first();
        if($updateClaim->approver_id != Auth::id() || $updateClaim->claim_status != 1){
            Log::alert('user '.(Auth::user()->id).' trying to access forbidden route',['claim'=>$updateClaim, 'user'=>Auth::user()]);
            abort('403','forbidden access');
        }
        $newStatus = 6;
        $updateClaim->claim_status = $newStatus;
        $newTime = date("Y-m-d H:i:s");
        $updateClaim->updated_at = $newTime;
        $updateClaim->save();
        Log::info('user ('.Auth::id().") ".(Auth::user()->name)." reject claim ".$updateClaim->id);
        return redirect('/home/approver/received');
    }

    /**
     * Shows all Claims that this Approver has received; meaning only Claims with value 1 in it's claim_status will be shown.
     * @return View
     *	Shows all Claims with "Sent" status.
     */
    public function showReceived()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())
            ->where('claim_status', '=', '1')->get();
        return view('approve.list', compact('claims'));
    }

    /**
     * Shows all Claims that this Approver has approved; meaning only Claims with value 2 in it's claim_status will be shown.
     * @return View
     *	Shows all Claims with "Approved" status, that was rejected by this Approver.
     */
    public function showApproved()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())
            ->where('claim_status', '!=', '1')->where('claim_status', '!=', '6')->get();
        return view('approve.list', compact('claims'));
    }

    /**
     * Shows all Claims that this Approver has rejected; meaning only Claims with value 6 in it's claim_status will be shown.
     * @return View
     *	Shows all Claims with "Rejected" status, that was rejected by this Approver.
     */
    public function showRejected()
    {
        $claims = Claim::where('approver_id', '=', Auth::id())
            ->where('claim_status', '=', '6')->get();
        return view('approve.list', compact('claims'));
    }
}
