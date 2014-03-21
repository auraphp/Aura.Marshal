Aura Marshal
============

[![Build Status](https://travis-ci.org/auraphp/Aura.Marshal.png?branch=develop)](https://travis-ci.org/auraphp/Aura.Marshal)

> marshal (verb): to arrange in proper order; set out in an orderly manner;
> arrange clearly: to marshal facts; to marshal one's arguments. --
> [dictionary.com](http://dictionary.reference.com/browse/marshal)

The Aura Marshal package is a data-object marshalling tool. It takes results
from data sources and marshals those result sets into domain model objects of
your own design, preserving data relationships along the way.

This package is compliant with [PSR-0][], [PSR-1][], and [PSR-2][]. If you
notice compliance oversights, please send a patch via pull request.

[PSR-0]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

Overview
--------

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

Example Schema
--------------

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


Basic Usage
===========

Instantiation
-------------

First, instantiate a `Manager` so we can define our `Type` objects and
relationships.

```php
<?php
$manager = include '/path/to/Aura.Marshal/scripts/instance.php';
?>
```

Alternatively, you can add Aura Marshal to your autoloader and instantiate it
manually:

```php
<?php
use Aura\Marshal\Manager;
use Aura\Marshal\Type\Builder as TypeBuilder;
use Aura\Marshal\Relation\Builder as RelationBuilder;

$manager = new \Aura\Marshal\Manager(
    new TypeBuilder,
    new RelationBuilder
);
?>
```

Defining Types
--------------

Now we add definitions for each of the entity types in our domain model. These
do not have to map directly to tables, but it is often the case that they do.
Because Aura Marshal does not read schemas, we need to identify explicitly the
primary key fields and the relationships (along with the relationship fields).

First, let's set the basic definitions for each type in the domain model. In
this case it turns out they all have the same primary key, so it's always
`'id'`, but each could have a different primary key depending on your data
source.

```php
<?php
$manager->setType('authors',    ['identity_field' => 'id']);
$manager->setType('posts',      ['identity_field' => 'id']);
$manager->setType('comments',   ['identity_field' => 'id']);
$manager->setType('summaries',  ['identity_field' => 'id']);
$manager->setType('tags',       ['identity_field' => 'id']);
$manager->setType('posts_tags', ['identity_field' => 'id']);
?>
```
    

Defining Relationships
----------------------

Aura Marshal recognizes four kinds of relationships between types:

- `has_one`: A one-to-one relationship where the native entity is the owner of
  one foreign entity.

- `belongs_to`: A many-to-one relationship where the native entity is owned by
  one foreign entity. (The foreign entity might be the owner of many other
  entities.)

- `has_many`: A one-to-many relationship where the native entity is the owner
  of many foreign entities.

- `has_many_through`: A many-to-many relationship where each native entity is
  linked to many foreign entities; at the same time, each foreign entity is
  linked to many native entities. This kind of relationship requires an
  association mapping type through which the native and foreign entities are
  linked to each other.

Let's add the simpler relationships to our `Manager` using the `setRelation()`
method. The first parameter is the name of the type we're setting the
relationship on, the second parameter is the field name the related data
should be saved in (as well as the implicit foreign type), and the third
parameter is an array of information about the relationship.

```php
<?php
// each author has many posts
$manager->setRelation('authors', 'posts', [
    
    // the kind of relationship
    'relationship'  => 'has_many',
    
    // the authors field to match against
    'native_field'  => 'id',
    
    // the posts field to match against
    'foreign_field' => 'author_id',
]);

// each post belongs to one author
$manager->setRelation('posts', 'author', [
    
    // the kind of relationship
    'relationship'  => 'belongs_to',
    
    // normally the second param doubles as the foreign_type, but here
    // we are using plural type names, so we need to specify the
    // foreign_type explicitly
    'foreign_type'  => 'authors',
    
    // the posts field to match against
    'native_field'  => 'author_id',
    
    // the authors field to match against
    'foreign_field' => 'id',
]);

// posts have one summary
$manager->setRelation('posts', 'summary', [
    
    // the kind of relationship
    'relationship'  => 'has_one',
    
    // the explicit foreign type
    'foreign_type'  => 'summaries',
    
    // the posts field to match against
    'native_field'  => 'id',
    
    // the summaries field to match against
    'foreign_field' => 'post_id'
]);

// posts have many comments
$manager->setRelation('posts', 'comments', [
    // the kind of relationship
    'relationship'  => 'has_many',
    
    // the posts field to match against
    'native_field'  => 'id',
    
    // the comments field to match against
    'foreign_field' => 'post_id'
]);
?>
```
    
Now let's set up the more complex many-to-many relationship between posts and
tags.

```php
<?php
// posts have many tags, as mapped through posts_tags
$manager->setRelation('posts', 'tags', [
    
    // the kind of relationship
    'relationship' => 'has_many_through',
    
    // the association mapping type that links posts and tags
    'through_type' => 'posts_tags',
    
    // the posts field that should map to the "posts" side of the
    // association mapping type
    'native_field' => 'id',
    
    // the "posts" side of the association mapping type
    'through_native_field' => 'post_id',
    
    // the "tags" side of the association mapping type
    'through_foreign_field' => 'tag_id',
    
    // the tags field that should map to the "tags" side of the
    // association mapping type
    'foreign_field' => 'id',
]);

// tags have many posts, as mapped through posts_tags
$manager->setRelation('tags', 'posts', [
    
    // the kind of relationship
    'relationship' => 'has_many_through',
    
    // the association mapping type that links posts and tags
    'through_type' => 'posts_tags',
    
    // the tags field that should map to the "tags" side of the
    // association mapping type
    'native_field' => 'id',
    
    // the "tags" side of the association mapping
    'through_native_field' => 'tag_id',
    
    // the "posts" side of the association mapping
    'through_foreign_field' => 'post_id',
    
    // the posts field that should map to the "posts" side of the
    // association mapping
    'foreign_field' => 'id',
]);
?>
```


Loading Data
------------

Now that we have defined the `Type` objects and their relationships to each
other in the `Manager`, we can load data into the `Type` objects. In the
following example, we load data using [Aura SQL](https://github.com/auraphp/Aura.Sql),
but any database access tool can be used.

```php
<?php
/**
 * @var Aura\Sql\AdapterFactory $adapter_factory 
 */
// instantiate a database adapter for MySQL
$sql = $adapter_factory->newInstance(
    'mysql',
    [
        'host'   => '127.0.0.1',
        'dbname' => 'database_name',
    ]
    'user_name',
    'pass_word'
);

// select the first 10 posts in the system
$result = $sql->fetchAll('SELECT * FROM posts LIMIT 10');

// load the results into the posts type object, and get back the
// identity (primary key) values for the loaded results.
$post_ids = $manager->posts->load($result);

// select and load all the comments on all the posts at once.
$result = $sql->fetchAll(
    'SELECT * FROM comments WHERE post_id IN (:post_ids)',
    [
        'post_ids' => $post_ids,
    ]
);
$manager->comments->load($result);
?>
```
    
Note that we are able to select all the comments for all the posts at once.
This means that instead of issuing 10 queries to get comments (one for each
blog post), we can issue a single query to get all comments at one time; the
`Type` objects will wire up the related collections for us automatically as
defined by the relationships. This helps us avoid the N+1 problem easily.
Let's continue:

```php
<?php
// add the authors for the posts.  first, we need to know
// the author_id values for all the posts so far ...
$author_ids = $manager->posts->getFieldValues('author_id');

// ... then we can query and load.
$result = $sql->fetchAll(
    'SELECT * FROM authors WHERE id IN (:author_ids)',
    [
        'author_ids' => $author_ids,
    ]
);
$manager->authors->load($result);

// query and load post summaries.
$result = $sql->fetchAll(
    'SELECT * FROM summaries WHERE post_id IN (:post_ids)',
    [
        'post_ids' => $post_ids,
    ]
);
$manager->summaries->load($result);

// query and load the association mapping type linking posts and tags
$result = $sql->fetchAll(
    'SELECT * FROM posts_tags WHERE post_id IN (:post_ids)',
    [
        'post_ids' => $post_ids,
    ]
);
$manager->posts_tags->load($result);

// finally, query and load all tags regardless of posts
$result = $sql->fetchAll('SELECT * FROM tags');
$manager->tags->load($result);
?>
```

Reading Data
------------

Now that the domain model has been loaded with data, we can read out the
entity objects, with related data wired up for us automatically.

```php
<?php
// get a collection of the post IDs we just loaded
$posts = $manager->posts->getCollection($post_ids);

// loop through posts collection, getting a post entity each time
foreach ($posts as $post) {
    
    // address the native and foreign fields
    echo "The post titled {$post->title} "
       . "was written by {$post->author->name}. "
       . "It has been read {$post->summary->read_sum} times "
       . "and has " . count($post->comments) . " comments. ";
    
    // loop through the tags
    if ($post->tags->isEmpty()) {
        echo "It has no tags.";
    } else {
        echo "It has these tags: ";
        $tags = [];
        foreach ($post->tags as $tag) {
            $tags[] = $tag->name;
        }
        echo implode(', ', $tags);
    }
    
    echo PHP_EOL;
}
?>
```

Advanced Usage
==============

Entity and Collection Builders
------------------------------

We have a good amount of control over how the type objects create entities and
collections. The instantiation responsibilities are delegated to builder
objects. We can tell the type object what builders to use for entity and
collection objects by specifying `'entity_builder'` and `'collection_builder'`
values when defining the type. Similarly, we can tell the type object that
the entity builder will generate a particular class of object; this lets the
type object know when the loaded data has been converted to a entity object.

```php
<?php
$manager->setType('posts', [
    // the field with the unique identifying value
    'identity_field' => 'id',
    
    // an object to build entities; default is a new instance of
    // Aura\Marshal\Entity\Builder
    'entity_builder' => new \Vendor\Package\Posts\EntityBuilder,
    
    // an object to build collections; default is a new instance of
    // Aura\Marshal\Collection\Builder
    'collection_builder' => new \Vendor\Package\Posts\CollectionBuilder,
]);
?>
```
    
The builders should implement `Aura\Marshal\Entity\BuilderInterface` and
`Aura\Marshal\Collection\BuilderInterface`, respectively.


Indexing
--------

By default, the `Type` objects do not index the values when loading entities.
You are likely to see a performance improvement when Aura Marshal wires up
related collections if you add indexes for native fields used in
relationships. For example, you could tell the `posts_tags` association mapping
type to index on `post_id` and `tag_id` for faster lookups:

```php
<?php
$manager->setType('posts_tags', [
    'identity_field' => 'id',
    'index_fields'   => ['post_id', 'tag_id'],
]);
?>
```

We suggest adding an index for all `native_field` fields in the relationships
for a `Type` (except the `identity_field`, which is a special case and does
not need indexing). Typically this is needed only on a type that `belongs_to`
another type.

Indexes are created *only at `load()` time*. They are not updated when the
entity object is modified.


Removing and Clearing Entities
------------------------------

There are times at which you will want to mark an entity for removal. You can
do so using the `Type::removeEntity()` method; this will remove it from the
the indexes, but retains it in the type object. Later, you can check to see
which entities have been removed using the `Type::getRemovedEntities()` method.

```php
<?php
// remove the post with identity value 88
$manager->posts->removeEntity('88');

// get the list of removed entities
$removed_posts = $manager->posts->getRemovedEntities();
?>
```

Alternative, you can clear out all entity objects from a type using
`Type::clear()`. To clear all entity objects from all types, use
`Manager::clear()`. These will reset the type objects to their initial
unloaded states, unsetting all objects, indexes, and references internally.

```php
<?php
// clear all post entities
$manager->posts->clear();

// clear all entities from all types
$manager->clear();
?>
```


All-At-Once Definition
----------------------

You can define all your types and their relationships through the manager at
instantiation time. The following is the equivalent all-at-once definition
array for the above programmatic definitions, including indexes and
relationships:

```php
<?php
use Aura\Marshal\Manager;
use Aura\Marshal\Type\Builder as TypeBuilder;
use Aura\Marshal\Relation\Builder as RelationBuilder;

$manager = new Manager(new TypeBuilder, new RelationBuilder, [
    
    'authors' => [
        'identity_field'                => 'id',
        'relation_names'                => [
            'posts'                     => [
                'relationship'          => 'has_many',
                'native_field'          => 'id',
                'foreign_field'         => 'author_id',
            ],
        ],
    ],
    
    'posts' => [
        'identity_field'                => 'id',
        'index_fields'                  => ['author_id'],
        'relation_names'                => [
            'meta'                      => [
                'relationship'          => 'has_one',
                'foreign_type'          => 'metas',
                'native_field'          => 'id',
                'foreign_field'         => 'post_id',
            ],
            'comments'                  => [
                'relationship'          => 'has_many',
                'native_field'          => 'id',
                'foreign_field'         => 'post_id'
            ],
            'author'                    => [
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'authors',
                'native_field'          => 'author_id',
                'foreign_field'         => 'id',
            ],
            'tags'                      => [
                'relationship'          => 'has_many_through',
                'through_type'          => 'posts_tags',
                'native_field'          => 'id',
                'through_native_field'  => 'post_id',
                'through_foreign_field' => 'tag_id',
                'foreign_field'         => 'id'
            ],
        ],
    ],
    
    'metas' => [
        'identity_field'                => 'id',
        'index_fields'                  => ['post_id'],
        'relation_names'                => [
            'post'                      => [
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ],
        ],
    ],
    
    'comments' => [
        'identity_field'                => 'id',
        'index_fields'                  => ['post_id'],
        'relation_names'                => [
            'post'                      => [
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ],
        ],
    ],
    
    'posts_tags' => [
        'identity_field'                => 'id',
        'index_fields'                  => ['post_id', 'tag_id'],
        'relation_names'                => [
            'post'                      => [
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ],
            'tag'                       => [
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'tags',
                'native_field'          => 'tag_id',
                'foreign_field'         => 'id',
            ],
        ],
    ],
    
    'tags' => [
        'identity_field'                => 'id',
        'relation_names'                => [
            'posts'                     => [
                'relationship'          => 'has_many_through',
                'native_field'          => 'id',
                'through_type'          => 'posts_tags',
                'through_native_field'  => 'tag_id',
                'through_foreign_field' => 'post_id',
                'foreign_field'         => 'id'
            ],
        ],
    ],
]);
?>
```
