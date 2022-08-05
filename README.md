# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.

## Installation
To install the annotator run `sh install-annotator.sh`.

Optional Flags:
- `-c`: Builds the Docker image without using the cache
- `-d`: Clears the database volumes and rebuilds them
- `-f`: Runs the containers in the foreground (opposite of `-d` in `docker compose up`)

## JavaScript Debug Logging
The JavaScript conole output can be turned on by adding the `debug` parameter to the URL query string.


## Acknowlegements
 - Addtional Composer Packages
    - Parsedown: https://parsedown.org