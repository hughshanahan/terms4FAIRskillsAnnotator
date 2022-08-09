# Uninstalls the annotator
# Stops and clears the containers and then deletes the volumes

echo "Stopping annotator..."

docker compose down

docker volume rm terms4fairskillsannotator_mysql_database
docker volume rm terms4fairskillsannotator_mysql_data
docker volume rm terms4fairskillsannotator_web_data

echo "Annotator uninstalled"