# Stark Infra PHP SDK - Beta

Welcome to the Stark Infra PHP SDK! This tool is made for PHP 
developers who want to easily integrate with our API.
This SDK version is compatible with the Stark Infra API v2.

# Introduction

## Index

- [Introduction](#introduction)
    - [Supported PHP versions](#supported-php-versions)
    - [API documentation](#stark-infra-api-documentation)
    - [Versioning](#versioning)
- [Setup](#setup)
    - [Install our SDK](#1-install-our-sdk)
    - [Create your Private and Public Keys](#2-create-your-private-and-public-keys)
    - [Register your user credentials](#3-register-your-user-credentials)
    - [Setting up the user](#4-setting-up-the-user)
    - [Setting up the error language](#5-setting-up-the-error-language)
- [Resource listing and manual pagination](#resource-listing-and-manual-pagination)
- [Testing in Sandbox](#testing-in-sandbox) 
- [Usage](#usage)
    - [Issuing](#issuing)
        - [Products](#query-issuingproducts): View available sub-issuer card products (a.k.a. card number ranges or BINs)
        - [Holders](#create-issuingholders): Manage card holders
        - [Cards](#create-issuingcards): Create virtual and/or physical cards
        - [Design](#query-issuingdesigns): View your current card or package designs
        - [EmbossingKit](#query-issuingembossingkits): View your current embossing kits
        - [Stock](#query-issuingstocks): View your current stock of a certain IssuingDesign linked to an Embosser on the workspace
        - [Restock](#create-issuingrestocks): Create restock orders of a specific IssuingStock object
        - [EmbossingRequest](#create-issuingembossingrequests): Create embossing requests
        - [TokenRequest](#create-an-issuingtokenrequest): Generate the payload to create the token
        - [Token](#process-token-authorizations): Authorize and manage your tokens
        - [TokenActivation](#process-token-activations): Get notified on how to inform the activation code to the holder 
        - [TokenDesign](#get-an-issuingtokendesign): View your current token card arts
        - [Purchases](#process-purchase-authorizations): Authorize and view your past purchases
        - [Invoices](#create-issuinginvoices): Add money to your issuing balance
        - [Withdrawals](#create-issuingwithdrawals): Send money back to your Workspace from your issuing balance
        - [Balance](#get-your-issuingbalance): View your issuing balance
        - [Transactions](#query-issuingtransactions): View the transactions that have affected your issuing balance
        - [Enums](#issuing-enums): Query enums related to the issuing purchases, such as merchant categories, countries and card purchase methods
        - [SimulatePurchaseAuthorization](#simulate-card-purchase): Process purchase authorizations in the sandbox environment
    - [Pix](#pix)
        - [PixRequests](#create-pixrequests): Create Pix transactions
        - [PixReversals](#create-pixreversals): Reverse Pix transactions
        - [PixBalance](#get-your-pixbalance): View your account balance
        - [PixStatement](#create-a-pixstatement): Request your account statement
        - [PixKey](#create-a-pixkey): Create a Pix Key
        - [PixClaim](#create-a-pixclaim): Claim a Pix Key
        - [PixDirector](#create-a-pixdirector): Create a Pix Director
        - [PixInfraction](#create-pixinfractions): Create Pix Infraction reports
        - [PixFraud](#create-a-pixfraud): Create a Pix Fraud
        - [PixUser](#get-a-pixuser): Get fraud statistics of a user
        - [PixChargeback](#create-pixchargebacks): Create Pix Chargeback requests
        - [PixDomain](#query-pixdomains): View registered SPI participants certificates
        - [StaticBrcode](#create-staticbrcodes): Create static Pix BR codes
        - [DynamicBrcode](#create-dynamicbrcodes): Create dynamic Pix BR codes
        - [BrcodePreview](#create-brcodepreviews): Read data from BR Codes before paying them
    - [Lending](#lending)
        - [CreditNote](#create-creditnotes): Create credit notes
        - [CreditPreview](#create-creditpreviews): Create credit previews
        - [CreditHolmes](#create-creditholmes): Create credit holmes debt verification
    - [Identity](#identity)
        - [IndividualIdentity](#create-individualidentities): Create individual identities
        - [IndividualDocument](#create-individualdocuments): Create individual documents
    - [Webhook](#webhook):
        - [Webhook](#create-a-webhook-subscription): Configure your webhook endpoints and subscriptions
        - [WebhookEvents](#process-webhook-events): Manage Webhook events
        - [WebhookEventAttempts](#query-failed-webhook-event-delivery-attempts-information): Query failed webhook event deliveries
- [Handling errors](#handling-errors)
- [Help and Feedback](#help-and-feedback)

## Supported PHP Versions

This library supports the following PHP versions:

* PHP 7.1
* PHP 7.2
* PHP 7.3
* PHP 7.4
* PHP 8.0
* PHP 8.1
* PHP 8.2
* PHP 8.3

## Stark Infra API documentation

Feel free to take a look at our [API docs](https://www.starkinfra.com/docs/api).

## Versioning

This project adheres to the following versioning pattern:

Given a version number MAJOR.MINOR.PATCH, increment:

- MAJOR version when the **API** version is incremented. This may include backwards incompatible changes;
- MINOR version when **breaking changes** are introduced OR **new functionalities** are added in a backwards compatible manner;
- PATCH version when backwards compatible bug **fixes** are implemented.

# Setup

## 1. Install our SDK

1.1 Composer: To install the package with Composer, run:

```sh
composer require starkinfra/sdk
```

To use the bindings, use following the command:

```sh
require_once('vendor/autoload.php');
```

## 2. Create your Private and Public Keys

We use ECDSA. That means you need to generate a secp256k1 private
key to sign your requests to our API, and register your public key
with us, so we can validate those requests.

You can use one of following methods:

2.1. Check out the options in our [tutorial](https://starkbank.com/faq/how-to-create-ecdsa-keys).

2.2. Use our SDK:

```php
use StarkInfra;

list($privateKey, $publicKey) = StarkInfra\Key::create();

# or, to also save .pem files in a specific path
list($privateKey, $publicKey) = StarkInfra\Key::create("file/keys/");
```

**NOTE**: When you are creating new credentials, it is recommended that you create the
keys inside the infrastructure that will use it, in order to avoid risky internet
transmissions of your **private-key**. Then you can export the **public-key** alone to the
computer where it will be used in the new Project creation.

## 3. Register your user credentials

You can interact directly with our API using two types of users: Projects and Organizations.

- **Projects** are workspace-specific users, that is, they are bound to the workspaces they are created in.
One workspace can have multiple Projects.
- **Organizations** are general users that control your entire organization.
They can control all your Workspaces and even create new ones. The Organization is bound to your company's tax ID only.
Since this user is unique in your entire organization, only one credential can be linked to it.

3.1. To create a Project in Sandbox:

3.1.1. Log into [StarkInfra Sandbox](https://web.sandbox.starkinfra.com)

3.1.2. Go to Menu > Integrations

3.1.3. Click on the "New Project" button

3.1.4. Create a Project: Give it a name and upload the public key you created in section 2

3.1.5. After creating the Project, get its Project ID

3.1.6. Use the Project ID and private key to create the object below:

```php
use StarkInfra;

// Get your private key from an environment variable or an encrypted database.
// This is only an example of a private key content. You should use your own key.
$privateKeyContent = "
-----BEGIN EC PARAMETERS-----
BgUrgQQACg==
-----END EC PARAMETERS-----
-----BEGIN EC PRIVATE KEY-----
MHQCAQEEIMCwW74H6egQkTiz87WDvLNm7fK/cA+ctA2vg/bbHx3woAcGBSuBBAAK
oUQDQgAE0iaeEHEgr3oTbCfh8U2L+r7zoaeOX964xaAnND5jATGpD/tHec6Oe9U1
IF16ZoTVt1FzZ8WkYQ3XomRD4HS13A==
-----END EC PRIVATE KEY-----
";

$project = new StarkInfra\Project([
    "environment" => "sandbox",
    "id" => "5656565656565656",
    "privateKey" => $privateKeyContent
]);
```

3.2. To create Organization credentials in Sandbox:

3.2.1. Log into [StarkInfra Sandbox](https://web.sandbox.starkinfra.com)

3.2.2. Go to Menu > Integrations

3.2.3. Click on the "Organization public key" button

3.2.4. Upload the public key you created in section 2 (only a legal representative of the organization can upload the public key)

3.2.5. Click on your profile picture and then on the "Organization" menu to get the Organization ID

3.2.6. Use the Organization ID and private key to create the object below:

```php
use StarkInfra;

// Get your private key from an environment variable or an encrypted database.
// This is only an example of a private key content. You should use your own key.
$privateKeyContent = "
-----BEGIN EC PARAMETERS-----
BgUrgQQACg==
-----END EC PARAMETERS-----
-----BEGIN EC PRIVATE KEY-----
MHQCAQEEIMCwW74H6egQkTiz87WDvLNm7fK/cA+ctA2vg/bbHx3woAcGBSuBBAAK
oUQDQgAE0iaeEHEgr3oTbCfh8U2L+r7zoaeOX964xaAnND5jATGpD/tHec6Oe9U1
IF16ZoTVt1FzZ8WkYQ3XomRD4HS13A==
-----END EC PRIVATE KEY-----
";

$organization = new StarkInfra\Organization([
    "environment" => "sandbox",
    "id" => "5656565656565656",
    "privateKey" => $privateKeyContent,
    "workspaceId" => null // You only need to set the workspaceId when you are operating a specific workspaceId
]);

// To dynamically use your organization credentials in a specific workspaceId,
// you can use the Organization::replace() method:
$balance = StarkInfra\IssuingBalance::get(StarkInfra\Organization::replace($organization, "4848484848484848"));
```

NOTE 1: Never hard-code your private key. Get it from an environment variable or an encrypted database.

NOTE 2: We support `'sandbox'` and `'production'` as environments.

NOTE 3: The credentials you registered in `sandbox` do not exist in `production` and vice versa.


## 4. Setting up the user

There are three kinds of users that can access our API: **Organization**, **Project** and **Member**.

- `Project` and `Organization` are designed for integrations and are the ones meant for our SDKs.
- `Member` is the one you use when you log into our webpage with your e-mail.

There are two ways to inform the user to the SDK:

4.1 Passing the user as argument in all functions:

```php
use StarkInfra;

$balance = StarkInfra\IssuingBalance::get($project);  # or organization
```

4.2 Set it as a default user in the SDK:

```php
use StarkInfra;

StarkInfra\Settings::setUser($project);  # or organization

$balance = StarkInfra\IssuingBalance::get();  # or organization
```

Just select the way of passing the user that is more convenient to you.
On all following examples we will assume a default user has been set.

## 5. Setting up the error language

The error language can also be set in the same way as the default user:

```php
use StarkInfra;

StarkInfra\Settings::setLanguage("en-US");
```

Language options are "en-US" for english and "pt-BR" for brazilian portuguese. English is default.

# Resource listing and manual pagination

Almost all SDK resources provide a `query` and a `page` function.

- The `query` function provides a straight forward way to efficiently iterate through all results that match the filters you inform,
seamlessly retrieving the next batch of elements from the API only when you reach the end of the current batch.
If you are not worried about data volume or processing time, this is the way to go.

```php
use StarkInfra;

$transactions = StarkInfra\IssuingTransaction::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($transactions as $transaction) {
    print_r($transaction);
}
```

- The `page` function gives you full control over the API pagination. With each function call, you receive up to 100 results and the cursor to retrieve the next batch of elements. This allows you to stop your queries and pick up from where you left off whenever it is convenient. When there are no more elements to be retrieved, the returned cursor will be `null`.

```php
use StarkInfra;

$cursor = null;
while (true) { 
    list($page, $cursor) = StarkInfra\IssuingTransaction::page($options = ["limit" => 5, "cursor" => $cursor]);
    foreach ($page as $transaction) {
        print_r($transaction);
    }
    if ($cursor == null) {
        break;
    }
}
```

To simplify the following SDK examples, we will only use the `query` function, but feel free to use `page` instead.

# Testing in Sandbox

Your initial balance is zero. For many operations in Stark Infra, you'll need funds
in your account, which can be added to your balance by creating a starkbank.Invoice. 

In the Sandbox environment, most of the created starkbank.Invoices will be automatically paid,
so there's nothing else you need to do to add funds to your account. Just create
a few starkbank.Invoices and wait around a bit.

In Production, you (or one of your clients) will need to actually pay this starkbank.Invoice
for the value to be credited to your account.


# Usage

Here are a few examples on how to use the SDK. If you have any doubts, check out the function or class docstring to get more info or go straight to our [API docs].

## Issuing

### Query IssuingProducts

To take a look at the sub-issuer card products available to you, just run the following:

```php
use StarkInfra\IssuingProduct;

$products = IssuingProduct::query();

foreach ($products as $product) {
    print_r($product);
}
```

This will tell which card products and card number prefixes you have at your disposal.

### Create IssuingHolders

You can create card holders to which your cards will be bound.
They support spending rules that will apply to all underlying cards.

```php
use StarkInfra\IssuingHolder;

$holders = IssuingHolder::create([
    new IssuingHolder([
        "name" => "Iron Bank S.A.",
        "taxId" => "012.345.678-90",
        "externalId" => "1234",
        "tags" => [
            "Traveler Employee"
        ],
        "rules" => [
            new StarkInfra\IssuingRule([
                "name" => "General USD",
                "interval" => "day",
                "amount" => 100000,
                "currencyCode" => "USD"
            ])
        ]
    ]),
]);

foreach ($holders as $holder) {
    print_r($holder);
}
```

**Note**: Instead of using IssuingHolder objects, you can also pass each transfer element in dictionary format

### Query IssuingHolders

You can query multiple holders according to filters.

```php
use StarkInfra\IssuingHolder;

$holders = IssuingHolder::query();

foreach ($holders as $holder) {
    print_r($holder);
}
```

### Cancel an IssuingHolder

To cancel a single Issuing Holder by its id, run:

```php
use StarkInfra\IssuingHolder;

$holder = IssuingHolder::cancel("5155165527080960");

print_r($holder);
```

### Get an IssuingHolder

To get a single Issuing Holder by its id, run:

```php
use StarkInfra\IssuingHolder;

$holder = IssuingHolder::get("5155165527080960");

print_r($holder);
```

### Query IssuingHolder logs

You can query holder logs to better understand holder life cycles.

```php
use StarkInfra\IssuingHolder;

$logs = IssuingHolder\Log::query(["limit" => 50]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingHolder log

You can also get a specific log by its id.

```php
use StarkInfra\\IssuingHolder;

$log = IssuingHolder\Log::get("5155165527080960");

print_r($log);
```

### Create IssuingCards

You can issue cards with specific spending rules.

```php
use StarkInfra\IssuingCard;

$cards = IssuingCard::create([
    new IssuingCard([
        "holdeNname" => "Developers",
        "holderTaxId" => "012.345.678-90",
        "holderExternalId" => "1234",
        "rules" => [
            new StarkInfra\IssuingRule([
                "name" => "general",
                "interval" => "week",
                "amount" => 50000,
                "currencyCode" => "USD"
            ])
        ]
    ]),
]);

foreach ($cards as $card) {
    print_r($card);
}
```

### Query IssuingCards

You can get a list of created cards given some filters.

```php
use StarkInfra\IssuingCard;

$cards = IssuingCard::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($cards as $card) {
    print_r($card);
}
```

### Get an IssuingCard

After its creation, information on a card may be retrieved by its id.

```php
use StarkInfra\IssuingCard;

$card = IssuingCard::get("5155165527080960");

print_r($card);
```

### Update an IssuingCard

You can update a specific card by its id.

```php
use StarkInfra\IssuingCard;

$card = IssuingCard::update("5155165527080960", ["status" => "blocked"]);

print_r($card);
```

### Cancel an IssuingCard

You can also cancel a card by its id.

```php
use StarkInfra\IssuingCard;

$card = IssuingCard::cancel("5155165527080960");

print_r($card);
```

### Query IssuingCard logs

Logs are pretty important to understand the life cycle of a card.

```php
use StarkInfra\IssuingCard;

$logs = IssuingCard\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingCard log

You can get a single log by its id.

```php
use StarkInfra\IssuingCard;

$log = IssuingCard\Log::get("5155165527080960");

print_r($log);
```

### Query IssuingDesigns

You can get a list of available designs given some filters.

```php
use StarkInfra\IssuingDesign;

$designs = IssuingDesign::query(["limit" => 1]);

foreach ($designs as $design) {
    print_r($design);
}
```

### Get an IssuingDesign

Information on a design may be retrieved by its id.

```php
use StarkInfra\IssuingDesign;

$design = IssuingDesign::get("5155165527080960");

print_r($design);
```

### Query IssuingEmbossingKits

You can get a list of created embossing kits given some filters.

```php
use StarkInfra\IssuingEmbossingKit;

$kits = IssuingEmbossingKit::query([
    "after" => "2022-11-01",
    "before" => "2022-12-01"
]);

foreach ($kits as $kit) {
    print_r($kit);
}
```

### Get an IssuingEmbossingKit

After its creation, information on an embossing kit may be retrieved by its id.

```php
use StarkInfra\IssuingEmbossingKit;

$kit = IssuingEmbossingKit::get("5155165527080960");

print_r($kit);
```

### Query IssuingStocks

You can get a list of available stocks given some filters.

```php
use StarkInfra\IssuingStock;

$stocks = IssuingStock::query(["limit" => 1]);

foreach ($stocks as $stock) {
    print_r($stock);
}
```

### Get an IssuingStock

Information on a stock may be retrieved by its id.

```php
use StarkInfra\IssuingStock;

$stock = IssuingStock::get("5155165527080960");

print_r($stock);
```

### Query IssuingStock logs

Logs are pretty important to understand the life cycle of a stock.

```php
use StarkInfra\IssuingStock;

$logs = IssuingStock\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingStock log

You can get a single log by its id.

```php
use StarkInfra\IssuingStock;

$log = IssuingStock\Log::get("5155165527080960");

print_r($log);
```

### Create IssuingRestocks

You can order restocks for a specific IssuingStock.

```php
use StarkInfra\IssuingRestock;

$restocks = IssuingRestock::create([
    new IssuingRestock([
        "count" => 100,
        "stockId" => "5136459887542272"
    ]),
]);

foreach ($restocks as $restock) {
    print_r($restock);
}
```

### Query IssuingRestocks

You can get a list of created restocks given some filters.

```php
use StarkInfra\IssuingRestock;

$restocks = IssuingRestock::query(["limit" => 1]);

foreach ($restocks as $restock) {
    print_r($restock);
}
```

### Get an IssuingRestock

After its creation, information on a restock may be retrieved by its id.

```php
use StarkInfra\IssuingRestock;

$restock = IssuingRestock::get("5664445921492992");
    
print_r($restock);
```

### Query IssuingRestock logs

Logs are pretty important to understand the life cycle of a restock.

```php
use StarkInfra\IssuingRestock;

$logs = IssuingRestock\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingRestock log

You can get a single log by its id.

```php
use StarkInfra\IssuingRestock;

$log = IssuingRestock\Log::get("5155165527080960");

print_r($log);
```

### Create IssuingEmbossingRequests

You can create a request to emboss a physical card.

```php
use StarkInfra\IssuingEmbossingRequest;

$requests = IssuingEmbossingRequest::create([
    new IssuingEmbossingRequest([
        "kitId" => "5648359658356736", 
        "cardId" => "5714424132272128", 
        "displayName1" => "Antonio Stark", 
        "shippingCity" => "Sao Paulo",
        "shippingCountryCode" => "BRA",
        "shippingDistrict" => "Bela Vista",
        "shippingService" => "loggi",
        "shippingStateCode" => "SP",
        "shippingStreetLine1" => "Av. Paulista, 200",
        "shippingStreetLine2" => "10 andar",
        "shippingTrackingNumber" => "My_custom_tracking_number",
        "shippingZipCode" => "12345-678",
        "embosserId" => "5746980898734080"
    ])
]);

foreach ($requests as $request) {
    print_r($request);
}
```

### Query IssuingEmbossingRequests

You can get a list of created embossing requests given some filters.

```php
use StarkInfra\IssuingEmbossingRequest;

$requests = IssuingEmbossingRequest::query(["limit" => 10]);

foreach ($requests as $request) {
    print_r($request);
}
```

### Get an IssuingEmbossingRequest

After its creation, information on an embossing request may be retrieved by its id.

```php
use StarkInfra\IssuingEmbossingRequest;

$request = IssuingEmbossingRequest::get("5664445921492992");
    
print_r($request);
```

### Query IssuingEmbossingRequest logs

Logs are pretty important to understand the life cycle of an embossing request.

```php
use StarkInfra\IssuingEmbossingRequest;

$logs = IssuingEmbossingRequest\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingEmbossingRequest log

You can get a single log by its id.

```php
use StarkInfra\IssuingEmbossingRequest;

$log = IssuingEmbossingRequest\Log::get("5155165527080960");

print_r($log);
```

### Create an IssuingTokenRequest

You can create a request that provides the required data you must send to the wallet app.

```php
use StarkInfra\IssuingTokenRequest;

$requests = IssuingTokenRequest::create([
    new IssuingTokenRequest([
        "cardId" => "5189831499972623", 
        "walletId" => "google", 
        "methodCode" => "app"
    ])
]);

foreach ($requests as $request) {
    print_r($request);
}
```

### Process Token authorizations

It's easy to process token authorizations delivered to your endpoint.
Remember to pass the signature header so the SDK can make sure it's StarkInfra that sent you the event.
If you do not approve or decline the authorization within 2 seconds, the authorization will be denied.

```php
use StarkInfra\IssuingToken;

$request = listen();  # this is your handler to listen for authorization requests

$token = IssuingToken::parse(
    $request->content, 
    $request->headers["Digital-Signature"]
);

# after parsing you should analyse the authorization request and then respond

# To approve:
sendResponse(  # you should also implement this method to respond the read request
    IssuingToken::response([
        "status" => "approved",
        "activation_methods" =>[
            {
                "type" => "app",
                "value" => "com.subissuer.android"
            },
            {
                "type" => "text",
                "value" => "** *****-5678"
            }
        ],
        "designId" => "4584031664472031",
        "tags" => ["token", "user/1234"]
    ]);
);

# To deny:
sendResponse(  # you should also implement this method to respond the read request
    IssuingToken::response([
        "status" => "denied",
        "reason" => "other",
    ]);
);
```

### Process Token activations

It's easy to process token activation notifications delivered to your endpoint.
Remember to pass the signature header so the SDK can make sure it's Stark Infra that sent you the event.


```php
use StarkInfra\IssuingToken;

$request = listen();  # this is the method you made to get the events posted to your tokenAuthorizationUrl endpoint

$token = IssuingToken::parse(
    $request->content, 
    $request->headers["Digital-Signature"]
);
```

After that, you may generate the activation code and send it to the cardholder.
The cardholder enters the received code in the wallet app. We'll receive and send it to
tokenAuthorizationUrl for your validation. Completing the provisioning process. 

```php
use StarkInfra\IssuingToken;

$request = listen();  # this is the method you made to get the events posted to your tokenAuthorizationUrl endpoint

sendResponse(  # you should also implement this method to respond the read request
    IssuingToken::response([
        "status" => "approved",
        "tags" => ["token", "user/1234"]
    ]);
);

# To deny:
sendResponse(  # you should also implement this method to respond the read request
    IssuingToken::response([
        "status" => "denied",
        "reason" => "other",
        "tags" => ["token", "user/1234"]
    ]);
);
```

### Get an IssuingToken

You can get a single token by its id.

```php
use StarkInfra\IssuingToken;

$token = IssuingToken::get("5749080709922816");
    
print_r($token);
```

### Query IssuingTokens

You can get a list of created tokens given some filters.

```php
use StarkInfra\IssuingToken;

$tokens = IssuingToken::query(["limit" => 10]);

foreach ($tokens as $token) {
    print_r($token);
}
```
 
### Update an IssuingToken

You can update a specific token by its id.

```php
use StarkInfra\IssuingToken;

$token = IssuingToken::update(
    "5155165527080960",
    "status" => "blocked"
);

print_r($token);
```

### Cancel an IssuingToken

You can also cancel a token by its id.

```php
use StarkInfra\IssuingToken;

$token = IssuingToken::cancel("5155165527080960");

print_r($token);
```

### Get an IssuingTokenDesign

You can get a single design by its id.

```php
use StarkInfra\IssuingTokenDesign;

$design = IssuingTokenDesign::get("5749080709922816");
    
print_r($design);
```

### Query IssuingTokenDesigns 

You can get a list of available designs given some filters.

```php
use StarkInfra\IssuingTokenDesign;

$designs = IssuingTokenDesign::query(["limit" => 5]);

foreach ($designs as $design) {
    print_r($design);
}
```
## Get an IssuingTokenDesign PDF

A design PDF can be retrieved by its id. 

```php
use StarkInfra\IssuingTokenDesign;

$pdf = IssuingTokenDesign::pdf("5155165527080960");

$fp = fopen('design.zip', 'w');
fwrite($fp, $pdf);
fclose($fp);
```

## Process Purchase Authorizations

It's easy to process purchase authorizations delivered to your endpoint.
Remember to pass the signature header so the SDK can make sure it's StarkInfra that sent you the event.
If you do not approve or decline the authorization within 2 seconds, the authorization will be denied.

```php
use StarkInfra\IssuingPurchase;

$request = listen();  # this is your handler to listen for authorization requests

$purchase = IssuingPurchase::parse(
    $request->content, 
    $request->headers["Digital-Signature"]
);

# after parsing you should analyse the authorization request and then respond

# To approve:
sendResponse(  # you should also implement this method to respond the read request
    IssuingPurchase::response([
        "status" => "approved",
    ]);
);

# To deny:
sendResponse(  # you should also implement this method to respond the read request
    IssuingPurchase::response([
        "status" => "denied",
        "reason" => "stolenCard",
    ]);
);
```

### Query IssuingPurchases

You can get a list of created purchases given some filters.

```php
use StarkInfra\IssuingPurchase;

$purchases = IssuingPurchase::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($purchases as $purchase) {
    print_r($purchase);
}
```

### Get an IssuingPurchase

After its creation, information on a purchase may be retrieved by its id. 

```php
use StarkInfra\IssuingPurchase;

$log = IssuingPurchase::get("5155165527080960");

print_r($log);
```

### Query IssuingPurchase logs

Logs are pretty important to understand the life cycle of a purchase.

```php
use StarkInfra\IssuingPurchase;

$logs = IssuingPurchase\Log::query(["limit" => 150]);

foreach($logs as $log) {
    print_r($log);
}
```

### Get an IssuingPurchase log

You can get a single log by its id.

```php
use StarkInfra\IssuingPurchases;

$log = IssuingPurchase\Log::get("5155165527080960");

print_r($log);
```

### Create IssuingInvoices

You can create Pix invoices to transfer money from accounts you have in any bank to your Issuing balance, allowing you to run your issuing operation.

```php
use StarkInfra\IssuingInvoice;

$invoices = IssuingInvoice::create(
    new IssuingInvoice([
        "amount" => 1000
    ])
);

foreach ($invoices as $invoice) {
    print_r($invoice);
}
```

**Note**: Instead of using Invoice objects, you can also pass each invoice element in dictionary format

### Get an IssuingInvoice

After its creation, information on an invoice may be retrieved by its id. 
Its status indicates whether it's been paid.

```php
use StarkInfra\IssuingInvoice;

$invoice = IssuingInvoice::get("5155165527080960");

print_r($invoice);
```

### Query IssuingInvoices

You can get a list of created invoices given some filters.

```php
use StarkInfra\IssuingInvoice;

$invoices = IssuingInvoice::query(
    "after" => "2020-01-01",
    "before" => "2020-03-01"
);

foreach ($invoices as $invoice) {
    print_r($invoice);
}
```

### Query IssuingInvoice logs

Logs are pretty important to understand the life cycle of an invoice.

```php
use StarkInfra\IssuingInvoice;

$logs = IssuingInvoice\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an IssuingInvoice log

You can also get a specific log by its id.

```php
use StarkInfra\IssuingInvoice;

$log = IssuingInvoice\Log::get("5155165527080960");

print_r($log);
```

### Create IssuingWithdrawals

You can create withdrawals to send cash back from your Issuing balance to your Banking balance
by using the Withdrawal resource.

```php
use StarkInfra\IssuingWithdrawal;

$withdrawals = IssuingWithdrawal::create(
    new IssuingWithdrawal([
        "amount" => 10000.
        "externalId" => "123",
        "description" => "Sending back"
    ])
);

foreach ($withdrawals as $withdrawal) {
    print_r($withdrawal);
}
```

**Note**: Instead of using Withdrawal objects, you can also pass each withdrawal element in dictionary format

### Get an IssuingWithdrawal

After its creation, information on a withdrawal may be retrieved by its id.

```php
use StarkInfra\IssuingWithdrawal;

$withdrawal = IssuingWithdrawal::get("5155165527080960");

print_r($withdrawal);
```

### Query IssuingWithdrawals

You can get a list of created withdrawals given some filters.

```php
use StarkInfra\IssuingWithdrawal;

$withdrawals = IssuingWithdrawal::query(
    "after" => "2020-01-01",
    "before" => "2020-03-01"
);

foreach ($withdrawals as $withdrawal) {
    print_r($withdrawal);
}
```

### Get your IssuingBalance

To know how much money you have available to run authorizations, run:

```php
use StarkInfra\IssuingBalance;

$balance = IssuingBalance::get();

print_r($balance);
```

### Query IssuingTransactions

To understand your balance changes (issuing statement), you can query
transactions. Note that our system creates transactions for you when
you make purchases, withdrawals, receive issuing invoice payments, for example.

```php
use StarkInfra\IssuingTransaction;

$transactions = IssuingTransaction::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($transactions as $transaction) {
    print_r($transaction);
}
```

### Get an IssuingTransaction

You can get a specific transaction by its id:

```php
use StarkInfra\IssuingTransaction;

$transaction = IssuingTransaction::get("5155165527080960");

print_r($transaction);
```

### Issuing Enums

#### Query MerchantCategories

You can query any merchant categories using this resource.
You may also use MerchantCategories to define specific category filters in IssuingRules.
Either codes (which represents specific MCCs) or types (code groups) will be accepted as filters.

```php
use StarkInfra\MerchantCategory;

$categories = MerchantCategory::query([
    "search" => "food"
]);

foreach ($categories as $category) {
    print_r($category);
}
```

#### Query MerchantCountries

You can query any merchant countries using this resource.
You may also use MerchantCountries to define specific country filters in IssuingRules.

```php
use StarkInfra\MerchantCountry;

$countries = MerchantCountry::query([
    "search" => "brazil"
]);

foreach ($countries as $country) {
    print_r($country);
}
```

#### Query CardMethods

You can query available card methods using this resource.
You may also use CardMethods to define specific purchase method filters in IssuingRules.

```php
use StarkInfra\CardMethod;

$methods = CardMethod::query([
    "search" => "token"
]);

foreach ($methods as $method) {
    print_r($method);
}
```

### SimulatePurchaseAuthorization

#### Simulate a test purchase

You can simulate a purchase authorization to test your integration.

```php
use StarkInfra\SimulatePurchaseAuthorization;

// Opção 1: Passando os parâmetros diretamente para o método purchase
$authorization = SimulatePurchaseAuthorization::purchase([
    "cardNumber" => "1122334455667788",
    "cardExpiration" => "2025-07",
    "securityCode" => 123,
    "amount" => 2500,
    "merchantName" => "Test Merchant 1",
    "merchantCategoryCode" => "hotelsMotelsAndResorts",
    "merchantCountryCode" => "BRA",
    "merchantCurrencyCode" => "BRL",
    "methodCode" => "contactless",
    "walletId" => "apple",
    "status" => "approved",
    "partial" => false
]);

// Opção 2: Criando um objeto SimulatePurchaseAuthorization primeiro
$params = new SimulatePurchaseAuthorization([
    "cardNumber" => "1122334455667788",
    "cardExpiration" => "2025-07",
    "securityCode" => 123,
    "amount" => 2500,
    "merchantName" => "Test Merchant 1",
    "merchantCategoryCode" => "hotelsMotelsAndResorts",
    "merchantCountryCode" => "BRA",
    "merchantCurrencyCode" => "BRL",
    "methodCode" => "contactless",
    "walletId" => "apple",
    "status" => "",
    "partial" => false
]);
$authorization = SimulatePurchaseAuthorization::purchase($params);

print_r($authorization);
```

## Pix

### Create PixRequests

You can create a Pix request to transfer money from one of your users to anyone else:

```php
use StarkInfra\PixRequest;
use StarkInfra\Utils\EndToEndId;

$requests = PixRequest::create([
    new PixRequest([
        "amount" => 1000,
        "externalId" => "my-external-id:1",
        "senderAccountNumber" => "76543-8",
        "senderBranchCode" => "2201",
        "senderAccountType" => "checking",
        "senderName" => "Tony Stark",
        "senderTaxId" => "594.739.480-42",
        "receiverBankCode" => "341",
        "receiverAccountNumber" => "00000-0",
        "receiverBranchCode" => "0001",
        "receiverAccountType" => "checking",
        "receiverName" => "Daenerys Targaryen Stormborn",
        "receiverTaxId" => "012.345.678-90",
        "endToEndId" => EndToEndId::create("20018183"),
    ]),
    new PixRequest([
        "amount" => 200,
        "externalId" => "my-external-id:2",
        "senderAccountNumber" => "76543-8",
        "senderBranchCode" => "2201",
        "senderAccountType" => "checking",
        "senderName" => "Tony Stark",
        "senderTaxId" => "594.739.480-42",
        "receiverBankCode" => "341",
        "receiverAccountNumber" => "00000-0",
        "receiverBranchCode" => "0001",
        "receiverAccountType" => "checking",
        "receiverName" => "Daenerys Targaryen Stormborn",
        "receiverTaxId" => "012.345.678-90",
        "endToEndId" => EndToEndId::create("20018183"),
    ]);
]);

foreach($requests as $request){
    print_r($request);
}
```

**Note**: Instead of using Pix Request objects, you can also pass each element in dictionary format

## Query PixRequests

You can query multiple Pix Requests according to filters.

```php
use StarkInfra\PixRequest;

$requests = PixRequest::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "success",
    "tags" => ['iron', 'suit'],
    "endToEndIds" => ['E79457883202101262140HHX553UPqeq'],
]);

foreach($requests as $request){
    print_r($request);
}
```

## Get a PixRequest

After its creation, information on a Pix Request may be retrieved by its id. Its status indicates whether it has been paid.

```php
use StarkInfra\PixRequest;

$request = PixRequest::get("5155966664310784");

print_r($request);
```

### Process inbound PixRequest authorizations

It's easy to process authorization requests that arrived at your endpoint.
Remember to pass the signature header so the SDK can make sure it's StarkInfra that sent you the event.
If you do not approve or decline the authorization within 1 second, the authorization will be denied.

```php
use StarkInfra\PixRequest;

$request = listen();  # this is your handler to listen for authorization requests

$request = PixRequest::parse(
    $request->content, 
    $request->headers["Digital-Signature"]
);

# after parsing you should analyse the authorization request and then respond

# To approve:
sendResponse(  # you should also implement this method to respond the read request
    PixRequest::response([
        "status" => "approved",
    ]);
);

# To deny:
sendResponse(  # you should also implement this method to respond the read request
    PixRequest::response([
        "status" => "denied",
        "reason" => "invalidAccountNumber",
    ]);
);
```

## Query PixRequest logs

You can query Pix Request Logs to better understand Pix Request life cycles.

```php
use StarkInfra\PixRequest;

$logs = PixRequest\Log::query([
    "limit" => 10,
    "types" => "created",
    "after" => "2020-04-30",
]);

foreach($logs as $log){
    print_r($log->id);
}
```

## Get a PixRequest log

You can also get a specific log by its id.

```php
use StarkInfra\PixRequest;

$log = PixRequest\Log::get("5155165527080960");

print_r($log);
```

## Create PixReversals

You can reverse a PixRequest either partially or totally using a PixReversal.

```php
use StarkInfra\PixReversal;

$reversals = PixReversal::create([
    new PixReversal([
        "amount" => 100,
        "externalId" => "my-external-id:3",
        "endToEndId" => "E00000000202201060100rzsJzG9PzMg",
        "reason" => "fraud",
    ]),
    new PixReversal([
        "amount" => 200,
        "externalId" => "my-external-id:4",
        "endToEndId" => "E00000000202201060100rzsJzG9P1GH",
        "reason" => "fraud",
    ]);
]);

foreach($reversals as $reversal){
    print_r($reversal);
}
```

## Query PixReversals

You can query multiple Pix Reversals according to filters.

```php
use StarkInfra\PixReversal;

$reversals = PixReversal::query([
    "fields" => ['amount', 'senderName'],
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "success",
    "tags" => ['iron', 'suit'],
    "returnIds" => ['D20018183202202030109X3OoBHG74wo'],
]);

foreach($reversals as $reversal){
    print_r($reversals);
}
```

## Get a PixReversal

After its creation, information on a Pix Reversal may be retrieved by its id. Its status indicates whether it has been paid.

```php
use StarkInfra\PixReversal;

$reversal = PixReversal::get("5155966664310784");

print_r($reversal);
```

### Process inbound PixReversal authorizations

It's easy to process authorization requests that arrived at your endpoint.
Remember to pass the signature header so the SDK can make sure it's StarkInfra that sent you the event.
If you do not approve or decline the authorization within 1 second, the authorization will be denied.

```php
use StarkInfra\PixReversal;

$request = listen();  # this is your handler to listen for authorization requests

$reversal = PixReversal::parse(
    $request->content, 
    $request->headers["Digital-Signature"]
);

# after parsing you should analyse the authorization request and then respond

# To approve:
sendResponse(  # you should also implement this method to respond the read request
    PixReversal::response([
        "status" => "approved",
    ]);
);

# To deny:
sendResponse(
    PixReversal::response([
        "status" => "denied",
        "reason" => "invalidAccountNumber",
    ]);
);

```

## Query PixReversal logs

You can query Pix Reversal logs to better understand Pix Reversal life cycles.

```php
use StarkInfra\PixReversal;

$logs = PixReversal\Log::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
]);

foreach($logs as $log){
    print_r($log->id);
}
```

## Get a PixReversal log

You can also get a specific log by its id.

```php
use StarkInfra\PixReversal;

$log = PixReversal\Log::get("5155165527080960");

print_r($log);
```

## Get your PixBalance

To see how much money you have in your account, run:

```php
use StarkInfra\PixBalance;

$balance = PixBalance::get();

print_r($balance);
```

## Create a PixStatement

Statements are generated directly by the Central Bank and are only available for direct participants.
To create a statement of all the transactions that happened on your account during a specific day, run:

```php
use StarkInfra\PixStatement;

$statement = PixStatement::create(
    new PixStatement([
        "after" => "2022-01-01",
        "before" => "2022-01-01",
        "type" => "transaction",
    ]);
);

print_r($statement)
```
## Query PixStatements

You can query multiple Pix Statements according to filters.

```php
use StarkInfra\PixStatement;

$statements = PixStatement::query([
    "limit" => 10,
    "ids" => ["5155165527080960"],
]);

foreach($statements as $statement){
    print_r($statement);
}
```

## Get a PixStatement

Statements are only available for direct participants. To get a Pix Statement by its id:

```php
use StarkInfra\PixStatement;

$statement = PixStatement::get("5155966664310784");

print_r($statement);
```

## Get a PixStatement .csv file

To get a .csv file of a Pix Statement using its id, run:

```php
use StarkInfra\PixStatement;

$csv = PixStatement::csv("5656565656565656");

$fp = fopen('statement.zip', 'w');
fwrite($fp, $csv);
fclose($fp);
```

### Create a PixKey

You can create a Pix Key to link a bank account information to a key id:

```php
use StarkInfra\PixKey;

$keys = PixKey::create(
    new PixKey([
        "accountCreated" => "2022-01-01",
        "accountNumber" => "76543",
        "accountType" => "salary",
        "branchCode" => "1234",
        "name" => "Jamie Lannister",    
        "taxId" => "012.345.678-90"
    ]);
);

foreach($keys as $key){
    print_r($key);
}
```

### Query PixKeys

You can query multiple Pix Keys you own according to filters.

```php
use StarkInfra\PixKey;

$keys = PixKey::query([
    "after" => "2020-04-01",
    "before" => "2020-04-30"
]);

foreach($keys as $key){
    print_r($key);
}
```

### Get a PixKey

Information on a Pix Key may be retrieved by its id and the tax ID of the consulting agent.
An endToEndId must be informed so you can link any resulting purchases to this query,
avoiding sweep blocks by the Central Bank.

```php
use StarkInfra\PixKey;
use StarkInfra\Utils\EndToEndId;

$key = PixKey::get(
    "5915632394567680",
    "20.018.183/0001-80",
    [
        "endToEndId" => EndToEndId::create("20018183")
    ]
);

print_r($key);

```

### Update a PixKey

Update the account information linked to a Pix Key.

```php
use StarkInfra\PixKey;

$key = PixKey::update(
    "6203417408045056",
    "reconciliation"
    [
        "name" => "Tony Stark"
    ]
);

print_r($key);
```

### Cancel a PixKey

Cancel a specific Pix Key using its id.

```php
use StarkInfra\PixKey;

$key = PixKey::cancel("5915632394567680");

print_r($key);
```

### Query PixKey logs

You can query Pix Key logs to better understand a Pix Key life cycle. 

```php
use StarkInfra\PixKey;

$logs = PixKey\Log::query([
    "limit" => 50, 
    "after" => "2022-01-01",
    "before" => "2022-01-20",
    "types" => [
        "created"
    ]
]);

foreach($logs as $log){
    print_r($log);
}
```

### Get a PixKey log

You can also get a specific log by its id.

```php
use StarkInfra\PixKey;

$log = PixKey\Log::get("5155165527080960");

print_r($log);
```

### Create a PixClaim

You can create a Pix Claim to request the transfer of a Pix Key from another bank to one of your accounts:

```php
use StarkInfra\PixClaim;

$claim = PixClaim::create(
    new PixClaim([
        "accountCreated" => "2022-01-01",
        "accountNumber" => "76549", 
        "accountType" => "salary", 
        "branchCode" => "1234",
        "name" => "Random Name",
        "taxId" => "012.345.678-90",
        "keyId" => "+551165857989",
    ]);
);

print_r($claim)
```

### Query PixClaims

You can query multiple Pix claims according to filters.

```php
use StarkInfra\PixClaim;

$claims = PixClaim::query([
    "limit" => 1,
    "after" => "2022-01-01",
    "before" => "2022-01-12",
    "status" => "delivered",
    "ids" => ["5729405850615808"],
    "type" => "ownership",
    "flow" => "in",
    "keyType" => "phone",
    "keyId" => "+5511989898989"
]);

foreach ($claims as $claim){
    print_r($claim);
}

```

### Get a PixClaim

After its creation, information on a Pix Claim may be retrieved by its id.

```php
use StarkInfra\PixClaim;

$claim = PixClaim::get("5155165527080960");

print_r($claim);
```

### Update a PixClaim

A Pix Claim can be confirmed or canceled by patching its status.
A received Pix Claim must be confirmed by the donor to be completed.
Ownership Pix Claims can only be canceled by the donor if the reason is "fraud".
A sent Pix Claim can also be canceled.

```php
use StarkInfra\PixClaim;

$claim = PixClaim::update(
    "5155165527080960",
    "canceled"
);

print_r($claim);
```

### Query PixClaim logs

You can query Pix Claim logs to better understand Pix Claim life cycles.

```php
use StarkInfra\PixClaim;

$logs = PixClaim\Log::query([
    "limit" => 50, 
    "ids" => ["5729405850615808"],
    "after" => "2022-01-01",
    "before" => "2022-01-20",
    "types" => ["created"],
    "claimIds" => ["5719405850615809"]
]);

foreach ($logs as $log){
    print_r($log)
};
```

### Get a PixClaim log

You can also get a specific log by its id.

```php
use StarkInfra\PixClaim;

$log = PixClaim\Log::get("5155165527080960");

print_r($log);
```

### Create a PixDirector

To register the Pix Director contact information at the Central Bank, run the following:

```php
use StarkInfra\PixDirector;

$director = PixDirector::create(
    new PixDirector ([
        "name" => "Edward Stark",
        "taxId" => "03.300.300/0001-00",
        "phone" => "+55-11999999999",
        "email" => "ned.stark@company.com",
        "password" => "12345678",
        "teamEmail" => "pix.team@company.com",
        "teamPhones" => [
            "+55-11988889999", "+55-11988889998"
        ]
    ]);
);

print_r($director);
```

### Create PixInfractions

Pix Infractions are used to report transactions that raise fraud suspicion, to request a refund or to 
reverse a refund. Pix Infractions can be created by either participant of a transaction.

```php
use StarkInfra\PixInfraction;

$infractions = PixInfraction::create([
    new PixInfraction([
        "referenceId" => "E20018183202201201450u34sDGd19lz",
        "type" => "fraud",
        "method" => "scam",
        "operatorEmail" => "fraud@company.com",
        "operatorPhone" => "+5511989898989",
    ]);
]);

foreach ($infractions as $infraction){
    print_r($infraction);
}
```

### Query PixInfractions

You can query multiple Pix Infractions according to filters.

```php
use StarkInfra\PixInfraction;

$infractions = PixInfraction::query([
    "limit" => 1,
    "after" => "2022-01-01",
    "before" => "2022-01-12",
    "status" => "delivered",
    "ids" => ["5155165527080960"],
]);

foreach ($infractions as $infraction){
    print_r($infraction);
}
```

### Get a PixInfraction

After its creation, information on a Pix Infraction may be retrieved by its id.

```php
use StarkInfra\PixInfraction;

$infraction = PixInfraction::get("5155165527080960");

print_r($infraction);
```

### Update a PixInfraction

A received Pix Infraction can be confirmed or declined by patching its status.
After a Pix Infraction is patched, its status changes to closed.

```php
use StarkInfra\PixInfraction;

$infraction = PixInfraction::update(
    "5155165527080960",
    "agreed"
)

print_r($infraction)
```

### Cancel a PixInfraction

Cancel a specific Pix Infraction using its id.

```php
use StarkInfra\PixInfraction;

$infraction = PixInfraction::cancel("5155165527080960");

print_r($infraction);
```

### Query PixInfraction logs

You can query Pix Infraction Logs to better understand their life cycles. 

```php
use StarkInfra\PixInfraction;

$logs = PixInfraction\Log::query([
    "limit" => 50, 
    "ids" => ["5729405850615808"],
    "after" =>"2022-01-01",
    "before" =>"2022-01-20",
    "types" => ["created"],
    "infractionIds" => ["5155165527080960"]
]);

foreach ($logs as $log){
    print_r($log)
}
```

### Get a PixInfraction log

You can also get a specific log by its id.

```php
use StarkInfra\PixInfraction;

$log = PixInfraction\Log::get("5155165527080960");

print_r($log);
```

### Create a PixFraud 

Pix Frauds can be created by either participant or automatically when a Pix Infraction is accepted.

```php
use StarkInfra\PixFraud;

$frauds = PixFraud::create([
    new PixFraud([
        "externalId" => "my_external_id_1234",
        "type" => "mule",
        "taxId" => "01234567890",
    ]);
]);

foreach ($frauds as $fraud){
    print_r($fraud);
}
```

### Query PixFrauds

You can query multiple Pix Frauds according to filters.

```php
use StarkInfra\PixFraud;

$frauds = PixFraud::query([
    "limit" => 1,
    "after" => "2022-01-01",
    "before" => "2022-01-12",
    "status" => "delivered",
    "ids" => ["6638842090094592", "4023146587080960"],
]);

foreach ($frauds as $fraud){
    print_r($fraud);
}
```

### Get a PixFraud

After its creation, information on a Pix Fraud may be retrieved by its id.

```php
use StarkInfra\PixInfraction;

$infraction = PixInfraction::get("5155165527080960");

print_r($infraction);
```

### Cancel a PixFraud

Cancel a specific Pix Fraud using its id.

```php
use StarkInfra\PixFraud;

$fraud = PixFraud::cancel("5155165527080960");

print_r($fraud);
```

### Get a PixUser

You can get a specific fraud statistics of a user with his taxId.

```php
use StarkInfra\PixUser;

$user = PixUser::get("01234567890");

print_r($user);
```

### Create PixChargebacks

A Pix Chargeback can be created when fraud is detected on a transaction or a system malfunction 
results in an erroneous transaction.

```php
use StarkInfra\PixChargeback;

$chargebacks = PixChargeback::create([
    new PixChargeback([
        "amount" => 100,
        "referenceId" => "E20018183202201201450u34sDGd19lz",
        "reason" => "fraud"
    ]);
]);

foreach ($chargebacks as $chargeback){
    print($chargeback);
}    
```

### Query PixChargebacks

You can query multiple Pix Chargebacks according to filters.

```php
use StarkInfra\PixChargeback;

$chargebacks = PixChargeback::query([
    "limit" => 1,
    "after" => "2022-01-01",
    "before" => "2022-01-12",
    "status" => "delivered",
    "ids" => ["5155165527080960"]
]);

foreach ($chargebacks as $chargeback){
    print($chargeback);
}    
```

### Get a PixChargeback

After its creation, information on a Pix Chargeback may be retrieved by its.

```php
use StarkInfra\PixChargeback;

$chargeback = PixChargeback::get("5155165527080960");

print_r($chargeback);
```

### Update a PixChargeback

A received Pix Chargeback can be approved or denied by patching its status.
After a Pix Chargeback is patched, its status changes to closed.

```php
use StarkInfra\PixChargeback;

$chargeback = PixChargeback::update(
    "5155165527080960",
    "accepted"
);

print_r($chargeback);
```

### Cancel a PixChargeback

Cancel a specific Pix Chargeback using its id.

```php
use StarkInfra\PixChargeback;

$chargeback = PixChargeback::cancel("5155165527080960");

print_r($chargeback);
```

### Query PixChargeback logs

You can query Pix Chargeback Logs to better understand reversal request life cycles. 

```php
use StarkInfra\PixChargeback;

$logs = PixChargeback\Log::query([
    "limit" => 50, 
    "ids" => ["5729405850615808"],
    "after" => "2022-01-01",
    "before" => "2022-01-20",
    "types" => ["created"],
    "chargebackIds" => ["5155165527080960"]
]);

foreach ($logs as $log){
    print_r($log);
}
```

### Get a PixChargeback log

You can also get a specific log by its id.

```php
use StarkInfra\PixChargeback;

$log = PixChargeback\Log::get("5155165527080960");

print_r($log);
```

### Query PixDomains

Here you can list all Pix Domains registered at the Brazilian Central Bank. The Pix Domain object displays the domain name and the QR Code domain certificates of registered Pix participants able to issue dynamic QR Codes.

```php
use StarkInfra\PixDomain;

$domains = PixDomain::query();

foreach ($domains as $domain){
    print($domain);
}
```

### Create StaticBrcodes

StaticBrcodes store account information via a QR code or an image 
that represents a PixKey and a few extra fixed parameters, such as 
an amount and a reconciliation ID. They can easily be used to 
receive Pix transactions.

```php
use StarkInfra\StaticBrcode;

$brcodes = StaticBrcode::create([
    new StaticBrcode([
        "name" => "Jamie Lannister",
        "keyId" => "+5511988887777",
        "amount" => 100,
        "reconciliationId" => "123",
        "city" =>"Rio de Janeiro"
    ]);
]);

foreach ($brcodes as $brcode){
    print($brcode);
}
```

### Query StaticBrcodes

You can query multiple StaticBrcodes according to filters.

```php
use StarkInfra\StaticBrcode;

$brcodes = StaticBrcode::query([
    "limit" => 50, 
    "after" => "2022-01-01",
    "before" => "2022-01-20",
    "uuids" => ["5ddde28043a245c2848b08cf315effa2"],
]);

foreach ($brcodes as $brcode){
    print_r($brcode);
}
```

### Get a StaticBrcodes

After its creation, information on a StaticBrcode may be retrieved by its UUID.

```php
use StarkInfra\StaticBrcode;

$brcode = StaticBrcode::get("5ddde28043a245c2848b08cf315effa2");

print_r($brcode);
```

### Create DynamicBrcodes

BR codes store information represented by Pix QR Codes, which are used to send 
or receive Pix transactions in a convenient way.
DynamicBrcodes represent charges with information that can change at any time,
since all data needed for the payment is requested dynamically to an URL stored
in the BR Code. Stark Infra will receive the GET request and forward it to your
registered endpoint with a GET request containing the UUID of the BR code for
identification.

```php
use StarkInfra\DynamicBrcode;

$brcodes = DynamicBrcode::create([
    new DynamicBrcode([
        "name" => "Jamie Lannister",
        "city" =>"Rio de Janeiro"
        "externalId" => "my_unique_id",
        "type" => "instant",
    ]);
]);

foreach ($brcodes as $brcode){
    print($brcode);
}
```

### Query DynamicBrcodes

You can query multiple DynamicBrcodes according to filters.

```php
use StarkInfra\DynamicBrcode;

$brcodes = DynamicBrcode::query([
    "limit" => 50, 
    "after" => "2022-01-01",
    "before" => "2022-01-20",
    "uuids" => ["5ddde28043a245c2848b08cf315effa2"],
]);

foreach ($brcodes as $brcode){
    print_r($brcode);
}
```

### Get a DynamicBrcode

After its creation, information on a DynamicBrcode may be retrieved by its UUID.

```php
use StarkInfra\DynamicBrcode;

$brcode = DynamicBrcode::get("5ddde28043a245c2848b08cf315effa2");

print_r($brcode);
```

### Verify a DynamicBrcode Read

When a DynamicBrcode is read by your user, a GET request will be made to the URL stored in the DynamicBrcode to retrieve additional information needed to complete the transaction.
Use this method to verify the authenticity of a GET request received at your registered endpoint.
If the provided digital signature does not check out with the Stark public key, a
StarkInfra\Exception\InvalidSignatureException will be raised.

```php
use StarkInfra\DynamicBrcode;

$request = listen();  # this is the method you made to get the read requests posted to your registered endpoint

$uuid = DynamicBrcode::verify(
    $request->content, 
    $request->headers["Digital-Signature"]
);
```

### Answer to a Due DynamicBrcode Read

When a Due DynamicBrcode is read by your user, a GET request containing 
the BR code UUID will be made to your registered URL to retrieve additional 
information needed to complete the transaction.

The GET request must be answered within 5 seconds, with a HTTP status code 200, and 
in the following format.

```php
use StarkInfra\DynamicBrcode;

$request = listen();  # this is the method you made to get the read requests posted to your registered endpoint

$uuid = DynamicBrcode::verify(
    getUuid($request->url),  # you should implement this method to extract the UUID from the request's URL
    $request->headers["Digital-Signature"]
);

$invoice = getInvoice($uuid); # you should implement this method to get information on the BR code from its uuid

sendResponse(  # you should also implement this method to respond the read request
    DynamicBrcode::responseDue([
        "version" => $invoice->version,
        "created" => $invoice->created,
        "due" => $invoice->due,
        "keyId" => $invoice->keyId,
        "status" => $invoice->status,
        "reconciliationId" => $invoice->reconciliationId,
        "amount" => $invoice->amount,
        "senderName" => $invoice->senderName,
        "senderTaxId" => $invoice->senderTaxId,
        "receiverName" => $invoice->receiverName,
        "receiverTaxId" => $invoice->receiverTaxId,
        "receiverStreetLine" => $invoice->receiverStreetLine,
        "receiverCity" => $invoice->receiverCity,
        "receiverStateCode" => $invoice->receiverStateCode,
        "receiverZipCode" => $invoice->receiverZipCode
    ]);
);
```

### Answer to an Instant DynamicBrcode read

When an instant DynamicBrcode is read by your user, a GET request containing the 
BR code's UUID will be made to your registered URL to retrieve additional information 
needed to complete the transaction.

The GET request must be answered within 5 seconds, with a HTTP status code 200, and 
in the following format.

```php
use StarkInfra\DynamicBrcode;

$request = listen();  # this is the method you made to get the read requests posted to your registered endpoint

$uuid = DynamicBrcode::verify(
    getUuid($request->url),  # you should implement this method to extract the uuid from the request's URL
    $request->headers["Digital-Signature"]
);

$invoice = getInvoice($uuid); # you should implement this method to get the information of the BR code from its uuid

sendResponse(  # you should also implement this method to respond the read request
    DynamicBrcode::responseDue([
        "version" => $invoice->version,
        "created" => $invoice->created,
        "keyId" => $invoice->keyId,
        "status" => $invoice->status,
        "reconciliationid" => $invoice->reconciliationId,
        "amount" => $invoice->amount,
        "cashierType" => $invoice->cashierType,
        "cashierBankCode" => $invoice->cashierBankCode,
        "cashAmount" => $invoice->cashAmount
    ]);
);
```

## Create BrcodePreviews

You can create BrcodePreviews to preview BR Codes before paying them.

```php
use StarkInfra\BrcodePreview;

$previews = BrcodePreview::create([
    new BrcodePreview([
        "id" => "00020126420014br.gov.bcb.pix0120nedstark@hotmail.com52040000530398654075000.005802BR5909Ned Stark6014Rio de Janeiro621605126674869738606304FF71",
        "payerId" => "123.456.780-01"
    ]),
    new BrcodePreview([
        "id" => "00020126430014br.gov.bcb.pix0121aryastark@hotmail.com5204000053039865406100.005802BR5910Arya Stark6014Rio de Janeiro6216051262678188104863042BA4",
        "payerId" => "123.456.780-01"
    ])
]);

foreach ($previews as $preview) {
    print_r($preview);
}
```

## Lending

If you want to establish a lending operation, you can use Stark Infra to
create a CCB contract. This will enable your business to lend money without
requiring a banking license, as long as you use a Credit Fund 
or Securitization company.

The required steps to initiate the operation are:
 1. Have funds in your Credit Fund or Securitization account
 2. Request the creation of an [Identity Check](#create-individualidentities)
for the credit receiver (make sure you have their documents and express authorization)
 3. (Optional) Create a [Credit Simulation](#create-creditpreviews) 
with the desired installment plan to display information for the credit receiver
 4. Create a [Credit Note](#create-creditnotes)
with the desired installment plan

## Create CreditNotes 

For lending operations, you can create a CreditNote to generate a CCB contract.

Note that you must have recently created an identity check for that same Tax ID before
being able to create a credit operation for them.

```php
use StarkInfra\CreditNote;
use StarkInfra\CreditSigner;
use StarkInfra\CreditNote\Invoice;
use StarkInfra\CreditNote\Transfer;

$notes = CreditNote::create([
    new CreditNote([
        "templateId" => "0123456789101112",
        "name" => "Jamie Lannister",
        "taxId" => "012.345.678-90",
        "nominalAmount" => 100000,
        "scheduled" => "2022-05-11",
        "payment" => new Transfer([
            "bankCode" => "00000000",
            "branchCode" => "1234",
            "accountNumber" => "129340-1",
            "taxId" => "012.345.678-90", 
            "name" => "Jamie Lannister"
        ]),
        "paymentType" => "transfer",
        "invoices" =>[
            new Invoice([
                "amount" => 120000,
                "due" => "2022-07-11",
                "fine" => 3.0,
                "interest" => 1.0
            ])
        ], 
        "signers" =>[
            new CreditSigner([
                "contact" =>  "jamie.lannister@gmail.com",
                "method" => "link",
                "name" => "Jamie Lannister",
            ])
        ],
        "rebateAmount" => 0,
        "tags" => [
            'War supply',
            'Invoice #1234'
        ],
        "externalId" => "my_unique_id",
        "streetLine1" => "Av. Paulista, 200",
        "streetLine2" => "10 andar",
        "district" => "Bela Vista",
        "city" => "Sao Paulo",
        "stateCode" => "SP",
        "zipCode" => "01310-000"
    ]);
]);

foreach($notes as $note){
    print_r($note);
}
```

**Note**: Instead of using CreditNote objects, you can also pass each element in dictionary format

## Query CreditNotes

You can query multiple Credit Notes according to filters.

```php
use StarkInfra\CreditNote;

$notes = CreditNote::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "signed",
]);

foreach($notes as $note){
    print_r($note);
}
```

## Get a CreditNote

After its creation, information on a Credit Note may be retrieved by its id.

```php
use StarkInfra\CreditNote;

$note = CreditNote::get("5155966664310784");

print_r($note);
```

## Cancel a CreditNote

You can cancel a Credit Note if it has not been signed yet.

```php
use StarkInfra\CreditNote;

$note = CreditNote::cancel("5155966664310784");

print_r($note);
```

## Query CreditNote logs

You can query Credit Note logs to better understand credit note life cycles.

```php
use StarkInfra\CreditNote\Log;

$logs = Log::query(["limit" => 10]);

foreach($logs as $log){
    print_r($log);
}
```

## Get a CreditNote log

You can also get a specific log by its id.

```php
use StarkInfra\CreditNote\Log;

$log = Log::get("5155966664310784");

print_r($log);
```

### Create CreditPreviews

You can preview a credit operation before creating them (Currently we only have CreditNote / CCB previews):

```php
use StarkInfra\CreditPreview;
use StarkInfra\CreditNote\Invoice;

$previews = CreditPreview::create([
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "type" => "american",
            "nominalAmount" => 100000,
            "scheduled" => "2022-10-11",
            "taxId" => "012.345.678-90",
            "initialDue" => "2022-11-11",
            "nominalInterest" => 10,
            "count" => 5,
            "interval" => "month",
        ])
    ]),
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "initialAmount" => 2478,
            "initialDue" => "2022-10-22",
            "nominalAmount" => 90583,
            "nominalInterest" => 3.7,
            "rebateAmount" => 23,
            "scheduled" => "2022-09-28",
            "taxId" => "477.954.506-44",
            "type" => "sac"
        ])
    ]),
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "initialAmount" => 4449,
            "initialDue" => "2022-09-16",
            "interval" => "year",
            "nominalAmount" => 96084,
            "nominalInterest" => 3.1,
            "rebateAmount" => 239,
            "scheduled" => "2022-09-02",
            "taxId" => "81.882.684/0001-02",
            "type" => "price"
        ])
    ]),
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "count" => 8,
            "initialDue" => "2022-09-18",
            "nominalAmount" => 6161,
            "nominalInterest" => 3.2,
            "scheduled" => "2022-09-03",
            "taxId" => "59.352.830/0001-20",
            "type" => "american"
        ])
    ]),
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "initialDue" => "2022-09-13",
            "nominalAmount" => 86237,
            "nominalInterest" => 2.6,
            "scheduled" => "2022-09-03",
            "taxId" => "37.293.955/0001-94",
            "type" => "bullet"
        ])
    ]),
    new CreditPreview([
        "type" => "credit-note",
        "credit" => new CreditPreview\CreditNotePreview([
            "invoices" => [
                new Invoice([
                    "amount" => 14500,
                    "due" => "2022-10-19"
                ]),
                new Invoice([
                    "amount" => 14500,
                    "due" => "2022-11-25"
                ])
            ],
            "nominalAmount" => 29000,
            "rebateAmount" => 900,
            "scheduled" => "2022-09-31",
            "taxId" => "36.084.400/0001-70",
            "type" => "custom"
        ])
    ]),
]);

foreach($previews as $preview){
    print_r($preview);
}
```

**Note**: Instead of using CreditPreview objects, you can also pass each element in dictionary format

### Create CreditHolmes

Before you request a credit operation, you may want to check previous credit operations
the credit receiver has taken.

For that, open up a CreditHolmes investigation to receive information on all debts and credit
operations registered for that individual or company inside the Central Bank's SCR.

```php
use StarkInfra\CreditHolmes;

$holmes = CreditHolmes::create([
    new CreditHolmes([
        "taxId" => "012.345.678-90",
        "competence" => "2022-09"
    ]),
    new CreditHolmes([
        "taxId" => "012.345.678-90",
        "competence" => "2022-08"
    ]),
    new CreditHolmes([
        "taxId" => "012.345.678-90",
        "competence" => "2022-07"
    ]);
]);

foreach($holmes as $sherlock){
    print_r($sherlock);
}
```

### Query CreditHolmes

You can query multiple CreditHolmes according to filters.

```php
use StarkInfra\CreditHolmes;

$holmes = CreditHolmes::query([
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "success",
]);

foreach($holmes as $sherlock){
    print_r($sherlock);
}
```

### Get a CreditHolmes

After its creation, information on a CreditHolmes may be retrieved by its id.

```php
use StarkInfra\CreditHolmes;

$logs = CreditHolmes::get("5155165527080960");

print_r($log);
```

### Query CreditHolmes logs

You can query CreditHolmes logs to better understand their life cycles. 

```php
use StarkInfra\CreditHolmes\Log;

$logs = Log::query([
    "limit" => 10,
    "ids" => ["5729405850615808"],
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "types" => ["created"]
]);

foreach($logs as $log){
    print_r($log);
}
```

### Get a CreditHolmes log

You can also get a specific log by its id.

```php
use StarkInfra\CreditHolmes\Log;

$logs = CreditHolmes\Log::get("5155165527080960");

print_r($log);
```

## Identity

Several operations, especially credit ones, require that the identity
of a person or business is validated beforehand.

Identities are validated according to the following sequence:
1. The Identity resource is created for a specific Tax ID
2. Documents are attached to the Identity resource
3. The Identity resource is updated to indicate that all documents have been attached
4. The Identity is sent for validation and returns a webhook notification to reflect
the success or failure of the operation

### Create IndividualIdentities

You can create an IndividualIdentity to validate a document of a natural person

```php
use StarkInfra\IndividualIdentity;

$identities = IndividualIdentity::create([
    new IndividualIdentity([
        "name" => "Walter White",
        "taxId" => "012.345.678-90",
        "tags" =>["breaking", "bad"]
    ]);
]);

foreach($identities as $identity){
    print_r($identity);
}
```

**Note**: Instead of using IndividualIdentity objects, you can also pass each element in dictionary format

### Query IndividualIdentity

You can query multiple individual identities according to filters.

```php
use StarkInfra\IndividualIdentity;

$identities = IndividualIdentity::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "success",
    "tags" =>["breaking", "bad"]
]);

foreach($identities as $identity){
    print_r($identity);
}
```

### Get an IndividualIdentity

After its creation, information on an individual identity may be retrieved by its id.

```php
use StarkInfra\IndividualIdentity;

$identity = IndividualIdentity::get("5155165527080960");

print_r($identity);
```

### Update an IndividualIdentity

You can update a specific identity status to "processing" for send it to validation.

```php
use StarkInfra\IndividualIdentity;

$identity = IndividualIdentity::update(
    "5155165527080960",
    "processing"
)

print_r($identity)
```

**Note**: Before sending your individual identity to validation by patching its status, you must send all the required documents using the create method of the CreditDocument resource. Note that you must reference the individual identity in the create method of the CreditDocument resource by its id.


### Cancel an IndividualIdentity

You can cancel an individual identity before updating its status to processing.

```php
use StarkInfra\IndividualIdentity;

$identity = IndividualIdentity::cancel("5155165527080960");

print_r($identity);
```

### Query IndividualIdentity logs

You can query individual identity logs to better understand individual identity life cycles. 

```php
use StarkInfra\IndividualIdentity\Log;

$logs = IndividualIdentity\Log::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30"
]);

foreach($logs as $log){
    print_r($log);
}
```

### Get an IndividualIdentity log

You can also get a specific log by its id.

```php
use StarkInfra\IndividualIdentity\Log;

$log = IndividualIdentity\Log::get("5155165527080960");

print_r($log);
```

### Create IndividualDocuments

You can create an individual document to attach images of documents to a specific individual Identity.
You must reference the desired individual identity by its id.

```php
$documents = IndividualDocument::create([
    new IndividualDocument([
        "type" => "identity-front",
        "content" => "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAASABIAAD...",
        "identityId" => "5155165527080960",
        "tags" => ["breaking", "bad"]
    ]),
    new IndividualDocument([
        "type" => "identity-back",
        "content" => "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAASABIAAD...",
        "identityId" => "5155165527080960",
        "tags" => ["breaking", "bad"]
    ]),
    new IndividualDocument([
        "type" => "selfie",
        "content" => "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAASABIAAD...",
        "identityId" => "5155165527080960",
        "tags" => ["breaking", "bad"]
    ]);
]);

foreach($documents as $document){
    print_r($document);
}
```

**Note**: Instead of using IndividualDocument objects, you can also pass each element in dictionary format

### Query IndividualDocuments

You can query multiple individual documents according to filters.

```php
use StarkInfra\IndividualDocument;

$logs = IndividualDocument::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30",
    "status" => "success",
    "tags" => ["breaking", "bad"]
]);

foreach($logs as $log){
    print_r($log);
}
```

### Get an IndividualDocument

After its creation, information on an individual document may be retrieved by its id.

```php
use StarkInfra\IndividualDocument;

$document = IndividualDocument::get("5155165527080960");

print_r($document);
```
  
### Query IndividualDocument logs

You can query individual document logs to better understand individual document life cycles. 

```php
use StarkInfra\IndividualDocument\Log;

$logs = IndividualDocument\Log::query([
    "limit" => 10,
    "after" => "2020-04-01",
    "before" => "2020-04-30"
]);

foreach($logs as $log){
    print_r($log);
}
```

### Get an IndividualDocument log

You can also get a specific log by its id.

```php
use StarkInfra\IndividualDocument\Log;

$log = IndividualDocument\Log::get("5155165527080960");

print_r($log);
```

## Webhook

## Create a webhook

To create a Webhook and be notified whenever an event occurs, run:

```php
use StarkInfra\Webhook;

$webhook = Webhook::create(
    new Webhook(
        "url" => "https://webhook.site/",
        "subscriptions" =>[
            "credit-note"
            "issuing-card", "issuing-invoice", "issuing-purchase",
            "pix-request.in", "pix-request.out", "pix-reversal.in", "pix-reversal.out", "pix-claim", "pix-key", "pix-infraction", "pix-chargeback"
        ]
    );
);

print_r($webhook);
```

## Query webhooks

To search for registered Webhooks, run:

```php
use StarkInfra\Webhook;

$webhooks = Webhook::query();

foreach($webhooks as $webhook){
    print_r($webhook);
}
```

## Get a webhook

You can get a specific Webhook by its id.

```php
use StarkInfra\Webhook;

$webhook = Webhook::get("1082736198236817");

print_r($webhook);
```

## Delete a webhook

You can also delete a specific Webhook by its id.

```php
use StarkInfra\Webhook;

$webhook = Webhook::delete("1082736198236817");

print_r($webhook);
```

## Process Webhook events

It's easy to process events delivered to your Webhook endpoint.
Remember to pass the signature header so the SDK can make sure it was StarkInfra that sent you the event.

```php
use StarkInfra\Event;

$response = listen()  # this is the method you made to get the events posted to your webhook

$event = Event::parse($response->content, $response->headers["Digital-Signature"]);

if ($event->subscription == "pix-request.in"){
    print_r($event->log->request);
} elseif ($event->subscription == "pix-claim"){
    print_r($event->log->claim);
} elseif ($event->subscription == "pix-key"){
    print_r($event->log->key);
} elseif ($event->subscription == "pix-infraction"){
    print_r($event->log->infraction);
} elseif ($event->subscription == "pix-chargeback"){
    print_r($event->log->chargeback);
} elseif ($event->subscription == "pix-request.out"){
    print_r($event->log->request);
} elseif ($event->subscription == "pix-reversal.in"){
    print_r($event->log->reversal);
} elseif ($event->subscription == "pix-reversal.out"){
    print_r($event->log->reversal);
} elseif ($event->subscription == "issuing-card"){
    print_r($event->log->card);
} elseif ($event->subscription == "issuing-invoice"){
    print_r($event->log->invoice);
} elseif ($event->subscription == "issuing-purchase"){
    print_r($event->log->purchase);
} 
```

## Query webhook events

To search for webhooks events, run:

```php
use StarkInfra\Event;

$events = Event::query(["after" => "2020-03-20", "isDelivered" => false]);

foreach($events as $event){
    print_r($event);
}
```

## Get a webhook event

You can get a specific webhook event by its id.

```php
use StarkInfra\Event;

$event = Event::get("1082736198236817");

print_r($event);
```

## Delete a webhook event

You can also delete a specific webhook event by its id.

```php
use StarkInfra\Event;

$event = Event::delete("1082736198236817");

print_r($event);
```

## Set webhook events as delivered

This can be used in case you've lost events.
With this function, you can manually set events retrieved from the API as
"delivered" to help future event queries with `"isDelivered" => false`.

```php
use StarkInfra\Event;

$event = Event::update("1298371982371929", true);

print_r($event);
```

## Query failed webhook event delivery attempts information

You can also get information on failed webhook event delivery attempts.

```php
use StarkInfra\Event\Attempt;

$attempts = Attempt::query(["eventIds" => $event->id, "limit" => 1]);

foreach($attempts as $attempt){
    print_r($attempt);
}
```

## Get a failed webhook event delivery attempt information

To retrieve information on a single attempt, use the following function:

```php
use StarkInfra\Event\Attempt;

$attempt = Attempt::get("1616161616161616");

print_r($attempt);
```

# request

This resource allows you to send HTTP requests to StarkInfra routes.

## GET

You can perform a GET request to any StarkInfra route.

It's possible to get a single resource using its id in the path.

```php
use StarkInfra\Request;

$request = Request::get("pix-request/5155165527080960");

print_r($request);
```

You can also get the specific resource log,

```php
use StarkInfra\Request;

$request = Request::get("pix-request/log/5155165527080960");

print_r($request);
```

This same method will be used to list all created items for the requested resource.

```php
use StarkInfra\Request;

$query = [
    "limit" => 10,
    "status" => "created"
]
$request = Request::get(
    "pix-request/log/5155165527080960",
    $query
    );

print_r($request);
```

To list logs, you will use the same logic as for getting a single log.

```php
use StarkInfra\Request;

$query = [
    "limit" => 10,
    "status" => "created"
]
$request = Request::get(
    "pix-request/log/5155165527080960",
    $query
    );

print_r($request);
```

## POST

You can perform a POST request to any StarkInfra route.

This will create an object for each item sent in your request

**Note**: It's not possible to create multiple resources simultaneously. You need to send separate requests if you want to create multiple resources, such as invoices and boletos.

```php
use StarkInfra\Request;
$ext = "this-is-my-unique-external-id";
$body = [
    "holders" => [
        [
            "name" => "Holder Test",
            "taxId" => "012.345.678-90",
            "externalId" => $ext,
            "tags" => ["Traveler Employee"],
        ]
    ]
];
$request = Request::post(
    "suing-holder",
    $body
);

print_r($request)
```

## PATCH

You can perform a PATCH request to any StarkInfra route.
```php
use StarkInfra\Request;

$path = "issuing-holder/5155165527080960";
$request = Request::patch(
    $path, 
    ["tags" => ["arya", "stark"]]
)->content;
$content = json_decode($request, true);
print_r($content)
```

## DELETE

You can perform a DELETE request to any StarkInfra route.

It's possible to delete a single item of a StarkInfra resource.
```php
use StarkInfra\Request;

$path = "issuing-holder/5155165527080960";
$request = Request::delete($path)->content;
$result = json_decode($request, true);

print_r($result);
```

# Handling errors

The SDK may raise one of four types of errors: __InputErrors__, __InternalServerError__, __UnknownError__, __InvalidSignatureError__

__InputErrors__ will be raised whenever the API detects an error in your request (status code 400).
If you catch such an error, you can get its elements to verify each of the
individual errors that were detected in your request by the API.
For example:

```php
use StarkInfra;
use StarkInfra\Error\InputErrors;

try {
    $cards = StarkInfra\IssuingCard::create([
        new StarkInfra\IssuingCard([
            "holdeNname" => "Developers",
            "holderTaxId" => "012.345.678-90",
            "holderExternalId" => "1234",
            "rules" => [
                new StarkInfra\IssuingRule([
                    "name" => "general",
                    "interval" => "week",
                    "amount" => 50000,
                    "currencyCode" => "USD"
                ])
            ]
        ]),
    ]);
} catch (InputErrors $e) {
    foreach($e->errors as $error){
        echo "\n\ncode: " . $error->errorCode;
        echo "\nmessage: " . $error->errorMessage;
    }
}
```

__InternalServerError__ will be raised if the API runs into an internal error.
If you ever stumble upon this one, rest assured that the development team
is already rushing in to fix the mistake and get you back up to speed.

__UnknownError__ will be raised if a request encounters an error that is
neither __InputErrors__ nor an __InternalServerError__, such as connectivity problems.

__InvalidSignatureError__ will be raised specifically by StarkInfra\Event::parse()
when the provided content and signature do not check out with the Stark Infra public
key.

# Help and Feedback

If you have any questions about our SDK, just send us an email.
We will respond you quickly, pinky promise. We are here to help you integrate with us ASAP.
We also love feedback, so don't be shy about sharing your thoughts with us.

Email: help@starkbank.com
