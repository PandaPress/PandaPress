.PHONY: build watch

build:
	npm run css:layout
	npm run js:build
	mkdir -p ../../public/assets/papermod
	cp -r ./css ../../public/assets/papermod
	mv -r ./js/index.min.js ../../public/assets/papermod/js/index.min.js

watch:
	npm run css:watch
