Aura.Marshal
============

> marshal (verb): to arrange in proper order; set out in an orderly manner; arrange clearly: to marshal facts; to marshal one's arguments. --  [dictionary.com](http://dictionary.reference.com/browse/marshal)

The Aura.Marshal package is a data-object marshalling tool. It takes results from data sources (whether from SQL, Mongo, CSV, or something else) and marshals those result sets into domain model objects of your own design, preserving data relationships along the way.

You can use any database access layer you like with Aura.Marshal, such as ...

- [`mysql`](http://php.net/mysql) or the other PHP database function sets
- [`PDO`](http://php.net/PDO)
- [`Aura Sql`](https://github.com/auraphp/Aura.Sql)
- [`Solar_Sql_Adapter`](http://solarphp.com/class/Solar_Sql_Adapter)
- [`Zend_Db_Adapter`](http://framework.zend.com/manual/en/zend.db.adapter.html)
- [`Doctrine2 DBAL`](http://www.doctrine-project.org/docs/dbal/2.1/en)

... or anything else.  (In theory, you should be able to retrieve data from XML, CSV, Mongo, or anything else, and load it into Aura.Marshal.)

With Aura.Marshal, you use the data retrieval tools of your choice and write your own queries to retrieve data from a data source. You then load that result data into Aura.Marshal, and it creates record and collection objects for you based on a mapping scheme you define for it.

Above all, Aura.Marshal makes it easy to avoid the N+1 problem when working with a domain model.

It is important to remember that Aura.Marshal, despite resembling an ORM iin many ways, it *not* an ORM proper:

- it does not have a query-building facility
- it will not issue queries on its own
- it will not handle persistence for you
- it will not lazy-load results from a data source
- it will not read metadata or schemas from the datasource

Those things are outside the scope of the Aura.Marshal pacakge. Their absence does provide a great amount of flexibility for power users who write their own hand-tuned SQL and need a way to marshal their result sets into a domain model, especially in legacy codebases.

Aura.Marshal works by using `Type` objects (which define the entity types in the domain model). Each `Type` has a definition indicating its identity field, how to build records and collections, and the relationships to other `Type` objects.  The `Type` objects are accessed through a type `Manager`. You load data into each `Type` in the `Manager`, then you retrieve records and collections from each `Type`.

Example Schema
--------------

For the rest of this narrative, we will assume the existence of the following SQL tables and columns in a naive multiuser blogging system:

- `authors`: primary key `id`; column `name`
- `posts`: primary key `id`; columns `author_id`, `title`, and `body`
- `summaries`: primary key `id`; column `comment_count`
- `comments`: primary key `id`; columns `post_id` and `body`
- `tags`: primary key `id`; column `name`
- `posts_tags`: primary key `id`; columns `post_id` and `tag_id`

(Note that the primary key and foreign key names are not important; they can be anything at all.)

Each author can have many posts.

Each post belongs to one author, has one summary, and can have many comments.

Posts and tags have a many-to-many relationship; that is, each post can have many tags, and each tag can be applied to many posts. They map to each other through `posts_tags`.


Basic Usage
===========

Instantiation
-------------

First, instantiate a `Manager` so we can define our `Type` objects and relationships.

    <?php
    $manager = include '/path/to/Aura.Marshal/scripts/instance.php';

Alternatively, you can add Aura.Marshal to your autoloader and instantiate it manually:

    <?php
    $manager = new Aura\Marshal\Manager(
        new Aura\Marshal\Type\Builder,
        new Aura\Marshal\Relation\Builder
    );

Defining Types
--------------

Now we add definitons for each of the entity types in our domain model. These do not have to map directly to tables, but it is often the case that they do.  Because Aura.Marshal does not read schemas, we need to identify explicitly the primary key fields and the relationships (along with the relationship fields).

First, let's set the basic definitions for each type in the domain model. In this case it turns out they all have the same primary key, so it's always `'id'`, but each could have a different primary key depending on your data source.

    <?php
    $manager->setType('authors', array(
        'identity_field' => 'id',
    ));
    
    $manager->setType('posts', array(
        'identity_field' => 'id',
    ));
    
    $manager->setType('summaries', array(
        'identity_field' => 'id',
    ));
    
    $manager->setType('tags', array(
        'identity_field' => 'id',
    ));
    
    $manager->setType('posts_tags', array(
        'identity_field' => 'id',
    ));
    
Defining Relationships
----------------------

Aura.Marshal recognizes four kinds of relationships between types:

- `has_one`: A one-to-one relationship where the native record is the owner of one foreign record.

- `belongs_to`: A many-to-one relationship where the native record is owned by one foreign record.  (The foreign record might be the owner of many other records.)

- `has_many`: A one-to-many relationship where the native record is the owner of many foreign records.

- `has_many_through`: A many-to-many relationship where each native record is linked to many foreign records; at the same time, each foreign record is linked to many native records. This kind of relationship requires an association mapping type through which the native and foreign records are linked to each other.

Let's add the simpler relationships to our `Manager` using the `setRelation()` method.  The first parameter is the name of the type we're setting the relationship on, the second parameter is the field name the related data should be saved in (as well as the implicit foreign type), and the third parameter is an array of information about the relationship.

    <?php
    // each author has many posts
    $manager->setRelation('authors', 'posts', array(
        
        // the kind of relationship
        'relationship'  => 'has_many',
        
        // the authors field to match against
        'native_field'  => 'id',
        
        // the posts field to match against
        'foreign_field' => 'author_id',
    ));
    
    // each post belongs to one author
    $manager->setRelation('posts', 'author', array(
        
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
    ));
    
    // posts have one summary
    $manager->setRelation('posts', 'summary', array(
        
        // the kind of relationship
        'relationship'  => 'has_one',
        
        // the explicit foreign type
        'foreign_type'  => 'summaries',
        
        // the posts field to match against
        'native_field'  => 'id',
        
        // the summaries field to match against
        'foreign_field' => 'post_id'
    ));
    
    // posts have many comments
    $manager->setRelation('posts', 'comments', array(
        // the kind of relationship
        'relationship'  => 'has_many',
        
        // the posts field to match against
        'native_field'  => 'id',
        
        // the comments field to match against
        'foreign_field' => 'post_id'
    ));
    
Now let's set up the more complex many-to-many relationship between posts and tags.

    <?php
    // posts have many tags, as mapped through posts_tags
    $manager->setRelation('posts', 'tags', array(
        
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
    ));
    
    // tags have many posts, as mapped through posts_tags
    $manager->setRelation('tags', 'posts', array(
        
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
    ));


Loading Data
------------

Now that we have defined the `Type` objects and their relationships to each other in the `Manager`, we can load data into the `Type` objects.  In the following example, we load data using `Zend_Db`, but any database access tool can be used.

Note that we are able to select, for example, all the comments for all the posts at once. This means that instead of issuing 10 queries to get comments (one for each blog post), we can issue a single query to get all comments at one time; the `Type` objects will wire up the related collections for us automatically as defined by the relationships.  This helps us avoid the N+1 problem easily.

    <?php
    // instantiate a Zend_Db connection
    $db = new Zend_Db_Adapter_Pdo_Mysql(array(
        'host'     => '127.0.0.1',
        'username' => 'webuser',
        'password' => 'xxxxxxxx',
        'dbname'   => 'test'
    ));
    
    // query for the first 10 posts in the system
    $result = $db->fetchAll('SELECT * FROM posts LIMIT 10');
    
    // load the results into the posts type object, and get back the
    // identity (primary key) values for the loaded results.
    $post_ids = $manager->posts->load($result);
    
    // query for and load all the comments on all the posts at once.
    $result = $db->fetchAll(
        'SELECT * FROM comments WHERE post_id IN (?)',
        array($post_ids)
    );
    $manager->comments->load($result);
    
    // add the authors for the posts.  first, we need to know
    // the author_id values for all the posts so far ...
    $author_ids = $manager->posts->getFieldValues('author_id');
    
    // ... then we can query and load.
    $result = $db->fetchAll(
        'SELECT * FROM authors WHERE id IN (?)',
        array($author_ids)
    );
    $manager->comments->load($result);
    
    // query and load post summaries.
    $result = $db->fetchAll(
        'SELECT * FROM summaries WHERE post_id IN (?)',
        array($post_ids)
    );
    $manager->summaries->load($result);
    
    // query and load the association mapping type linking posts and tags
    $result = $db->fetchAll(
        'SELECT * FROM posts_tags WHERE post_id IN (?)',
        array($post_ids)
    );
    $manager->posts_tags->load($result);
    
    // finally, query and load all tags regardless of posts
    $result = $db->fetchAll('SELECT * FROM tags');


Reading Data
------------

Now that the domain model has been loaded with data, we can read out the record objects, with related data wired up for us automatically.

    <?php
    
    // get a collection of the post IDs we just loaded
    $posts = $manager->posts->getCollection($post_ids);
    
    // loop through posts collection, getting a post record each time
    foreach ($posts as $post) {
        
        // address the native and foreign fields
        echo "The post titled {$post->title} "
           . "was written by {$post->author->display_name} "
           . "and has " . count($post->comments) . " comments. ";
        
        // loop through the tags
        if ($post->tags->isEmpty()) {
            echo "It has no tags on it.";
        } else {
            echo "It has these tags: ";
            $tags = array();
            foreach ($post->tags as $tag) {
                $tags[] = $tag->name;
            }
            echo implode(', ', $tags);
        }
        
        echo PHP_EOL;
    }

Advanced Usage
==============

(forthcoming)
