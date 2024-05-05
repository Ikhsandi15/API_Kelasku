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
        'request_friendship',
        'accept_friendship',
        'status'
    ];

    public function user()
    {
        return $this->hasMany(Friendship::class, 'accept_friendship', 'request_friendship');
    }
}
