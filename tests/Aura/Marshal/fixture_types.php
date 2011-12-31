<?php
return [
    
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
        'index_fields'                  => [
            'author_id',
        ],
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
        'index_fields'                  => [
            'post_id',
        ],
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
        'index_fields'                  => [
            'post_id',
        ],
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
        'index_fields'                  => [
            'post_id',
            'tag_id',
        ],
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
        ]
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
];
