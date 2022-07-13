docker build -f ./.Dockerfile -t affiliatets .
docker-compose -p affiliatets -f ./docker-compose.yml build
