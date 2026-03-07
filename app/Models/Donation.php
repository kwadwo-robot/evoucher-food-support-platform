<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Donation extends Model {
    protected $fillable = ['donor_user_id','donor_name','donor_email','org_name','amount','currency','stripe_payment_id','stripe_session_id','status','vouchers_allocated','notes'];
    protected $casts = ['amount' => 'decimal:2', 'vouchers_allocated' => 'decimal:2'];
    public function donor() { return $this->belongsTo(User::class, 'donor_user_id'); }
}
