<?php 
namespace DGvai\Nagad;

/**
 * 
 *  Author
 *  Jalal Uddin (https://github.com/dgvai)
 *  
 *  License MIT 
 * 
 */

use DGvai\Nagad\Helpers\NagadHelper;
use Illuminate\Support\Facades\Http;

class NagadGenerator
{
    protected $MERCHANT_ID;
    protected $BASE_URL;
    protected $ORDER_ID;
    protected $AMOUNT;
    protected $ADDITIONAL;
    protected $DATETIME;
    protected $PAYMENT_REF_ID;
    protected $CHALLANGE;
    protected $CALLBACK_URL;
    protected $REDIRECT_URL;
    protected $CALLBACK_RESPONSE;
    protected $VERIFIED_RESPONSE;
    
    /**
     * generateSensitiveData
     *
     * @return array
     */
    protected function generateSensitiveData() : array
    {
        return [
            'merchantId' => $this->MERCHANT_ID,
            'datetime' => $this->DATETIME,
            'orderId' => $this->ORDER_ID,
            'challenge' => NagadHelper::generateRandomString()
        ];
    }
    
    /**
     * generateSensitiveDataOrder
     *
     * @return array
     */
    protected function generateSensitiveDataOrder() : array 
    {
        return [
            'merchantId' => $this->MERCHANT_ID,
            'orderId' => $this->ORDER_ID,
            'currencyCode' => '050',        //050 = BDT
            'amount' => $this->AMOUNT,
            'challenge' => $this->CHALLANGE
        ];
    }
    
    /**
     * generatePaymentRequest
     *
     * @param  mixed $sensitiveData
     * @return array
     */    
    protected function generatePaymentRequest(array $sensitiveData) : array
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-KM-Api-Version' => 'v-0.2.0',
            'X-KM-IP-V4' => NagadHelper::getClientIp(),
            'X-KM-Client-Type' => 'PC_WEB'
        ])->post($this->BASE_URL.config('nagad.endpoints.checkout-init').'/'.$this->MERCHANT_ID.'/'.$this->ORDER_ID, [
            'accountNumber' => config('nagad.merchant.phone'),
            'dateTime' => $this->DATETIME,
            'sensitiveData' => NagadHelper::EncryptDataWithPublicKey(json_encode($sensitiveData)),
            'signature' => NagadHelper::SignatureGenerate(json_encode($sensitiveData)) 
        ])->json();
    }
    
    /**
     * decryptInitialResponse
     *
     * @param  mixed $response
     * @return bool
     */
    protected function decryptInitialResponse(array $response): bool
    {
        $plainResponse = json_decode(NagadHelper::DecryptDataWithPrivateKey($response['sensitiveData']), true);

        if(isset($plainResponse['paymentReferenceId']) && isset($plainResponse['challenge'])) {
            $this->PAYMENT_REF_ID = $plainResponse['paymentReferenceId'];
            $this->CHALLANGE = $plainResponse['challenge'];
            return true;
        }
        return false;
    }
    
    /**
     * completePaymentRequest
     *
     * @param  mixed $sensitiveOrderData
     * @return array
     */
    protected function completePaymentRequest(array $sensitiveOrderData): array
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-KM-Api-Version' => 'v-0.2.0',
            'X-KM-IP-V4' => NagadHelper::getClientIp(),
            'X-KM-Client-Type' => 'PC_WEB'
        ])->post($this->BASE_URL.config('nagad.endpoints.checkout-complete').'/'.$this->PAYMENT_REF_ID, [
            'sensitiveData' => NagadHelper::EncryptDataWithPublicKey(json_encode($sensitiveOrderData)),
            'signature' => NagadHelper::SignatureGenerate(json_encode($sensitiveOrderData)),
            'merchantCallbackURL' => $this->CALLBACK_URL,
            'additionalMerchantInfo' => (object)$this->ADDITIONAL
        ])->json();
    }
    
    /**
     * verifyPayment
     *
     * @return void
     */
    protected function verifyPayment()
    {
        $payment_ref_id = $this->CALLBACK_RESPONSE->payment_ref_id;
        $this->VERIFIED_RESPONSE = Http::get($this->BASE_URL.config('nagad.endpoints.payment-verify').'/'.$payment_ref_id)->json();
    }
}