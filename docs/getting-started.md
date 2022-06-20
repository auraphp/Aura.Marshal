# Getting Started

The Aura Marshal package is a data-object marshalling tool. It takes results
from data sources and marshals those result sets into domain model objects of
your own design, preserving data relationships along the way.

## Instantiation

First, instantiate a `Manager` so we can define our `Type` objects and
relationships.

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

## Defining Types

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


## Defining Relationships

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


## Loading Data

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

## Reading Data

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