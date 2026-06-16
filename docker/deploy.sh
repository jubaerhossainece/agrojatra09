#!/bin/bash
set -e

# Resolve paths relative to this script's location (docker/)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
REPO_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

# Pull latest commits from GitHub so the local SHA matches what will be cloned
# inside the Docker build. Without this, a stale local SHA means Docker reuses
# its cached git clone layer and never picks up the new code.
echo "Pulling latest from GitHub..."
git -C "$REPO_DIR" pull

GIT_SHA=$(git -C "$REPO_DIR" rev-parse HEAD 2>/dev/null || echo "unknown")

echo "Deploying commit: $GIT_SHA"

GIT_SHA=$GIT_SHA docker compose \
  -f "$SCRIPT_DIR/docker-compose.yml" \
  --env-file "$REPO_DIR/.env" \
  up --build -d

echo "Done. App running at http://localhost:8080"
