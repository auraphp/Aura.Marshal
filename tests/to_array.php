<?php

return  [
    [
        'id' => '1',
        'author_id' => '1',
        'body' => 'Anna post #1',
        'fake_field' => '69',
        'null_field' => null,
        'meta' =>  [
            'id' => '1',
            'post_id' => '1',
            'data' => 'meta 1',
        ],
        'comments' =>  [
            [
                'id' => '1',
                'post_id' => '1',
                'body' => 'comment #1 on anna #1',
            ],
            [
                'id' => '2',
                'post_id' => '1',
                'body' => 'comment #2 on anna #1',
            ],
            [
                'id' => '3',
                'post_id' => '1',
                'body' => 'comment #3 on anna #1',
            ],
        ],
        'author' =>  [
            'id' => '1',
            'name' => 'Anna',
        ],
        'tags' =>  [
            [
                'id' => '1',
                'name' => 'zim',
            ],
            [
                'id' => '2',
                'name' => 'dib',
            ],
        ],
    ],
    [
        'id' => '2',
        'author_id' => '1',
        'body' => 'Anna post #2',
        'fake_field' => '69',
        'null_field' => null,
        'meta' =>  [
            'id' => '2',
            'post_id' => '2',
            'data' => 'meta 2',
        ],
        'comments' =>  [],
        'author' =>  [
            'id' => '1',
            'name' => 'Anna',
        ],
        'tags' =>  [
            [
                'id' => '2',
                'name' => 'dib',
            ],
            [
                'id' => '3',
                'name' => 'gir',
            ],
        ],
    ],
    [
        'id' => '3',
        'author_id' => '1',
        'body' => 'Anna post #3',
        'fake_field' => '69',
        'null_field' => null,
        'meta' =>  [
            'id' => '3',
            'post_id' => '3',
            'data' => 'meta 3',
        ],
        'comments' =>  [],
        'author' =>  [
            'id' => '1',
            'name' => 'Anna',
        ],
        'tags' =>  [
            [
                'id' => '3',
                'name' => 'gir',
            ],
            [
                'id' => '1',
                'name' => 'zim',
            ],
        ],
    ],
    [
        'id' => '4',
        'author_id' => '2',
        'body' => 'Clara post #1',
        'fake_field' => '88',
        'null_field' => null,
        'meta' =>  [
            'id' => '4',
            'post_id' => '4',
            'data' => 'meta 4',
        ],
        'comments' =>  [],
        'author' =>  [
            'id' => '2',
            'name' => 'Betty',
        ],
        'tags' =>  [
            [
                'id' => '1',
                'name' => 'zim',
            ],
            [
                'id' => '2',
                'name' => 'dib',
            ],
            [
                'id' => '3',
                'name' => 'gir',
            ],
        ],
    ],
    [
        'id' => '5',
        'author_id' => '2',
        'body' => 'Clara post #2',
        'fake_field' => '88',
        'null_field' => null,
        'meta' =>  [
            'id' => '5',
            'post_id' => '5',
            'data' => 'meta 5',
        ],
        'comments' =>  [
            [
                'id' => '4',
                'post_id' => '5',
                'body' => 'comment #1 on clara #2',
            ],
            [
                'id' => '5',
                'post_id' => '5',
                'body' => 'comment #2 on clara #2',
            ],
            [
                'id' => '6',
                'post_id' => '5',
                'body' => 'comment #3 on clara #2',
            ],
        ],
        'author' =>  [
            'id' => '2',
            'name' => 'Betty',
        ],
        'tags' =>  [
            [
                'id' => '2',
                'name' => 'dib',
            ],
        ],
    ],
];
