test:
	./vendor/bin/phpunit ./src

install:
	composer install

javabridge-setup:
	cd Resources/scripts && mvn dependency:copy-dependencies -DoutputDirectory=libs -Dhttps.protocols=TLSv1.2 && \
	cd libs && wget https://sourceforge.net/projects/php-java-bridge/files/Binary%20package/php-java-bridge_7.2.1/exploded/JavaBridge.jar/download -O javabridge-7.2.1.jar

javabridge-start:
	cd Resources/scripts && ./php-java-bridge.sh start

javabridge-stop:
	cd Resources/scripts && ./php-java-bridge.sh stop



