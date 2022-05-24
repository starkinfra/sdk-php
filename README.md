# Stark Infra PHP SDK - Beta

Welcome to the Stark Infra PHP SDK! This tool is made for PHP 
developers who want to easily integrate with our API.
This SDK version is compatible with the Stark Infra API v2.

If you have no idea what Stark Infra is, check out our [website](https://starkinfra.com/) 
and discover a world where receiving or making payments 
is as easy as sending a text message to your client!

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
        - [Transactions](#query-issuing-transactions): Account statement entries
        - [Balance](#get-issuing-balance): Account balance
        - [Holders](#create-issuing-holders): Wallet Card holders
        - [BINs](#query-issuing-bins): Account sub-issue BINs
        - [Issuing Invoices](#create-issuing-invoices): Charge your Issuing account
        - [Withdrawals](#create-issuing-withdrawals): Send money back to your Stark Bank account
        - [Cards](#create-issuing-cards): Create virtual Cards
        - [Purchases](#query-issuing-purchases): View your past purchases
        - [Authorization Requests](#process-authorization-requests): Receive incoming Authorization requests
    - [Pix](#pix)
        - [PixRequests](#create-pixrequests): Create Pix transactions
        - [PixReversals](#create-pixreversals): Reverse Pix transactions
        - [PixBalance](#get-your-pixbalance): View your account balance
        - [PixStatement](#create-a-pixstatement): Request your account statement
        - [PixKey](#create-a-pixkey): Create a Pix Key
        - [PixClaim](#create-a-pixclaim): Claim a Pix Key
        - [PixDirector](#create-a-pixdirector): Create a Pix Director
        - [PixInfraction](#create-pixinfractions): Create Pix Infraction reports
        - [PixChargeback](#create-pixchargebacks): Create Pix Chargeback requests
        - [PixDomain](#query-pixdomain): View registered SPI participants certificates
- [Handling errors](#handling-errors)
- [Help and Feedback](#help-and-feedback)

## Supported PHP Versions

This library supports the following PHP versions:

* PHP 7.1
* PHP 7.2
* PHP 7.3
* PHP 7.4

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

### 1. Install our SDK

1.1 Composer: To install the package with Composer, run:

```sh
composer require starkinfra/sdk
```

To use the bindings, use Composer's autoload:

```sh
require_once('vendor/autoload.php');
```

1.2 Manual installation: You can also download the latest release from GitHub and then, to use the bindings, include the init.php file.

```sh
require_once('/path/to/starkinfra/sdk-php/init.php');
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

3.1.1. Log into [Starkinfra Sandbox](https://web.sandbox.starkinfra.com)

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

3.2.1. Log into [Starkinfra Sandbox](https://web.sandbox.starkinfra.com)

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

- The `page` function gives you full control over the API pagination. With each function call, you receive up to
100 results and the cursor to retrieve the next batch of elements. This allows you to stop your queries and
pick up from where you left off whenever it is convenient. When there are no more elements to be retrieved, the returned cursor will be `None`.

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
in your account, which can be added to your balance by creating an Issuing Invoice. 

In the Sandbox environment, most of the created Invoices will be automatically paid,
so there's nothing else you need to do to add funds to your account. Just create
a few Invoices and wait around a bit.

In Production, you (or one of your clients) will need to actually pay this Invoice
for the value to be credited to your account.


# Usage

## Issuing

### Query Issuing Transactions

To understand your balance changes (issuing statement), you can query
transactions. Note that our system creates transactions for you when
you make purchases, withdrawals, receive issuing invoice payments, for example.

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

### Get an Issuing Transaction

You can get a specific transaction by its id:

```php
use StarkInfra;

$transaction = StarkInfra\IssuingTransaction::get("5155165527080960");

print_r($transaction);
```

### Get Issuing Balance

To know how much money you have in your workspace, run:

```php
use StarkInfra;

$balance = StarkInfra\IssuingBalance::get();

print_r($balance);
```

### Create Issuing Holders

You can create card holders to your Workspace.

```php
use StarkInfra;

$holders = StarkInfra\IssuingHolder::create([
    new StarkInfra\IssuingHolder([
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
```

**Note**: Instead of using IssuingHolder objects, you can also pass each transfer element in dictionary format

### Query Issuing Holders

You can query multiple holders according to filters.

```php
use StarkInfra;

$holders = StarkInfra\IssuingHolder::query();

foreach ($holders as $holder) {
    print_r($holder);
```

### Delete an Issuing Holder

To cancel a single Issuing Holder by its id, run:

```php
use StarkInfra;

$holder = StarkInfra\IssuingHolder::delete("5155165527080960");

print_r($holder);
```

### Get an Issuing Holder

To get a single Issuing Holder by its id, run:

```php
use StarkInfra;

$holder = StarkInfra\IssuingHolder::get("5155165527080960");

print_r($holder);
```

### Query Issuing Holder logs

You can query holder logs to better understand holder life cycles.

```php
use StarkInfra;

$logs = StarkInfra\IssuingHolder\Log::query(["limit" => 50]);

foreach ($logs as $log) {
    print_r($log);
}
```

### Get an Issuing Holder log

You can also get a specific log by its id.

```php
use StarkInfra;

$log = StarkInfra\IssuingHolder\Log::get("5155165527080960");

print_r($log);
```

### Query Issuing BINs

To take a look at the sub-issuer BINs linked to your workspace, just run the following:

```php
use StarkInfra;

$bins = StarkInfra\IssuingBin::query();

foreach ($bins as $bin) {
    print_r($bin);
}
```

### Create Issuing Invoices

You can create dynamic QR Code invoices to receive money from accounts you have in other banks to your Issuing account.

Since the banking system only understands value modifiers (discounts, fines and interest) when dealing with **dates** (instead of **datetimes**), these values will only show up in the end user banking interface if you use **dates** in the "due" and "discounts" fields. 

If you use **datetimes** instead, our system will apply the value modifiers in the same manner, but the end user will only see the final value to be paid on his interface.

Also, other banks will most likely only allow payment scheduling on invoices defined with **dates** instead of **datetimes**.

```php
use StarkInfra;

$invoices = StarkInfra\IssuingInvoice::create(
    new StarkInfra\IssuingInvoice([
        "amount" => 1000
    ])
);

foreach ($invoices as $invoice) {
    print_r($invoice);
}
```

**Note**: Instead of using Invoice objects, you can also pass each invoice element in dictionary format

### Get an Issuing Invoice

After its creation, information on an invoice may be retrieved by its id. 
Its status indicates whether it's been paid.

```php
use StarkInfra;

$invoice = StarkInfra\IssuingInvoice::get("5155165527080960");

print_r($invoice);
```

### Query Issuing Invoices

You can get a list of created invoices given some filters.

```php
use StarkInfra;

$invoices = StarkInfra\IssuingInvoice::query();

foreach ($invoices as $invoice) {
    print_r($invoice);
```

### Query Issuing Invoice logs

Logs are pretty important to understand the life cycle of an invoice.

```php
use StarkInfra;

$logs = StarkInfra\IssuingInvoice\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
```

### Create Issuing Withdrawals

You can create withdrawals to send back cash to your Banking account by using the Withdrawal resource

```php
use StarkInfra;

$withdrawals = StarkInfra\IssuingWithdrawal::create(
    new StarkInfra\IssuingWithdrawal([
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

### Get an Issuing Withdrawal

After its creation, information on a withdrawal may be retrieved by its id.

```php
use StarkInfra;

$withdrawal = StarkInfra\IssuingWithdrawal::get("5155165527080960");

print_r($withdrawal);
```

### Query Issuing Withdrawals

You can get a list of created invoices given some filters.

```php
use StarkInfra;

$withdrawals = StarkInfra\IssuingWithdrawal::query();

foreach ($withdrawals as $withdrawal) {
    print_r($withdrawal);
}
```

### Create Issuing Cards

You can issue cards with specific spending rules to make purchases.

```php
use StarkInfra;

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

foreach ($cards as $card) {
    print_r($card);
```

### Query Issuing Cards

You can get a list of created cards given some filters.

```php
use StarkInfra;

$cards = StarkInfra\IssuingCard::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($cards as $card) {
    print_r($card);
}
```

### Get an Issuing Card

After its creation, information on a card may be retrieved by its id.

```php
use StarkInfra;

$card = StarkInfra\IssuingCard::get("5155165527080960");

print_r($card);
```

### Update an Issuing Card

You can update a specific Issuing Card by its id.

```php
use StarkInfra;

$card = StarkInfra\IssuingCard::update("5155165527080960", ["status" => "blocked"]);

print_r($card);
```

### Delete an Issuing Card

You can also cancel a card by its id.
Note that this is not possible if it has been processed already.

```php
use StarkInfra;

$card = StarkInfra\IssuingCard::delete("5155165527080960");

print_r($card);
```

### Query Issuing Card logs

Logs are pretty important to understand the life cycle of a card.

```php
use StarkInfra;

$logs = StarkInfra\IssuingCard\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
```

### Get an Issuing Card log

You can get a single log by its id.

```php
use StarkInfra;

$log = StarkInfra\IssuingCard\Log::get("5155165527080960");

print_r($log);
```

### Query Issuing Purchases

You can get a list of created purchases given some filters.

```php
use StarkInfra;

$purchases = StarkInfra\IssuingPurchase::query([
    "after" => "2020-01-01",
    "before" => "2020-03-01"
]);

foreach ($purchases as $purchase) {
    print_r($purchase);
}
```

### Get an Issuing Purchase

After its creation, information on a purchase may be retrieved by its id. 

```php
use StarkInfra;

$log = StarkInfra\IssuingPurchase::get("5155165527080960");

print_r($log);
```

### Query Issuing Purchase logs

Logs are pretty important to understand the life cycle of a purchase.

```php
use StarkInfra;

$logs = StarkInfra\IssuingPurchase\Log::query(["limit" => 150]);

foreach ($logs as $log) {
    print_r($log);
```

### Get an Issuing Purchase log

You can get a single log by its id.

```php
use StarkInfra;

$log = StarkInfra\IssuingPurchase\Log::get("5155165527080960");

print_r($log);
```

**Note**: the Organization user can only update a workspace with the Workspace ID set.

### Process Authorization requests

It’s easy to process events delivered to your Webhook endpoint. 
Remember to pass the signature header so the SDK can make sure 
it was really StarkInfra that sent you the event.

```php
use StarkInfra;

$response = listen();  # this is the method you made to get the events posted to your webhook

$authorization = IssuingAuthorization::parse($response->content, $response->headers["Digital-Signature"]);

print_r($authorization);
```


## Pix

## Create PixRequests
You can create Pix Requests to charge a user:

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
        "endToEndId" => EndToEndId.create("20018183"),
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
        "endToEndId" => EndToEndId.create("20018183"),
    ]);
]);

foreach($requests as $request){
    print_r($request);
}
```

**Note**: Instead of using Pix Request objects, you can also pass each transaction element in dictionary format

## Query PixRequests

You can query multiple Pix Requests according to filters.

```php
use StarkInfra\PixRequest;

$requests = PixRequest::query([
    "fields" => ['amount', 'senderName'],
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

## Process PixRequest authorization requests

It's easy to process authorization requests that arrived in your handler. Remember to pass the
signature header so the SDK can make sure it's StarkInfra that sent you
the event.

```php
use StarkInfra\PixRequest;

$response = listen();  # this is your handler to listen for authorization requests

$request = PixRequest::parse($response->content, $response->headers["Digital-Signature"]);

print_r($request);
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

You can reverse a Pix Request by whole or by a fraction of its amount using a Pix Reversal.

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

## Process PixReversal authorization reversals

It's easy to process authorization reversals that arrived in your handler. Remember to pass the
signature header so the SDK can make sure it's StarkInfra that sent you
the event.

```php
use StarkInfra\PixReversal;

$response = listen();  # this is your handler to listen for authorization requests

$reversal = PixReversal::parse($response->content, $response->headers["Digital-Signature"]);

print_r($reversal);
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

To know how much money you have in your workspace, run:

```php
use StarkInfra\PixBalance;

$balance = PixBalance::get();

print_r($balance);
```

## Create PixStatement

Statements are only available for direct participants. To create a statement of all the transactions that happened on your workspace during a specific day, run:

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

$key = PixKey::get(
    "5915632394567680",
    "20.018.183/0001-80",
    [
        "name" => "Jamie Lannister"
    ]
);

print_r($key);

```

### Patch a PixKey

Update the account information linked to a Pix Key.

```php
use StarkInfra\PixKey;

$key PixKey::update(
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

You can query multiple Pix Claims according to filters.

```php
use StarkInfra\PixClaim;

$claims = PixClaim::query([
    "limit" => 1,
    "after" => "2022-01-01",
    "before" => "2022-01-12",
    "status" => "delivered",
    "ids" => ["5729405850615808"],
    "type" => "ownership",
    "agent" => "claimed",
    "keyType" => "phone",
    "keyId" => "+5511989898989"
]);

foreach $claim in $claims{
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

### Patch a PixClaim

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

foreach $log in $logs{
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
    ]);
]);

foreach $infraction in $infractions{
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

for $infraction in $infractions{
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

### Patch a PixInfraction

A received Pix Infraction can be confirmed or declined by patching its status.
After a Pix Infraction is patched, its status changes to closed.

```php
use StarkInfra\PixInfraction;

$infraction = PixInfraction::update(
    "5155165527080960",
    [
        "result" => "agreed"
    ]
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

for $log in $logs{
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

for $chargeback in $chargebacks{
    print($chargeback);
}    
```

### Query PixChargeback

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

for $chargeback in $chargebacks{
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

### Patch a PixChargeback

A received Pix Chargeback can be accepted or rejected by patching its status.
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

for $log in $logs{
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

### Query PixDomain

You can query for certificates of registered SPI participants able to issue dynamic QR Codes.

```php
use StarkInfra\PixDomain;

$domains = PixDomain::query();

for $domain in $domains{
    print($domain);
}
```  

## Process webhook events

It's easy to process events delivered to your Webhook endpoint. Remember to pass the
signature header so the SDK can make sure it was really StarkInfra that sent you
the event.

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
