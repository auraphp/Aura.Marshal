# Overview

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

### Example Schema

For the rest of this narrative, we will assume the existence of the following
SQL tables and columns in a naive multiuser blogging system:

- `authors`: primary key `id`; column `name`
- `posts`: primary key `id`; columns `author_id`, `title`, and `body`
- `summaries`: primary key `id`; columns `post_id` and `read_sum`
- `comments`: primary key `id`; columns `post_id` and `body`
- `tags`: primary key `id`; column `name`
- `posts_tags`: primary key `id`; columns `post_id` and `tag_id`

(Note that the primary key and foreign key names are not important; they can
be anything at all.)

Each author can have many posts.

Each post belongs to one author, has one summary, and can have many comments.

Posts and tags have a many-to-many relationship; that is, each post can have
many tags, and each tag can be applied to many posts. They map to each other
through `posts_tags`.