# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.

## Host System Scripts
Shell Scripts that are run from the terminal of the host.

### install-annotator.sh
To install the annotator run `sh install-annotator.sh`.

Optional Flags:
- `-c`: Builds the Docker image using the cache
- `-d`: Clears the database volumes and rebuilds them
- `-f`: Runs the containers in the foreground (opposite of `-d` in `docker compose up`)

### update-annotator.sh
When a change has been made, it needs to be updated in the Docker Volume, this is done using the `sh update-annotator.sh` script.

Optional Flags:
- `-c`: Updates the Composer dependencies using `composer install`
- `-d`: Skips the generation of documentation using `jsdoc` and `phpDocumentor`

## Web Container Scripts
Shell Scripts that are run from the shell of the terms4fairskills_annotator_web container.

### generate-documentation.sh
This script generates the documentation for the web server, it uses `phpDocumentor` and `jsdoc` for the backend and frontend respectively. 

## JavaScript Debug Logging
By default only the minimum is logged to the console by the frontend JavaScript. To enable more logging for debugging, add `?debug` to the URL. For example `localhost:8000` becomes `localhost:8000/?debug`.

## Acknowlegements
 - Addtional Composer Packages
    - Parsedown: https://parsedown.org