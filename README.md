# PGEPowerOutage plugin dla LMS

Shows power outages in areas served by PGE.

![](pge-power-outages.png?raw=true)

## Requirements

Installed [LMS](https://lms.org.pl/) or [LMS-PLUS](https://lms-plus.org) (recommended).

## Installation

* Copy files to `<path-to-lms>/plugins/`
* Run `composer update` or `composer update --no-dev`
* Go to LMS website and activate it `Configuration => Plugins`


## How to add your area?

You need to know which AREA to monitor. So lets go throuh

First open web browser and go to https://pgedystrybucja.pl/planowane-wylaczenia
Press F12 and go to the Network tab then select `Oddział` and `Rejon Energetyczny`
Watch for new city `Name` when appears click on it. 
Then go to `Headers` and last part of `Request URL` is your AREA


## Configuration

* Import default settings `configexport-pge-wartoscglobalna.ini`
* Go to `<path-to-lms>/?m=configlist` adjust the settings for yourself

## Donation

* Bitcoin (BTC): bc1qvwahntcrwjtdp0ntfd0l6kdvdr9d9h6atp6qrr
* Ethereum (ETH): 0xEFCd4b066195652a885d916183ffFfeEEd931f40
