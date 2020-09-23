<?php
namespace DGvai\Nagad\Exceptions;

/**
 * 
 *  Author
 *  Jalal Uddin (https://github.com/dgvai)
 *  
 *  License MIT 
 * 
 */ 

class NagadException extends \Exception
{
    public static function invalidInitResponse($error)
    {
        return new static('Invalid checkout-initialize response. Error Code: '.$error['reason'].', Message: '.$error['message']);
    }

    public static function couldNotDecryptInitResponse($error)
    {
        return new static('Unable to decrypt checkout-initialize response. Error Code: '.$error['reason'].', Message: '.$error['message']);
    }

    public static function couldNotCompleteOrder($error)
    {
        return new static('The checkout-complete request was incomplete. Possibility of missing post data. Error Code: '.$error['reason'].', Message: '.$error['message']);
    }
}