# terms4FAIRskillsAnnotator
Tool for annotating materials with terms4FAIRskills ontology.

## Project Structure 
### db
The `db` directory holds all the files required for the terms4fairskills_annotator_db container. 

### scripts
The `scripts` directory stores all the shell scripts that are called by the `annotator.sh` to carry out different operations.

### web
The `web` directory stores the files that are copied into the terms4fairskillsannotator_web_data volume.

## annotator.sh
annotator.sh is the script to interact with the container's and their volumes.

## JavaScript Debug Logging
By default only the minimum is logged to the console by the frontend JavaScript. To enable more logging for debugging, add `?debug` to the URL. For example `localhost:8000` becomes `localhost:8000/?debug`.

## Acknowlegements
 - Addtional Composer Packages
    - Parsedown: https://parsedown.org