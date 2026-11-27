<?php

namespace Test\IssuingCardWithoutHolder;
use \Exception;
use StarkInfra\IssuingCard;
use StarkInfra\IssuingHolder;
use StarkInfra\IssuingProduct;
use StarkCore\Error\InputErrors;
use StarkCore\Error\UnknownError;


class SkipTest extends Exception
{
}


// Only the outgoing HTTP call is replaced in the *Payload tests (an https stream wrapper records the request): payload
// building and signing run for real. The wrapper cannot set $http_response_header, which the core reads for the status, so
// the core reports the answer as UnknownError after the request was sent: those tests cover the request half, and the
// response half is covered by the sandbox tests below.
class RecordingHttps
{
    public static $requests = [];
    public $context;
    private $position = 0;
    private $content = '{"cards": [], "card": {}}';

    public function stream_open($path, $mode, $options, &$openedPath)
    {
        $http = stream_context_get_options($this->context)["http"];
        self::$requests[] = ["url" => $path, "method" => $http["method"], "body" => $http["content"] ?? null];
        return true;
    }

    public function stream_read($count)
    {
        $chunk = substr($this->content, $this->position, $count);
        $this->position += strlen($chunk);
        return $chunk;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($this->content);
    }

    public function stream_stat()
    {
        return [];
    }
}


function assertEqual($expected, $actual, $message)
{
    if ($expected != $actual) {
        throw new Exception($message . ": expected " . json_encode($expected) . ", got " . json_encode($actual));
    }
}

function recordRequest($call)
{
    RecordingHttps::$requests = [];
    stream_wrapper_unregister("https");
    stream_wrapper_register("https", RecordingHttps::class);
    try {
        $call();
    } catch (UnknownError $error) {
        // expected: see the note above RecordingHttps
    } finally {
        stream_wrapper_restore("https");
    }
    assertEqual(1, count(RecordingHttps::$requests), "number of requests");
    return RecordingHttps::$requests[0];
}

// The API rejection reaches the caller untouched, with the error code of the API.
function assertRejected($call, $code)
{
    try {
        $call();
    } catch (InputErrors $error) {
        $codes = array_map(function ($element) { return $element->errorCode; }, $error->errors);
        if (!in_array($code, $codes)) {
            throw new Exception("expected code " . $code . ", got " . json_encode($codes));
        }
        return;
    }
    throw new Exception("expected the API to reject the call");
}

function firstProduct()
{
    foreach (IssuingProduct::query(["limit" => 1]) as $product) {
        return $product;
    }
    throw new Exception("no issuing product in the sandbox Project");
}

// A holder can only be linked to a product of its own type: business holders have a CNPJ (14 digits) and individual holders a
// CPF (11 digits). Existing holders are used so no personal data is created by the test.
function findExistingHolderForProduct($product)
{
    $digitsByType = ["business" => 14, "individual" => 11];
    if (!isset($digitsByType[$product->holderType])) {
        throw new Exception("unknown holderType of the sandbox product: " . json_encode($product->holderType));
    }
    $digits = $digitsByType[$product->holderType];
    foreach (IssuingHolder::query(["status" => "active", "limit" => 100]) as $holder) {
        if (strlen(preg_replace("/\D/", "", $holder->taxId)) == $digits) {
            return $holder;
        }
    }
    return null;
}

// The sandbox product ids differ per Project, so the product is looked up instead of hard-coded.
function cardsWithoutHolder($n, $type, $productId)
{
    $cards = [];
    for ($i = 0; $i < $n; $i++) {
        $cards[] = new IssuingCard(["productId" => $productId, "type" => $type, "tags" => ["sdk-test"]]);
    }
    return $cards;
}

// A failed cancel must not hide the failure of the test (a leftover card is reported by the sandbox-test plan).
function cancelAll($cards)
{
    foreach ($cards as $card) {
        try {
            IssuingCard::cancel($card->id);
        } catch (Exception $error) {
            echo "\n\t! could not cancel card " . $card->id;
        }
    }
}

// Creates pending physical cards without holder, runs the test with them and always cancels them.
function withPendingCards($n, $test)
{
    $product = firstProduct();
    $holder = findExistingHolderForProduct($product);
    if (is_null($holder)) {
        throw new SkipTest("no existing holder compatible with the sandbox product");
    }
    $cards = IssuingCard::create(cardsWithoutHolder($n, "physical", $product->id));
    try {
        $test($cards, $holder);
    } finally {
        cancelAll($cards);
    }
}

function holderFields($holder)
{
    return ["holderName" => $holder->name, "holderTaxId" => $holder->taxId, "holderExternalId" => $holder->externalId];
}


class TestIssuingCardWithoutHolderPayload
{
    public function testHolderFieldsAreNotSentWhenOmitted()
    {
        $request = recordRequest(function () {
            IssuingCard::create([new IssuingCard(["productId" => "123", "type" => "physical"])]);
        });
        assertEqual(["cards" => [["productId" => "123", "type" => "physical"]]], json_decode($request["body"], true), "create body");
    }

    public function testHolderFieldsAreSentWhenInformed()
    {
        $request = recordRequest(function () {
            IssuingCard::create([new IssuingCard([
                "holderName" => "Tony Stark",
                "holderTaxId" => "012.345.678-90",
                "holderExternalId" => "my-entity/123",
                "productId" => "123",
            ])]);
        });
        assertEqual(["cards" => [[
            "holderName" => "Tony Stark",
            "holderTaxId" => "012.345.678-90",
            "holderExternalId" => "my-entity/123",
            "productId" => "123",
        ]]], json_decode($request["body"], true), "create body");
    }
}


class TestIssuingCardUpdatePayload
{
    public function testHolderFieldsAreNotSentWhenOmitted()
    {
        $request = recordRequest(function () {
            IssuingCard::update("5656565656565656", ["displayName" => "ANTHONY STARK"]);
        });
        assertEqual("PATCH", $request["method"], "method");
        assertEqual(["displayName" => "ANTHONY STARK"], json_decode($request["body"], true), "update body");
    }

    public function testHolderFieldsAreSentAlone()
    {
        $fields = ["holderName" => "Tony Stark", "holderTaxId" => "012.345.678-90", "holderExternalId" => "my-entity/123"];
        $request = recordRequest(function () use ($fields) {
            IssuingCard::update("5656565656565656", $fields);
        });
        assertEqual($fields, json_decode($request["body"], true), "update body");
    }

    public function testHolderFieldsAreSentWithStatusAndPin()
    {
        $fields = ["status" => "active", "pin" => "1234", "holderName" => "Tony Stark", "holderTaxId" => "012.345.678-90", "holderExternalId" => "my-entity/123"];
        $request = recordRequest(function () use ($fields) {
            IssuingCard::update("5656565656565656", $fields);
        });
        assertEqual($fields, json_decode($request["body"], true), "update body");
    }
}


class TestIssuingCardUpdateMultiPayload
{
    public function testItemsAreSentInTheCardsListWithOnlyInformedFields()
    {
        $request = recordRequest(function () {
            IssuingCard::updateMulti([
                new IssuingCard([
                    "id" => "1",
                    "holderName" => "Tony Stark",
                    "holderTaxId" => "012.345.678-90",
                    "holderExternalId" => "my-entity/123",
                ]),
                ["id" => "2", "tags" => ["batch"]],
            ]);
        });
        assertEqual("PATCH", $request["method"], "method");
        if (!str_ends_with($request["url"], "/v2/issuing-card")) {
            throw new Exception("unexpected url " . $request["url"]);
        }
        assertEqual(["cards" => [
            ["id" => "1", "holderName" => "Tony Stark", "holderTaxId" => "012.345.678-90", "holderExternalId" => "my-entity/123"],
            ["id" => "2", "tags" => ["batch"]],
        ]], json_decode($request["body"], true), "updateMulti body");
    }
}


class TestIssuingCardWithoutHolder
{
    public function testPhysicalCardWithoutHolderIsCreatedPending()
    {
        $cards = IssuingCard::create(cardsWithoutHolder(1, "physical", firstProduct()->id));
        try {
            assertEqual(1, count($cards), "cards created");
            assertEqual("pending", $cards[0]->status, "status");
            assertEqual("physical", $cards[0]->type, "type");
            assertEqual("", $cards[0]->holderId, "holderId");
            assertEqual("", $cards[0]->holderName, "holderName");
        } finally {
            cancelAll($cards);
        }
    }

    public function testApiRejectsVirtualCardWithoutHolder()
    {
        assertRejected(function () {
            cancelAll(IssuingCard::create(cardsWithoutHolder(1, "virtual", firstProduct()->id)));
        }, "invalidHolder");
    }

    public function testApiRejectsIncompleteHolderFields()
    {
        $cards = cardsWithoutHolder(1, "physical", firstProduct()->id);
        $cards[0]->holderName = "Tony Stark";
        assertRejected(function () use ($cards) {
            cancelAll(IssuingCard::create($cards));
        }, "invalidHolder");
    }
}


class TestIssuingCardAssignHolder
{
    public function testHolderIsAssignedToAPendingCard()
    {
        withPendingCards(1, function ($cards, $holder) {
            $card = IssuingCard::update($cards[0]->id, holderFields($holder));
            assertEqual("pending", $card->status, "status");
            assertEqual($holder->id, $card->holderId, "holderId");
        });
    }

    public function testHolderIsAssignedAndCardActivatedInTheSameCall()
    {
        withPendingCards(1, function ($cards, $holder) {
            $card = IssuingCard::update($cards[0]->id, array_merge(holderFields($holder), ["status" => "active", "pin" => "1234"]));
            assertEqual("active", $card->status, "status");
            assertEqual($holder->id, $card->holderId, "holderId");
        });
    }

    public function testApiRejectsIncompleteHolderFields()
    {
        withPendingCards(1, function ($cards, $holder) {
            assertRejected(function () use ($cards, $holder) {
                IssuingCard::update($cards[0]->id, ["holderName" => $holder->name]);
            }, "invalidHolder");
        });
    }

    public function testApiRejectsActivationWithoutHolder()
    {
        withPendingCards(1, function ($cards, $holder) {
            assertRejected(function () use ($cards) {
                IssuingCard::update($cards[0]->id, ["status" => "active", "pin" => "1234"]);
            }, "invalidHolder");
        });
    }
}


class TestIssuingCardUpdateMulti
{
    public function testHolderIsAssignedToSeveralCards()
    {
        withPendingCards(2, function ($cards, $holder) {
            $items = array_map(function ($card) use ($holder) {
                return array_merge(["id" => $card->id], holderFields($holder));
            }, $cards);
            $updated = IssuingCard::updateMulti($items);
            $ids = array_map(function ($card) { return $card->id; }, $updated);
            $expected = array_map(function ($card) { return $card->id; }, $cards);
            sort($ids);
            sort($expected);
            assertEqual($expected, $ids, "updated ids");
            foreach ($updated as $card) {
                assertEqual($holder->id, $card->holderId, "holderId");
            }
        });
    }

    public function testApiRejectsTheWholeListWhenOneItemIsInvalid()
    {
        withPendingCards(2, function ($cards, $holder) {
            assertRejected(function () use ($cards, $holder) {
                IssuingCard::updateMulti([
                    array_merge(["id" => $cards[0]->id], holderFields($holder)),
                    array_merge(["id" => "0000000000000001"], holderFields($holder)),
                ]);
            }, "invalidCard");
            assertEqual("", IssuingCard::get($cards[0]->id)->holderId, "holderId of the untouched card");
        });
    }

    public function testApiRejectsIncompleteHolderFields()
    {
        withPendingCards(2, function ($cards, $holder) {
            assertRejected(function () use ($cards, $holder) {
                IssuingCard::updateMulti([["id" => $cards[0]->id, "holderName" => $holder->name]]);
            }, "invalidHolder");
        });
    }

    public function testApiRejectsPinInTheBatch()
    {
        withPendingCards(2, function ($cards, $holder) {
            assertRejected(function () use ($cards) {
                IssuingCard::updateMulti([["id" => $cards[0]->id, "pin" => "1234"]]);
            }, "invalidJson");
        });
    }

    public function testApiRejectsDisplayNameInTheBatch()
    {
        withPendingCards(2, function ($cards, $holder) {
            assertRejected(function () use ($cards) {
                IssuingCard::updateMulti([["id" => $cards[0]->id, "displayName" => "Name"]]);
            }, "invalidJson");
        });
    }
}


// Run when the file is included by tests/test.php (or any other runner that calls the test methods itself, such as the
// sandbox-test suite runner, does not trigger this block).
if (basename($_SERVER["SCRIPT_FILENAME"]) == "test.php") {
    echo "\n\nIssuingCard without holder:";
    foreach ([
        new TestIssuingCardWithoutHolderPayload(),
        new TestIssuingCardUpdatePayload(),
        new TestIssuingCardUpdateMultiPayload(),
        new TestIssuingCardWithoutHolder(),
        new TestIssuingCardAssignHolder(),
        new TestIssuingCardUpdateMulti(),
    ] as $test) {
        foreach (get_class_methods($test) as $method) {
            echo "\n\t- " . get_class($test) . "::" . $method;
            try {
                $test->$method();
                echo " - OK";
            } catch (SkipTest $skip) {
                echo " - SKIPPED (" . $skip->getMessage() . ")";
            }
        }
    }
}
