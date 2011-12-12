<?php
return array(
    
    'authors' => array(
        'identity_field'                => 'id',
        'relation_names'                => array(
            'posts'                     => array(
                'relationship'          => 'has_many',
                'native_field'          => 'id',
                'foreign_field'         => 'author_id',
            ),
        ),
    ),
    
    'posts' => array(
        'identity_field'                => 'id',
        'index_fields'                  => array(
            'author_id',
        ),
        'relation_names'                => array(
            'meta'                      => array(
                'relationship'          => 'has_one',
                'foreign_type'          => 'metas',
                'native_field'          => 'id',
                'foreign_field'         => 'post_id',
            ),
            'comments'                  => array(
                'relationship'          => 'has_many',
                'native_field'          => 'id',
                'foreign_field'         => 'post_id'
            ),
            'author'                    => array(
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'authors',
                'native_field'          => 'author_id',
                'foreign_field'         => 'id',
            ),
            'tags'                      => array(
                'relationship'          => 'has_many_through',
                'through_type'          => 'posts_tags',
                'native_field'          => 'id',
                'through_native_field'  => 'post_id',
                'through_foreign_field' => 'tag_id',
                'foreign_field'         => 'id'
            ),
        ),
    ),
    
    'metas' => array(
        'identity_field'                => 'id',
        'index_fields'                  => array(
            'post_id',
        ),
        'relation_names'                => array(
            'post'                      => array(
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ),
        ),
    ),
    
    'comments' => array(
        'identity_field'                => 'id',
        'index_fields'                  => array(
            'post_id',
        ),
        'relation_names'                => array(
            'post'                      => array(
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ),
        ),
    ),
    
    'posts_tags' => array(
        'identity_field'                => 'id',
        'index_fields'                  => array(
            'post_id',
            'tag_id',
        ),
        'relation_names'                => array(
            'post'                      => array(
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'posts',
                'native_field'          => 'post_id',
                'foreign_field'         => 'id',
            ),
            'tag'                       => array(
                'relationship'          => 'belongs_to',
                'foreign_type'          => 'tags',
                'native_field'          => 'tag_id',
                'foreign_field'         => 'id',
            ),
        )
    ),
    
    'tags' => array(
        'identity_field'                => 'id',
        'relation_names'                => array(
            'posts'                     => array(
                'relationship'          => 'has_many_through',
                'native_field'          => 'id',
                'through_type'          => 'posts_tags',
                'through_native_field'  => 'tag_id',
                'through_foreign_field' => 'post_id',
                'foreign_field'         => 'id'
            ),
        ),
    ),
);
