# Most Cited plugin
[![Build Status](https://travis-ci.com/jonasraoni/mostCited.svg?branch=master)](https://travis-ci.com/jonasraoni/mostCited)
![GitHub release (latest by date including pre-releases)](https://img.shields.io/github/v/release/jonasraoni/mostCited?include_prereleases&label=latest%20release)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/jonasraoni/mostCited)
![GitHub](https://img.shields.io/github/license/jonasraoni/mostCited)
[![OJS-Version](https://img.shields.io/badge/pkp--ojs-3.4--x-brightgreen)](https://github.com/pkp/ojs/tree/stable-3_4_0)
![GitHub All Releases](https://img.shields.io/github/downloads/jonasraoni/mostCited/total)

### Compatibility
OJS 3.4

### Description
The plugin displays the list of the most cited submissions on the journal's index page.

### Installation
Install the plugin via the Plugin Gallery (`Settings -> Website -> Plugins -> Plugin Gallery`) or download the release matching your OJS version and install it via `Settings -> Website -> Plugins -> Upload A New Plugin`.

## How it works
After configuring the plugin, the installation will start fetching citation counts from the chosen provider, for all the submissions that have a DOI.

Once the citation harvesting work is complete, the plugin will build the list of most cited submissions, and start displaying them on the index page. Therefore, in case you've got a lot of submissions on your installation, the list of the most cited submissions might take a while to be displayed on the first usage.

The plugin synchronizes the citation counts monthly using a background service.

### Configuration
Go to the plugin settings, select a suitable data provider and fill in the required fields:

![Settings](https://github.com/pkp/pln/assets/361921/e0755a8e-4059-4914-bde9-5f283ab317e5 "Settings")

### Credits
This work is heavily based on the plugins https://github.com/RBoelter/mostViewed and https://github.com/RBoelter/citations.
