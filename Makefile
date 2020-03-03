################################################################################
# Global defines
################################################################################
WSL					:= false
LANDO 			:= true
DRUPAL_ROOT := /var/www/html/web
LANDO_CMD 	:=

include .env

ifeq ($(LANDO),true)
LANDO_CMD=lando
endif

# COLORS
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

.DEFAULT_GOAL := help

################################################################################
# Database
################################################################################
.PHONY : db-dump
db-dump: ##@database Downloads latest db backup from dev.
	rm -f database.sql.gz; \
	if [ -z "$(E)" ]; then $(eval E=dev):; fi; \
	platform db:dump -f database.sql.gz --environment $(E) --gzip -y

.PHONY : db-import
db-import: ##@database Imports database into lando
	lando db-import database.sql.gz

.PHONY : db
db: db-dump db-import ##@database Downloads latest db backup from dev and imports into lando

################################################################################
# Development
################################################################################
.PHONY : cr
cr: ##@drupal Clears drupal caches
	$(LANDO_CMD) drush cr

.PHONY : uli
uli: ##@drupal Creates a log in link.
	$(LANDO_CMD) drush uli --no-browser;

################################################################################
# Development
################################################################################
.PHONY : update
update: ##@development Clears caches, runs database updates, entity updates, and config imports.
	$(LANDO_CMD) composer update --no-interaction --prefer-dist; \
	$(LANDO_CMD) drush cr; \
	$(LANDO_CMD) drush -y updb; \
	$(LANDO_CMD) drush -y entup; \
	$(LANDO_CMD) drush -y config-import

.PHONY : dev-mode
dev-mode: ##@development Enables development modules and puts site into dev mode
	$(LANDO_CMD) drush en devel kint stage_file_proxy -y; \
	$(LANDO_CMD) drupal smo dev

.PHONY : prod-mode
prod-mode: ##@development Disables development modules and puts site into production mode
	$(LANDO_CMD) drush pm-uninstall devel kint stage_file_proxy -y; \
	$(LANDO_CMD) drupal smo prod

################################################################################
# Composer
################################################################################
.PHONY : require
require: ##@development Disables development modules and puts site into production mode
	$(LANDO_CMD) composer require $(P) --no-interaction --prefer-dist;

################################################################################
# CSS
################################################################################
.PHONY : sass
sass: ##@development Compiles scss
	sass web/themes/custom/spacebase/scss/style.scss web/themes/custom/spacebase/css/style.css \
	&& make cr

################################################################################
# Lando
################################################################################
.PHONY : rebuild
rebuild: ##@lando Rebuilds lando environment from the ground up, authenticating with pantheon, syncing db, building pattern lab, running local updates, and putting the site into dev mode.
	lando destroy -y; \
	lando start; \
	make db; \
	make update; \
	make sass; \
	make uli;

################################################################################
# Help
################################################################################
HELP_FUN = \
    %help; \
    while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^([a-zA-Z\-]+)\s*:.*\#\#(?:@([a-zA-Z\-]+))?\s(.*)$$/ }; \
    print "\nusage: ${YELLOW}make [target]${RESET}\n\n"; \
    for (sort keys %help) { \
    print "${WHITE}$$_:${RESET}\n"; \
    for (@{$$help{$$_}}) { \
    $$sep = " " x (15 - length $$_->[0]); \
    print "  ${YELLOW}$$_->[0]${RESET}$$sep${GREEN}$$_->[1]${RESET}\n"; \
    }; \
    print "\n"; }

.PHONY : help
help: ##@help Shows this help.

	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

# https://stackoverflow.com/a/6273809/1826109
%:
	@: