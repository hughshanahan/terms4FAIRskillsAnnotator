# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.

## Specification
The annotator uses port `8500` and the database uses port `8501`. These ports were chosen as they are unlikely to confict with anything else running on the host and do not confict with the materials browser, if installed.

## Installation
To install the annotator, clone this repository and run `sh annotator.sh install`, see below for more details about `annotator.sh` This creates the Docker Containers and Volumes required for the project. 

When the script outputs that the annotator has started, there is a few minutes delay before the web server begins while dependencies are installed and generated files are created.


## annotator.sh
annotator.sh is the script to interact with the container's and their volumes.

 - To install the project, run `sh annotator.sh install`.
 - To update the project, run `sh annotator.sh update`.
 - To uninstall the project, run `sh annotator.sh uninstall`.
 - To generate new documentation for the source of the annotator, run `sh annotator.sh docs`.
 - To run the PHPUnit tests for the project, run `sh annotator.sh test`.
 - To see more details about the annotator.sh script, run `sh annotator.sh help`.


## Project Structure 
### db
The `db` directory holds all the files required for the terms4fairskills_annotator_db container. 

### scripts
The `scripts` directory stores all the shell scripts that are called by the `annotator.sh` to carry out different operations.

### web
The `web` directory stores the files that are copied into the terms4fairskillsannotator_web_data volume.


## JavaScript Debug Logging
By default only the minimum is logged to the console by the frontend JavaScript. 
To enable more logging for debugging, add `?debug` to the URL. 
For example `localhost:8500` becomes `localhost:8500/?debug`.


## Acknowlegements
 - Addtional Composer Packages
    - Parsedown: https://parsedown.org