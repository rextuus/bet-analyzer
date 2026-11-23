#!/bin/sh
# bet-analyzer – Datenbank-Dump mit Rotation. Läuft im Dienst `backup` aus compose.prod.yaml (Image mysql:8.4.13),
# nie direkt auf dem Host:
#   docker compose -p bet-analyzer -f compose.prod.yaml --env-file .env.prod.local run --rm backup [anlass]
# Schreibt /backups/bet-analyzer-db-<UTC-zeit>-<anlass>.sql.gz. Anlass (Standard „manuell“): daily (Komodo-Procedure
# bet-analyzer-backup), pre-deploy (vor jedem Deploy) oder frei. Je Anlass bleiben die neuesten BACKUP_KEEP (7).
# Ziel /backups = auf dem Host BACKUP_DIR (Standard /srv/bet-analyzer/backups). Bricht bei jedem Fehler mit
# Exit-Code ≠ 0 ab, ein halber Dump bleibt nie liegen.
#
# Anders als bei den kleinen Projekten wird direkt komprimiert geschrieben: Unkomprimiert wäre der Dump über
# 10 GB groß. --rsyncable hält die gzip-Blöcke stabil, damit restic (Offsite) unveränderte Tabellen erkennt.
set -eu
# Dumps enthalten Passwort-Hashes: nur für root lesbar.
umask 077

reason=$(printf '%s' "${1:-manuell}" | tr -c 'A-Za-z0-9-' '-')
keep=${BACKUP_KEEP:-7}
dir=/backups
name="bet-analyzer-db-$(date -u +%Y%m%dT%H%M%SZ)-${reason}.sql.gz"
tmp="${dir}/.${name}.partial"
rc_file="${dir}/.${name}.rc"

case "$keep" in
  ''|*[!0-9]*|0) echo "Anzahl zu behaltender Dumps muss eine Zahl ≥ 1 sein (ist: '${keep}')." >&2; exit 2 ;;
esac

# Täglich läuft dieses Skript ohne pre-deploy.sh (RunStackService) – also hier selbst prüfen, ob Komodo das
# Secret eingesetzt hat (eine unbekannte Variable bleibt wörtlich in doppelten eckigen Klammern stehen).
case "${MYSQL_PWD:-}" in
  ''|*'[['*']]'*) echo "MYSQL_ROOT_PASSWORD fehlt oder ist ein unaufgelöster Platzhalter – Secret in Komodo prüfen." >&2; exit 2 ;;
esac

trap 'rm -f "$tmp" "$rc_file"' EXIT

test -w "$dir" || { echo "Sicherungsordner ${dir} ist nicht beschreibbar." >&2; exit 1; }
chmod 700 "$dir"

# Passwort kommt über MYSQL_PWD (nicht in der Prozessliste). sh kennt kein pipefail: Den Exit-Code von
# mysqldump darum in eine Datei schreiben und danach prüfen.
{
  rc=0
  mysqldump -h database -u root \
    --single-transaction --quick --routines --triggers --events \
    --hex-blob --default-character-set=utf8mb4 \
    --set-gtid-purged=OFF --no-tablespaces \
    --databases "${MYSQL_DATABASE:?MYSQL_DATABASE fehlt}" || rc=$?
  echo "$rc" > "$rc_file"
} | gzip -6 --rsyncable > "$tmp"

[ "$(cat "$rc_file")" = 0 ] || { echo "mysqldump ist fehlgeschlagen (Exit $(cat "$rc_file"))." >&2; exit 1; }
gzip -t "$tmp"
# mysqldump schreibt am Ende „-- Dump completed“; fehlt die Zeile, ist der Dump unvollständig.
gzip -dc "$tmp" | tail -n 1 | grep -q '^-- Dump completed' || { echo "Dump unvollständig (Abschlusszeile fehlt)." >&2; exit 1; }
gzip -dc "$tmp" | grep -q -m 1 '^CREATE TABLE' || { echo "Dump enthält keine Tabellen." >&2; exit 1; }

sync "$tmp"
mv "$tmp" "${dir}/${name}"
sync "$dir"
rm -f "$rc_file"
trap - EXIT

echo "Gesichert: ${name} ($(du -h "${dir}/${name}" | cut -f1))"

# Rotation je Anlass, damit viele Deploys nicht die täglichen Dumps verdrängen.
# Der Zeitstempel im Namen sortiert chronologisch: neueste zuerst, alles ab Nummer keep+1 löschen.
printf '%s\n' "$dir"/bet-analyzer-db-*-"$reason".sql.gz | sort -r | tail -n +"$((keep + 1))" | while IFS= read -r old; do
  rm -f -- "$old"
  echo "Entfernt (Rotation): $(basename "$old")"
done
