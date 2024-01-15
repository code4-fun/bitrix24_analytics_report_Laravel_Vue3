<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomField extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'identifier'
  ];

  protected $table = 'custom_fields';

  public function webhook()
  {
    return $this->belongsTo(Webhook::class);
  }
}
