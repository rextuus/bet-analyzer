#!/bin/sh
# bet-analyzer – Komodo „Pre Deploy“: Datenbank sichern, bevor neue Images gebaut und gestartet werden.
# Läuft im Periphery-Container im Stack-Ordner (/etc/komodo/stacks/bet-analyzer), nach `git pull` und nachdem
# Komodo .env.prod.local geschrieben hat. Braucht nur sh und die docker-CLI. Exit ≠ 0 bricht den Deploy ab.
#
# In Komodo: pre_deploy.command = "sh deploy/komodo/pre-deploy.sh"
# Von Hand im selben Ordner genauso aufrufbar.
set -eu

project=${COMPOSE_PROJECT:-bet-analyzer}
env_file=${ENV_FILE:-.env.prod.local}
dc() { docker compose -p "$project" -f compose.prod.yaml --env-file "$env_file" "$@"; }

test -f "$env_file" || { echo "${env_file} fehlt – in Komodo Env File Path und Environment prüfen." >&2; exit 1; }
# Komodo schreibt die Datei als root mit Rechten 600; von Hand also mit sudo aufrufen.
test -r "$env_file" || { echo "${env_file} ist nicht lesbar – als root (sudo) aufrufen." >&2; exit 1; }

# Projekt und Volumes müssen zusammenpassen: Produktion = Projekt bet-analyzer ohne VOLUME_PREFIX, Probelauf =
# eigenes Projekt mit eigenem VOLUME_PREFIX. Sonst landet ein Probelauf in echten Daten.
if grep -Eq '^[[:space:]]*VOLUME_PREFIX[[:space:]]*=[[:space:]]*[^[:space:]]' "$env_file"; then prefixed=yes; else prefixed=no; fi
case "${project}:${prefixed}" in
  bet-analyzer:yes) echo "VOLUME_PREFIX ist gesetzt (Probelauf), das Projekt heißt aber bet-analyzer – COMPOSE_PROJECT setzen." >&2; exit 1 ;;
  bet-analyzer:no|*:yes) ;;
  *) echo "Projekt ${project} ohne VOLUME_PREFIX würde die echten bet-analyzer_*-Volumes benutzen – VOLUME_PREFIX setzen." >&2; exit 1 ;;
esac

# Komodo lässt einen Platzhalter in doppelten eckigen Klammern stehen, wenn es die Variable bzw. das Secret
# nicht gibt. Dann lieber abbrechen, als mit dem wörtlichen Platzhalter als Passwort zu starten.
if missing=$(grep -o '\[\[[A-Za-z0-9_]*\]\]' "$env_file"); then
  echo "In Komodo fehlen diese Variablen/Secrets (Settings → Variables):" >&2
  echo "$missing" | sort -u | sed 's/^/  /' >&2
  exit 1
fi

# Läuft die Datenbank, den Dump mit --no-deps gegen genau diesen Container ziehen. Ohne --no-deps würde Compose
# eine im neuen Commit geänderte database (z. B. neues MySQL-Image) schon VOR der Sicherung neu erstellen.
deps=--no-deps
# Allererster Deploy auf einem leeren Rechner: noch keine Datenbank, nichts zu sichern.
if [ -z "$(dc ps -q --status running database 2>/dev/null)" ]; then
  # Volume-Name so, wie compose.prod.yaml ihn festlegt (bet-analyzer_database_data bzw. mit VOLUME_PREFIX).
  # Erst die ganze Konfiguration holen: Scheitert `compose config`, bricht set -e hier ab, statt die Datenbank
  # fälschlich für nicht vorhanden zu halten.
  config=$(dc config --format json)
  volume=$(printf '%s' "$config" | grep -o '"[^"]*_database_data"' | head -n 1 | tr -d '"')
  if [ -n "$volume" ] && docker volume inspect "$volume" >/dev/null 2>&1; then
    echo "Datenbank-Volume vorhanden, aber Container läuft nicht – wird für die Sicherung gestartet."
    deps=
  else
    echo "Noch keine Datenbank vorhanden – Sicherung übersprungen."
    exit 0
  fi
fi

# shellcheck disable=SC2086  # $deps ist leer oder genau ein Schalter
dc run --rm --no-TTY $deps backup pre-deploy
