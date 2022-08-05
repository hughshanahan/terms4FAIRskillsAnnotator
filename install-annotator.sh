# Installs the terms4FAIRskills Annotator

echotitle() {
    echo "";
    echo " ===== $1 ===== ";
    echo "";
}

# Set variable defaults
USECACHE=0;
FOREGROUND=0;
CLEARDATABASE=0;

# get the flags that were passed
while getopts 'cdf' OPTION; do
    case "$OPTION" in
        c)
            # Building the Docker Image using the cache
            USECACHE=1;
            ;;
        d)
            # Clearing the database volumes
            CLEARDATABASE=1;
            ;;
        f)
            # Running the containers in the foreground
            FOREGROUND=1;
            ;;
        ?)
            echo "script usage: install-annotator.sh [-c] [-d] [-f]" >&2
            exit 1
        ;;
    esac
done
shift "$(($OPTIND -1))"

# Print the title
echo "+--------------------------------------+";
echo "| terms4FAIRskills Annotator Installer |";
echo "+--------------------------------------+";

# Clearing previous containers
echotitle "Clearing previous containers";
docker compose down;

# Clearing the web_data volume
echotitle "Clearing terms4fairskills_web_data Volume"
if docker volume ls --filter "name=terms4fairskillsannotator_web_data" --quiet | grep -q 'terms4fairskillsannotator_web_data'; then
    docker volume rm terms4fairskillsannotator_web_data --force;
    echo "Cleared the web_data volume";
else
    echo "terms4fairskills_web_data Volume doesn't exist - nothing to clear";
fi

# Clearing the mysql_database and _mysql_data volumes
if [ $CLEARDATABASE -eq 1 ]; then
    # if the database volumes should also be cleared
    echotitle "Clearing the database volumes";
    docker volume rm terms4fairskillsannotator_mysql_data --force;
    docker volume rm terms4fairskillsannotator_mysql_database --force;
    echo "Cleared the database volumes"
fi


# Build the docker image
if [ $USECACHE -eq 1 ]; then
    # if the install command does not have the no cache flag
    echotitle "Building Docker Image (using cache)";
    docker build .;
else 
    # if the install command has the --no-cache flag
    echotitle "Building Docker Image";
    docker build --no-cache .;
fi

# Start the docker containers
if [ $FOREGROUND -eq 1 ]; then
    # if the containers should be run in the foreground
    echotitle "Starting Docker Containers (in the foreground)";
    docker compose up;
else 
    # if the containers should be run in the background
    echotitle "Starting Docker Containers";
    docker compose up -d;
fi

# Update the annotator contents
sh update-annotator.sh

# Report that the installation was completed
echotitle "Installation complete";