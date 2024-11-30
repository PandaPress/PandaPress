.PHONY: d-up d-stop d-clean d-setup

d-setup:
	@chmod +x scripts/setup.sh
	@./scripts/setup.sh

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
	docker compose down -v --rmi all
	rm -rf compose.yml nginx/default.conf nginx/ssl.conf

