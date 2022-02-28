<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
class ValidHand implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validChars = array("2","3","4","5","6","7","8","9","10","J","K","Q","A"," ",);
        $str_num = 0;
        $int_num = 0;
        $needs_Space = 0;
        //check to see it is in array
        $array = str_split($value);
        foreach($array as $char){
            if(!in_array($char, $validChars)){
                return $value = false;
            }
        }
        foreach($array as $char){

                if(!$needs_Space && $char == ""){
                    echo "returning false";
                    return $value = false;
                };
                $needs_Space = !$needs_Space;
            }
        //other  rules

        return $value = true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your hand is invalid.';
    }
}
