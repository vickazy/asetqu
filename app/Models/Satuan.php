<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
  protected $table = 'satuan';
  protected $fillable = ['name','updated_at'];
  protected $quarded = ['created_at'];
  public $timestamps = true;
}