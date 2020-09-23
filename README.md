# Nagad (Bangladesh) payment gateway for Laravel 6.x+

[![Latest Stable Version](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/v/stable)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![Total Downloads](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/downloads)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![Latest Unstable Version](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/v/unstable)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![License](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/license)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![Monthly Downloads](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/d/monthly)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![Daily Downloads](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/d/daily)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)
[![composer.lock](https://poser.pugx.org/dgvai/laravel-notification-channel-isms/composerlock)](https://packagist.org/packages/dgvai/laravel-notification-channel-isms)

[Nagad](https://nagad.com.bd) is one of the Financial Services in Bangladesh. This package is built for Nagad Payment Gateway for Laravel 6.x, 7.x and 8.x+ 

## Contents

- [Installation](#installation)
	- [Setting up your configuration](#setting-up-your-configuration)
- [Usage](#usage)
- [Changelog](#changelog)
- [License](#license)

## Installation

You can install the package via composer:

``` bash
composer require dgvai/laravel-nagad
```

### Setting up your configuration
Extract the nagad config files:

```bash
php artisan vendor:publish --tag=nagad-config
```

- This will publish and config file in ``config_path()`` of your application. Eg. `config/nagad.php`

- Configure the configurations for the nagad merchant acocunt. Use `sandbox = true` for development stage.

- Be sure to set the **timezone** of you application to `Asia/Dhaka` in order to work with Nagad PG. To do this:
go to `config/app.php` and set `'timezone' => 'Asia/Dhaka'`

## Usage

NagadPG uses three stages of payment process, and two of theme are simultaneous. To get started, first you have to setup 
a callback route (`GET`) for the Nagad Callback and name the route in the nagad config file.

``` php
    // in routes/web.php
    Route::get('/nagad/callback', 'NagadController@callback')->name('nagad.callback');

    //in config/nagad.php
    'callback' => 'nagad.callback' // or use env variable to store
```

To Start payment, in your controller:
```php
    // in SomeController.php
    use DGvai\Nagad\Facades\Nagad;

    public function createPayement() 
    {
        /**
         * Method 1: Quickest
         * This will automatically redirect you to the Nagad PG Page
         * */

        return Nagad::setOrderID('ORDERID123')
            ->setAmount('540')
            ->checkout()
            ->redirect();
        
        /**
         * Method 2: Manual Redirection
         * This will return only the redirect URL and manually redirect to the url
         * */

        $url = Nagad::setOrderID('ORDERID123')
            ->setAmount('540')
            ->checkout()
            ->getRedirectUrl();

        return ['url' => $url];


        /**
         * Method 3: Advanced 
         * You set additional params which will be return at the callback
         * */

        return Nagad::setOrderID('ORDERID123')
            ->setAmount('540')
            ->setAddionalInfo(['pid' => 9, 'myName' => 'DG'])
            ->checkout()
            ->redirect();


        /**
         * Method 4: Advanced Custom Callabck
         * You can set/override callback url while creating payment
         * */

        return Nagad::setOrderID('ORDERID123')
            ->setAmount('540')
            ->setAddionalInfo(['pid' => 9, 'myName' => 'DG'])
            ->setCallbackUrl("https://manual-callback.url/callback")
            ->checkout()
            ->redirect();
    }
    
```

To receive the callback response, in your callback controller method: 

```php
     // in CallbackController.php
    use DGvai\Nagad\Facades\Nagad;
    use Illuminate\Http\Request;

    /**
     * This is the routed callback method
     * which receives a GET request.
     * 
     * */

    public function callback(Request $request)
    {
        $verified = Nagad::callback($request)->verify();
        if($verified->success()) {

            // Get Additional Data
            dd($verified->getAdditionalData());
            
            // Get Full Response
            dd($verified->getVerifiedResponse());
        } else {
            dd($verified->getErrors());
        }
    }
```

## Available Methods  
### For Checking-out  
- `setOrderID(string $orderID)` : ``$orderID`` to be any unique AlphaNumeric String
- `setAmount(string $amount)` : ``$amount`` to be any valid currency numeric String
- `setAddionalInfo(array $array)` : ``$array`` to be any array to be returned at callback
- `setCallbackUrl(string $url)` : ``$url`` to be any url string to be overidden the defualt callback url set in config
- `checkout()` : to initiate checkout process.
- `redirect()` : to direct redirect to the NagadPG Web Page.
- `getRedirectUrl()` : instead of redirecting, getting the redirect url manually.

### For Callback 
- `callback($request)` : ``$request`` to be ```Illuminate\Http\Request``` instance
- `verify()` : to verify the response.
- `success()` : to check if transaction is succeed.
- `getErrors()` : to get the error and errorCode if fails transactions | <kbd>returns</kbd> `array[]`
- `getVerifiedResponse()` : to get the full verified response | <kbd>returns</kbd> `array[]`
- `getAdditionalData(bool $object)` : to get the additional info passed during checkout. `$object` is to set return object or array.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
