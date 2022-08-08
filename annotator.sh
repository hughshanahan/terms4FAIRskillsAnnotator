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
elif [ $1 = "help" ]; then
    sh scripts/help.sh
else
    # An argument was prrovided but it wasn't valid
    echo "No valid argument provided..."
fi