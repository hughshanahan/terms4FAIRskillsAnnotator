echo "Starting Annotator..."

# Build the docker images
docker build ./web --no-cache

# Start the containers
docker compose up -d

echo "Annotator started!"