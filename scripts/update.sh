# Updates the contents of the volumes

echo "+------------------------------------+";
echo "| terms4FAIRskills Annotator Updater |";
echo "+------------------------------------+";


echo "Updating container contents..."
CONTAINERID=$(docker ps --filter "name=terms4FAIRskills_annotator_web" --quiet | head -n 1)
docker cp ./web/. $CONTAINERID:/var/www/
echo "Updated Container Contents"

# Call the script to update the documentation
sh scripts/docs.sh