#!/usr/bin/env bash
set -euo pipefail
umask 077
: "${BACKUP_DIR:?Set BACKUP_DIR to an encrypted backup volume}"
mkdir -p "$BACKUP_DIR"
backup_file="$BACKUP_DIR/datum-$(date -u +%Y%m%dT%H%M%SZ).sql.gz"
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysqldump -uroot --single-transaction --routines --triggers --no-tablespaces datum' | gzip > "$backup_file"
gzip -t "$backup_file"
printf '%s\n' "$backup_file"
