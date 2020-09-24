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

use DGvai\Nagad\Exceptions\NagadException;
use DGvai\Nagad\Helpers\NagadHelper;
use Illuminate\Http\Request;

class Nagad extends NagadGenerator
{    
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->__initialize();
    }
    
    /**
     * __initialize
     *
     * @return void
     */
    private function __initialize()
    {
        $this->BASE_URL = config('nagad.sandbox') ? config('nagad.domain.sandbox') : config('nagad.domain.live');
        $this->MERCHANT_ID = config('nagad.merchant.id');
        $this->CALLBACK_URL = route(config('nagad.callback'));
        $this->DATETIME = now()->format('YmdHis');
    }
    
    /**
     * getCallbackUrl
     *
     * @return void
     */
    public function getCallbackUrl()
    {
        return $this->CALLBACK_URL;
    }
    
    /**
     * setOrderID
     *
     * @param  mixed $id
     * @return Nagad
     */
    public function setOrderID($id) : Nagad
    {
        $this->ORDER_ID = $id;
        return $this;
    }
    
    /**
     * setAmount
     *
     * @param  mixed $amount
     * @return Nagad
     */
    public function setAmount($amount) : Nagad
    {
        $this->AMOUNT = $amount;
        return $this;
    }
    
    /**
     * setAddionalInfo
     *
     * @param  mixed $info
     * @return Nagad
     */
    public function setAddionalInfo(array $info = []) : Nagad
    {
        $this->ADDITIONAL = $info;
        return $this;
    }
    
    /**
     * setCallbackUrl
     *
     * @param  mixed $url
     * @return Nagad
     */
    public function setCallbackUrl($url) : Nagad
    {
        $this->CALLBACK_URL = $url;
        return $this;
    }
    
    /**
     * checkout
     *
     * @return void
     */
    public function checkout()
    {
        $sensitiveData = $this->generateSensitiveData();
        $response = $this->generatePaymentRequest($sensitiveData);

        if((isset($response['sensitiveData']) && isset($response['signature']))
        && ($response['sensitiveData'] != "" && $response['signature'] != "")) 
        {
            if($this->decryptInitialResponse($response)) {
                $sensitiveOrderData = $this->generateSensitiveDataOrder();
                $completeResponse = $this->completePaymentRequest($sensitiveOrderData);
                if(isset($completeResponse['status']) && $completeResponse['status'] == 'Success') {
                    $this->REDIRECT_URL = $completeResponse['callBackUrl'];
                    return $this;
                } else {
                    throw NagadException::couldNotCompleteOrder($completeResponse);
                }
            } else {
                throw NagadException::couldNotDecryptInitResponse($response);
            }
        } else {
            throw NagadException::invalidInitResponse($response);
        }
    }
    
    /**
     * getRedirectUrl
     *
     * @return string
     */
    public function getRedirectUrl() : string
    {
        return $this->REDIRECT_URL;
    }
    
    /**
     * redirect
     *
     * @return void
     */
    public function redirect()
    {
        return redirect()->away($this->REDIRECT_URL);
    }
    
    /**
     * callback
     *
     * @param  mixed $request
     * @return Nagad
     */
    public function callback(Request $request) : Nagad
    {
        $this->CALLBACK_RESPONSE = $request;
        return $this;
    }
    
    /**
     * verify
     *
     * @return Nagad
     */
    public function verify() : Nagad
    {
        $this->verifyPayment();
        return $this;
    }
    
    /**
     * success
     *
     * @return bool
     */
    public function success() : bool
    {
        return $this->VERIFIED_RESPONSE['status'] == 'Success';
    }
    
    /**
     * getErrors
     *
     * @return array
     */
    public function getErrors() : array
    {
        return ['status' => $this->VERIFIED_RESPONSE['status'], 'statusCode' => $this->VERIFIED_RESPONSE['statusCode']];
    }
    
    /**
     * getVerifiedResponse
     *
     * @return array
     */
    public function getVerifiedResponse() : array
    {
        return $this->VERIFIED_RESPONSE;
    }
    
    /**
     * getAdditionalData
     *
     * @param  mixed $object
     * @return void
     */
    public function getAdditionalData($object = true)
    {
        return json_decode($this->VERIFIED_RESPONSE['additionalMerchantInfo'], $object);
    }

}