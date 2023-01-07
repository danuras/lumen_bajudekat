<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;

class RajaOngkirController extends BaseController
{
  public function getCity(Request $request){
        $client = new Client();
        $res = $client->request('GET', 'https://api.rajaongkir.com/starter/city', [
            'headers' => [
                'key' => '',
            ]
        ]);
    
        return response()->json(json_decode($res->getBody()), $res->getStatusCode());
  }

  public function getCityById(Request $request){
    $client = new Client();
    $res = $client->request('GET', 'https://api.rajaongkir.com/starter/city?id='.$request->input('id'), [
        'headers' => [
            'key' => '',
        ]
    ]);

    return response()->json(json_decode($res->getBody()), $res->getStatusCode());
}

public function getCost(Request $request){
    $client = new Client();
    $res = $client->request('POST', 'https://api.rajaongkir.com/starter/cost', [
        'headers' => [
            'key' => '',
        ], 
        'form_params' => [
            'destination' => $request->input('destination'),
            'origin' => $request->input('origin'),
            'weight' => $request->input('weight'),
            'courier' => 'jne',
        ]
    ]);

    return response()->json(json_decode($res->getBody()), $res->getStatusCode());
}
}
