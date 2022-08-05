# Updates the contents of the volumes

echotitle() {
    echo "";
    echo " ===== $1 ===== ";
    echo "";
}

echo "+------------------------------------+";
echo "| terms4FAIRskills Annotator Updater |";
echo "+------------------------------------+";

# Get the container ID for the web container
echotitle "Getting Container"
CONTAINERID=$(docker ps --filter "name=terms4FAIRskills_annotator_web" --quiet | head -n 1)
echo "The container ID is $CONTAINERID"

# Copy the host files to the web container
echotitle "Copying the project to the container"
docker cp ./ $CONTAINERID:/var/www/
echo "Updated Container Contents"

# Update the documentation
echotitle "Updating documentation"
docker exec terms4FAIRskills_annotator_web sh generate-documentation.sh