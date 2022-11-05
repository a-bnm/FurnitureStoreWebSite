<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fourniture extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'name','price','dimensions','small_description','description','quantity','category'
    ];

    public function images(){
        return $this->hasMany('App\Image','fourniture_id');
    }

    public function scopeSearchName($query,$name){
       if($name){
            return $query->where('name','LIKE','%'.$name.'%');
       } 
    }
    public function scopeSearchPrice($query,$price){
        if($price){
         return $query->where('price','<=',$price);
        } 
    }
    public function scopeSearchCategory($query,$category){
        if($category){
         return $query->where('category',$category);
        } 
     }
}
