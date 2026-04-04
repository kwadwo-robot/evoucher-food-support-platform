<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmailLog extends Model {
    protected $fillable = ['recipient_email','subject','type','status','sent_at'];
    protected $casts = ['sent_at' => 'datetime'];
}
