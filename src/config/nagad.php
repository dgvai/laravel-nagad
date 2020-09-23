<?php 

/**
 * 
 *  Author
 *  Jalal Uddin (https://github.com/dgvai)
 *  
 *  License MIT 
 * 
 */

return [

    /**
     * Nagad Payment Gateway Sandbox Mode
     * use 'true' to enable Test Payments
     * use 'false' to enable Live Payaments
     */

    'sandbox' => env('NAGAD_SANDBOX', true),

    'domain' => [

        /**
         * Domains for Live and Sandbox Mode
         * Do not change unless Nagad PG Updates their BaseURL
         */
        'sandbox'   => env('NAGAD_BASEURL', "http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs"),
        'live'      => env('NAGAD_BASEURL', "https://api.mynagad.com/api/dfs")
    ],
    'endpoints' => [
        /**
         * Endpoints for Live and Sandbox Mode
         * Do not change unless Nagad PG Updates their Api Endpoints
         */
        'checkout-init'         => '/check-out/initialize/',
        'checkout-complete'     => '/check-out/complete/',
        'payment-verify'        => '/verify/payment/',
    ],

    /**
     * The Merchant Informations
     */
    'merchant' => [
        /**
         * Merchant ID
         * --------------------
         * Given upon registration as a merchant
         */
        'id'    => env('NAGAD_MERHCANT_ID', null),
        /**
         * Merchant Phone
         * --------------------
         * Given upon registration as a merchant
         */
        'phone' => env('NAGAD_MERHCANT_PHONE', null),
        'key'   => [
            /**
             * The NagadPG Public Key
             * Usually given through email upon registration
             */
            'public'    => env('NAGAD_KEY_PUBLIC', null),
            /**
             * The Merchant Private Key
             * Usually given through email upon registration
             */
            'private'   => env('NAGAD_KEY_PRIVATE', null),
        ]
    ],
    /**
     * Calback URL Route Name
     * --------------------------
     * This is the laravel route name for
     * the callback url of your application.
     * 
     * eg: 'nagad.callback'
     * 
     * Notes: Do not use url here, only route name
     */
    'callback' => env('NAGAD_CALLBACK_URL', null)

];