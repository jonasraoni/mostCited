# Most Cited Plugin
[![Build Status](https://travis-ci.com/jonasraoni/mostCited.svg?branch=master)](https://travis-ci.com/jonasraoni/mostCited)
![GitHub release (latest by date including pre-releases)](https://img.shields.io/github/v/release/jonasraoni/mostCited?include_prereleases&label=latest%20release)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/jonasraoni/mostCited)
![GitHub](https://img.shields.io/github/license/jonasraoni/mostCited)
[![OJS-Version](https://img.shields.io/badge/pkp--ojs-3.4--x-brightgreen)](https://github.com/pkp/ojs/tree/stable-3_4_0)
![GitHub All Releases](https://img.shields.io/github/downloads/jonasraoni/mostCited/total)

### Description
This plugin displays the list of most cited submissions on the journal index page, and it was written to work under OJS 3.4.
This work is based on the https://github.com/RBoelter/mostViewed and https://github.com/RBoelter/citations.

### Installation
Install the plugin via the Plugin Gallery (`Settings -> Website -> Plugins -> Plugin Gallery`) or download the release matching your OJS Version and install it via `Settings -> Website -> Plugins -> Upload A New Plugin`.

### Settings
Go to the plugin settings, select a suitable data provider and fill in the required fields:

![Settings](https://github.com/pkp/pln/assets/361921/e0755a8e-4059-4914-bde9-5f283ab317e5 "Settings")

Upon saving, the installation will start fetching citation statistics from the chosen provider, once the work is complete, the journal will build the list of most cited submissions and display them its index page.

The plugin is responsible to update the statistics with a service that runs on the background.
