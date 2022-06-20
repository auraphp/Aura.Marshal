# Aura Marshal

> marshal (verb): to arrange in proper order; set out in an orderly manner;
> arrange clearly: to marshal facts; to marshal one's arguments. --
> [dictionary.com](http://dictionary.reference.com/browse/marshal)

The Aura Marshal package is a data-object marshalling tool. It takes results
from data sources and marshals those result sets into domain model objects of
your own design, preserving data relationships along the way.

## Foreword

### Installation

This library requires PHP 7.2 or later; we recommend using the latest available version of PHP as a matter of principle. It has no userland dependencies.

It is installable and autoloadable via Composer as [aura/marshal](https://packagist.org/packages/aura/marshal).

Alternatively, [download a release](https://github.com/auraphp/Aura.Marshal/releases) or clone this repository, then require or include its _autoload.php_ file.

### Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/auraphp/Aura.Marshal/badges/quality-score.png?b=4.x)](https://scrutinizer-ci.com/g/auraphp/Aura.Marshal/)
[![codecov](https://codecov.io/gh/auraphp/Aura.Marshal/branch/4.x/graph/badge.svg?token=UASDouLxyc)](https://codecov.io/gh/auraphp/Aura.Marshal)
[![Continuous Integration](https://github.com/auraphp/Aura.Marshal/actions/workflows/continuous-integration.yml/badge.svg?branch=4.x)](https://github.com/auraphp/Aura.Marshal/actions/workflows/continuous-integration.yml)

To run the unit tests at the command line, issue `composer install` and then `./vendor/bin/phpunit` at the package root. This requires [Composer](http://getcomposer.org/) to be available as `composer`.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

### Community

To ask questions, provide feedback, or otherwise communicate with the Aura community, please join our [Google Group](http://groups.google.com/group/auraphp), follow [@auraphp on Twitter](http://twitter.com/auraphp), or chat with us on #auraphp on Freenode.



## Overview

You can use any database access layer you like with Aura Marshal, such as ...

- [`mysql`](http://php.net/mysql) or the other PHP database function sets
- [`PDO`](http://php.net/PDO)
- [`Aura SQL`](https://github.com/auraphp/Aura.Sql)
- [`Solar_Sql_Adapter`](http://solarphp.com/class/Solar_Sql_Adapter)
- [`Zend_Db_Adapter`](http://framework.zend.com/manual/en/zend.db.adapter.html)
- [`Doctrine2 DBAL`](http://www.doctrine-project.org/docs/dbal/2.1/en)

... or anything else. (In theory, you should be able to retrieve data from
XML, CSV, Mongo, or anything else, and load it into Aura Marshal.)

With Aura Marshal, you use the data retrieval tools of your choice and write
your own queries to retrieve data from a data source. You then load that
result data into an entity type object, and it creates entity and collection
objects for you based on a mapping scheme you define for it.

Aura Marshal makes it easy to avoid the N+1 problem when working with a domain
model. It also uses an identity map (per type) to avoid retaining multiple
copies of the same object.

It is important to remember that Aura Marshal, despite resembling an ORM in
many ways, it *not* an ORM proper:

- it does not have a query-building facility
- it will not issue queries on its own
- it will not handle persistence for you
- it will not lazy-load results from a data source
- it will not read metadata or schemas from the datasource

Those things are outside the scope of the Aura Marshal package. Their absence
does provide a great amount of flexibility for power users who write their own
hand-tuned SQL and need a way to marshal their result sets into a domain
model, especially in legacy codebases.

Aura Marshal works by using `Type` objects (which define the entity types in
the domain model). Each `Type` has a definition indicating its identity field,
how to build entities and collections, and the relationships to other `Type`
objects. The `Type` objects are accessed through a type `Manager`. You load
data into each `Type` in the `Manager`, then you retrieve entities and
collections from each `Type`.

## Documentation

This package is fully documented [here](./docs/index.md).