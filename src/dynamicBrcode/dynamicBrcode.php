<?php

namespace StarkInfra;
use StarkInfra\Utils\API;
use StarkInfra\Utils\Rest;
use StarkInfra\Utils\Parse;
use StarkInfra\Utils\Checks;
use StarkInfra\Utils\Resource;
use StarkInfra\Utils\StarkDate;


class DynamicBrcode extends Resource
{
    /**
    # DynamicBrcode object

    BR codes store information represented by Pix QR Codes, which are used to 
    send or receive Pix transactions in a convenient way.
    DynamicBrcodes represent charges with information that can change at any time, 
    since all data needed for the payment is requested dynamically to an URL 
    stored in the BR Code.
    When you initialize a DynamicBrcode, the entity will not be automatically
    created in the Stark Infra API. The 'create' function sends the objects
    to the Stark Infra API and returns the created object.

    ## Parameters (required):
        - name [string]: receiver's name. ex: "Tony Stark"
        - city [string]: receiver's city name. ex: "Rio de Janeiro"
        - externalId [string]: string that must be unique among all your DynamicBrcodes. Duplicated external ids will cause failures. ex: "my-internal-id-123456"

    ## Parameters (optional):
        - type [string, default "instant"]: type of the DynamicBrcode. Options: "instant", "due"

    ## Attributes (return-only):
        - id [string]: id returned on creation, this is the BR code. ex: "00020126360014br.gov.bcb.pix0114+552840092118152040000530398654040.095802BR5915Jamie Lannister6009Sao Paulo620705038566304FC6C"
        - uuid [string]: unique uuid returned when the DynamicBrcode is created. ex: "4e2eab725ddd495f9c98ffd97440702d"
        - url [string]: url to the BR code's image. ex: "https://brcode-h.development.starkinfra.com/dynamic-qrcode/901e71f2447c43c886f58366a5432c4b.png"
        - updated [DateTime]: latest update datetime for the DynamicBrcode.
        - created [DateTime]: creation datetime for the DynamicBrcode.
     */
    function __construct(array $params)
    {
        parent::__construct($params);

        $this-> name = Checks::checkParam($params, "name");
        $this-> city = Checks::checkParam($params, "city");
        $this-> externalId = Checks::checkParam($params, "externalId");
        $this-> type = Checks::checkParam($params, "type");
        $this-> url = Checks::checkParam($params, "url");
        $this-> uuid = Checks::checkParam($params, "uuid");
        $this-> updated = Checks::checkDateTime(Checks::checkParam($params, "updated"));
        $this-> created = Checks::checkDateTime(Checks::checkParam($params, "created"));
        
        Checks::checkParams($params);
    }

    /**
    # Create DynamicBrcode objects

    Create DynamicBrcodes in the Stark Infra API

    ## Parameters (required):
        - brcodes [array of DynamicBrcode objects]: list of DynamicBrcode objects to be created in the API.
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - Array of DynamicBrcode objects with updated attributes.
     */
    public static function create($brcodes, $user = null)
    {
        return Rest::post($user, DynamicBrcode::resource(), $brcodes);
    }

    /**
    # Retrieve a DynamicBrcode object

    Retrieve a DynamicBrcode object linked to your Workspace in the Stark Infra API using its uuid.
    
    ## Parameters (required):
        - uuid [string]: object unique uuid. ex: "97756273400d42ce9086404fe10ea0d6".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - DynamicBrcode object that corresponds to the given uuid.
     */
    public static function get($uuid, $user = null)
    {
        return Rest::getId($user, DynamicBrcode::resource(), $uuid);
    }

    /**
    # Retrieve DynamicBrcode objects

    Receive an enumerator of DynamicBrcode objects previously created in the Stark Infra API
    
        ## Parameters (optional):
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - uuids [array of strings, default null]: list of uuids to filter retrieved objects. ex: ["97756273400d42ce9086404fe10ea0d6", "12212250d9cd43e68b3b7c474c9b0e36"]
        - externalIds [array of strings, default null]: list of externalIds to filter retrieved objects. ex: ["my_external_id1", "my_external_id2"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - enumerator of DynamicBrcode objects with updated attributes
     */
    public static function query($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getList($user, DynamicBrcode::resource(), $options);
    }

    /**
    # Retrieve paged DynamicBrcodes
    
    Receive a list of up to 100 DynamicBrcode objects previously created in the Stark Infra API and the cursor to the next page.
    Use this function instead of query if you want to manually page your requests.
    
    ## Parameters (optional):
        - cursor [string, default null]: cursor returned on the previous page function call.
        - limit [integer, default 100]: maximum number of objects to be retrieved. Max = 100. ex: 35
        - after [Date or string, default null] date filter for objects created only after specified date. ex: "2020-04-03"
        - before [Date or string, default null] date filter for objects created only before specified date. ex: "2020-04-03"
        - uuids [array of strings, default null]: list of uuids to filter retrieved objects. ex: ["97756273400d42ce9086404fe10ea0d6", "12212250d9cd43e68b3b7c474c9b0e36"]
        - externalIds [array of strings, default null]: list of externalIds to filter retrieved objects. ex: ["my_external_id1", "my_external_id2"]
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call
    
    ## Return:
        - cursor to retrieve the next page of DynamicBrcode objects
        - list of DynamicBrcode objects with updated attributes
     */
    public static function page($options = [], $user = null)
    {
        $options["after"] = new StarkDate(Checks::checkParam($options, "after"));
        $options["before"] = new StarkDate(Checks::checkParam($options, "before"));

        return Rest::getPage($user, DynamicBrcode::resource(), $options);
    }

    /**
    # Verify a DynamicBrcode read request

    When a DynamicBrcode is read by a user, a GET request will be made to your registered URL 
    to retrieve additional information needed to complete the transaction.
    These GET requests will have their uuids signed with a digital signature passed in the header.
    Use this method to verify the uuid with the digital signature.
    If the provided digital signature does not check out with the Stark public key, a
    StarkInfra\Exception\InvalidSignatureException will be raised.
    
    ## Parameters (required):
        - uuid [string]: object unique uuid. ex: "97756273400d42ce9086404fe10ea0d6".
    
    ## Parameters (optional):
        - user [Organization/Project object, default null]: Organization or Project object. Not necessary if StarkInfra\Settings::setUser() was set before function call

    ## Return:
        - uuid [string]: object unique uuid. ex: "97756273400d42ce9086404fe10ea0d6".
     */
    public static function verify($uuid, $signature, $user = null)
    {
        return Parse::verify($uuid, $signature, $user);
    }

    /** 
    # Helps you respond to an instant DynamicBrcode read

    When an instant DynamicBrcode is read by your user, a GET request containing the BR code's UUID will be made 
    to your registered URL to retrieve additional information needed to complete the transaction.
    The GET request must be answered within 5 seconds, with an HTTP status code 200, and 
    in the following format.

    ## Parameters (required):
        - version [integer]: integer that represents how many times the BR code was updated. ex: 1.
        - created [DateTime or string]: creation datetime of the DynamicBrcode. ex: "2022-05-17"
        - keyId [string]: receiver's PixKey id. Can be a tax_id (CPF/CNPJ), a phone number, an email or an alphanumeric sequence (EVP). ex: "+5511989898989"
        - status [string]: BR code's status. Options: "created", "overdue", "paid", "canceled" or "expired"
        - reconciliationId [string]: id to be used for conciliation of the resulting Pix transaction. ex: "cd65c78aeb6543eaaa0170f68bd741ee"
        - amount [integer]: positive integer that represents the amount in cents of the resulting Pix transaction. ex: 1234 (= R$ 12.34)

    ## Parameters (conditionally-required):
        - cashierType [string]: cashier's type. Required if the cashAmount is different from 0. Options: "merchant", "participant" and "other"
        - cashierBankCode [string]: cashier's bank code. Required if the cashAmount is different from 0. ex: "20018183"

    ## Parameters (optional):
        - cashAmount [integer, default 0]: amount to be withdrawn from the cashier in cents. ex: 1000 (= R$ 10.00)
        - expiration [DateInterval or integer, default 86400 (1 day)]: time in seconds counted from the creation datetime until the DynamicBrcode expires. After expiration, the BR code cannot be paid anymore. ex: 10000
        - senderName [string, default null]: sender's full name. ex: "Anthony Edward Stark"
        - senderTaxId [string, default null]: sender's CPF (11 digits formatted or unformatted) or CNPJ (14 digits formatted or unformatted). ex: "01.001.001/0001-01"
        - description [string, default null]: additional information to be shown to the sender at the moment of payment.
        - amountType [string, default "fixed"]: amount type of the BR code. If the amount type is "custom" the BR code's amount can be changed by the sender at the moment of payment. Options: "fixed"or "custom"

    ## Return:
        - Dumped JSON string that must be returned to us
    */
    public static function responseInstant($params)
    {
        $params = ([
            "version" => Checks::checkParam($params, "version"),
            "created" => Checks::checkDateTime(Checks::checkParam($params, "created")),
            "keyId" => Checks::checkParam($params, "keyId"),
            "status" => Checks::checkParam($params, "status"),
            "reconciliationId" => Checks::checkParam($params, "reconciliationId"),
            "amount" => Checks::checkParam($params, "amount"),
            "cashierType" => Checks::checkParam($params, "cashierType"),
            "cashierBankCode" => Checks::checkParam($params, "cashierBankCode"),
            "cashAmount" => Checks::checkParam($params, "cashAmount"),
            "expiration" => Checks::checkDateInterval(Checks::checkParam($params, "expiration")),
            "senderName" => Checks::checkParam($params, "senderName"),
            "senderTaxId" => Checks::checkParam($params, "senderTaxId"),
            "description" => Checks::checkParam($params, "description"),
            "amountType" => Checks::checkParam($params, "amountType"),
        ]);
        return json_encode(API::apiJson($params));
    }

    /** 
    # Helps you respond to a due DynamicBrcode read

    When a due DynamicBrcode is read by your user, a GET request containing the BR code's 
    UUID will be made to your registered URL to retrieve additional information needed 
    to complete the transaction.
    The GET request must be answered within 5 seconds, with an HTTP status code 200, and 
    in the following format.

    ## Parameters (required):
        - version [integer]: integer that represents how many times the BR code was updated.
        - created [DateTime or string]: creation datetime in ISO format of the DynamicBrcode. ex: "2022-05-17"
        - due [DateTime or string]: requested payment due datetime in ISO format. ex: "2022-06-17"
        - keyId [string]: receiver's PixKey id. Can be a tax_id (CPF/CNPJ), a phone number, an email or an alphanumeric sequence (EVP). ex: "+5511989898989"
        - status [string]: BR code's status. Options: "created", "overdue", "paid", "canceled" or "expired"
        - reconciliationId [string]: id to be used for conciliation of the resulting Pix transaction. ex: "cd65c78aeb6543eaaa0170f68bd741ee"
        - nominalAmount [integer]: A positive integer that represents the amount in cents of the resulting Pix transaction. If the amount is zero, the sender must set it at the moment of payment. Example: amount=100 (R$1.00).
        - senderName [string]: sender's full name. ex: "Anthony Edward Stark"
        - receiverName [string]: receiver's full name. ex: "Jamie Lannister"
        - receiverStreetLine [string]: receiver's main address. ex: "Av. Paulista, 200"
        - receiverCity [string]: receiver's address city name. ex: "Sao Paulo"
        - receiverStateCode [string]: receiver's address state code. ex: "SP"
        - receiverZipCode [string]: receiver's address zip code. ex: "01234-567"
        
    ## Parameters (optional):
        - expiration [DateInterval or integer, default 86400 (1 day)]: time in seconds counted from the creation datetime until the DynamicBrcode expires. After expiration, the BR code cannot be paid anymore.
        - senderTaxId [string, default null]: sender's CPF (11 digits formatted or unformatted) or CNPJ (14 digits formatted or unformatted). ex: "01.001.001/0001-01"
        - receiverTaxId [string, default null]: receiver's CPF (11 digits formatted or unformatted) or CNPJ (14 digits formatted or unformatted). ex: "012.345.678-90"
        - description [string, default null]: additional information to be shown to the sender at the moment of payment.
        - fine [float, default 2.0]: Percentage charged if the sender pays after the due datetime.
        - interest [float, default 1.0]: Interest percentage charged if the sender pays after the due datetime.
        - discounts [array of dictionaries, default null]: array of dictionaries with "percentage":float and "due":DateTime or string pairs.

    ## Return:
        - Dumped JSON string that must be returned to us
    */
    public static function responseDue($params)
    {
        $params = ([
            "version" => Checks::checkParam($params, "version"),
            "created" => Checks::checkDateTime(Checks::checkParam($params, "created")),
            "due" => Checks::checkDateTime(Checks::checkParam($params, "due")),
            "keyId" => Checks::checkParam($params, "keyId"),
            "status" => Checks::checkParam($params, "status"),
            "reconciliationId" => Checks::checkParam($params, "reconciliationId"),
            "nominalAmount" => Checks::checkParam($params, "nominalAmount"),
            "senderName" => Checks::checkParam($params, "senderName"),
            "receiverName" => Checks::checkParam($params, "receiverName"),
            "receiverStreetLine" => Checks::checkParam($params, "receiverStreetLine"),
            "receiverCity" => Checks::checkParam($params, "receiverCity"),
            "receiverStateCode" => Checks::checkParam($params, "receiverStateCode"),
            "receiverZipCode" => Checks::checkParam($params, "receiverZipCode"),
            "expiration" => Checks::checkDateInterval(Checks::checkParam($params, "expiration")),
            "senderTaxId" => Checks::checkParam($params, "senderTaxId"),
            "receiverTaxId" => Checks::checkParam($params, "receiverTaxId"),
            "description" => Checks::checkParam($params, "description"),
            "fine" => Checks::checkParam($params, "fine"),
            "interest" => Checks::checkParam($params, "interest"),
            "discounts" => Checks::checkParam($params, "discounts"),
        ]);
        return json_encode(API::apiJson($params));
    }

    private static function resource()
    {
        $brcode = function ($array) {
            return new DynamicBrcode($array);
        };
        return [
            "name" => "DynamicBrcode",
            "maker" => $brcode,
        ];
    }    
}
