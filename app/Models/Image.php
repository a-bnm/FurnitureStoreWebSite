<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    use HasFactory;
    protected $fillable = [
        'url', 'fourniture_id'
    ];

    public function fournitures(){
       return  $this->belongsTo('App\Fourniture','fourniture_id');
    }
}
