<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Bitrix24Service;
use Illuminate\Http\Request;


class Bitrix24Controller extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $bx = new Bitrix24Service(config('custom.BX_URI'));
    $result = $bx->calculateAnalytics();
    return json_encode($result);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   */
  public function show(Password $password)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //
  }
}
