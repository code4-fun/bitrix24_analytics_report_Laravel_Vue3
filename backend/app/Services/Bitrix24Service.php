<?php

namespace App\Services;

use App\Exceptions\IntegrationException;
use App\Exceptions\ReportException;
use Exception;

/**
 * Class Bitrix24Service
 *
 * The Bitrix24Service class provides methods to interact with the Bitrix24 API.
 * It allows you to invoke Bitrix methods and perform analytics calculations on leads and deals.
 *
 * @package App\Services
 */
class Bitrix24Service
{
  /**
   * Invoke a Bitrix24 API method with the given parameters.
   *
   * @param string $method The Bitrix24 API method to invoke (for example crm.lead.list).
   * @param array $data The parameters to pass to the API method.
   *
   * @return string|array The JSON response from the Bitrix24 API.
   */
  public function invokeBitrixMethod(string $webhook, string $method, array $data):string|array
  {
    $webhook_uri = $webhook.$method;
    $query_params = http_build_query($data);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_POST => 1,
      CURLOPT_HEADER => 0,
      CURLOPT_SSL_VERIFYPEER => 0,
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_URL => $webhook_uri,
      CURLOPT_POSTFIELDS => $query_params,
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    return json_decode($result, 1);
  }

  /**
   * Calculate analytics based on leads and deals data from Bitrix24.
   *
   * @return array An array containing calculated analytics data.
   * @throws ReportException
   */
  public function calculateAnalytics():array
  {
    $webhook = auth()->user()->webhooks()->where('name', 'report_integration')->first();

    if (!$webhook) {
      throw new ReportException('There is no integration set up to generate Analytics report');
    }

    $leadDailygrow = $webhook->customFields()->where('name', 'leadDailygrow')->first()['identifier'];
    $dealDailygrow = $webhook->customFields()->where('name', 'dealDailygrow')->first()['identifier'];
    $dealCost = $webhook->customFields()->where('name', 'dealCost')->first()['identifier'];

    $leads = $this->invokeBitrixMethod(url($webhook->uri . '/'),  'crm.lead.list.json', [
      'select' => ['ID', 'STATUS_SEMANTIC_ID', $leadDailygrow]
    ]);

    $deals = $this->invokeBitrixMethod(url($webhook->uri . '/'),  'crm.deal.list.json', [
      'select' => ['ID', 'OPPORTUNITY', 'STAGE_SEMANTIC_ID', $dealDailygrow, $dealCost]
    ]);

    if (is_array($leads) && array_key_exists('error', $leads)) {
      throw new ReportException();
    }

    if (is_array($deals) && array_key_exists('error', $deals)) {
      throw new ReportException();
    }

    $result = [];
    $summary = [];

    foreach ($deals['result'] as $item){
      if(!array_key_exists($item[$dealDailygrow], $summary)){
        $summary[$item[$dealDailygrow]] = [];
        if($item['STAGE_SEMANTIC_ID'] == 'S'){
          $summary[$item[$dealDailygrow]]['sales'] = 1;
        } else {
          $summary[$item[$dealDailygrow]]['sales'] = 0;
        }
        $summary[$item[$dealDailygrow]]['count'] = 1;
        $summary[$item[$dealDailygrow]]['revenue'] = (float)$item['OPPORTUNITY'];
        $summary[$item[$dealDailygrow]]['cost'] = (float)$item[$dealCost];
      } else {
        if($item['STAGE_SEMANTIC_ID'] == 'S'){
          $summary[$item[$dealDailygrow]]['sales'] += 1;
        }
        $summary[$item[$dealDailygrow]]['count'] += 1;
        $summary[$item[$dealDailygrow]]['revenue'] += (int)$item['OPPORTUNITY'];
        $summary[$item[$dealDailygrow]]['cost'] += $item[$dealCost];
      }
    }

    foreach ($leads['result'] as $item){
      if(!array_key_exists($item[$leadDailygrow], $summary)) {
        $summary[$item[$leadDailygrow]] = [];
        $summary[$item[$leadDailygrow]]['count'] = 1;
        $summary[$item[$leadDailygrow]]['sales'] = 0;
        $summary[$item[$leadDailygrow]]['revenue'] = 0;
        $summary[$item[$leadDailygrow]]['cost'] = 0;
      } else {
        $summary[$item[$leadDailygrow]]['count'] += 1;
      }
    }

    foreach ($summary as $key => $value){
      $result[] = [
        'name' => $key,
        'orders' => $value['count'],
        'conversion' => floatval(number_format((float)(($value['sales'] / $value['count']) * 100), '2', '.', '')),
        'sales' => $value['sales'],
        'revenue' => (int)$value['revenue'],
        'average' => (int)($value['sales'] ? $value['revenue'] / $value['sales'] : 0),
        'income' => (int)($value['revenue'] - $value['cost']),
      ];
    }

    $total = [
      'orders' => 0,
      'sales' => 0,
      'revenue' => 0,
      'income' => 0
    ];

    foreach ($result as $item){
      $total['orders'] +=  $item['orders'];
      $total['sales'] +=  $item['sales'];
      $total['revenue'] +=  $item['revenue'];
      $total['income'] +=  $item['income'];
    }

    $total['conversion'] = floatval(number_format((float)(($total['sales'] / $total['orders']) * 100), '2', '.', ''));
    $total['average'] = (int)($total['sales'] ? $total['revenue'] / $total['sales'] : 0);
    $total['roi'] = floor(($total['income'] / ($total['revenue'] - $total['income'])) * 100);
    $total['name'] = 'Итого/Среднее';

    $result[] = $total;

    usort($result, function($a, $b){
      if ($a['sales'] == $b['sales']) {
        return $b['conversion'] - $a['conversion'];
      }
      return $b['sales'] - $a['sales'];
    });

    return $result;
  }

  /**
   * Make a new integration by creating a webhook and associated custom fields.
   *
   * @param array $data Data for creating the integration.
   *
   * @return array An array containing the HTTP status code and a message.
   * @throws IntegrationException
   */
  public function makeIntegration(array $data): array
  {
    try {
      $webhook = auth()->user()->webhooks()->create([
        'name' => $data['webhookName'],
        'uri' => $data['webhookUrl']
      ]);

      $webhook->customFields()->createMany([
        ['name' => 'leadDailygrow', 'identifier' => $data['leadDailygrow']],
        ['name' => 'dealDailygrow', 'identifier' => $data['dealDailygrow']],
        ['name' => 'dealCost', 'identifier' => $data['dealCost']]
      ]);

      return ['status' => 200, 'message' => 'Integration created successfully'];
    } catch (Exception $e) {
      throw new IntegrationException();
    }
  }

  /**
   * Remove integration by webhook name.
   *
   * @param  string $data The name of the webhook to be removed.
   *
   * @return array An associative array containing the status and message of the operation.
   * @throws IntegrationException Thrown if an error occurs during integration removal.
   */
  public function removeIntegration(string $data): array
  {
    try {
      auth()->user()->webhooks()->where('name', $data)->delete();

      return ['status' => 200, 'message' => 'Integration removed successfully'];
    } catch (Exception $e) {
      throw new IntegrationException();
    }
  }
}
