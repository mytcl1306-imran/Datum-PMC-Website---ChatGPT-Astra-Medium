#!/usr/bin/env bash
set -euo pipefail
: "${RESTORE_FILE:?Set RESTORE_FILE to a verified SQL gzip backup}"
: "${CONFIRM_RESTORE:?Set CONFIRM_RESTORE=RESTORE into an isolated recovery environment}"
[ "$CONFIRM_RESTORE" = RESTORE ] || exit 1
gzip -t "$RESTORE_FILE"
gzip -dc "$RESTORE_FILE" | docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_ROOT_PASSWORD" exec mysql -uroot datum'
