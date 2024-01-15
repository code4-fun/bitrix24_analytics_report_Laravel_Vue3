<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'uri'
  ];

  protected $table = 'webhooks';

  public function customFields()
  {
    return $this->hasMany(CustomField::class);
  }
}
