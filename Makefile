.PHONY: d-init d-up d-stop d-clean d-nginx-reload g-pull

d-init:
	./scripts/init.sh

d-up:
	@if [ ! -f compose.yml ]; then \
		echo "\033[31mERROR: compose.yml not found. Run 'make d-setup' first.\033[0m"; \
		exit 1; \
	fi
	docker compose up -d

d-stop:
	@if [ ! -f compose.yml ]; then \
		echo "\033[31mERROR: compose.yml not found. Run 'make d-setup' first.\033[0m"; \
		exit 1; \
	fi
	docker compose stop

d-clean:
	@if [ ! -f compose.yml ]; then \
		echo "\033[31mERROR: compose.yml not found. Nothing to clean.\033[0m"; \
		exit 1; \
	fi
	@if docker info > /dev/null 2>&1; then \
		echo "Docker is running, cleaning up containers and images and files..."; \
		docker compose down -v --rmi all; \
	else \
		echo "Docker is not running, cleaning up files only..."; \
	fi
	rm -rf compose.yml nginx/default.conf

d-nginx-reload:
	@if [ ! -f compose.yml ]; then \
		echo "\033[31mERROR: compose.yml not found. Run 'make d-setup' first.\033[0m"; \
		exit 1; \
	fi
	@if ! docker compose ps --services --filter "status=running" | grep -q "server"; then \
		echo "\033[31mERROR: Nginx container is not running.\033[0m"; \
		exit 1; \
	fi
	@echo "Reloading Nginx configuration..."
	docker compose exec server nginx -s reload




g-pull:
	git pull origin main

