# Advantage Connector

An interface to a custom API developed by World News Group to communicate with Advantage software.

## Installing via Composer

Only Composer (v2.0) is support at this time.

Add the following in your composer.json:

```YAML
...
"repositories": [
    {
        "type": "vcs",
        "url": "<Git url to project>
    }
]
...
```

Then require the library in composer:

`composer require world-news-group/advantage-connector`

## Code Use

Firstly, add the appropriate require and use statement (goes something like this):

```PHP
require('vendor/autoload.php');

use WorldNewsGroup\Advantage\AdvantageConnector;
```

Secondary, configure the environment the connector will use:

```PHP
AdvantageConnector::configure($api_key, $endpoint);

AdvantageConnector::getCustomer('999999999');
```

All results are returned as SQL queries, so column names often won't match up with the "official" Advantage API.

