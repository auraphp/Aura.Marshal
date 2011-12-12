<?php
return array(
    'authors' => array(
        array(
            'id' => '1',
            'name' => 'Anna',
        ),
        array(
            'id' => '2',
            'name' => 'Betty',
        ),
        array(
            'id' => '3',
            'name' => 'Clara',
        ),
    ),
    'posts' => array(
        array(
            'id' => '1',
            'author_id' => '1',
            'body' => 'Anna post #1',
            'fake_field' => '69',
        ),
        array(
            'id' => '2',
            'author_id' => '1',
            'body' => 'Anna post #2',
            'fake_field' => '69',
        ),
        array(
            'id' => '3',
            'author_id' => '1',
            'body' => 'Anna post #3',
            'fake_field' => '69',
        ),
        array(
            'id' => '4',
            'author_id' => '2',
            'body' => 'Clara post #1',
            'fake_field' => '88',
        ),
        array(
            'id' => '5',
            'author_id' => '2',
            'body' => 'Clara post #2',
            'fake_field' => '88',
        ),
    ),
    'metas' => array(
        array(
            'id' => '1',
            'post_id' => '1',
            'data' => 'meta 1',
        ),
        array(
            'id' => '2',
            'post_id' => '2',
            'data' => 'meta 2',
        ),
        array(
            'id' => '3',
            'post_id' => '3',
            'data' => 'meta 3',
        ),
        array(
            'id' => '4',
            'post_id' => '4',
            'data' => 'meta 4',
        ),
        array(
            'id' => '5',
            'post_id' => '5',
            'data' => 'meta 5',
        ),
    ),
    'comments' => array(
        array(
            'id' => '1',
            'post_id' => '1',
            'body' => 'comment #1 on anna #1',
        ),
        array(
            'id' => '2',
            'post_id' => '1',
            'body' => 'comment #2 on anna #1',
        ),
        array(
            'id' => '3',
            'post_id' => '1',
            'body' => 'comment #3 on anna #1',
        ),
        array(
            'id' => '4',
            'post_id' => '5',
            'body' => 'comment #1 on clara #2',
        ),
        array(
            'id' => '5',
            'post_id' => '5',
            'body' => 'comment #2 on clara #2',
        ),
        array(
            'id' => '6',
            'post_id' => '5',
            'body' => 'comment #3 on clara #2',
        ),

    ),
    'posts_tags' => array(
        array(
            'id' => '1',
            'post_id' => '1',
            'tag_id'  => '1',
        ),
        array(
            'id' => '2',
            'post_id' => '1',
            'tag_id'  => '2',
        ),
        array(
            'id' => '3',
            'post_id' => '2',
            'tag_id'  => '2',
        ),
        array(
            'id' => '4',
            'post_id' => '2',
            'tag_id'  => '3',
        ),
        array(
            'id' => '5',
            'post_id' => '3',
            'tag_id'  => '3',
        ),
        array(
            'id' => '6',
            'post_id' => '3',
            'tag_id'  => '1',
        ),
        array(
            'id' => '7',
            'post_id' => '4',
            'tag_id'  => '1',
        ),
        array(
            'id' => '8',
            'post_id' => '4',
            'tag_id'  => '2',
        ),
        array(
            'id' => '9',
            'post_id' => '4',
            'tag_id'  => '3',
        ),
        array(
            'id' => '10',
            'post_id' => '5',
            'tag_id'  => '2',
        ),
    ),
    
    'tags' => array(
        array(
            'id' => '1',
            'name' => 'zim',
        ),
        array(
            'id' => '2',
            'name' => 'dib',
        ),
        array(
            'id' => '3',
            'name' => 'gir',
        ),
    ),
);
