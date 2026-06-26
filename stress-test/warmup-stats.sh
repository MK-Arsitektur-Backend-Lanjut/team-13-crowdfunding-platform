#!/usr/bin/env sh
set -eu

TARGET="${1:-http://localhost:8000/api/donations/stats}"
WARMUP_REQUESTS="${2:-100}"

echo "Warming up ${TARGET} (${WARMUP_REQUESTS} requests)..."

i=1
while [ "$i" -le "$WARMUP_REQUESTS" ]; do
  curl -s -o /dev/null -w "warmup ${i}: %{http_code} %{time_total}s\n" "$TARGET"
  i=$((i + 1))
done

echo "Warmup complete."
