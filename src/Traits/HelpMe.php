<?php
namespace ITHilbert\LaravelKit\Traits;


/*
 * Some functions for a smarter life.
 */

trait HelpMe
{
    /**
    * checks if the value is set, otherwise the function returns the alternative parameter
    *
    * Example:
    * ns($_Session['UserID'],0);
    * If a User ID exists, it is returned otherwise 0.
    *
    * @param type $value
    * @param type $else
    * @return value
    */
    function ns(&$value, $else=''){
        if(isset($value)){
            return $value;
        }else{
            return $else;
        }
    }


}
