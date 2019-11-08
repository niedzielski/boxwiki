<?php

// error_reporting( -1 );
// ini_set( 'display_errors', 1 );

# Protect against web entry
if (!defined('MEDIAWIKI')) {
  exit;
}

$wgServer = '//localhost:8181';
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
  'resourceloader' => '/var/www/html/logs/resourceloader.log',
  'exception' => '/var/www/html/logs/exception.log',
  'error' => '/var/www/html/logs/error.log'
);
$wgDebugLogFile = "/var/www/html/logs/debug.log";
// $wgDebugComments = true;
$wgDebugToolbar = true;
// $wgShowDebug = true;
$wgDevelopmentWarnings = true;
$wgShowExceptionDetails = true;
$wgShowDBErrorBacktrace = true;
$wgShowSQLErrors = true;

$wgEnableUploads = true;

$wgEnableJavaScriptTest = true;

wfLoadExtension('BetaFeatures');

wfLoadExtension('CentralNotice');
$wgNoticeInfrastructure = true;
$wgNoticeProjects = array('centralnoticeproject'); # 'centralnoticeproject' can be any string
$wgNoticeProject = 'centralnoticeproject'; # must be the same as above
$wgCentralHost = 'localhost';
$wgCentralSelectedBannerDispatcher = 'http://localhost:8181/w/index.php?title=Special:BannerLoader';
$wgCentralDBname = $wgDBname; # the same as $wgDBname
$wgCentralNoticeGeoIPBackgroundLookupModule = 'ext.centralNotice.freegeoipLookup';

wfLoadExtension('Cite');

wfLoadExtension('CiteThisPage');

// require_once("$IP/extensions/Collection/Collection.php");

wfLoadExtension('Echo');

wfLoadExtension('ElectronPdfService');
$wgElectronPdfServiceRESTbaseURL = '/api/rest_v1/page/pdf/';

$wgEventLoggingBaseUri = '/event.gif';
$wgEventLoggingFile = '/var/www/html/logs/events.log';
wfLoadExtension('EventLogging');

wfLoadExtension('GeoData');

wfLoadExtension('PageImages');

$wgMFAdvancedMobileContributions = true;
$wgMFUseDesktopSpecialHistoryPage = [
  "amc" => true
];
$wgMinervaOverflowInPageActions = [
  "amc" => true
];
$wgMFAlwaysUseContentProvider = true;
$wgMFContentProviderScriptPath = 'https://en.wikipedia.org/w';
$wgMFContentProviderClass = 'MobileFrontend\ContentProviders\MwApiContentProvider';

//$wgMFContentProviderClass = 'MobileFrontend\ContentProviders\McsContentProvider';
// $wgMFMcsContentProviderBaseUri = "https://he.wikipedia.org/api/rest_v1";

$wgMFEnableXAnalyticsLogging = true;
$wgMFEnableBeta = true;
//$wgMFEnableMobilePreferences = true;
$wgMFLazyLoadImages = ['base' => true, 'beta' => true];
$wgMFNearby = true;
$wgUseInstantCommons = true;
$wgMFNearbyEndpoint = 'https://en.wikipedia.org/w/api.php';
$wgMFMwApiContentProviderBaseUri = 'https://en.wikipedia.org/w/api.php';
$wgMFUseWikibase = true;
$wgMFDisplayWikibaseDescriptions = [
  'search' => true,
  'nearby' => true,
  'watchlist' => true,
  'tagline' => true,
];
$wgMFExperiments['betaoptin'] = array(
  "name" => "betaoptin",
  'buckets' => [
    'control' => 0,
    'A' => 1,
  ],
  'enabled' => true,
);
wfLoadExtension('MobileFrontend');

wfLoadExtension('OAuth');
$wgGroupPermissions['sysop']['mwoauthproposeconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthmanageconsumer'] = true;
$wgGroupPermissions['sysop']['mwoauthviewprivate'] = true;
$wgGroupPermissions['sysop']['mwoauthupdateownconsumer'] = true;

$wgPopupsGateway = 'restbaseHTML';
$wgPopupsRestGatewayEndpoint = 'https://en.wikipedia.org/api/rest_v1/page/summary/';
$wgPopupsReferencePreviews = true;
$wgPopupsVirtualPageViews = true;
$wgPopupsEventLogging = true;
wfLoadExtension('Popups');

wfLoadExtension('QuickSurveys');
include_once "$IP/extensions/QuickSurveys/tests/browser/LocalSettings.php";

wfLoadExtension('RelatedArticles');

wfLoadExtension('VisualEditor');
// Enable by default for everybody
$wgDefaultUserOptions['visualeditor-enable'] = 1;
$wgVirtualRestConfig['modules']['parsoid'] = array(
  // URL to the Parsoid instance
  // Use port 8142 if you use the Debian package
  'url' => 'https://en.wikipedia.org',
  // Parsoid "domain", see below (optional)
  'domain' => 'en.wikipedia.org',
  // Parsoid "prefix", see below (optional)
  'prefix' => 'localhost'
);
$wgVisualEditorFullRestbaseURL = 'https://en.wikipedia.org/api/rest_';

## Wikibase
require_once "$IP/extensions/Wikibase/vendor/autoload.php";
require_once "$IP/extensions/Wikibase/lib/WikibaseLib.php";
require_once "$IP/extensions/Wikibase/repo/Wikibase.php";
require_once "$IP/extensions/Wikibase/repo/ExampleSettings.php";
require_once "$IP/extensions/Wikibase/client/WikibaseClient.php";
require_once "$IP/extensions/Wikibase/client/ExampleSettings.php";
$wgEnableWikibaseRepo = true;
$wgEnableWikibaseClient = true;
$wgWBClientSettings['pageSchemaNamespaces'] = [0, 6, 120];
$wgWBClientSettings['pageSchemaSplitTestSamplingRatio'] = .5;
$wgWBClientSettings['pageSchemaSplitTestBuckets'] = ['control', 'treatment'];
$wgWBClientSettings['siteGlobalID'] = 'enwiki';

wfLoadExtension('WikibaseImport');

wfLoadExtension('WikimediaEvents');
$wgWMEReadingDepthEnabled = true;
$wgWMEReadingDepthSamplingRate = 1;

wfLoadExtension('WikimediaMessages');

$wgMinervaDownloadIcon = true;
$wgMinervaApplyKnownTemplateHacks = true;
$wgMinervaABSamplingRate = 1;
$wgMinervaErrorLogSamplingRate = 1;
$wgMinervaPageIssuesNewTreatment = ['base' => true, 'beta' => true];
//#$skinOptions = ['MinervaPageIssuesNewTreatment' => true];
// $wgMinervaFeatures = ['MinervaPageIssuesNewTreatment'];
$wgMinervaShowShareButton = ['base' => true, 'beta' => true];
$wgMinervaShowCategoriesButton['base'] = true;
$wgMinervaCountErrors = true;
wfLoadSkin('MinervaNeue');

wfLoadExtension( 'SandboxLink' );

wfLoadExtension('TextExtracts');

wfLoadSkin('Vector');

wfLoadSkin('Eigenvector');
