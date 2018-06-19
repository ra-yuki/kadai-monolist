<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function items(){
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }
    
    public function want_items(){
        return $this->items()->where('type', 'want');
    }
    
    public function want($item_id){
        $exist = $this->is_wanting($item_id);
        
        if($exist){
            return false;
        }
        else {
            $this->items()->attach($item_id, ['type'=>'want']);
            return true;
        }
    }
    
    public function dont_want($item_id){
        $exist = $this->is_wanting($item_id);
        
        if($exist){
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [\Auth::user()->id, $item_id]);
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_wanting($itemIdOrCode){
        if(is_numeric($itemIdOrCode)){
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        }
        else {
            $item_code_exists = $this->want_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    public function have_items(){
        return $this->items()->where('type', 'have');
    }
    
    public function have($item_id){
        $exist = $this->is_having($item_id);
        
        if($exist){
            return false;
        }
        else {
            $this->items()->attach($item_id, ['type' => 'have']);
            return true;
        }
    }
    
    public function dont_have($item_id){
        $exist = $this->is_having($item_id);
        
        if($exist){
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [\Auth::user()->id, $item_id]);
            return true;
        }
        else{
            return false;
        }
    }
    
    public function is_having($itemIdOrCode){
        if(is_numeric($itemIdOrCode)){
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        }
        else {
            $item_code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
}
