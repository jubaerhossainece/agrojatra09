#!/bin/bash
set -e

GIT_SHA=$(git rev-parse HEAD)

echo "Deploying commit: $GIT_SHA"

GIT_SHA=$GIT_SHA docker compose \
  -f docker/docker-compose.yml \
  --env-file .env \
  up --build -d

echo "Done. App running at http://localhost:8080"
