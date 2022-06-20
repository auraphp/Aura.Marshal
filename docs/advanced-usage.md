# Advanced Usage

## Entity and Collection Builders

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


## Indexing

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


## Removing and Clearing Entities

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


## All-At-Once Definition

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
