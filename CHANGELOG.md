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
