# PageTreeManager

PageTreeManager is a package that aims to easily manage website pages as an ordered tree structure. Basically it's a wrapper around the [Baum](https://github.com/etrepat/baum) package for Laravel 5's Eloquent ORM.

## Documentation

* [Installation](#installation)
* [Usage](#usage)
* [Scope Support](#scope-support)
* [Contributing](#contributing)

## <a name="installation">Installation</a>

In order to install **Laravel 5 PageTreeManager**, just add the following entry to your `composer.json` file:

```
"selfcomposer/page-tree-manager": "~0.0.1"
```

Run `composer install` to install it.

Otherwise you can simply run the `composer require` command from your terminal:

```
composer require selfcomposer/page-tree-manager:0.0.1
```

Finally in your `config/app.php` you need to register the _service provider_. Just add the following into the `providers` array:


```
SelfComposer\PageTreeManager\Providers\ServiceProvider::class,
```

After that, you can publish the config-file with the following artisan command:

```
php artisan vendor:publish --provider="SelfComposer\PageTreeManager\Providers\ServiceProvider" --tag="config"
```

and the migration with:

```
php artisan vendor:publish --provider="SelfComposer\PageTreeManager\Providers\ServiceProvider" --tag="migrations"
```

The package assumes that you use two tables: one for storing the tree structure of the pages and one for storing the multilanguage names and slugs of the pages. The default configuration assumes *"pages"* and *"slugs"* as names for these tables. You are free to change the predefined values, but be sure to take a look to the generated files in order to understand how they can be customized.

After the migration has been published you can create the tables by running the migrations:

```
php artisan migrate
```

### Model Configuration

To work with the package you need to extends the two models provided: `PageNode` and `Slug`.

```
class Page extends SelfComposer\PageTreeManager\PageNode {

}
```

The class `PageNode` itself extends `Baum\Node` in order to provide the Nested Set functionality.

Take a look to the original source to know how the column names can be customized.

Each `PageNode` has a [one-to-many](https://laravel.com/docs/eloquent-relationships#one-to-many) relationship with the `Slug` model, meaning that a page can have multiple slugs, each one in a different language. Look also to the source of this model to know how to customize the column names.

## <a name="usage">Usage</a>

After you've configured the models and run the migration, you are ready to use the package. Here's a couple of functionalities, but keep in mind that you can still use all the [Baum's functions](https://github.com/etrepat/baum#usage) to manage the trees:

### Creating a root node

By default, all nodes are created as roots:

```
// create a page
$root = Page::create();
// ...and a slug for it
$root->slugs()->create(['lang_code' => 'en', 'name' => 'Root Page']);
```

### Inserting nodes

```
// Directly with a relation
$child = $root->children()->create([]);
$child->slugs()->create(['lang_code' => 'en', 'name' => 'Child Page']);

// with the `makeChildOf` method
$child2 = Page::create();
$child2->slugs()->create(['lang_code' => 'en', 'name' => 'Child Page'])
$child2->makeChildOf($root);
```

### Deleting nodes

```
$child->delete();
```

The deletion process will also delete any _slug_ associated with the page.

Descendants of deleted nodes will also be deleted.

<hr>

You can check all the other methods in the [documentation for Baum](https://github.com/etrepat/baum).

In addition to those methods, the package provides a few other functions to easily manage your pages:

### Names, slugs and urls

* `$page->getName($langCode)`: gets the name of the page in the language code
* `$page->getSlug($langCode)`: gets the slug of the page in the language code
* `$page->getUrl($langCode, $langPrefix = false)`: gets the entire Url of the page in the current language code, using the slugs of the parent pages. The `$langPrefix` parameter prefixes the language code to the url

### Moving pages

* `$page->makeRoot()`: make the current page a root page, provided that another page with the same scope (see below) does not exist
* `$page->moveUpTo($otherPage)`: moves the page above another page. Is an alias for Baum's `moveToLeftOf($otherNode)`
* `$page->moveDownTo($otherPage)`: moves the page below another page. Is an alias for Baum's `moveToRightOf($otherNode)`

### Asking questions to your pages

* `$page->isSiblingOf($otherPage)`: returns true if the page is in the same subtree and at the same level of another page
* `$page->isExactlyAbove($otherPage)`: returns true if the page is exactly above (or at the left) of another page
* `$page->isExactlyBelow($otherPage)`: returns true if the page is exactly below (or at the left) of another page

### Accessing the pages

* `$page->match($url, $langCode, $scoped)`: returns the page that matches the url, the language code and optional scoped attributes
* `$page->getTree()`: dumps the hierarchy tree in the form of a nested collection representing the queried tree. It's a wrapper for `$page->getDescendatnsAndSelf()->toHierarchy()`

## Scope Support

Baum provides a simpe method to provide Nested Set "scoping" which restricts parts of a nested set tree. This should allow for multiple nested set trees in the same database table. PageTreeManager enforces this functionality, allowing multiple root nodes with the same url, provided they have different scopes.

## Soft Deletes

At the time this functionality is not present in the package.

## <a name="contributing">Contributing</a>

Contributions are more than welcome.

1. Fork & clone the project: git clone git@github.com:your-username/page-tree-manager.git.
2. Run the tests and make sure that they pass with your setup: phpunit.
3. Create your bugfix/feature branch and code away your changes. Add tests for your changes.
4. Make sure all the tests still pass: phpunit.
5. Push to your fork and submit new a pull request.

## License

_Licensed to the Apache Software Foundation (ASF) under one or more contributor license agreements.  See the NOTICE file distributed with this work for additional information regarding copyright ownership.  The ASF licenses this file to you under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with the License.  You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0) Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied._

_See the License for the specific language governing permissions and limitations under the License._