<?php

use application\welcome\request\User;

$input = [
    'name' => 'Johnny Appleseed',
    'age' => '23',
    'clr' => 'Orange',
];

$request = new User([], $input);

if ($request->isValid()) {
    var_dump($request->name);
    var_dump($request->age);
    var_dump($request->color);

    var_dump($request->asTable());
} else {
    var_dump($request->errors());
}
