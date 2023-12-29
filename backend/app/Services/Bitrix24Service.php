<?php

namespace App\Services;

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
   * @var string $uri The base URI for Bitrix24 API requests.
   */
  private string $uri;

  /**
   * Bitrix24Service constructor.
   *
   * @param string $uri The Bitrix24 API URI.
   */
  public function __construct(string $uri)
  {
    $this->uri = $uri;
  }

  /**
   * Invoke a Bitrix24 API method with the given parameters.
   *
   * @param string $method The Bitrix24 API method to invoke (for example crm.lead.list).
   * @param array $data The parameters to pass to the API method.
   *
   * @return string|array The JSON response from the Bitrix24 API.
   */
  public function invokeBitrixMethod(string $method, array $data):string|array
  {
    $webhook_uri = $this->uri.$method;
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
   */
  public function calculateAnalytics():array
  {
    $leads = $this->invokeBitrixMethod('crm.lead.list.json', [
      'select' => ['ID', 'STATUS_SEMANTIC_ID', 'UF_CRM_1702887958251']
    ]);

    $deals = $this->invokeBitrixMethod('crm.deal.list.json', [
      'select' => ['ID', 'OPPORTUNITY', 'STAGE_SEMANTIC_ID', 'UF_CRM_1702888018563', 'UF_CRM_1702915796704']
    ]);

    if (is_array($leads) && array_key_exists('error', $leads)) {
      $message = 'Error occurred while getting data from leads';
      abort(500, $message);
    }

    if (is_array($deals) && array_key_exists('error', $deals)) {
      $message = 'Error occurred while getting data from deals';
      abort(500, $message);
    }

    $result = [];
    $summary = [];

    foreach ($deals['result'] as $item){
      if(!array_key_exists($item['UF_CRM_1702888018563'], $summary)){
        $summary[$item['UF_CRM_1702888018563']] = [];
        if($item['STAGE_SEMANTIC_ID'] == 'S'){
          $summary[$item['UF_CRM_1702888018563']]['sales'] = 1;
        } else {
          $summary[$item['UF_CRM_1702888018563']]['sales'] = 0;
        }
        $summary[$item['UF_CRM_1702888018563']]['count'] = 1;
        $summary[$item['UF_CRM_1702888018563']]['revenue'] = (float)$item['OPPORTUNITY'];
        $summary[$item['UF_CRM_1702888018563']]['cost'] = (float)$item['UF_CRM_1702915796704'];
      } else {
        if($item['STAGE_SEMANTIC_ID'] == 'S'){
          $summary[$item['UF_CRM_1702888018563']]['sales'] += 1;
        }
        $summary[$item['UF_CRM_1702888018563']]['count'] += 1;
        $summary[$item['UF_CRM_1702888018563']]['revenue'] += (int)$item['OPPORTUNITY'];
        $summary[$item['UF_CRM_1702888018563']]['cost'] += $item['UF_CRM_1702915796704'];
      }
    }

    foreach ($leads['result'] as $item){
      if(!array_key_exists($item['UF_CRM_1702887958251'], $summary)) {
        $summary[$item['UF_CRM_1702887958251']] = [];
        $summary[$item['UF_CRM_1702887958251']]['count'] = 1;
        $summary[$item['UF_CRM_1702887958251']]['sales'] = 0;
        $summary[$item['UF_CRM_1702887958251']]['revenue'] = 0;
        $summary[$item['UF_CRM_1702887958251']]['cost'] = 0;
      } else {
        $summary[$item['UF_CRM_1702887958251']]['count'] += 1;
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
}
