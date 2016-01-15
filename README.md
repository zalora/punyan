# Punyan [![Build Status](https://travis-ci.com/zalora/punyan.svg?token=xVdK7vmHNmWkzySFLTpU&branch=master)](https://travis-ci.com/zalora/punyan)

Punyan is the PHP implementation of the [Bunyan Logger](https://github.com/trentm/node-bunyan) originally written for node.js

## Requirements

* Linux / OS X (Maybe someone with Windows wants to try?)
* PHP 5.3+
* Composer

## Installation

`$ composer require zalora/punyan`

## tl;dr logger to go

```json
{
  "filters": [],
  "writers": [
    {
      "stream": {
        "url": "/tmp/myproject.log",
        "filters": [{ "priority": { "priority": "info" } }]
      }
    }
  ]
}
```

```php
$config = array(
    'filters' => array(),
    'writers' => array(
        array('stream' => array(
            'url' => '/tmp/myproject.log',
            'filters' => array(
                array('priority' => array('priority' => 'info'))
            )
        ))
    )
);
```

```php
<?php
use Zalora\Punyan\Logger;
use Zalora\Punyan\ZLog;

ZLog::setInstance(new Logger('MyAppName', $config));

...

ZLog::info('Informative log message', array('param1' => 'stuffs'));
```

```bash
$ sudo npm install -g bunyan
$ tail -f /tmp/myproject.log | bunyan
```

Bunyan is actually one of the few npm modules which installed without a single warning or error!

## The Config file

The example config is written in JSON-format, every other format works, too as long as it represents the same structure.
The logger itself expects an array with the same structure.

```json
{
  "mute": false,
  "filters": [
    { "priority": {"priority": "info", "operator": ">="} }
  ],
  "writers": [
    {
      "Stream": {
        "mute": false,
        "url": "php://stdout",
        "filters": [
          { "priority": { "priority": "warn" } },
          { "ns": { "namespace": "Service", "searchMethod": "contains" } }
        ]
      }
    }
  ]
}
```

The top level mute mutes the whole logger, the mute inside the writers only
applies for this specific writer. Same for the filters. Every logger can have
zero or more filters and writers, every writer can have zero or more filters.

### Filters

* Callback
* DiscoBouncer
* NoFilter
* Namespace
* Priority
* Regular Expression

To add a filter you have to add a structure like this to the filters array:

`{ "<filter_name>": { "option1": "value1", "option2": "value2" } }`

#### Callback

Callback filters have to return (true|false) and are passed in the LogEvent object to have some data for evaluation.

The only option is:

* function: You better make sure it's callable

If you don't return anything, false is assumed. It's of course also your responsibility to make sure the class is loaded or an autoloader is present to do that.

Example:

`{ "callback": { "function": "MyClass::myStaticMethod" }`

#### DiscoBouncer

The name says it all: It will filter everything. This filter doesn't have options and is currently used for unit tests. If you find a real world use case, let me know. I'm curious.

Example:

`{ "discoBouncer": {} }`

#### NoFilter

Opposite of the DiscoBouncer, lets everything pass.

Example:

`{ "noFilter": {} }`

#### Namespace (Ns)

This filter is applied on your classname (including the namespace), so you can e.g. use it to assign 
your modules a separate logfile. Regular Expressions are validated during init of the logger. 

Options: 

* namespace: What to search for in the namespace
* searchMethod: (optional, default 'startsWith') One of those: (startsWith|contains|regexp)

If you use regexp as searchMethod, namespace contains your regular expression

Examples:

This will accept only logs from classes which contain the token 'Service':

`{ "ns": { "namespace": "Service", "searchMethod": "contains" } }`

My regex foo is incredibly low, sorry for this example. I hope this matches every class which ends with Hello:

`{ "ns": { "namespace": "/Hello$/", "searchMethod": "regexp" } }`

#### Priority

Priority filters filter by priority (Surprise...), e.g.
if the filter level is set to 'info' then everything from info on passes. I guess there's not that much to explain.

Here's the full options list:

* priority: One of those (trace|debug|info|warn|error|fatal)
* operator: (optional, default >=) You don't want to modify that...

Example:

`{ "priority": { "priority": "warn", "operator": ">=" } }`

#### Regexp

Match a field (log message by default) against your regular expression pattern. These are the options of the filter:

* pattern: (mandatory) your regular expression
* field: (optional, default 'msg') The field in the context to run your regexp against . To access nested values, use a dot as separator, e.g. `request.src` to access `$context['request']['src']`
* returnValueOnMissingField: (optional, default false) If the field doesn't exist return either true or false

Example:

`{ "regexp": { "pattern": "/^https/", "field": "request.url", "returnValueOnMissingField": false } }`

Match all URLs which start with https, in case there are no URLs logged, discard the log entry. 

### Writers

Currently there's only one writer (StreamWriter), in a later version I'll add a few more to support FirePHP and Slack

#### StreamWriter

The StreamWriter supports all writeable streams (http://php.net/manual/en/wrappers.php), e.g. 

* php://memory
* file:///tmp/myproject.log
* php://stdout

Options:

* url: The url you want to write (append) to
* filters: Filters are optional, but at least an empty array must exist
* mute: (optional, default false) Like every writer, it can be muted

The stream will be opened during the init process, so in case of files you know very early if it's writeable or not

## Known Problems

* It's impossible to include external writers or filters, because the namespace is hard wired. This will change in a future version
* The only supported format is Bunyan; this is not really a problem, more a design decision. I might loosen that to be able to support other JSON based services

## FAQs

### Why reinvent the wheel?

Actually I couldn't find a logger I liked, which does NOT implement the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) interface.

As we want to use the tooling the bunyan project provides, we have to stay format-compatible. And here we have yet another logger for PHP.

### What's planned?

#### Automatic Content Enrichment (I still need a proper name for that)

I think an example should explain it best: For a certain module you want to log every HTTP related variables, e.g. 

* URL 
* HTTP method
* the POST array

Of course you can do that manually, but it's a lot of work and it clutters the code, because it will be executed even if the log message itself is filtered out. So you move this code into a "content enricher" and attach it to a writer. From that moment on it will gather all the required information automatically

#### Log Interruption

I want to add an option to stop propagating to other writers

#### Configuration storage

In order to change log levels you have to change a file or even worse change code. I want to provide a small CLI program which changes the configuration without a need for writeable files or redeploy. This can be achieved by storing the configuration in a key value store
