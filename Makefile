# Lokale Entwicklung läuft ohne App-Container direkt auf dem Host (Datenbank: docker-compose.yml).
# Produktion: compose.prod.yaml über Komodo (docs/deployment.md), prod-* nur als Notweg auf abyss.
PHP      ?= php8.5
COMPOSER ?= $(PHP) $(shell command -v composer)
# Derselbe Port wie auf dem Server (HTTP_PORT in .env.prod.dist).
HTTP_PORT ?= 8198
DB_PORT   ?= 3308
DC        = docker compose
DB_ROOT   = $(DC) exec -T database mysql -uroot -psecret

.DEFAULT_GOAL := help
# Hintergrundprozesse von `make start`: PID- und Logdateien.
RUN_DIR  := var/log
BG_PROCS := serve watch worker

.PHONY: help secrets up down start stop logs install assets watch-assets db migration db-shell import-dump serve watch worker console collect outcomes simulators test lint qa prod-up prod-down prod-logs prod-console prod-status prod-backup

help: ## Diese Hilfe
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  %-14s %s\n", $$1, $$2}'

secrets: ## .env.local für die lokale Datenbank anlegen – überschreibt nichts
	@test -f .env.local || ( printf '%s\n' \
		'# Lokal (make secrets). Datenbank: docker-compose.yml' \
		'APP_SECRET='"$$(openssl rand -hex 16)" \
		'DATABASE_URL="mysql://bet_analyzer:bet_analyzer@127.0.0.1:$(DB_PORT)/bet_analyzer?serverVersion=8.4.13&charset=utf8mb4"' \
		'TELEGRAM_DSN=null://null' \
		'TELEGRAM_ENABLED=0' \
		'MAILER_DSN=null://null' \
		'TRUSTED_PROXIES=127.0.0.1' \
		> .env.local && echo ".env.local erzeugt" )

up: secrets ## Datenbank starten und warten, bis sie bereit ist
	DB_PORT=$(DB_PORT) $(DC) up -d --wait database

down: stop ## Alles stoppen (Daten bleiben im Volume bet-analyzer-dev_database)
	$(DC) down

start: up ## Komplett im Hintergrund: Datenbank, Migrationen, Webserver, Asset-Watcher, Worker
	@$(MAKE) --no-print-directory db
	@mkdir -p $(RUN_DIR)
	$(call bg,serve,$(MAKE) --no-print-directory serve)
	$(call bg,watch,$(MAKE) --no-print-directory watch-assets)
	$(call bg,worker,$(MAKE) --no-print-directory worker)
	@echo "Läuft: http://127.0.0.1:$(HTTP_PORT) · Logs: make logs · Stoppen: make stop"

stop: ## Hintergrundprozesse von `make start` beenden (Datenbank läuft weiter)
	@for p in $(BG_PROCS); do f=$(RUN_DIR)/dev-$$p.pid; \
		if [ -f $$f ] && kill -0 $$(cat $$f) 2>/dev/null; then kill -- -$$(cat $$f) && echo "$$p gestoppt"; fi; \
		rm -f $$f; done

logs: ## Logs der Hintergrundprozesse verfolgen
	tail -f $(foreach p,$(BG_PROCS),$(RUN_DIR)/dev-$(p).log)

install: ## Composer-Abhängigkeiten installieren, Assets einmal bauen
	$(COMPOSER) install
	@$(MAKE) --no-print-directory assets

# AssetMapper liefert JS/CSS in dev direkt aus; nur das SCSS muss kompiliert werden (symfonycasts/sass-bundle).
assets: ## Assets vorbereiten: Importmap-Pakete holen, SCSS einmal bauen
	$(PHP) bin/console importmap:install
	$(PHP) bin/console sass:build

watch-assets: ## SCSS bei Änderungen neu bauen (Vordergrund, Abbruch mit Strg+C)
	$(PHP) bin/console sass:build --watch

db: ## Datenbank migrieren und Messenger-Tabelle anlegen (wie der Dienst migrate live)
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(PHP) bin/console messenger:setup-transports --no-interaction

migration: ## Migration aus geänderten Entities erzeugen
	$(PHP) bin/console doctrine:migrations:diff

db-shell: ## MySQL-Shell auf der lokalen Datenbank
	$(DC) exec database mysql -uroot -psecret bet_analyzer

import-dump: ## Dump einspielen, ersetzt die lokale DB komplett (DUMP=pfad/zum/dump.sql.gz)
	@test -n "$(DUMP)" && test -f "$(DUMP)" || ( echo "DUMP=… fehlt oder Datei nicht gefunden"; exit 1 )
	@gzip -t "$(DUMP)" && echo "Dump ist intakt: $$(du -h "$(DUMP)" | cut -f1)"
	@# Worker anhalten: ohne Tabelle messenger_messages stürzt er sonst ab
	@f=$(RUN_DIR)/dev-worker.pid; if [ -f $$f ] && kill -0 $$(cat $$f) 2>/dev/null; then kill -- -$$(cat $$f); rm -f $$f; echo "worker angehalten"; fi
	$(DB_ROOT) -e 'DROP DATABASE IF EXISTS bet_analyzer; CREATE DATABASE bet_analyzer; GRANT ALL ON bet_analyzer.* TO "bet_analyzer"@"%"'
	@# Funktioniert mit dem securitas-Dump (ohne CREATE DATABASE) und den Produktions-Backups (mit USE bet_analyzer)
	time (gunzip -c "$(DUMP)" | $(DB_ROOT) bet_analyzer)
	@$(MAKE) --no-print-directory db
	$(PHP) bin/console doctrine:schema:validate --skip-mapping

serve: ## Webserver auf http://127.0.0.1:$(HTTP_PORT)
	PHP_CLI_SERVER_WORKERS=4 $(PHP) -S 127.0.0.1:$(HTTP_PORT) -t public

worker: ## Messenger-Worker (Simulatoren, Statistiken, Telegram)
	$(PHP) -d memory_limit=1536M bin/console messenger:consume async -vv --memory-limit=1024M

console: ## Symfony-Konsole, z. B. make console CMD="debug:router"
	$(PHP) bin/console $(CMD)

collect: ## Tagesspiele von Tipico holen (wie der Cron um 00:10), ARGS="-vvv"
	$(PHP) -d memory_limit=1G bin/console CollectDailyMatchesCommand --no-interaction $(ARGS)

outcomes: ## Ergebnisse beendeter Spiele nachtragen (wie der stündliche Cron)
	$(PHP) -d memory_limit=1G bin/console CheckMatchOutcomes --no-interaction $(ARGS)

simulators: ## Simulatoren anstoßen (SET=three_way_simulators), rechnen tut der Worker
	$(PHP) -d memory_limit=512M bin/console ProcessSimulators $(or $(SET),three_way_simulators) --no-interaction

test: ## PHPUnit
	$(PHP) bin/phpunit

lint: ## Container, Twig-Templates und YAML prüfen
	$(PHP) bin/console lint:container
	$(PHP) bin/console lint:twig templates
	$(PHP) bin/console lint:yaml config docker/cron --parse-tags

qa: lint test ## Alle Prüfungen

# ---------- Produktion (auf abyss im Stack-Ordner ausführen; Standard ist Komodo, siehe docs/deployment.md) ----------
PROD_ENV     ?= .env.prod.local
PROD_PROJECT ?= bet-analyzer
PROD_DC       = docker compose -p $(PROD_PROJECT) -f compose.prod.yaml --env-file $(PROD_ENV)

# Dasselbe wie ein Deploy in Komodo, nur ohne git pull.
prod-up: ## Produktion: Datenbank sichern, Images bauen, migrieren, starten, aufräumen (Notweg neben Komodo)
	COMPOSE_PROJECT=$(PROD_PROJECT) ENV_FILE=$(PROD_ENV) sh deploy/komodo/pre-deploy.sh
	$(PROD_DC) up -d --build --wait --wait-timeout 600
	COMPOSE_PROJECT=$(PROD_PROJECT) ENV_FILE=$(PROD_ENV) sh deploy/komodo/post-deploy.sh

prod-down: ## Produktion: stoppen (Daten bleiben im Volume)
	$(PROD_DC) down

prod-logs: ## Produktion: Logs verfolgen
	$(PROD_DC) logs -f --tail=100

prod-console: ## Produktion: Symfony-Konsole, z. B. make prod-console CMD="messenger:stats"
	$(PROD_DC) exec app php bin/console $(CMD)

prod-backup: ## Produktion: Datenbank jetzt sichern (BACKUP_DIR, Anlass „manuell“)
	$(PROD_DC) run --rm --no-TTY --no-deps backup manuell

prod-status: ## Produktion: Zustand auf einen Blick
	@$(PROD_DC) ps --format 'table {{.Service}}\t{{.Status}}'

# $(call bg,name,befehl): startet befehl in eigener Prozessgruppe (damit `stop` auch Kindprozesse erwischt).
define bg
	@f=$(RUN_DIR)/dev-$(1).pid; \
	if [ -f $$f ] && kill -0 $$(cat $$f) 2>/dev/null; then echo "$(1) läuft schon"; \
	else setsid nohup $(2) > $(RUN_DIR)/dev-$(1).log 2>&1 < /dev/null & echo $$! > $$f; echo "$(1) gestartet"; fi
endef
