<?php

namespace StarkInfra\Utils;

class PixSubscriptionBacenId
{
    /**
    # Create a random Pix subscription bacenId

    Generates a random Pix subscription bacenId based on your bank code (ISPB), used as the
    bacenId of a PixPullSubscription. The date part is always generated in UTC, regardless of
    the PHP default timezone.

    ## Parameters (required):
        - bankCode [string]: your bank code (ISPB). ex: "20018183"
        - prefix [string]: subscription prefix. ex: "RR"

    ## Return:
        - 29-character bacenId composed of the prefix, the bank code, the current UTC date (Ymd) and 11 random alphanumeric characters. ex: "RR2001818320261016aB3xK9mQ2wZ"
     */
    public static function create($bankCode, $prefix)
    {
        return $prefix . BacenId::create($bankCode, 'Ymd');
    }
}
