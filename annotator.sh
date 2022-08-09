# Shell Script for managing the terms4FAIRskills annotator from the host machine

# Check for the first argument
if [ -z "$1" ]; then
    echo "No command supplied"
    sh scripts/help.sh
    exit 1;
fi

# A first argument was provided - swtich depending on value
if [ $1 = "install" ]; then
    sh scripts/install.sh

elif [ $1 = "update" ]; then
    sh scripts/update.sh

elif [ $1 = "uninstall" ]; then
    sh scripts/uninstall.sh

elif [ $1 = "docs" ]; then
    sh scripts/docs.sh

elif [ $1 = "test" ]; then
    sh scripts/test.sh

elif [ $1 = "help" ]; then
    sh scripts/help.sh

else
    # An argument was prrovided but it wasn't valid
    echo "No valid argument provided..."
    sh scripts/help.sh
fi