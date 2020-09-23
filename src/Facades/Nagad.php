<?php 
namespace DGvai\Nagad\Facades;

/**
 * 
 *  Author
 *  Jalal Uddin (https://github.com/dgvai)
 *  
 *  License MIT 
 * 
 */

use Illuminate\Support\Facades\Facade;

class Nagad extends Facade 
{    
    /**
     * getFacadeAccessor
     *
     * @return void
     */
    protected static function getFacadeAccessor()
    {
        return 'nagad';
    }
}