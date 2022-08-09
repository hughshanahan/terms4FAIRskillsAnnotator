# Generates the documentation for the terms4FAIRskills annotator

echotitle() {
    echo "";
    echo " ===== $1 ===== ";
    echo "";
}

echo "+----------------------------------------------------+";
echo "| terms4FAIRskills Annotator Documentation Generator |";
echo "+----------------------------------------------------+";

# Generate PHP Documentation for the source
echotitle "Generating docs for the backend source"
phpDocumentor -d /var/www/src -t /var/www/docs/backend/src
echo "Generated docs for the backend source"

# Generate PHP Documentation for the tests
echotitle "Generating docs for the backend tests"
phpDocumentor -d /var/www/tests -t /var/www/docs/backend/tests
echo "Generated docs for the backend tests"

# Generate JS Documentation for the frontend
echotitle "Generating docs for the frontend JavaScript"
# Install jsdoc as it may not be installed
npm install -g jsdoc
# Generate the docs
jsdoc /var/www/public/scripts --destination /var/www/docs/frontend -r
echo "Generated docs for the frontend JavaScript"
