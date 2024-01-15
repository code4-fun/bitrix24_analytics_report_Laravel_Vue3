<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Bitrix24Service;
use Illuminate\Http\Request;


class Bitrix24Controller extends Controller
{
  private Bitrix24Service $bitrix24Service;

  public function __construct(Bitrix24Service $bitrix24Service)
  {
    $this->bitrix24Service = $bitrix24Service;
  }

  /**
   * Display a listing of the resource.
   */
  public function report()
  {
    $result = $this->bitrix24Service->calculateAnalytics();
    return json_encode(['data' => $result]);
  }

  /**
   * Set up integration.
   */
  public function integrate()
  {
    $data = request()->only([
      'webhookName',
      'webhookUrl',
      'leadDailygrow',
      'dealDailygrow',
      'dealCost'
    ]);

    $validatedData = request()->validate([
      'webhookName' => 'required|string|max:50|unique:webhooks,name',
      'webhookUrl' => 'required|url|max:200',
      'leadDailygrow' => 'required|string|max:50',
      'dealDailygrow' => 'required|string|max:50',
      'dealCost' => 'required|string|max:50',
    ], [
      'webhookName.unique' => 'This integration has already been set up.'
    ]);

    $result = $this->bitrix24Service->makeIntegration($validatedData);
    return json_encode(['data' => $result]);
  }

  /**
   * Remove integration.
   */
  public function removeIntegration()
  {
    $webhookName = request()->input('webhookName');
    $result = $this->bitrix24Service->removeIntegration($webhookName);
    return json_encode(['data' => $result]);
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
