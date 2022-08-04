
echotitle() {
    echo ""
    echo " ===== $1 ===== "
    echo ""
}

echo "+--------------------------------------+"
echo "| terms4FAIRskills Annotator Installer |"
echo "+--------------------------------------+"

# Clearing previous containers
echotitle "Clearing previous containers"
docker compose down

# Build the docker image
echotitle "Building Docker Image"
docker build --no-cache .

# Start the docker container
echotitle "Starting Docker Containers"
docker compose up -d

# Report that the installation was completed
echotitle "Installation complete"