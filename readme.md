# boxwiki
A loose composition of Docker containers wrapping a MediaWiki master mount for
development. Based on [wikibase-docker].

[wikibase-docker]: https://github.com/wmde/wikibase-docker

## Prerequisites

```bash
# MediaWiki development dependencies.
sudo apt install \
  composer docker-compose php-codesniffer php-xml php-mbstring php-cli phpunit

# Obtain a copy of Node.js from https://nodejs.org/.

# Download Core.
repo_base=https://gerrit.wikimedia.org/r/mediawiki
time git clone --recursive "$repo_base/core.git"
cd core

# Download extensions.
time while read i; do git -C extensions clone --recursive "$i"; done << eof
$repo_base/extensions/BetaFeatures
$repo_base/extensions/CentralNotice
$repo_base/extensions/CirrusSearch
$repo_base/extensions/Cite
$repo_base/extensions/CiteThisPage
$repo_base/extensions/Collection
$repo_base/extensions/Echo
$repo_base/extensions/ElectronPdfService
$repo_base/extensions/EventLogging
$repo_base/extensions/GeoData
$repo_base/extensions/PageImages
$repo_base/extensions/MobileFrontend
$repo_base/extensions/OAuth
$repo_base/extensions/PageImages
$repo_base/extensions/Popups
$repo_base/extensions/QuickSurveys
$repo_base/extensions/RelatedArticles
$repo_base/extensions/TextExtracts
$repo_base/extensions/VisualEditor
$repo_base/extensions/Wikibase
$repo_base/extensions/WikimediaEvents
$repo_base/extensions/WikimediaMessages
https://github.com/filbertkm/WikibaseImport
eof

# Download skins.
time while read i; do git -C skins clone --recursive "$i"; done << eof
$repo_base/skins/MinervaNeue
$repo_base/skins/Vector
eof

# Install PHP dependencies.
time for i in . extensions/*/ skins/*/; do composer --working-dir="$i" install; composer --working-dir="$i" update; done

# Install NPM dependencies.
cd .. && . ~/.nvm/nvm.sh && nvm use && cd core &&
time for i in . extensions/*/ skins/*/; do npm -C "$i" i; done

# Link yur LocalSettings.php as LocalSettingsDev.php. E.g., `ln "$PWD/LocalSettingsDev.php" core/`.

# Link your .htaccess file. E.g., `ln "$PWD/.htaccess" core/`.
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
  git -C "$i" fetch --all
  { git -C "$i" pull && git -C "$i" submodule update --recursive --init; } || echo -e "\033[0;31m████████████ $i ████████████\033[0m"
  composer --working-dir="$i" install
  composer --working-dir="$i" update
  npm -C "$i" i
done
```

## Log into the box
```bash
docker exec -it boxwiki_mediawiki_1 bash
su stephen
```

### Import kittens from Wikidata
```bash
docker exec -it boxwiki_mediawiki_1 php extensions/WikibaseImport/maintenance/importEntities.php --entity Q147
docker exec -it boxwiki_mediawiki_1 php maintenance/update.php --quick
```

### Run PHPUnit tests
```bash
time php tests/phpunit/phpunit.php --filter PageSplitTesterTest
```

### Add an image
```bash
docker exec -it boxwiki_mediawiki_1 php maintenance/importImages.php images
docker exec -it boxwiki_mediawiki_1 bash -c "php maintenance/edit.php kitten <<< '[[File:Kitten.jpg]]'"
```

### Run PHP unit tests
```bash
docker exec -it boxwiki_mediawiki_1 php tests/phpunit/phpunit.php --filter TextExtracts
```
