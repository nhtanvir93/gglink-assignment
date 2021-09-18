<?php

return [
    'is_registration_available' => true,

    'api_key' => 'AF0F56B4962DB226607A4C83F41CAF7E',
    //Validity in minutes
    'token_validity' => 60,

    'date_time_format' => 'Y-m-d H:i:s',

    'input_max_sizes' => [
        'string' => 255
    ],

    'upload_paths' => [
        'avatar' => 'uploads/avatars'
    ],
    //Default Group Id : 1
    'default_group_id' => 1,

    'default_avatar_path' => 'default_avatar.png',

    'permissions' => [
        'user/add' => [
            //api_user_add Id : 2
            'except_groups' => [
                2
            ]
        ],
        'user/update' => [
            //api_user_update Id : 7
            'except_groups' => [
                7
            ]
        ],
        'user/delete' => [
            //api_user_delete Id : 3
            'except_groups' => [
                3
            ]
        ],
        'user/detail' => [
            //api_user_detail Id : 4
            'except_groups' => [
                4
            ]
        ],
        'user/all' => [
            //api_user_all Id : 5
            'except_groups' => [
                5
            ]
        ]
    ]
];