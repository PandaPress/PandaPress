.PHONY: d-up d-stop

d-up:
	docker compose up -d

d-stop:
	docker compose stop
