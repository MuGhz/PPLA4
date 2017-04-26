<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Claim;

class ClaimController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($status)
    {
        //
				$claims = Claim::where('claimer_id',Auth::id())->where('claim_status',$status)->get();
				return view('tickets.list',compact('claims'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
		if(empty($detailClaim) || !isset($detailClaim[0])) {
			return abort('404','404 - Page not found');
		}
		if($detailClaim[0]->claimer_id != Auth::id()) {
			return abort('403','403 - Unauthorized access');
		}
		return view('claim.viewclaim',compact('detailClaim'));
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
			$claim->delete();
		}
		return redirect('/home');
    }
}
