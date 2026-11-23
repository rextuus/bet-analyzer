# Offene Fragen und Annahmen (20.11.2026)

Gesammelt während der Komodo-Umstellung. Zu jeder Frage steht, was ich ohne Antwort angenommen bzw. umgesetzt
habe – nichts davon ist unumkehrbar.

## Dringend

1. ✅ *23.11.: Apache sperrt `/_profiler`, `/_wdt` und `/backups` (403). `APP_ENV=prod` und das Rotieren des DB-Passworts
   entfallen – securitas wird abgelöst, abyss bekommt ein neues Passwort. Telegram-Token bleibt vorerst (optional später
   per BotFather erneuern).*
   **securitas läuft im Debug-Modus** – `/_profiler/` ist öffentlich (geprüft: HTTP 200) und zeigt
   Umgebungsvariablen. Bitte sofort `APP_ENV=prod` setzen (Befehle in `docs/deployment.md`, Schritt 0) und
   danach rotieren: Telegram-Bot-Token (BotFather), MySQL-Passwort von `bet_analyzer` auf securitas.
2. **Sportmonks-Keys** stehen in der Git-History (`SportsmonkApiGateway.php`, zwei Keys). Der Code ist entfernt,
   die Keys sollten beim Anbieter widerrufen werden. Ist das GitHub-Repo öffentlich? (Konnte ich nicht prüfen,
   `gh` fehlt.) Falls ja, auch `docker-compose.yml` (festes DB-Passwort im Klartext) und `tipico-api.txt` bedenken.

## Zum Umzug

3. ✅ *23.11.: bestätigt – Projekt läuft nur auf securitas.*
   **Steckbrief vs. Wirklichkeit**: Laut Steckbrief läuft das Projekt „auf abyss in Docker“, tatsächlich auf
   **securitas** nativ (homelab `docs/securitas.md`, Live-Seite zeigt `/var/www/bet-analyzer`). Der Umzugsplan
   geht von securitas aus. Gibt es doch eine Instanz auf abyss?
4. ✅ *23.11.: 43 Nachrichten, alle vom 22./23.11. – ziehen mit um, Worker arbeitet sie ab.*
   **Messenger-Rückstau**: Auf securitas läuft kein Worker für bet-analyzer. Wie viele Einträge hat
   `messenger_messages`? Annahme: Der neue Worker darf alles abarbeiten; uralte Einträge und solche der
   entfernten Sportmonks-Klassen landen als Fehler in `failed`. Alternative: vor dem Dump leeren.
5. ✅ *23.11.: ziehen nicht mit, nur im tar-Archiv auf securitas. Volume startet leer.*
   **JSON-Rohdaten** (`public/backups/{tipico,betano,bwin}`): Sollen die alten Dateien mit umziehen? Annahme:
   optional (Inhalt steckt schon in der DB), neues Volume `bet-analyzer_provider_files`, **nicht offsite**.
   Ab jetzt kommen täglich ≈ 2 MB Tipico-JSON dazu (≈ 0,8 GB/Jahr).
6. ✅ *23.11.: erledigt sich – ohne Sportmonks-Altdaten ist die DB ≈ 1,3 GB, Dumps einige 100 MB.*
   **Backup-Zeit 00:30 und `BACKUP_KEEP=7`** statt 14: Der Dump wird mehrere GB groß, 2 × 14 Stück wären
   viel Platz und Upload nach B2. Passt das? (00:30 war frei; es liegt nach dem Einlesen um 00:03/00:10.)
7. ✅ *23.11.: ja, aktiv (1).*
   **`TELEGRAM_ENABLED`**: in der `.toml` auf 1 gesetzt (der Feed scheint gewollt). Lokal stand 0. Richtig?
8. **Freier Platz auf abyss** für ≈ 1,3 GB DB + Import-Dump + Sicherungen (≥ 15 GB) – bitte vor dem Umzug prüfen (am
   23.11. war abyss per SSH nicht erreichbar).
9. ✅ *23.11.: neu erzeugen.*
   **APP_SECRET übernehmen?** Mit dem alten Wert bleiben Remember-me-Cookies gültig; da der Wert über den
   Profiler sichtbar war, empfehle ich einen neuen (alle müssen sich einmal neu anmelden).

10a. ✅ *23.11. – neu entdeckt:* **Sportmonks-Altdaten** (`spm_odd` 12 GB u. a., 12,6 GB von 13,9 GB) kommen
nicht in die neue DB, sondern als Archiv-Dump nach securitas `/home/master/abschied` (deployment.md 2.3b).

## Zum Code

10. **Login-Pflicht für alles**: Bisher war nur `/admin` geschützt. Jetzt braucht jede Seite eine Anmeldung.
    Soll irgendetwas öffentlich lesbar bleiben (z. B. Dashboard)?
11. **Daten-API**: ohne `API_TOKEN` geschlossen. Betano und Bwin antworten auf die Skripte mit 403 – sollen die
    Einlieferer wiederbelebt werden (dann Token setzen und `X-Api-Token` in den Skripten mitschicken) oder
    kann der Betano/Bwin-Teil ganz weg?
12. **Zweitserver 34.89.148.146** (`BACKUP_SERVER_IP`, `SyncBetFromBackupServer`) ist nicht erreichbar.
    Gibt es ihn noch? Wenn ja, braucht er nun ein Token für `/api/bets` (oder umgekehrt). Annahme: weg,
    Variable leer.
13. **Head-to-Head**: Das neue Tipico-Gateway liefert keine H2H-Quoten mehr (maxMarkets erlaubt nur 1 oder 5;
    5 enthält standard, Über/Unter, beide treffen, Handicap, Halbzeit). H2H-Simulatoren bleiben damit stehen.
    Akzeptabel, oder soll ich einen zweiten Abruf je Spiel (Event-Detail-Endpunkt) für H2H einbauen?
14. **Weitere Simulator-Läufe**: In der alten Crontab waren nur `three_way_simulators` aktiv,
    `both_teams_score`, `over_under`, `head_to_head` und `top_weekday` auskommentiert. Übernommen wie gehabt –
    die Top-Simulatoren in der Liste sind aber fast alle Über/Unter-Strategien. Sollen die wieder laufen?
15. **Symfony-Update**: Darf ich `composer update "symfony/*" "doctrine/*" --with-dependencies` innerhalb der
    aktuellen Hauptversionen machen (6.4.4 → 6.4.x, viele Sicherheitsfixes)? Habe ich nicht getan, weil es
    `composer.lock` breit ändert.
16. **Aufräumen**: Dürfen `AdministrationController` (Modal „Hallo“ ohne Wirkung), `favorite/test`,
    `TestCommand`, `TestCalculationCommand` (nicht eingecheckt), `MoveBetanoBackupToNewStructureCommand` +
    `src/Entity/BetanoBackup.php` (Entity nicht gemappt) und die AssetMapper-Konfiguration weg?
17. **Lokale Entwicklungsumgebung** (`docker-compose.yml`): enthält feste Passwörter und Mailcatcher, nutzt
    MySQL 8.0 statt 8.4. Nicht angefasst (laut Auftrag nicht ungefragt umbauen). Soll ich sie an Produktion
    angleichen?
18. **UI-Sprache**: Die Oberfläche ist englisch, ein paar Texte deutsch („Höchster Verlust“, „Woche Umsatz pro
    Spiel“). Einheitlich deutsch oder englisch?

## Annahmen zu Komodo

- `server = "abyss"` statt des Struct-Felds `server_id`: So steht es im Beispiel auf komo.do, und hub/Wedora
  nutzen es erfolgreich. Alle übrigen Felder gegen komodo_client 2.3.2 geprüft.
- `extra_args = ["--wait", "--wait-timeout", "600"]` – länger als bei hub (300), weil MySQL mit 14 GB länger
  zum Start braucht.
