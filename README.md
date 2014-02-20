# Time ago in words Twig extension

This is a Twig extension for Symfony2 Framework where you can easily convert a datetime/timestamp to a distance of time in words.

By example

	{{ user.lastLogin|ago }}

Output examples

	half a minute ago
	3 minutes ago
	about 1 hour ago
	Today at 04:14 AM
	Yersterday at 17:36 PM
	18 Feb at 3:26 PM
	22 Dec 2013

# Installation for Symfony2

1) Update your composer.json

```
{
	"require": {
		"mkusher/time-ago-in-words": "dev-master"
	}
}
```

or use composer's require command:

	composer require mkusher/time-ago-in-words:1.*

2) Register an Extension as a Service

Now you must let the Service Container know about your newly created Twig Extension:

YAML:

```
# app/config/config.yml
services:
	mkusher.twig.time_ago:
		class: Mkusher\Twig\Extension\TimeAgoExtension
		arguments: [@translator, @sonata.intl.templating.helper.datetime]
		tags:
		- { name: twig.extension }
```

XML:

```
# or into your bundle src\Acme\AcmeBundle\Resources\config\services.xml
<service id="mkusher.twig.time_ago" class="Mkusher\Twig\Extension\TimeAgoExtension">
	<tag name="twig.extension" />
	<argument type="service" id="translator" />
	<argument type="service" id="sonata.intl.templating.helper.datetime" />
</service>
```

And update your AppKernel.php:

```
<?php
// app/AppKernel.php
public function registerBundles()
{
    return array(
        // ...
        new Sonata\IntlBundle\SonataIntlBundle(),
        // ...
    );
}

```

To configure SonataIntlBundle follow [instructions][1]

# Usage

To display distance of time in words between a date and current date:

	{{ message.created|ago }}

To display distance of time between two custom dates you should use 

	{{ message.created|distance_of_time_in_words(message.updated) }}

You also have two available options, for both ago & distance_of_time_in_words filters
	
- include_seconds (boolean) if you need more detailed seconds approximations if time is less than a minute

Thus, if you don't want to have the seconds approximation, you should use:

	{{ message.created|ago(false) }}

# Translations

Add the following translations to your `\app\Resources\translations\messages.locale.yml`

This is a translation to spanish:

	# Time ago in words - Twig Extension
	less than %seconds seconds ago: hace menos de %seconds segundos
	half a minute ago: hace medio minuto
	less than a minute ago: hace menos de un minuto
	1 minute ago: hace 1 minuto
	%minutes minutes ago: hace %minutes minutos
	about 1 hour ago: hace casi 1 hora
	about %hours hours ago: hace %hours horas
	today at %time: today at %time
	yersterday at %time: yersterday at %time
	%date at %time: %date at %time
	%date: %date

[1]: http://sonata-project.org/bundles/intl/master/doc/index.html