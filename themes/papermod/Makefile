.PHONY: build watch

build:
	npm run css:layout
	npm run js:build
	mkdir -p ../../public/assets/papermod
	cp -r ./css ../../public/assets/papermod
	cp -r ./js ../../public/assets/papermod

watch:
	npm run css:watch
