# websiteMaster.php

## Prerequisites

__An HTTP Server__

nginx
```
apt-get install nginx
```

apache
```
apt-get install apache2
```

__PHP5__
```
php5-fpm php5-sqlite
```

## Installation:

```
cd /path/to/http/server/index
git clone https://github.com/psyb0t/websiteMaster.php.git .
git submodule init
git submodule update
```

`shell_exec` needs to be enabled for file mime-type detection

## Usage:

Expected input is a POST request containing raw JSON data structured as such:
```
  {
    "act": [act_name],
    "data": [act_data_object]
  }
```

The script returns a JSON response:
```
  {
    "status": [OK/ERROR],
    "message": [the_message],
    "data": [false/expected_data],
    "content_type": [document_content_type]
  }
```

The proper way to interact with websiteMaster.php is by websiteMasterClient.php. Check it out here: https://github.com/psyb0t/websiteMasterClient.php

## Templates

### The config.php file:

This file consists of the `wm_templateConfig()` function which returns the template configuration options as an associative array.

__Configuration declarations__

`required_data`

This declaration specifies the requirements for a site's __template_data__ database entry content.

Each entry specifies the data type for each data object.

__Eg. 1__:

In this example I specify strings only

```
$config['required_data'] = [
  'general' => [
    'site_title' => '',
    'robots' => ''
  ],
  'content' => [
    'homepage' => [
      'meta_description' => '',
      'meta_keywords' => '',
      'dynamic_word1' => '',
      'dynamic_word2' => ''
    ]
  ]
];
```
in the database:
```
SELECT template_data FROM sites WHERE hostname = '127.0.0.1' =>

{
  "general": {
    "site_title": "Example Site",
    "robots": "noindex, nofollow"
  },
  "content": {
    "homepage": {
      "meta_keywords": "example, site, keywords",
      "meta_description": "Example Site Description",
      "dynamic_word1": "DynamicWord1",
      "dynamic_word2": "DynamicWord2"
    }
  }
}

```

If any of the required object keys are empty an exception will be thrown when rendering

__Eg. 2__:

This second example will specify the same structure as above but the content->homepage object will be required to be an associative array, without specifying the contents of it:

```
$config['required_data'] = [
  'general' => [
    'site_title' => '',
    'robots' => ''
  ],
  'content' => [
    'homepage' => []
  ]
];
```

This results in the possibility of having no key called _dynamic_word1_ or one with any type of data content. No exceptions will be thrown in this case but, if needed, the contents of every content->homepage item must be checked within the template.

`rewrite_rules`

This declaration specifies the name of the arguments found in a URL which will get the values from the URL according to the regex pattern. If an argument is given and the pattern does not apply, an exception is thrown.

__Eg. :__
```
$config['rewrite_rules'] = [
  ['page' => '/^(\w+)$/'],
  ['pageArg' => '/^([a-z0-9]+)$/i']
];
```

In this example it is specified that for a URL like _http://example.org/homepage/theArgument/_ the template will be passed the following data:
```
[url_data] => Array
    (
        [page] => homepage
        [pageArg] => theArgument
    )
```

Also because only two arguments are expected, the _http://example.org/homepage/theArgument/anotherArgument/andAnother/_ will result in the same data passed to the template.

Remember that the order in which the rewrite rules appear in the configuration file is the order in which they are expected to appear in the URL path.

If `rewrite_rules` is is not used, for the _http://example.org/homepage/theArgument/_ URL the template will be passed the following data:
```
[url_data] => homepage/theArgument/
```

### The index.php file:

This is the template loader which will inherit the `$_data` array from the main core loader.

Here you start building the actual website template.
