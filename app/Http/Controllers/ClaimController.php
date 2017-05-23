<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Claim;
use Illuminate\Support\Facades\Log;
/**
*Class ClaimController
*
* Acts as a Controller for Claimer to make,view, delete and reject a claim.
*/
class ClaimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param int $status
     * @return \Illuminate\Http\Response
     */
    public function index($status)
    {
        $claims = Claim::where('claimer_id',Auth::id())->where('claim_status',$status)->get();
        return view('tickets.list',compact('claims'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detailClaim =  Claim::where('id',$id)->get();
        if(empty($detailClaim) || !isset($detailClaim[0]))
            return abort('404','404 - Page not found');
        if(!(($detailClaim[0]->claimer_id == Auth::id()) || ($detailClaim[0]->approver_id == Auth::id())
        || ($detailClaim[0]->finance_id == Auth::id())))
            return abort('403','403 - Unauthorized access');
        return view('claim.viewclaim',compact('detailClaim'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $claim = Claim::find($id);
        if ($user->id == $claim->claimer_id && $claim->claim_status == 1) {
            Log::info('user ('.Auth::id().") ".(Auth::user()->name)." cancel claim ".$claim->id);
            $claim->delete();
        }
        return redirect('/home');
    }

    /**
    *Reject a claim
    *Change status claim in database into 6(rejected)
    * @param Request $request, int $id
    * @return Redirect
    * Redirect to view of received claim
    */
    public function reject(Request $request,$id)
    {
        $alasanReject = $request->input("alasan_reject");
        $user = Auth::user();
        $claim = Claim::find($id);
        if((($claim->claim_status == 1) && ($user->role == "approver") && ($user->id == $claim->approver_id))
        || (($claim->claim_status == 2) && ($user->role == "finance")&& ($user->id == $claim->finance_id))) {
            $claim->claim_status = 6;
            $claim->alasan_reject= $alasanReject;
            $claim->save();
            Log::info('user ('.Auth::id().") ".(Auth::user()->name)." reject claim ".$claim->id);
        }
        else {
            Log::alert('user '.(Auth::user()->id).' trying to access forbidden route',['claim'=>$claim, 'user'=>Auth::user()]);
            return abort('403','403 - Unauthorized access');
        }
        return redirect("/home/".$user->role.'/received');
    }
    
    public function uploadProof(Request $request, $id)
    {
        if($request->hasFile('proof')){
            $file = $request->file('proof');
            $file->move('public/upload_proof',"$id.jpg");
            $claim = Claim::find($id);
            $claim->claim_status = 4;
            $claim->save();
            Log::info('user ('.Auth::id().") ".(Auth::user()->name)." uploaded proof for claim ".$claim->id);
            return redirect("/");
        }

    }
    public function verified($id)
    {
        $claim = Claim::find($id);
        $claim->claim_status = 5;
        $claim->save();
        Log::info('user ('.Auth::id().") ".(Auth::user()->name)." verified claim ".$claim->id)
        return redirect("/");
    }
}
