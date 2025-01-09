.PHONY: build watch

build:
	npm run css:build
	npm run js:build
	mkdir -p ../../public/assets/papermod
	cp -r ./assets/* ../../public/assets/papermod

watch:
	npm run css:watch
