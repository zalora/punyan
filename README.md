# Punyan [![Build Status](https://travis-ci.com/zalora/punyan.svg?token=xVdK7vmHNmWkzySFLTpU&branch=master)](https://travis-ci.com/zalora/punyan)

Punyan is the PHP implementation of the fantastic [Bunyan Logger](https://github.com/trentm/node-bunyan) originally written for node.js

## Installation

`$ composer require zalora/punyan`

## The Config file

The example config is written in JSON-format, every other format works, too as long as it represents the same structure. The logger itself expects an array with the same structure.

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
          { "priority": { "priority": "warn", "operator": ">=" } },
          { "regexp": { "pattern": "/Hallo/", "field": "", "returnValueOnMissingField": false } },
          { "ns": { "namespace": "Service", "searchMethod": "contains" } },
          {
            "callback": { "function": "Bob_Bootstrap::someFilter" }
          }
        ]
      }
    }
  ]
}
```

The top level mute mutes the whole logger, the mute inside the writers only applies for this specific writer. Same for the filters.

Every logger can have zero or more filters and writers, every writer can have zero or more filters.

### Available Filters

* Priority
* Namespace
* Regexp
*

#### Priority



## FAQs

### Why reinvent the wheel?

Actually I couldn't find a logger I liked, which does NOT implement the [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) interface.

As we want to use the tooling the bunyan project provides, we have to stay format-compatible. And here we have yet another logger for PHP.
