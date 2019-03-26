# WP Blade Check

[![Packagist](https://img.shields.io/packagist/v/roots/wp-blade-check.svg?style=flat-square)](https://packagist.org/packages/roots/wp-blade-check)
[![Packagist Downloads](https://img.shields.io/packagist/dt/roots/wp-blade-check.svg?style=flat-square)](https://packagist.org/packages/roots/wp-blade-check)

WP Blade Check is a simple Composer package that checks and displays an admin notice if your uncompiled Blade templates are publicly accessible. This is a sign of an improperly configured webserver, and for the privacy of your codebase, should be [handled accordingly](https://roots.io/sage/docs/theme-installation/). If you are using [Trellis](https://roots.io/trellis/) and/or Kinsta, this is already done for you.

## Installation

Install via Composer:

```sh
$ composer require roots/wp-blade-check
```

## Configuration

No configuration is needed, but you can pass a few optional settings through the `roots.blade.check` filter such as permanently hiding the notice or adding more file extensions to check against such as `.twig`.

```php
add_filter('roots.blade.check', function () {
    return [
        'hide'       => false, // Setting to true will permanently hide the notice.
        'duration'   => 60 * 60 * 24, // Duration between checking against the extensions.
        'extensions' => ['blade.php', 'twig'] // An array or string containing the extensions to check against.
    ];
});
```

## Contributing

Contributions are welcome from everyone. We have [contributing guidelines](https://github.com/roots/guidelines/blob/master/CONTRIBUTING.md) to help you get started.

## Community

Keep track of development and community news.

* Participate on the [Roots Discourse](https://discourse.roots.io/)
* Follow [@rootswp on Twitter](https://twitter.com/rootswp)
* Read and subscribe to the [Roots Blog](https://roots.io/blog/)
* Subscribe to the [Roots Newsletter](https://roots.io/subscribe/)
* Listen to the [Roots Radio podcast](https://roots.io/podcast/)
