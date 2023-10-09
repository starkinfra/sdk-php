# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to the following versioning pattern:

Given a version number MAJOR.MINOR.PATCH, increment:

- MAJOR version when the **API** version is incremented. This may include backwards incompatible changes;
- MINOR version when **breaking changes** are introduced OR **new functionalities** are added in a backwards compatible manner;
- PATCH version when backwards compatible bug **fixes** are implemented.


## [Unreleased]
### Added
- merchantCategoryType, description and holderId attributes to IssuingPurchase resource
### Changed
- senderTaxId parameter to DynamicBrcode resource

## [0.9.0] - 2023-06-19
### Added
- metadata attributes to IssuingPurchase resource

## [0.8.0] - 2023-05-11
### Added
- payerId and endToEndId parameter to BrcodePreview resource
- description parameter to StaticBrcode resource
### Changed
- change nominalAmount and amount parameter to conditionally required to CreditNote resource

## [0.7.0] - 2023-04-28
### Added
- IssuingEmbossingKit resource
- pin parameter to update method in IssuingCard resource
- cashierBankCode attribute to StaticBrcode resource
### Removed
- cardDesignId and envelopeDesignId attributes to IssuingEmbossingRequest resource

## [0.6.0] - 2023-03-31
### Added
- CreditHolmes resource
- CreditHolmes.Log resource
- IndividualDocument resource
- IndividualDocument.Log resource
- IndividualIdentity resource
- IndividualIdentity.Log resource
- IssuingDesign resource
- IssuingEmbossingRequest resource
- IssuingEmbossingRequest.Log resource
- IssuingRestock resource
- IssuingRestock.Log resource
- IssuingStock resource
- IssuingStock.Log resource
- pin parameter to update method in IssuingCard resource
- productId attribute to IssuingPurchase resource
- issuingTransactionId attribute to IssuingPurchase.Log
### Changed
- binId attribute to productId in IssuingCard resource
### Removed
- CreditNote.Signer sub-resource to CreditSigner sub-resource

## [0.5.2] - 2022-10-27
### Fixed
- ecdsa dependency missing classmap

## [0.5.1] - 2022-10-26
### Fixed
- repeated autoload requirement

## [0.5.0] - 2022-10-25
### Changed
- internal structure to use starkcore as a dependency
- PixDirector from resource to sub-resource

## [0.4.0] - 2022-08-26
### Changed
- settlement parameter to fundingType in IssuingProduct resource
- client parameter to holderType in IssuingProduct resource
- CreditNotePreview sub-resource to CreditPreview.CreditNotePreview sub-resource
- agent parameter to flow in PixInfraction and PixChargeback resources
- agent parameter to flow on query and page methods in PixInfraction and PixChargeback
- bankCode parameter to claimerBankCode in PixClaim resource
### Added
- brcode, link and due attributes to IssuingInvoice resource
- code attribute to IssuingProduct resource
- expand parameter to create method in IssuingHolder resource
- CreditPreview sub-resource
- default to fee, externalId and tags to parse method in PixRequest and PixReversal resources
- BrcodePreview resource
- tags parameter to PixClaim, PixInfraction, Pix Chargeback, DynamicBrcode and StaticBrcode resources
- flow parameter to PixClaim resource
- flow parameter to query and page methods in PixClaim resource
- tags parameter to query and page methods in PixChargeback, PixClaim and PixInfraction resources
- zipCode, purchase, isPartialAllowed, cardTags and holderTags attributes to IssuingPurchase resource
### Removed
- updated and category parameters from IssuingProduct resource
- bacenId parameter from PixChargeback and PixInfraction resources
- agent parameter from PixClaim\Log resource

## [0.3.1] - 2022-08-04
### Fixed
- responseDue method of DynamicBrcode resource

## [0.3.0] - 2022-08-02
### Changed
- fineAmount to fine, interestAmount to interest and discountAmount to discounts on DynamicBrcode::responseDue
- amount to nominalAmount on DynamicBrcode::responseDue
### Fixed
- JSON body returned from PixRequest::response() method
- JSON body returned from PixReversal::response() method

## [0.2.0] - 2022-07-07
### Added
- StaticBrcode resource
- DynamicBrcode resource 
- CreditNotePreview resource
- CardMethod sub-resource
- MerchantCountry sub-resource
- MerchantCategory sub-resource
- Event\Attempt sub-resource to allow retrieval of information on failed webhook event delivery attempts
- parse method for IssuingPurchase resource
- get, query, page, delete and update methods to the Event resource.
- response method to PixRequest, PixReversal and IssuingPurchase resources
### Changed
- resource name from IssuingBin to IssuingProduct
- Creditnote\Signer sub-resource to CreditSigner resource
### Removed 
- IssuingAuthorization resource
- bankCode attribute from PixReversal resource

## [0.1.0] - 2022-06-03
### Added
- credit receiver's billing address on CreditNote

## [0.0.4] - 2022-05-24
### Added
- PixChargeback resource for Direct Participants
- PixClaim resource for Direct Participants
- PixDirector resource for Direct Participants
- PixDomain resource for Indirect and Direct Participants
- PixInfraction resource for Indirect and Direct Participants
- PixKey resource for Indirect and Direct Participants
- CreditNote resource for money lending with Stark Infra's endorsement
- Webhook resource to receive Events 
### Changed
- delete methods name to cancel

## [0.0.3] - 2022-03-24
### Added
- PixRequest resource for Indirect and Direct Participants
- PixReversal resource for Indirect and Direct Participants
- PixBalance resource for Indirect and Direct Participants
- PixStatement resource for Direct Participants
- Event resource for webhook receptions

## [0.0.2] - 2022-03-15
### Added
- Key resource

## [0.0.1] - 2022-03-15
### Added
- IssuingAuthorization resource
- IssuingBalance resource
- IssuingBin resource
- IssuingCard resource
- IssuingHolder resource
- IssuingInvoice resource
- IssuingPurchase resource
- IssuingRule resource
- IssuingTransaction resource
- IssuingWithdrawal resource
