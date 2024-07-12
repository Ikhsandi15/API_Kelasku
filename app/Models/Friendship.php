<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friendship extends Model
{
    use HasFactory;

    protected $table = 'friendships';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'friend_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
