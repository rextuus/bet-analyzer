# bet-analyzer – Bestandsaufnahme (20.11.2026)

Was das Projekt kann, wo es hakt und was im Zuge der Komodo-Umstellung schon verbessert wurde.

## Was es tut

Jeden Tag holt ein Cronjob alle Fußballspiele des Tages mit Quoten von Tipico (1X2, Über/Unter, beide treffen).
Stündlich werden die Ergebnisse nachgetragen. Tausende „Simulatoren“ – je eine Strategie wie „setze auf
Unentschieden, wenn die Heimquote zwischen 5,2 und 5,3 liegt, Ziel: unter 1,5 Tore, nur am Wochenende“ – setzen
virtuell 100 € und werden fortlaufend ausgewertet. Die Oberfläche zeigt Ranglisten, Verläufe, Wochentags-Statistiken
und Favoritenlisten mit konkreten Tipps für heute.

## Stärken

- **Klare fachliche Idee** mit einem gut ausgebauten Strategie-Baukasten (`SimulationProcessors`,
  zusätzliche Prozessoren wie Wochentage oder Negativserien) und Unit-Tests dafür.
- **Saubere Schichten im Tipico-Teil**: Gateway → Response-Parser → Data-Objekte → Factory → Repository/Service.
  Das hat den API-Fix auf wenige Zeilen beschränkt.
- **Asynchrone Verarbeitung** über Symfony Messenger (Simulatoren, Detailstatistiken, Dateiimporte).
- **Viel Auswertung vorhanden**: Cashbox-Verläufe, Gewinnquote je Wochentag/Quote, Negativ-Perioden,
  Favoriten mit „heute setzen“-Ansicht.

## Schwächen und Befunde

### Behoben in diesem Umbau

| Befund                                                                                                                                                                                 | Folge                                                                                        | Behebung                                                                                                                                                                                |
|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| **Tipico-Einlesen seit 27.11.2025 kaputt**: `/json/program/hourEvents/today` leitet auf ein neues Gateway um, das ohne `maxMarkets` mit 400 antwortet                                  | ein Jahr keine neuen Spiele, Simulatoren standen still – unbemerkt                           | neue URL mit `maxMarkets=5`, Parser unverändert (Format gleich); Test mit echter Antwort (`tests/TipicoDailyMatchesResponseTest.php`); Dashboard warnt, wenn heute keine Spiele da sind |
| **Produktion im Debug-Modus** (`APP_ENV=dev` auf securitas)                                                                                                                            | `/_profiler` öffentlich, inkl. Umgebungsvariablen/Secrets; PHP-Deprecations im HTML          | Container läuft mit `APP_ENV=prod`; Sofortmaßnahme für securitas in `docs/deployment.md`                                                                                                |
| **Keine Anmeldung nötig** außer für `/admin`                                                                                                                                           | jeder konnte Simulatoren duplizieren, Favoritenlisten anlegen/löschen                        | alles außer `/login`, `/healthz`, `/api/*` verlangt `ROLE_USER`                                                                                                                         |
| **Offene Daten-API**                                                                                                                                                                   | Fremde konnten Dateien auf den Server schreiben und Jobs auslösen sowie alle Spiele abziehen | Token `API_TOKEN` (Header `X-Api-Token`), ohne Token geschlossen                                                                                                                        |
| **Rohdaten öffentlich** unter `/backups/*.json`                                                                                                                                        | jede Tagesliste frei herunterladbar                                                          | Volume nur in app/worker/cron, Caddy liefert 404                                                                                                                                        |
| **Secrets im Code/Repo**: Sportmonks-Keys im Quelltext, Telegram-Token und DB-Passwort in `.env`/`docker-compose.yml`                                                                  | Missbrauch der Keys                                                                          | Sportmonks komplett entfernt; Secrets nur noch über Komodo; Keys/Token beim Anbieter widerrufen (Offene Fragen)                                                                         |
| **Keine Migrationen** – Schema existierte nur in der Prod-DB                                                                                                                           | keine reproduzierbare Neuinstallation, Schema driftet                                        | Ausgangsschema + idempotente Angleichung + Indizes als Migrationen                                                                                                                      |
| **Keine Indizes** auf `tipico_bet`/`tipico_placement` außer Fremdschlüsseln                                                                                                            | Tagesabfragen und Simulator-Statistiken scannen Millionen Zeilen                             | Indizes auf Startzeit, `finished`, `tipico_id`, (simulator, created)                                                                                                                    |
| **Kein Worker** auf securitas (supervisor kennt nur digital-frame)                                                                                                                     | Messenger-Aufträge wurden vermutlich nie oder nur gelegentlich abgearbeitet                  | eigener Dienst `worker` mit Healthcheck und Speicherlimit                                                                                                                               |
| **Sportmonks-Altcode** (Saisons, Bet-Rows, Ligen; ~120 Dateien), Entities nicht einmal gemappt                                                                                         | Ballast, Fehlerquelle, Key im Code                                                           | entfernt; `BetOn`/`OddVariant` bleiben                                                                                                                                                  |
| `SyncBetFromBackupServer` mit fest verdrahteter IP, `--update` las `--dry`                                                                                                             | Option wirkungslos, IP nicht änderbar                                                        | nutzt `BACKUP_SERVER_IP`, Option korrigiert                                                                                                                                             |
| HTTP ohne Timeout (Guzzle)                                                                                                                                                             | ein hängender Buchmacher blockiert den Cronjob ewig                                          | 30 s Timeout                                                                                                                                                                            |
| Dashboard lud die Tagesspiele zweimal                                                                                                                                                  | doppelte teure Abfrage                                                                       | einmal laden, an das Diagramm weiterreichen                                                                                                                                             |
| Strategie-Parameter per `sprintf` als rohes HTML                                                                                                                                       | XSS-anfällig (Admin-Eingaben)                                                                | Array, Escaping durch Twig                                                                                                                                                              |
| UI: schwarze Schrift auf Dunkelblau, keine Navigation, Formulare ohne Styling, „Sign up“ ins Leere, Bootstrap 5.2 vom CDN ohne JS (Menü auf Mobil tot), `/` = Symfony-Willkommensseite | kaum benutzbar                                                                               | neues dunkles Theme (Bootstrap 5.3 aus npm), Navigation mit aktivem Punkt, Kennzahl-Kacheln, Bootstrap-Formulartheme, Diagrammfarben, `/` → Dashboard, responsive                       |
| Countdown „Next bet updates“ zählte auf Minute 10, Ergebnisse kommen zur Minute 5                                                                                                      | falsche Anzeige                                                                              | auf :05                                                                                                                                                                                 |

### Offen (bewusst nicht angefasst)

- **Abhängigkeiten veraltet**: Symfony 6.4.4 (Feb. 2024; 6.4 hat inzwischen viele Sicherheits-Patches),
  Doctrine ORM 2.18, Sentry 4.14 – unter PHP 8.5 nur Deprecations, funktional getestet. Empfehlung:
  `composer update "symfony/*" --with-dependencies` innerhalb 6.4 und danach den Probelauf wiederholen; mittelfristig
  Symfony 7.4 LTS. `puppeteer` in `package.json` wird nicht gebraucht.
- **Betano und Bwin** liefern auf die Abfragen der Skripte nur noch 403 (Bot-Schutz). Die Einlieferer-Skripte
  (`sendDaily*BetsToApi.py`) laufen nirgends mehr; die Verknüpfung Tipico ↔ Betano/Bwin ist damit tot.
- **Zweitserver 34.89.148.146** ist nicht erreichbar.
- **Head-to-Head-Quoten** bietet das neue Tipico-Gateway nicht mehr an → H2H-Simulatoren bekommen keine neuen
  Spiele (ihr Cronjob war ohnehin auskommentiert).
- **Messenger-Rückstau**: Wie viele alte Aufträge in `messenger_messages` liegen, zeigt erst der Dump.
- **Mehrere Parallel-Pfade für dasselbe**: `AdministrationController::add` (Modal „Hallo“, speichert nichts)
  neben `FavoriteController::add`; `templates/favorite/test.html.twig`, `TestCommand`, `TestTelegram` – aufräumen.
- ~~**Asset-Pipeline doppelt**~~: erledigt (2026-11-23) – Webpack Encore entfernt, alles läuft über
  AssetMapper/importmap, SCSS über `symfonycasts/sass-bundle`, Bootstrap-SCSS und Font Awesome per Composer.
- **Viele Arbeitsdateien im Repo-Ordner** (große JSON-Antworten, SQL-Dumps in `sql/`, Python-Skripte) – nicht
  eingecheckt, aber verwirrend; `.dockerignore` hält sie aus dem Image.
- **Tests**: nur Prozessoren und jetzt der Tipico-Parser; keine Controller-/Funktionstests.
- **Benennungen**: Tippfehler wie `dashbaord.scss`, `FixruteVisualizationTrait`, `loose` statt `lose` – kosmetisch.
- **Datenmenge**: ≈ 14 GB, fast alles Platzierungen (`tipico_placement` + zwei Join-Tabellen, je Millionen
  Zeilen). Simulatoren ohne Chance (Kasse < Mindestgrenze) erzeugen weiter Zeilen? Ein Aufräumkonzept (alte
  Platzierungen verdichten) würde Dump, Backup und Offsite deutlich entlasten.
