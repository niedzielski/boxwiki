# boxwiki
A loose composition of Docker containers wrapping a MediaWiki master mount for
development. Based on [wikibase-docker].

[wikibase-docker]: https://github.com/wmde/wikibase-docker

## Prerequisites

```bash
# MediaWiki development dependencies.
sudo apt install \
  composer docker-compose php-codesniffer php-xml php-mbstring php7.2-cli phpunit

# Obtain a copy of Node.js from https://nodejs.org/.

# Download Core.
repo_base=https://gerrit.wikimedia.org/r/p/mediawiki
time git clone --recursive "$repo_base/core.git"
cd core

# Download extensions.
time while read i; do git -C extensions clone --recursive "$i"; done << eof
$repo_base/extensions/EventLogging
$repo_base/extensions/PageImages
$repo_base/extensions/MobileFrontend
$repo_base/extensions/OAuth
$repo_base/extensions/Popups
$repo_base/extensions/QuickSurveys
$repo_base/extensions/RelatedArticles
$repo_base/extensions/WikimediaEvents
$repo_base/extensions/WikimediaMessages
eof

# Download skins.
time while read i; do git -C skins clone --recursive "$i"; done << eof
$repo_base/skins/MinervaNeue
$repo_base/skins/Vector
eof

# Install PHP dependencies.
time for i in . extensions/*/ skins/*/; do composer -d="$i" install; composer -d="$i" update; done

# Install NPM dependencies.
cd .. && . ~/.nvm/nvm.sh && nvm use && cd core &&
time for i in . extensions/*/ skins/*/; do npm -C "$i" i; done

# Add your LocalSettings.php as LocalSettingsDev.php.

ln -s . w

# Add your .htaccess file.
```

## Execution

### Start
```bash
docker-compose up --build
```

### Destroy the databases
```bash
rm -rf ./core/LocalSettings.php ./mysql ./wdqs
```

### Destroy the containers and their volumes
```bash
docker rm -v $(docker ps -aq --filter name=boxwiki)
```

## Updating MediaWiki

```bash
. ~/.nvm/nvm.sh && nvm use &&
cd core &&
time for i in . extensions/*/ skins/*/; do
  git -C "$i" pull
  composer -d="$i" install
  composer -d="$i" update
  npm -C "$i" i
done
```

## Log into the box
```bash
docker exec -it boxwiki_boxwiki_1 bash
su stephen
```

### Run PHPUnit tests
```bash
time php tests/phpunit/phpunit.php --filter PageSplitTesterTest
```

### Add an image
```bash
docker exec -it boxwiki_boxwiki_1 php maintenance/importImages.php images
docker exec -it boxwiki_boxwiki_1 bash -c "php maintenance/edit.php kitten <<< '[[File:Kitten.jpg]]'"
```

## Tested LocalSettingsDev.php
```php
<?php

# Protect against web entry
if (!defined('MEDIAWIKI')) {
  exit;
}

$wgDBserver = getenv('DB_SERVER');
$wgDBname = getenv('DB_NAME');
$wgDBuser = getenv('DB_USER');
$wgDBpassword = getenv('DB_PASS');

## Site Settings
$wgShellLocale = "en_US.utf8";
$wgLanguageCode = getenv('MW_SITE_LANG');
$wgSitename = getenv('MW_SITE_NAME');
$wgMetaNamespace = "Project";
# Configured web paths & short URLs
# This allows use of the /wiki/* path
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = "/w";        // this should already have been configured this way
$wgArticlePath = "/wiki/$1";

#Set Secret
$wgSecretKey = getenv('MW_WG_SECRET_KEY');

## RC Age
# https://www.mediawiki.org/wiki/Manual:$wgRCMaxAge
# Items in the recentchanges table are periodically purged; entries older than this many seconds will go.
# The query service (by default) loads data from recent changes
# Set this to 1 year to avoid any changes being removed from the RC table over a shorter period of time.
$wgRCMaxAge = 365 * 24 * 3600;

## Logs
$wgDebugLogGroups = array(
  'resourceloader' => '/var/log/mediawiki/resourceloader.log',
  'exception' => '/var/log/mediawiki/exception.log',
  'error' => '/var/log/mediawiki/error.log'
);
$wgDebugLogFile = "/var/log/mediawiki/debug.log";

$wgDebugToolbar = true;
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgShowSQLErrors = true;

$wgEnableUploads = true;

$wgEnableJavaScriptTest = true;

$wgEventLoggingBaseUri = '/event.gif';
$wgEventLoggingFile = '/var/log/mediawiki/events.log';
wfLoadExtension('EventLogging');

wfLoadExtension('PageImages');

$wgMFAlwaysUseContentProvider = true;
$wgMFContentProviderClass = 'MobileFrontend\ContentProviders\MwApiContentProvider';
$wgMFEnableBeta = true;
$wgMFEnableMobilePreferences = true;
$wgMFLazyLoadImages = [ 'base' => true, 'beta' => true ];
$wgMFNearby = true;
$wgMFNearbyEndpoint = 'https://en.wikipedia.org/w/api.php';
$wgMFMwApiContentProviderBaseUri = 'https://en.wikipedia.org/w/api.php';
wfLoadExtension('MobileFrontend');

wfLoadExtension('OAuth');
$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;
$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;

$wgPopupsGateway = 'restbaseHTML';
$wgPopupsRestGatewayEndpoint = 'https://en.wikipedia.org/api/rest_v1/page/summary/';
wfLoadExtension('Popups');

wfLoadExtension('QuickSurveys');

wfLoadExtension('RelatedArticles');

wfLoadExtension('WikimediaEvents');
$wgWMEReadingDepthEnabled = true;
$wgWMEReadingDepthSamplingRate = 1;

wfLoadExtension('WikimediaMessages');

$wgMinervaDownloadIcon = true;
$wgMinervaApplyKnownTemplateHacks = true;
$wgMinervaABSamplingRate = 1;
$wgMinervaErrorLogSamplingRate = 1;
$wgMinervaFeatures = ['MinervaPageIssuesNewTreatment'];
$wgMinervaShowCategoriesButton['base'] = true;
wfLoadSkin('MinervaNeue');

wfLoadSkin('Vector');
```

## Tested .htaccess
```htaccess
# This file is derived from the wikibase/wikibase docker image.
## http://www.mediawiki.org/wiki/Manual:Short_URL/Apache

# Enable the rewrite engine
RewriteEngine On

# Short url for wiki pages
RewriteRule ^/?wiki(/.*)?$ %{DOCUMENT_ROOT}/w/index.php [L]

# Redirect / to Main Page
RewriteRule ^/*$ %{DOCUMENT_ROOT}/w/index.php [L]
```
