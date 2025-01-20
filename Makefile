.PHONY: g-update d-init d-up d-stop d-clean d-reload


g-update:
	./scripts/g-update.sh

d-init:
	./scripts/init.sh

d-up:
	./scripts/d-up.sh

d-stop:
	./scripts/d-stop.sh

d-clean:
	./scripts/d-clean.sh

d-reload:
	./scripts/d-reload.sh



