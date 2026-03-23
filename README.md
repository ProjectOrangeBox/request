# orange/request

## WORK IN PROGRESS

`orange/request` is a small PHP 8.3+ package for describing request input with PHP attributes.

You create a request class that extends `orange\request\Request`, add attributes to each public property, and the package will:

- read incoming input by field name
- validate each field
- optionally cast/filter values
- expose valid data as typed properties
- build array output for plain use or database-style mapping

## How It Works

Each property on your request object can declare attributes such as:

- `#[FieldName('clr')]` to read from a different input key
- `#[Label('Favorite Color')]` to control validation messages
- `#[Column('fav_color')]` to map the property to a database column name
- `#[Table('user')]` to group fields under a table name
- validation attributes like `#[IsRequired]`, `#[MinLength(...)]`, `#[MaxLength(...)]`, `#[GreaterThan(...)]`, and `#[LessThan(...)]`
- filter attributes like `#[ToString]` and `#[ToInteger]`

When the request is constructed, it reflects on the class, discovers those attributes, and processes each property against the provided input array.

## Available Output

After a request is processed, valid data can be read in a few ways:

- typed properties such as `$request->name`
- `$request->asArray()` for a simple associative array keyed by property name
- `$request->asColumns()` for a flat column-value map
- `$request->asTable()` for table-grouped output
- `$request->errors()` for validation messages

Validation status is available through `$request->isValid()`.

## Example

```php
<?php

declare(strict_types=1);

namespace app\request;

use orange\request\Request;
use orange\request\attributes\Column;
use orange\request\attributes\FieldName;
use orange\request\attributes\Label;
use orange\request\attributes\Table;
use orange\request\attributes\filters\ToInteger;
use orange\request\attributes\filters\ToString;
use orange\request\attributes\validations\GreaterThan;
use orange\request\attributes\validations\IsRequired;
use orange\request\attributes\validations\LessThan;
use orange\request\attributes\validations\MaxLength;
use orange\request\attributes\validations\MinLength;

class UserRequest extends Request
{
    #[IsRequired]
    #[MaxLength(64)]
    #[MinLength(1)]
    #[Column('name')]
    #[Table('user')]
    #[ToString]
    #[Label('Name')]
    public string $name;

    #[IsRequired]
    #[ToInteger]
    #[GreaterThan(18)]
    #[LessThan(110)]
    #[Column('age')]
    #[Table('user')]
    #[Label('Age')]
    public int $age;

    #[IsRequired]
    #[MaxLength(16)]
    #[MinLength(4)]
    #[Column('fav_color')]
    #[Table('user')]
    #[FieldName('clr')]
    #[ToString]
    #[Label('Favorite Color')]
    public string $color;
}

$input = [
    'name' => 'Johnny Appleseed',
    'age' => 23,
    'clr' => 'Orange',
];

$request = new UserRequest($input);

if ($request->isValid()) {
    var_dump($request->name);
    var_dump($request->age);
    var_dump($request->color);

    var_dump($request->asArray());
    var_dump($request->asColumns());
    var_dump($request->asTable());
} else {
    var_dump($request->errors());
}
```

## Example Output Shapes

`asArray()`:

```php
[
    'name' => 'Johnny Appleseed',
    'age' => 23,
    'color' => 'Orange',
]
```

`asColumns()`:

```php
[
    'name' => 'Johnny Appleseed',
    'age' => 23,
    'fav_color' => 'Orange',
]
```

`asTable()`:

```php
[
    'user' => [
        'name' => 'Johnny Appleseed',
        'age' => 23,
        'fav_color' => 'Orange',
    ],
]
```

## Included Attributes

Metadata:

- `Column`
- `FieldName`
- `Label`
- `Table`

Filters:

- `ToInteger`
- `ToString`

Validations:

- `GreaterThan`
- `IsRequired`
- `LessThan`
- `MaxLength`
- `MinLength`

## Notes

- The package requires PHP `>=8.3`.
- Property names are used as defaults when `FieldName`, `Column`, or `Table` are not provided.
- Only fields that pass validation are assigned to the request object and included in array/table output.
