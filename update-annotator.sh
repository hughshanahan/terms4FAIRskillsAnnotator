# Updates the contents of the volumes

echotitle() {
    echo "";
    echo " ===== $1 ===== ";
    echo "";
}


# Set variable defaults
DEPENDENCIES=0;
SKIPDOCS=0;

# get the flags that were passed
while getopts 'cd' OPTION; do
    case "$OPTION" in
        c)
            # install composer dependencies
            DEPENDENCIES=1;
            ;;
        d)
            # skip documentation updates
            SKIPDOCS=1;
            ;;
        ?)
            echo "script usage: update-annotator.sh [-c] [-d]" >&2
            exit 1
        ;;
    esac
done
shift "$(($OPTIND -1))"

# print the title
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

# Install Composer dependencies
if [ $DEPENDENCIES -eq 1 ]; then
    # if the composer dependencies should be installed/updated
    echotitle "Installing Composer dependencies"
    docker exec terms4FAIRskills_annotator_web composer install
    echo "Installed composer dependencies"
fi

# Update the documentation
echotitle "Updating documentation"
if [ $SKIPDOCS -eq 1 ]; then
    # if the documentation should be skipped
    echo "Skipping documentation updates"
else
    # If the documentation should be updated
    docker exec terms4FAIRskills_annotator_web sh generate-documentation.sh
    echo "Updated documentation"
fi

# Copy the new web container files to host
echotitle "Copying the container contents"
docker cp $CONTAINERID:/var/www/. ./
echo "Updated Host Contents"
