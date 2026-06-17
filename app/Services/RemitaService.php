<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RemitaService
{
    protected $merchantId;
    protected $apiKey;
    protected $serviceTypeId;
    protected $gatewayUrl;

    public function __construct()
    {
        $this->merchantId = config('remita.merchant_id');
        $this->apiKey = config('remita.api_key');
        $this->serviceTypeId = config('remita.service_type_id');
        $this->gatewayUrl = config('remita.gateway_url');
    }

    public function generateRRR($data)
    {
        try {
            // Validate configuration
            if (empty($this->merchantId) || empty($this->apiKey) || empty($this->serviceTypeId)) {
                Log::error('Remita configuration missing', [
                    'has_merchant_id' => !empty($this->merchantId),
                    'has_api_key' => !empty($this->apiKey),
                    'has_service_type_id' => !empty($this->serviceTypeId)
                ]);
                return null;
            }

            $payload = [
                'serviceTypeId' => $this->serviceTypeId,
                'amount' => $data['amount'],
                'orderId' => $data['orderId'],
                'payerName' => $data['payerName'],
                'payerEmail' => $data['payerEmail'],
                'payerPhone' => $data['payerPhone'],
                'description' => $data['description'],
            ];
            
            $hash = hash('sha512', $this->merchantId . $this->serviceTypeId . $data['orderId'] . $data['amount'] . $this->apiKey);
            
            Log::info('Admin Remita RRR Request', [
                'merchantId' => $this->merchantId,
                'orderId' => $data['orderId'],
                'amount' => $data['amount'],
                'url' => $this->gatewayUrl . '/echannelsvc/merchant/api/paymentinit'
            ]);
            
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'remitaConsumerKey=' . $this->merchantId . ',remitaConsumerToken=' . $hash,
            ])->post($this->gatewayUrl . '/echannelsvc/merchant/api/paymentinit', $payload);
            
            $rawBody = $response->body();
            
            Log::info('Admin Remita RRR Raw Response', [
                'status' => $response->status(),
                'raw_body' => $rawBody
            ]);
            
            // Try to parse as JSON first
            $responseBody = $response->json();
            
            // If JSON parsing failed, try to extract from JSONP
            if (empty($responseBody) && !empty($rawBody)) {
                if (preg_match('/\{.*\}/', $rawBody, $matches)) {
                    $jsonString = $matches[0];
                    $responseBody = json_decode($jsonString, true);
                    Log::info('Admin: Extracted from JSONP', ['body' => $responseBody]);
                }
            }
            
            if ($response->successful() && isset($responseBody['RRR'])) {
                Log::info('Admin RRR Generated Successfully', ['RRR' => $responseBody['RRR']]);
                return $responseBody['RRR'];
            }
            
            Log::error('Admin RRR Generation Failed', [
                'status_code' => $response->status(),
                'response' => $responseBody
            ]);
            
            return null;
        } catch (\Exception $e) {
            Log::error('Admin RRR Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function verifyPayment($rrr)
    {
        try {
            $baseUrl = config('remita.demo_mode') ? 'https://demo.remita.net' : 'https://login.remita.net';
            $hash = hash('sha512', $rrr . $this->apiKey . $this->merchantId);
            $url = $baseUrl . '/remita/exapp/api/v1/send/api/echannelsvc/' . $this->merchantId . '/' . $rrr . '/' . $hash . '/status.reg';
            
            $response = Http::get($url);
            
            if ($response->successful()) {
                $result = $response->json();
                $statusCode = $result['status'] ?? $result['statuscode'] ?? null;
                
                if ($statusCode && ($statusCode == '00' || $statusCode == '01')) {
                    return 'success';
                }
            }
            
            return 'pending';
        } catch (\Exception $e) {
            Log::error('Payment Verification Error: ' . $e->getMessage());
            return 'failed';
        }
    }
}
