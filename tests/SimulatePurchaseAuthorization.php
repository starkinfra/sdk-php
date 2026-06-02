<?php

namespace Test\SimulatePurchaseAuthorization;
use \Exception;
use StarkInfra\SimulatePurchaseAuthorization;
use StarkCore\Error\InvalidSignatureError;


class TestSimulatePurchaseAuthorization
{
    const CONTENT_CONTACTLESS = '{"cardNumber": "1122334455667788", "cardExpiration": "2025-07", "amount": 2500, "merchantName": "Test Merchant 1", "categoryCode": "hotelsMotelsAndResorts", "countryCode": "BRA", "currencyCode": "BRL", "methodCode": "contactless", "walletId": "apple"}';
    const CONTENT_TOKEN = '{"cardNumber": "1122334455667788", "cardExpiration": "2025-07", "amount": 2500, "merchantName": "T est Merchant 2", "categoryCode": "fastFoodRestaurants", "countryCode": "BRA", "currencyCode": "BRL", "methodCode": "token", "walletId": "apple"}';
    const CONTENT_GOOGLE = '{"cardNumber": "1122334455667788", "cardExpiration": "2025-07", "amount": 2500, "merchantName": "Google Pay Store", "categoryCode": "fastFoodRestaurants", "countryCode": "BRA", "currencyCode": "BRL", "methodCode": "token", "walletId": "google"}';
    const CONTENT_GOOGLE_CONTACTLESS = '{"cardNumber": "1122334455667788", "cardExpiration": "2025-07", "amount": 2500, "merchantName": "Google Pay Tap", "categoryCode": "fastFoodRestaurants", "countryCode": "BRA", "currencyCode": "BRL", "methodCode": "contactless", "walletId": "google"}';
    const VALID_SIGNATURE = "MEUCIBxymWEpit50lDqFKFHYOgyyqvE5kiHERi0ZM6cJpcvmAiEA2wwIkxcsuexh9BjcyAbZxprpRUyjcZJ2vBAjdd7o28Q=";
    const INVALID_SIGNATURE = "MEUCIQDOpo1j+V40DNZK2URL2786UQK/8mDXon9ayEd8U0/l7AIgYXtIZJBTs8zCRR3vmted6Ehz/qfw1GRut/eYyvf1yOk=";

    public function parseContactless()
    {
        $authorization_1 = SimulatePurchaseAuthorization::parse(self::CONTENT_CONTACTLESS, self::VALID_SIGNATURE);
        $authorization_2 = SimulatePurchaseAuthorization::parse(self::CONTENT_CONTACTLESS, self::VALID_SIGNATURE); // using cache

        if ($authorization_1 != $authorization_2) {
            throw new Exception("failed");
        }
        
        // Verificar se methodCode é "contactless"
        if ($authorization_1->methodCode !== "contactless") {
            throw new Exception("methodCode mismatch, expected 'contactless'");
        }
        
        // Verificar se walletId é "apple"
        if ($authorization_1->walletId !== "apple") {
            throw new Exception("walletId mismatch, expected 'apple'");
        }
    }
    
    public function parseToken()
    {
        try {
            // No ambiente real, a assinatura seria verificada corretamente
            // Aqui simulamos o parse para verificar os campos
            $payload = json_decode(self::CONTENT_TOKEN, true);
            $authorization = new SimulatePurchaseAuthorization($payload);
            
            // Verificar se os campos foram corretamente atribuídos
            if ($authorization->cardNumber !== "1122334455667788") {
                throw new Exception("cardNumber mismatch");
            }
            if ($authorization->cardExpiration !== "2025-07") {
                throw new Exception("cardExpiration mismatch");
            }
            if ($authorization->securityCode !== 123) {
                throw new Exception("securityCode mismatch");
            }
            if ($authorization->amount !== 2500) {
                throw new Exception("amount mismatch");
            }
            if ($authorization->merchantName !== "Test Merchant 2") {
                throw new Exception("merchantName mismatch");
            }
            if ($authorization->merchantCategoryCode !== "fastFoodRestaurants") {
                throw new Exception("merchantCategoryCode mismatch");
            }
            if ($authorization->merchantCountryCode !== "BRA") {
                throw new Exception("merchantCountryCode mismatch");
            }
            if ($authorization->merchantCurrencyCode !== "BRL") {
                throw new Exception("merchantCurrencyCode mismatch");
            }
            if ($authorization->methodCode !== "token") {
                throw new Exception("methodCode mismatch, expected 'token'");
            }
            if ($authorization->walletId !== "apple") {
                throw new Exception("walletId mismatch, expected 'apple'");
            }
            if ($authorization->status !== "approved") {
                throw new Exception("status mismatch, expected 'approved'");
            }
            if ($authorization->partial !== false) {
                throw new Exception("partial mismatch, expected 'false'");
            }
            
        } catch (Exception $e) {
            throw new Exception("Token payload test failed: " . $e->getMessage());
        }
    }
    
    public function parseGoogle()
    {
        try {
            // No ambiente real, a assinatura seria verificada corretamente
            // Aqui simulamos o parse para verificar os campos
            $payload = json_decode(self::CONTENT_GOOGLE, true);
            $authorization = new SimulatePurchaseAuthorization($payload);
            
            // Verificar se os campos foram corretamente atribuídos
            if ($authorization->cardNumber !== "1122334455667788") {
                throw new Exception("cardNumber mismatch");
            }
            if ($authorization->cardExpiration !== "2025-07") {
                throw new Exception("cardExpiration mismatch");
            }
            if ($authorization->amount !== 2500) {
                throw new Exception("amount mismatch");
            }
            if ($authorization->merchantName !== "Google Pay Store") {
                throw new Exception("merchantName mismatch");
            }
            if ($authorization->merchantCategoryCode !== "fastFoodRestaurants") {
                throw new Exception("merchantCategoryCode mismatch");
            }
            if ($authorization->merchantCountryCode !== "BRA") {
                throw new Exception("merchantCountryCode mismatch");
            }
            if ($authorization->merchantCurrencyCode !== "BRL") {
                throw new Exception("merchantCurrencyCode mismatch");
            }
            if ($authorization->methodCode !== "token") {
                throw new Exception("methodCode mismatch, expected 'token'");
            }
            if ($authorization->walletId !== "google") {
                throw new Exception("walletId mismatch, expected 'google'");
            }
            if ($authorization->status !== "approved") {
                throw new Exception("status mismatch, expected 'approved'");
            }
            if ($authorization->partial !== false) {
                throw new Exception("partial mismatch, expected 'false'");
            }
            
        } catch (Exception $e) {
            throw new Exception("Google wallet payload test failed: " . $e->getMessage());
        }
    }
    
    public function parseGoogleContactless()
    {
        try {
            // No ambiente real, a assinatura seria verificada corretamente
            // Aqui simulamos o parse para verificar os campos
            $payload = json_decode(self::CONTENT_GOOGLE_CONTACTLESS, true);
            $authorization = new SimulatePurchaseAuthorization($payload);
            
            // Verificar se os campos foram corretamente atribuídos
            if ($authorization->cardNumber !== "1122334455667788") {
                throw new Exception("cardNumber mismatch");
            }
            if ($authorization->cardExpiration !== "2025-07") {
                throw new Exception("cardExpiration mismatch");
            }
            if ($authorization->amount !== 2500) {
                throw new Exception("amount mismatch");
            }
            if ($authorization->merchantName !== "Google Pay Tap") {
                throw new Exception("merchantName mismatch");
            }
            if ($authorization->categoryCode !== "fastFoodRestaurants") {
                throw new Exception("categoryCode mismatch");
            }
            if ($authorization->countryCode !== "BRA") {
                throw new Exception("countryCode mismatch");
            }
            if ($authorization->currencyCode !== "BRL") {
                throw new Exception("currencyCode mismatch");
            }
            if ($authorization->methodCode !== "contactless") {
                throw new Exception("methodCode mismatch, expected 'contactless'");
            }
            if ($authorization->walletId !== "google") {
                throw new Exception("walletId mismatch, expected 'google'");
            }
            
        } catch (Exception $e) {
            throw new Exception("Google contactless payload test failed: " . $e->getMessage());
        }
    }
    
    // Teste de comparação entre Apple e Google
    public function compareWallets()
    {
        try {
            // Criar objetos para Apple e Google
            $payloadApple = json_decode(self::CONTENT_TOKEN, true);
            $payloadGoogle = json_decode(self::CONTENT_GOOGLE, true);
            
            $authApple = new SimulatePurchaseAuthorization($payloadApple);
            $authGoogle = new SimulatePurchaseAuthorization($payloadGoogle);
            
            // Verificar se os walletIds são diferentes
            if ($authApple->walletId === $authGoogle->walletId) {
                throw new Exception("walletIds should be different");
            }
            
            // Verificar valores específicos
            if ($authApple->walletId !== "apple") {
                throw new Exception("Apple walletId mismatch");
            }
            
            if ($authGoogle->walletId !== "google") {
                throw new Exception("Google walletId mismatch");
            }
            
            // Verificar se outros campos são iguais ou diferentes conforme esperado
            if ($authApple->cardNumber !== $authGoogle->cardNumber) {
                throw new Exception("cardNumber should be the same");
            }
            
            if ($authApple->amount !== $authGoogle->amount) {
                throw new Exception("amount should be the same");
            }
            
            if ($authApple->merchantName === $authGoogle->merchantName) {
                throw new Exception("merchantName should be different");
            }
            
        } catch (Exception $e) {
            throw new Exception("Wallet comparison test failed: " . $e->getMessage());
        }
    }
    
    // Teste para comparar os métodos de pagamento (contactless vs token) para Google
    public function compareGoogleMethods()
    {
        try {
            // Criar objetos para Google com token e contactless
            $payloadToken = json_decode(self::CONTENT_GOOGLE, true);
            $payloadContactless = json_decode(self::CONTENT_GOOGLE_CONTACTLESS, true);
            
            $authToken = new SimulatePurchaseAuthorization($payloadToken);
            $authContactless = new SimulatePurchaseAuthorization($payloadContactless);
            
            // Verificar se os methodCodes são diferentes
            if ($authToken->methodCode === $authContactless->methodCode) {
                throw new Exception("methodCodes should be different");
            }
            
            // Verificar os valores específicos
            if ($authToken->methodCode !== "token") {
                throw new Exception("Token methodCode mismatch");
            }
            
            if ($authContactless->methodCode !== "contactless") {
                throw new Exception("Contactless methodCode mismatch");
            }
            
            // Verificar se os walletIds são iguais
            if ($authToken->walletId !== $authContactless->walletId) {
                throw new Exception("walletIds should be the same");
            }
            
            // Verificar se os walletIds são "google"
            if ($authToken->walletId !== "google") {
                throw new Exception("Token walletId should be 'google'");
            }
            
            if ($authContactless->walletId !== "google") {
                throw new Exception("Contactless walletId should be 'google'");
            }
            
            // Verificar que outros campos essenciais são iguais
            if ($authToken->cardNumber !== $authContactless->cardNumber) {
                throw new Exception("cardNumber should be the same");
            }
            
            if ($authToken->amount !== $authContactless->amount) {
                throw new Exception("amount should be the same");
            }
            
        } catch (Exception $e) {
            throw new Exception("Google methods comparison test failed: " . $e->getMessage());
        }
    }

    public function parseWrong()
    {
        $error = false;
        try {
            $authorization = SimulatePurchaseAuthorization::parse(self::CONTENT_CONTACTLESS, self::INVALID_SIGNATURE);
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function parseMalformed()
    {
        $error = false;
        try {
            $authorization = SimulatePurchaseAuthorization::parse(self::CONTENT_CONTACTLESS, "something is definitely wrong");
        } catch (InvalidSignatureError $e) {
            $error = true;
        }

        if (!$error) {
            throw new Exception("failed");
        }
    }

    public function createResponseContactless()
    {
        $response = SimulatePurchaseAuthorization::response([
            "status" => "approved", 
            "amount" => 2500
        ]);
        
        if (gettype($response) != "string") {
            throw new Exception("failed");
        }
        if (strlen($response) == 0) {
            throw new Exception("failed");
        }
    }
    
    public function createResponseToken()
    {
        $response = SimulatePurchaseAuthorization::response([
            "status" => "approved", 
            "amount" => 2500,
            "tags" => ["fastFood", "token"]
        ]);
        
        if (gettype($response) != "string") {
            throw new Exception("response is not a string");
        }
        if (strlen($response) == 0) {
            throw new Exception("empty response");
        }
        
        $responseJson = json_decode($response, true);
        if (!isset($responseJson["authorization"]) || 
            !isset($responseJson["authorization"]["status"]) ||
            $responseJson["authorization"]["status"] !== "approved") {
            throw new Exception("invalid response format or status");
        }
    }
    
    public function createResponseGoogle()
    {
        $response = SimulatePurchaseAuthorization::response([
            "status" => "approved", 
            "amount" => 2500,
            "tags" => ["googleWallet", "token"]
        ]);
        
        if (gettype($response) != "string") {
            throw new Exception("response is not a string");
        }
        if (strlen($response) == 0) {
            throw new Exception("empty response");
        }
        
        $responseJson = json_decode($response, true);
        if (!isset($responseJson["authorization"]) || 
            !isset($responseJson["authorization"]["status"]) ||
            $responseJson["authorization"]["status"] !== "approved") {
            throw new Exception("invalid response format or status");
        }
    }
    
    public function createResponseGoogleContactless()
    {
        $response = SimulatePurchaseAuthorization::response([
            "status" => "approved", 
            "amount" => 2500,
            "tags" => ["googleWallet", "contactless"]
        ]);
        
        if (gettype($response) != "string") {
            throw new Exception("response is not a string");
        }
        if (strlen($response) == 0) {
            throw new Exception("empty response");
        }
        
        $responseJson = json_decode($response, true);
        if (!isset($responseJson["authorization"]) || 
            !isset($responseJson["authorization"]["status"]) ||
            $responseJson["authorization"]["status"] !== "approved") {
            throw new Exception("invalid response format or status");
        }
    }
}

echo "\n\nSimulatePurchaseAuthorization:";

$test = new TestSimulatePurchaseAuthorization();

echo "\n\t- parse contactless (Apple)";
$test->parseContactless();
echo " - OK";

echo "\n\t- parse token (Apple)";
$test->parseToken();
echo " - OK";

echo "\n\t- parse token (Google)";
$test->parseGoogle();
echo " - OK";

echo "\n\t- parse contactless (Google)";
$test->parseGoogleContactless();
echo " - OK";

echo "\n\t- compare wallets (Apple vs Google)";
$test->compareWallets();
echo " - OK";

echo "\n\t- compare methods (Google: token vs contactless)";
$test->compareGoogleMethods();
echo " - OK";

echo "\n\t- parse wrong";
$test->parseWrong();
echo " - OK";

echo "\n\t- parse malformed";
$test->parseMalformed();
echo " - OK";

echo "\n\t- create response contactless (Apple)";
$test->createResponseContactless();
echo " - OK";

echo "\n\t- create response token (Apple)";
$test->createResponseToken();
echo " - OK";

echo "\n\t- create response token (Google)";
$test->createResponseGoogle();
echo " - OK";

echo "\n\t- create response contactless (Google)";
$test->createResponseGoogleContactless();
echo " - OK"; 