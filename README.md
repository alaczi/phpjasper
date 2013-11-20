PHP - JasperReports intergation with JavaBridge
====
This component is to run JasperReports reports in PHP with using JavaBridge.

##Why?

JasperReports is a great tool to generate reports with different output formats (for example: PDF, DOC, XLS),
and it comes with an wysiwyg editor, iReports.

##Requirements:
To get this work, u have to install JavaBridge and add required .jars from JasperReports.

JavaBridge: http://php-java-bridge.sourceforge.net/pjb/
JasperReports: http://community.jaspersoft.com/project/jasperreports-library
iReport: http://community.jaspersoft.com/project/ireport-designer

There is a script in the Resources/script dir to easily start the standalone JavaBridge server with the jar files included in the classpath

##Install:

Clone the project:
```BASH
    git clone https://github.com/polarbearhandler/phpjasper.git
```

Using composer
```YML
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/polarbearhandler/phpjasper"
        }
    ],
    "require": {
        "polarbear/phpjasper": "dev-master",
    }
}
```
For JavaBridge include u should set allow_url_include to true

##Limitations

Currently the JavaBridge should run on the same machine.
It's recommended to run the JavaBridge with the same user as your webserver (www-data)

This library was tested with JasperReports 3.0.0 (upgrade preferable)

##Test and usage

Just run the tests
Check the test suite for an example how to pass datasource/parameters to the report

```BASH
    phpunit src/
```
