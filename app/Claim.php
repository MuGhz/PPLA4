<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    //
	protected $table = 'claims';
	protected $fillable = [
		'claim_type', 'claim_data_id', 'claimer_id', 'approver_id', 'finance_id', 'claim_status','order_information','alasan_reject'
	];
}
