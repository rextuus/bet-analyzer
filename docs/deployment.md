# bet-analyzer – Deployment, Umzug und Betrieb

Stand: 20.11.2026. Produktion läuft als Komodo-Stack `bet-analyzer` auf **abyss** (10.0.0.2:8198), erreichbar
über Caddy auf dem VPS unter <https://bet-analyzer.wh-company.de>. Vorbild: Wedora/hub.

## Überblick

| Dienst     | Aufgabe                                                                             | Dauerhaft?      | Healthcheck             |
|------------|-------------------------------------------------------------------------------------|-----------------|-------------------------|
| `migrate`  | `doctrine:migrations:migrate` bei jedem Deploy                                      | nein (One-Shot) | –                       |
| `app`      | PHP-FPM 8.5 (Symfony 6.4)                                                           | ja              | FPM-Ping                |
| `worker`   | `messenger:consume async` (Simulatoren, Statistiken, Betano/Bwin-Dateien, Telegram) | ja              | DB erreichbar           |
| `cron`     | supercronic mit `docker/cron/crontab` (früher Crontab von master@securitas)         | ja              | DB erreichbar           |
| `web`      | Caddy → `app:9000`, einziger Host-Port `${HTTP_BIND}:${HTTP_PORT}`                  | ja              | `/healthz` (bis zur DB) |
| `database` | MySQL 8.4.13                                                                        | ja              | `mysqladmin ping`       |
| `backup`   | Dump nach `/srv/bet-analyzer/backups` (Profil `backup`, nur auf Abruf)              | nein            | –                       |

Geplante Aufgaben (`docker/cron/crontab`, Europe/Berlin):

| Zeit          | Befehl                                                                                    |
|---------------|-------------------------------------------------------------------------------------------|
| 00:03         | `StoreTipicoBackupFileCommand` – Rohantwort von Tipico als JSON ins Volume                |
| 00:10         | `CollectDailyMatchesCommand` – Tagesspiele mit Quoten einlesen                            |
| stündlich :05 | `CheckMatchOutcomes` – Ergebnisse beendeter Spiele                                        |
| alle 4 h :15  | `ProcessSimulators three_way_simulators` – Simulatoren anstoßen (Arbeit macht der Worker) |

### Persistente Daten

| Was                                                               | Wo                                                                                                     | Gesichert                                                                                                                     |
|-------------------------------------------------------------------|--------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------|
| Datenbank `bet_analyzer` (≈ 1,3 GB, ohne die Sportmonks-Altdaten) | Volume `bet-analyzer_database_data`                                                                    | täglicher Dump + Dump vor jedem Deploy → `/srv/bet-analyzer/backups` → offsite                                                |
| JSON-Rohdaten der Buchmacher                                      | Volume `bet-analyzer_provider_files` (in app/worker/cron unter `public/backups`, **nicht** öffentlich) | nein – Inhalt steckt nach dem Einlesen in der DB. Startet leer; die alten Dateien (2,1 GB) liegen nur im Archiv auf securitas |
| Caddy-Daten                                                       | Volumes `bet-analyzer_caddy_*`                                                                         | nein (nichts Wichtiges, TLS macht der VPS)                                                                                    |

Nichts liegt im Stack-Ordner `/etc/komodo/stacks/bet-analyzer` – Komodo darf ihn jederzeit neu klonen.

### Variablen und Secrets

Vollständige Liste mit Erklärungen: [`.env.prod.dist`](../.env.prod.dist). In Komodo (Settings → Variables,
Häkchen „Secret“) anzulegen:

| Komodo-Variable                    | Wert / Herkunft                                                                                                                                                                                                 |
|------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `BET_ANALYZER_APP_SECRET`          | **neu**: `openssl rand -hex 32` (der alte war über den Profiler sichtbar; alle müssen sich einmal neu anmelden)                                                                                                 |
| `BET_ANALYZER_MYSQL_PASSWORD`      | neu: `openssl rand -hex 24` (neue Datenbank, das alte Passwort aus dem Repo nicht weiterverwenden)                                                                                                              |
| `BET_ANALYZER_MYSQL_ROOT_PASSWORD` | neu: `openssl rand -hex 24`                                                                                                                                                                                     |
| `BET_ANALYZER_TELEGRAM_DSN`        | `telegram://<BOT_TOKEN>@default?channel=<CHAT_ID>` – Token **neu erzeugen** (BotFather → /revoke), das alte stand im Klartext in `.env` und war über den offenen Profiler sichtbar; ohne Telegram `null://null` |
| `BET_ANALYZER_SENTRY_DSN`          | Sentry-DSN oder die Zeile im Environment der `.toml` löschen                                                                                                                                                    |
| `BET_ANALYZER_MAILER_DSN`          | `null://null` (es werden keine Mails verschickt)                                                                                                                                                                |

Nicht geheim und fest in `deploy/komodo/bet-analyzer.toml`: `DEFAULT_URI`, `HTTP_BIND=10.0.0.2`,
`HTTP_PORT=8198`, `REVERSE_PROXY_IPS=10.0.0.1`, `MYSQL_DATABASE/USER=bet_analyzer`, `TELEGRAM_ENABLED`,
`CASH_BOX_MIN_LIMIT=80`, `BACKUP_DIR`, `BACKUP_KEEP=7`.

## Umzug von securitas

**Ausgangslage** (nicht wie im Steckbrief „abyss in Docker“): Das Projekt läuft auf **securitas** nativ –
Apache + PHP-FPM aus `/var/www/bet-analyzer`, MySQL 8.0 nativ (`bet_analyzer`, 13,9 GB), Cronjobs in der
Crontab von `master`. Die Domain zeigt per DynDNS (Strato, `~master/strato_update.sh`) direkt auf securitas.

Wichtig für die Planung:

- Die Tipico-Daten enden am **27.11.2025**: Tipico hat die API umgestellt, seitdem sammelt securitas nichts
  mehr. Im Umzug steckt der Fix. Auf securitas wird also während des Umzugs nichts Wertvolles mehr
  geschrieben – die Ausfallzeit ist unkritisch.
- securitas läuft mit `APP_ENV=dev` – siehe [Sofortmaßnahme](#0-sofortmaßnahme-auf-securitas-unabhängig-vom-umzug).
- Von den 13,9 GB entfallen ≈ 12,6 GB auf Tabellen, die der neue Code nicht mehr kennt (Sportmonks: `spm_odd`
  allein 12 GB / 95 Mio. Zeilen, dazu `placed_bet`, `bet_row_*`, `simple_bet_row*`, `season_*`, `odd_outcome*`
  u. a.). Sie kommen **nicht** in die neue Datenbank, sondern einmal als Archiv-Dump auf securitas. Was umzieht,
  ist ≈ 1,3 GB groß.

### 0. Sofortmaßnahme auf securitas (unabhängig vom Umzug)

Die Seite läuft dort im **Debug-Modus**: `https://bet-analyzer.wh-company.de/_profiler/` ist öffentlich (HTTP 200) und
zeigt u. a. Umgebungsvariablen (DB-Passwort, `APP_SECRET`, Telegram-Token). Bis zum Umzug:

```sh
# auf securitas, als master
cd /var/www/bet-analyzer
grep -n '^APP_ENV\|^APP_DEBUG' .env .env.local 2>/dev/null
# APP_ENV=prod und APP_DEBUG=0 in .env.local setzen, dann:
APP_ENV=prod php bin/console cache:clear
curl -s -o /dev/null -w '%{http_code}\n' https://bet-analyzer.wh-company.de/_profiler/   # erwartet 404
```

Danach Telegram-Token rotieren (BotFather) und die Sportmonks-Keys beim Anbieter widerrufen (sie stehen in der
Git-History von `rextuus/bet-analyzer`).

### 1. Vorbereitung (nichts geht offline)

1. Diesen Stand prüfen, committen, auf `main` pushen.
2. GitHub: Repo `rextuus/bet-analyzer` zum Fine-grained-Token `komodo-abyss` hinzufügen (Contents: read).
3. abyss: `sudo install -d -m 700 /srv/bet-analyzer/backups`
4. abyss: freien Platz prüfen – Datenbank ≈ 1,3 GB im Volume (wächst mit Indizes und neuen Daten), dazu der
   Import-Dump und bis zu 14 Dumps à einige 100 MB in `/srv/bet-analyzer/backups`:
   `df -h /var/lib/docker /srv` (≥ 15 GB frei einplanen).
5. Komodo → Settings → Variables: die sechs `BET_ANALYZER_*`-Secrets anlegen (Tabelle oben).
6. Komodo → Syncs → New Resource Sync: Mode „Git Repo“, Repo `rextuus/bet-analyzer`, Branch `main`,
   Resource Path `deploy/komodo/bet-analyzer.toml`, „Delete Unmatched“ **aus** → Refresh → Änderungen prüfen (ein Stack,
   eine Procedure) → Execute Sync.
7. Komodo → Stack `bet-analyzer` → **Deploy**. Erster Deploy auf leerem Rechner: Pre-Deploy überspringt die
   Sicherung („Noch keine Datenbank vorhanden“), `migrate` legt das Ausgangsschema an, alle Dienste werden
   gesund. Im Post-Deploy-Log: `Webserver: {"status":"ok","database":"ok"}` und `Cron: Crontab gültig`.
8. Probe ohne DNS: `curl -s http://10.0.0.2:8198/healthz` (von abyss oder per WireGuard) → `{"status":"ok"…}`.
9. Den Cron **gleich abstellen**, bis die echten Daten drin sind (sonst sammelt er in die leere DB):
   `docker compose -p bet-analyzer -f compose.prod.yaml --env-file .env.prod.local stop cron worker`
   (im Stack-Ordner, als root; oder in Komodo die Dienste stoppen).

### 2. Daten übernehmen (Cutover)

Erwartete Dauer: Umzugs-Dump (≈ 1,3 GB Daten) wenige Minuten, Import in MySQL 8.4 grob **10–20 min**
(Probe: 275 MB in 1:47 min), Index-Migration einige Minuten. Der Archiv-Dump der Sportmonks-Tabellen (12,6 GB)
läuft unabhängig davon auf securitas und blockiert nichts. Die alte Seite bleibt währenddessen lesbar.

1. **securitas: Cronjobs aus.** In `crontab -e` (master) die vier Zeilen unter `#collect and check matches`
   und `#process simulators` auskommentieren.
2. **securitas: Warteschlange.** Auf securitas läuft kein Messenger-Worker für bet-analyzer (supervisor kennt
   nur digital-frame). Stand 23.11.2026: 43 Nachrichten, alle vom 22./23.11. – zieht mit um und wird vom
   neuen Worker abgearbeitet. Was er nicht lesen kann, landet in der `failed`-Queue (`messenger:failed:show`, weg mit
   `messenger:failed:remove`).
3. **securitas: Dumps** (als master, sudo für den MySQL-Root). Zwei getrennte Dumps: die Tabellen, die der neue
   Code nutzt (zieht um), und die Altdaten ohne Code (nur Archiv). Keine übernommene Tabelle hat einen
   Fremdschlüssel auf eine Alttabelle (geprüft 23.11.2026).
   ```sh
   ALT="spm_odd spm_score spm_fixture spm_round spm_standing spm_team spm_season spm_league
        odd_outcome_spm_fixture odd_outcome placed_bet bet_row_summary bet_row_odd_filter
        bet_row_combination_simple_bet_row bet_row_combination simple_bet_row_bet_row_odd_filter simple_bet_row
        season_summary season_statistic invalid_fixture betano_settings betano_backup"
   OPTS="--single-transaction --quick --routines --triggers --events --hex-blob --default-character-set=utf8mb4 --set-gtid-purged=OFF --no-tablespaces"
   IGNORE=$(for t in $ALT; do printf -- '--ignore-table=bet_analyzer.%s ' "$t"; done)

   # a) Umzug (≈ 1,3 GB Daten)
   sudo mysqldump $OPTS $IGNORE bet_analyzer | gzip -6 > /home/master/bet_analyzer-umzug-$(date +%F).sql.gz
   gzip -t /home/master/bet_analyzer-umzug-*.sql.gz && ls -lh /home/master/bet_analyzer-umzug-*.sql.gz
   # Prüfen: keine Alttabelle drin, tipico_bet drin
   zgrep -c '^CREATE TABLE `spm_' /home/master/bet_analyzer-umzug-*.sql.gz     # erwartet 0
   zgrep -c '^CREATE TABLE `tipico_bet`' /home/master/bet_analyzer-umzug-*.sql.gz  # erwartet 1

   # b) Archiv der Altdaten (12,6 GB, dauert; kann parallel laufen oder auch nach dem Umzug)
   mkdir -p /home/master/abschied
   sudo mysqldump $OPTS bet_analyzer $ALT | gzip -6 > /home/master/abschied/bet_analyzer-altdaten-sportmonks-$(date +%F).sql.gz
   gzip -t /home/master/abschied/bet_analyzer-altdaten-*.sql.gz && ls -lh /home/master/abschied/
   ```
   (bewusst **ohne** `--databases`: der Dump enthält kein `CREATE DATABASE/USE` und lässt sich in die
   vorhandene, leere Datenbank einspielen.)
4. **Übertragen** (über den Arbeitsrechner, der beide Schlüssel hat):
   ```sh
   scp securitas:/home/master/bet_analyzer-umzug-*.sql.gz /tmp/
   scp /tmp/bet_analyzer-umzug-*.sql.gz abyss:/tmp/
   ssh abyss 'sudo install -m 600 -o root /tmp/bet_analyzer-umzug-*.sql.gz /srv/bet-analyzer/backups/ && rm /tmp/bet_analyzer-umzug-*.sql.gz'
   ```
5. **abyss: Datenbank leeren und importieren** (im Stack-Ordner, als root):
   ```sh
   cd /etc/komodo/stacks/bet-analyzer
   dc() { docker compose -p bet-analyzer -f compose.prod.yaml --env-file .env.prod.local "$@"; }
   dc stop web app worker cron
   # Leere Datenbank samt doctrine_migration_versions verwerfen – sonst gelten die Migrationen als erledigt,
   # obwohl der Import ältere Tabellen mitbringt.
   dc exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" -e "DROP DATABASE bet_analyzer; CREATE DATABASE bet_analyzer; GRANT ALL ON bet_analyzer.* TO \"bet_analyzer\"@\"%\""'
   time (gunzip -c /srv/bet-analyzer/backups/bet_analyzer-umzug-*.sql.gz \
     | dc exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" bet_analyzer')
   ```
6. **Migrationen von Hand** (das Anlegen der Indizes auf `tipico_placement` dauert; so läuft der Komodo-Deploy
   danach nicht in sein Zeitlimit):
   ```sh
   dc run --rm migrate
   dc run --rm --no-deps migrate php bin/console doctrine:schema:validate --skip-mapping
   ```
   Erwartet: `Version20261120000000` ohne SQL („Bestehende Datenbank“), `…0100` ergänzt Fehlendes,
   `…0200` legt Indizes an; danach „The database schema is in sync“.
7. **Komodo → Deploy.** Pre-Deploy zieht den ersten echten Dump, danach starten alle Dienste. `migrate` hat
   nichts mehr zu tun.

Die alten JSON-Rohdaten (`public/backups`, 2,1 GB) ziehen **nicht** mit: Ihr Inhalt steckt in der DB, gelesen
wird eine Datei nur einmal direkt nach dem Speichern (`storeBetsFromBackupFile`). Die Einträge in
`betting_provider_backup_file` zeigen weiter auf `/var/www/bet-analyzer/…` – nur noch als Historie. Die Dateien
landen mit dem Projektordner im Archiv (Schritt 4.7).

### 3. Smoke-Test (vor der DNS-Umstellung)

```sh
curl -s http://10.0.0.2:8198/healthz                       # {"status":"ok","database":"ok"}
curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://10.0.0.2:8198/   # 302 → /login
dc exec -T cron supercronic -test /srv/app/docker/cron/crontab                   # crontab is valid
dc exec -T cron php bin/console CollectDailyMatchesCommand                        # holt heutige Spiele (Tipico)
dc exec -T cron php bin/console CheckMatchOutcomes
dc logs --tail=50 worker cron
```

Im Browser per SSH-Tunnel (`ssh -L 8198:10.0.0.2:8198 abyss`, dann <http://localhost:8198>): Login mit dem
bisherigen Konto, Dashboard zeigt „Matches today“ > 0 und **keine** gelbe Warnung zum Datenstand, Simulator-Liste
und -Detail laden.

### 4. Umschalten

1. homelab: Inventar-Eintrag (unten) anlegen, `bin/inventory`, `bin/gen-docs`, `bin/gen-caddy`,
   `bin/gen-offsite`, committen, `bin/caddy-deploy` (Trockenlauf) → `bin/caddy-deploy --apply`.
2. DNS bei Strato: `bet-analyzer.wh-company.de` → A-Record auf den VPS (217.154.91.97).
3. securitas: `bet-analyzer.wh-company.de` aus `HOSTNAMES_WH` in `~master/strato_update.sh` nehmen – sonst
   setzt das DynDNS-Skript die Domain bei der nächsten IP-Änderung zurück.
4. Prüfen: `curl -sI https://bet-analyzer.wh-company.de/healthz` (200, Zertifikat von Caddy), Login im Browser.
5. Komodo → Procedure `bet-analyzer-backup` einmal **Run** → neuer `…-daily.sql.gz` in `/srv/bet-analyzer/backups`.
6. homelab: `bin/monitors` → `bin/monitors --apply`, dann `bin/check`.
7. Nach ein paar ruhigen Tagen auf securitas: vHost abschalten (`a2dissite`, Zertifikat löschen), Ordner als
   tar nach `/home/master/abschied` – **ohne** `var/log` und `var/cache` (≈ 3,4 GB Dev-Log/-Cache), **mit**
   `public/backups` (Rohdaten):
   `tar --exclude=./var/log --exclude=./var/cache --exclude=./node_modules --exclude=./vendor -czf /home/master/abschied/bet-analyzer-www-$(date +%F).tar.gz -C /var/www/bet-analyzer .`
   Archiv-Dump der Altdaten liegt schon dort (Schritt 2.3b). Danach DB `bet_analyzer` auf securitas löschen (13,9 GB auf
   der vollen SSD).

### Rückfallebene

Bis Schritt 4.2 ist nichts umgestellt: securitas läuft unverändert weiter (nur die Cronjobs sind aus –
wieder einkommentieren). Nach der DNS-Umstellung: A-Record zurück auf securitas, Domain wieder in
`HOSTNAMES_WH`, Cronjobs an, Caddy-Block in homelab wieder auf `ingress: legacy`. Auf securitas wurde in der
Zwischenzeit nichts geschrieben, die Daten dort sind also der Stand vom Dump.

Ausfallzeit für Besucher: praktisch keine (die alte Seite bleibt bis zur DNS-Umstellung erreichbar; danach
DNS-TTL). Für die Datensammlung: vom Abschalten der Cronjobs bis zum Deploy – unkritisch, weil seit 11/2025
ohnehin nichts mehr gesammelt wurde.

### Eintrag für homelab/inventory.yaml

```yaml
  bet-analyzer:
    description: Sammelt jeden Tag Wett-Quoten und lässt Simulatoren auf diesen laufen
    repo: rextuus/bet-analyzer
    branch: main
    host: abyss
    komodo_stack: bet-analyzer
    compose_project: bet-analyzer
    status: live-komodo
    ingress: caddy
    domains:
      - bet-analyzer.wh-company.de
    monitor: { path: /healthz }
    port: 8198
    bind: 10.0.0.2
    # Kein caddy:-Block nötig: Caddy setzt X-Forwarded-* selbst, keine großen Uploads/Streams
    # (die Betano/Bwin-API nimmt höchstens einige MB JSON an; Caddy auf dem VPS begrenzt das nicht).
    data:
      - Named Volume bet-analyzer_database_data (MySQL 8.4, ≈ 1,3 GB)
      - Named Volume bet-analyzer_provider_files (JSON-Rohdaten der Buchmacher, nicht offsite)
      - /srv/bet-analyzer/backups
    backup:
      time: "00:30"
      what: DB
      target: /srv/bet-analyzer/backups
      active: true
      notes: >-
        Komodo-Procedure bet-analyzer-backup, täglich 00:30; dazu ein Dump vor jedem Deploy. Je Anlass bleiben 7
        (gzip --rsyncable für restic).
    notes: >-
      Von securitas umgezogen (/var/www/bet-analyzer, MySQL 8.0 → 8.4; 12,6 GB Sportmonks-Altdaten nur archiviert). Worker
      (messenger:consume) und Cron (supercronic, docker/cron/crontab) als eigene Dienste. Tipico-Einlesen war
      11/2025–11/2026 kaputt (API-Umstellung), mit dem Umzug repariert.
```

Während des Umzugs (Stack läuft schon, DNS noch auf securitas) passt stattdessen `status: migrating`,
`legacy_host: securitas`, `ingress: legacy`. Ein `caddy:`-Block ist nicht nötig; `backup.offsite_paths` entfällt (es
gibt keine Medien außerhalb des Dumps).

## Betrieb

Alle Befehle im Stack-Ordner `/etc/komodo/stacks/bet-analyzer` als root, mit
`dc() { docker compose -p bet-analyzer -f compose.prod.yaml --env-file .env.prod.local "$@"; }`.

### Deploy

Komodo → Stack → **Deploy**. Ablauf: `git pull` → `.env.prod.local` schreiben → Pre-Deploy (prüft Platzhalter,
Projekt/Volume-Präfix, zieht `…-pre-deploy.sql.gz`) → Build → `up -d --wait` (alle gesund, sonst Fehlschlag) →
Post-Deploy (Status, `/healthz`, Crontab, alte Images **dieses** Projekts löschen).

### Rollback

1. In `deploy/komodo/bet-analyzer.toml` bzw. im Komodo-UI `commit` auf den letzten guten Hash setzen → Deploy.
2. Hat eine Migration das Schema kaputt gemacht: Stack stoppen (bis auf `database`), letzten
   `…-pre-deploy.sql.gz` zurückspielen (siehe Restore), dann Deploy des alten Commits.

### Backup und Restore

- Täglich 00:30 Procedure `bet-analyzer-backup` (`RunStackService` → `backup daily`, `no_deps`: läuft der Stack
  nicht, scheitert der Dump absichtlich und Komodo alarmiert).
- Vor jedem Deploy `backup pre-deploy`. Von Hand: `dc run --rm --no-deps backup manuell`.
- Dateien: `/srv/bet-analyzer/backups/bet-analyzer-db-<UTC-zeit>-<anlass>.sql.gz`, Rechte 600, je Anlass 7.

Restore aus einem lokalen Dump:

```sh
dc stop web app worker cron
gunzip -c /srv/bet-analyzer/backups/bet-analyzer-db-<…>.sql.gz \
  | dc exec -T database sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD"'
dc up -d --wait
```

(Die Dumps aus `db-backup.sh` enthalten `CREATE DATABASE … / USE` – kein Datenbankname nötig.)

Restore aus der Offsite-Sicherung (restic → Backblaze B2): siehe homelab `docs/offsite.md`, Abschnitt
„Wiederherstellen“ – Pfad `/srv/bet-analyzer/backups` zurückholen, dann wie oben.

### Fehlersuche

| Symptom                                                            | Nachsehen                                                                                                                                                                                                                       |
|--------------------------------------------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Deploy bricht im Pre-Deploy ab: „In Komodo fehlen diese Variablen“ | Secret in Komodo anlegen/benennen, Tippfehler im `.toml`                                                                                                                                                                        |
| Deploy scheitert an `--wait`                                       | `dc ps`, `dc logs migrate`, `dc logs app`                                                                                                                                                                                       |
| Dashboard: gelbe Warnung „No matches for today“                    | `dc logs cron` um 00:10; `dc exec -T cron php bin/console CollectDailyMatchesCommand -vvv` – meist hat Tipico die API geändert (`src/Service/Tipico/Api/TipicoApiGateway.php`, Test `tests/TipicoDailyMatchesResponseTest.php`) |
| Simulatoren rechnen nicht                                          | `dc logs worker`; Warteschlange: `dc exec -T app php bin/console messenger:stats`; Fehler: `messenger:failed:show`                                                                                                              |
| `/healthz` 503                                                     | Datenbank: `dc logs database`, Speicher (`deploy.resources.limits.memory: 3g`)                                                                                                                                                  |
| Telegram schweigt                                                  | `TELEGRAM_ENABLED`, `BET_ANALYZER_TELEGRAM_DSN`, `dc exec -T app php bin/console TestTelegram`                                                                                                                                  |
| Backup-Procedure rot                                               | Stack läuft? `/srv/bet-analyzer/backups` existiert und ist root:700? Platz frei?                                                                                                                                                |

### Probelauf auf dem Entwicklungsrechner

```sh
cp .env.prod.dist /tmp/bet-analyzer-probe.env   # Werte ausfüllen, HTTP_BIND=127.0.0.1, VOLUME_PREFIX=betanalyzerprobe
install -d -m 700 /tmp/bet-analyzer-probe-backups   # als BACKUP_DIR eintragen
docker compose -p bet-analyzer-probe -f compose.prod.yaml --env-file /tmp/bet-analyzer-probe.env up -d --build --wait
COMPOSE_PROJECT=bet-analyzer-probe ENV_FILE=/tmp/bet-analyzer-probe.env sh deploy/komodo/pre-deploy.sh
docker compose -p bet-analyzer-probe -f compose.prod.yaml --env-file /tmp/bet-analyzer-probe.env down -v
```

Ohne eigenes `VOLUME_PREFIX` bricht `pre-deploy.sh` ab – so landet ein Probelauf nie in echten Volumes.
Realistischer im Periphery-Image (Repo und Env-Datei unter demselben Pfad einhängen):

```sh
docker run --rm -v /var/run/docker.sock:/var/run/docker.sock -v "$PWD:$PWD" -v /tmp:/tmp -w "$PWD" \
  -e COMPOSE_PROJECT=bet-analyzer-probe -e ENV_FILE=/tmp/bet-analyzer-probe.env \
  --entrypoint sh ghcr.io/moghtech/komodo-periphery:2 deploy/komodo/pre-deploy.sh
```
