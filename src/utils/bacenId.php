<?php

namespace StarkInfra\Utils;

class BacenId
{
    /**
    # Create a random bacenId

    Generates a random bacenId based on your bank code (ISPB). The date part is always
    generated in UTC, regardless of the PHP default timezone.

    ## Parameters (required):
        - bankCode [string]: your bank code (ISPB). ex: "20018183"

    ## Parameters (optional):
        - dateFormat [string, default "YmdHi"]: format of the UTC date part. ex: "Ymd"

    ## Return:
        - bacenId composed of the bank code, the current UTC date and 11 random alphanumeric characters
     */
    public static function create($bankCode, $dateFormat = 'YmdHi')
    {
        $source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $randLength = strlen($source)-1;
        $bacenId = $bankCode . gmdate($dateFormat);
        for ($i = 0; $i <= 10; $i++) {
            $bacenId .= $source[rand(0, $randLength)];
        }
        return $bacenId;
    }
}
