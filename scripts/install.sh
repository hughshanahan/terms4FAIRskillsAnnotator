echo "Starting Annotator..."

# Build the docker images
docker build ./web

# Start the containers
docker compose up -d

echo "Annotator started!"