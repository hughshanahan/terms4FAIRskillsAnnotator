
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

# Clearing the web_data volume
echotitle "Clearing terms4fairskills_web_data Volume"
if docker volume ls --filter "name=terms4fairskillsannotator_web_data" --quiet | grep -q 'terms4fairskillsannotator_web_data'; then
    docker volume rm terms4fairskillsannotator_web_data --force
else
    echo "terms4fairskills_web_data Volume doesn't exist - nothing to clear"
fi

# Build the docker image
echotitle "Building Docker Image"
docker build --no-cache .

# Start the docker container
echotitle "Starting Docker Containers"
docker compose up -d

# Report that the installation was completed
echotitle "Installation complete"