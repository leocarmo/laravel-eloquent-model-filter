# Laravel Eloquent model filter

[![Code Quality](https://scrutinizer-ci.com/g/leocarmo/laravel-eloquent-model-filter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/leocarmo/php-telegram-bot/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/leocarmo/laravel-eloquent-model-filter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/leocarmo/php-telegram-bot/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/leocarmo/laravel-eloquent-model-filter/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Total Downloads](https://img.shields.io/packagist/dt/leocarmo/laravel-eloquent-model-filter.svg)](https://packagist.org/packages/leocarmo/laravel-eloquent-model-filter)

Now you can filter your model dynamically. 
This is not a powerful search system, but this can help you with small projects 
when you need more time in others features and simple filters.

```
composer require leocarmo/laravel-eloquent-model-filter
```

## Usage

First, implement the `Trait` on your model, like this:

```php
use LeoCarmo\ModelFilter\Traits\FilterableModel;
use Illuminate\Database\Eloquent\Model;

class YourModel extends Model
{

    use FilterableModel;

}
```

Now you can use all available methods, we will pass thought later. 
But after this, some required attributes are required to use all features from this filter.

---
First attribute is `filterable`, with these you can set all allowed filters and the operator for this attribute. Like this:
```php
class YourModel extends Model
{

    use FilterableModel;

    protected $filterable = [
        'id',
        'name' => 'LIKE',
        'email',
        'age' => '>=',
        'phone'
    ];

}
```

The second is `filterable_select`, with these you set columns to select on query. 
This is very important for fast queries. If this attribute is not present on model, all
columns will be returned (`*` operator).

```php
class YourModel extends Model
{

    use FilterableModel;

    protected $filterable_select = [
        'id', 'name', 'email', 'age'
    ];

}
```

Now, all required configurations are set. You can start all your filters like this:
```php
class YourController
{

    public function filter(Request $request)
    {
        return (new YourModel)->filter($request->all())->get();
    }

}
```

*Important: This Trait return Illuminate\Database\Query\Builder instance, so you can use all available methods like `paginate()` and `orderBy()` to power up your filter*

# Tips

The default operator is `=`, so, for all queries this will be used. 
You can change this with `changeDefaultOperator()` method.

---
When you use `LIKE` operator, the search will be: `%SEARCH%`


# Available methods

To assume all the control, you can change model default filter 
configuration in a specific request.

#### pushFilterableSelect()

This method will push new columns to the select query.

---
Example: on your model you defined `id` and `name`, but in a specific request you want to show the `age`, you can use this method.

```php
$model->pushFilterableSelect('age');
// OR
$model->pushFilterableSelect(['age', 'created_at']);
```

#### pushFilterable()

With this, you can push new columns to allowed filter or change the operator for an existent column.

---
If you push a column that was defined on model with the attribute `filterable` and an operator, the original value will be override with the new operator.
```php
// this first example will not overide the original column if the default value has an operator seted, but will push to the allowed filters if was not defined
$model->pushFilterable('age'); 

// this example will overide the original operator
$model->pushFilterable(['age' => '>']);
```


#### changeDefaultOperator()

The default operator is `=` when no operator was defined on model. If you want, you can change this:
```php
$model->changeDefaultOperator('LIKE'); 
```

# Credits

- [Leonardo Carmo](https://github.com/leocarmo)
