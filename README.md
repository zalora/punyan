# Punyan [![Build Status](https://travis-ci.org/zalora/punyan.svg?branch=master)](https://travis-ci.org/zalora/punyan) [![Test Coverage](https://codeclimate.com/github/zalora/punyan/badges/coverage.svg)](https://codeclimate.com/github/zalora/punyan/coverage)

Punyan is the PHP implementation of the [Bunyan Logger](https://github.com/trentm/node-bunyan) originally
written for node.js

## Requirements

* Linux / OS X (Maybe someone with Windows wants to try?)
* PHP 5.3+
* Composer
* NPM

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
$ tail -f /tmp/myproject.log | bunyan -o short -L
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

If you don't return anything, false is assumed. It's of course also your responsibility to make sure the class is
loaded or an autoloader is present to do that.

Example:

`{ "callback": { "function": "MyClass::myStaticMethod" }`

#### DiscoBouncer

The name says it all: It will filter everything. This filter doesn't have options and is currently used for unit tests.
If you find a real world use case, let me know. I'm curious.

Example:

`{ "discoBouncer": {} }`

#### NoFilter

Opposite of the DiscoBouncer, lets everything pass.

Example:

`{ "noFilter": {} }`

#### Namespace (Ns)

This filter is applied on your class name (including the namespace), so you can e.g. use it to assign 
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

Currently there's only one writer (StreamWriter), in a later version I'll add a few more to support FirePHP and Slack. 
The three options every writer has are: 

* mute (true|false) default is false
* origin (true|false) default is true
* bubble (true|false) default is true

#### Mute

This will (Cpt. Obvious) mute the writer and suppress any potential output

#### Origin

Origin adds an array with information about where the log call was triggered, it contains the following fields: 

* file
* line
* class
* function

This is e.g. required for the Namespace filter. If you set origin to false and add a Ns-Filter, the filter will always
return false.

#### Bubble

Once a writer logged the message, the log event is not sent to other writers. Example: 
```json
{
  "filters": [
    { "priority": { "priority": "info" } }
  ],
  "writers": [
    {
      "Stream": {
        "origin": true,
        "bubble": false,
        "url": "services.log",
        "filters": [
          { "ns": { "namespace": "Service", "searchMethod": "contains" } }
        ]
      }
    },
    {
      "Stream": {
        "origin": true,
        "url": "common.log",
        "filters": []
      }
    }
  ]
}
```

The services.log keeps all log events which come from classes which contain the word Service in their class name. 
Without bubbling the log message would be in both files, as the stream writer for common.log doesn't have any filters.
So bubbling can be used to prevent duplicate log messages. Keep in mind that the order in which you define your 
writers is important when you use bubbling!

Disclaimer: I stole the word "bubbling" from [Monolog](https://github.com/Seldaek/monolog)

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

### Processors

I stole the name again from Monolog, because I couldn't come up with a better one... They're also doing the same: Add 
additional to every log event. 

Of course you can do that manually, but it's a lot of work and it clutters the code, because it will be executed even
if the log message itself is filtered out. So you move this code into a Processor and attach it to a writer.
From that moment on it will gather all the required information automatically.

The additional data is stored under the key 'proc', which is defined in `Zalora\Punyan\IProcessor`.

### Web Processor

The following data is added to the proc array of every log event:

* url: `$_SERVER['REQUEST_URI']`
* ip: `$_SERVER['REMOTE_ADDR']`
* http_method: `$_SERVER['REQUEST_METHOD']`
* server: `$_SERVER['SERVER_NAME']`
* referrer: `$_SERVER['HTTP_REFERER]`

## Known Problems

* The only supported format is Bunyan; this is not really a problem, more a design decision. I might loosen that to be
able to support other JSON based services if someone's interested...

## FAQs

### Why reinvent the wheel?

Actually I couldn't find a logger I liked, which does NOT implement the 
[PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) interface.
At first I thought implementing a Formatter for [Monolog](https://github.com/Seldaek/monolog) should do the trick, but
the log levels wouldn't match properly and using a mapping seemed too hackish. 

As we want to use the tooling the bunyan project provides, we have to stay format-compatible. And here we have yet 
another logger for PHP.

### What's planned?

#### Configuration storage

In order to change log levels you have to change a file or even worse change code. I want to provide a small CLI
program which changes the configuration without a need for writeable files or redeploy. This can be achieved by
storing the configuration in a key value store

#### New Filters

* *Rate Limit*: Together with bubbling turned off, you can omit sending half a billion emails overnight ;-)
* *Sampler*: Filter out a certain percentage of the events

#### New Writers

* *AMQP*: Sending logs to RabbitMQ
* *Slack*: Your favorite Messenger
* *FirePHP*: Sending log events to the browser during development
* *Responsys*: Sending important logs via E-Mail (Keep the rate limit in mind...)
* *New Relic*: Your favorite application monitoring tool
* *Redis*: Persist your logs to a Redis list
