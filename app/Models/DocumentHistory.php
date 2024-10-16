<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    use HasFactory;
    protected $fillable = ['userId', 'filePath', 'remarks'];

    public function getByUser(){
        return $this->hasOne(User::class,'userid','userId');
    }
}
