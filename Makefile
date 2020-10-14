BUILD_VERSION := $(shell awk -v RS="\n" -F "=" '{ if ($$1 == "version"){print $$2}}' plugin.manifest)
PLUGIN_NAME := $(shell awk -v RS="\n" -F "=" '{ if ($$1 == "pluginname"){print $$2}}' plugin.manifest)

build:
	echo "Building ZIP file"
	zip -r "$(PLUGIN_NAME)_v$(BUILD_VERSION).zip" WorkplaceAllocation.php plugin.manifest templates sql migrations img cronjobs conf classes assets locks