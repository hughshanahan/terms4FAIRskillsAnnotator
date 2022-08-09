# Runs the phpunit tests for the project

echo "Running tests..."
docker exec terms4FAIRskills_annotator_web sh test-annotator.sh
echo "Tests run"