# JEXUpdate

Joomla! Extension Update Server with GitHub integration.

## Installation

1.- Clone the repo.

```bash
git clone git@github.com:othercodes/jexupdate.git
```

2.- Run `composer install` to install the dependencies.

```bash
composer install
```

3.- Configure the github settings via `.env`.

```bash
DISPLAY_ERROR_DETAILS=false
ADD_CONTENT_LENGTHHEADER=false

GITHUB_URI="https://api.github.com/"
GITHUB_TOKEN="some-github token"
```

Or directly editing `app/configuration.php`.

```php
<?php

return [
    'settings' => [
        'displayErrorDetails' => env("DISPLAY_ERROR_DETAILS", false),
        'addContentLengthHeader' => env('ADD_CONTENT_LENGTHHEADER', false),
        'logger' => [
            'name' => 'jexupdate',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
    'github' => [
        'uri' => env('GITHUB_URI', 'https://api.github.com/'),
        'token' => env('GITHUB_TOKEN'),
    ]
];
```
4.- Configure the server options in `app/jexupdate.php`:

- **server.name**: The server name.
- **server.description**: Server description.
- **cache**: cache time in seconds.
- **repositories**: Assoc array of repositories to serve in the Joomla update server in format `repository => vendor`.

```php
<?php

return [
    'server' => [
        'name' => 'otherCode Extensions',
        'description' => 'otherCode Extensions Set'
    ],
    'cache' => 900,
    'repositories' => [
        'mod_simplecontactform' => 'othercodes',
    ],
];
```

**Important**: The update server only display releases with assets in zip format.
