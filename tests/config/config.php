<?php

/**
 * This file is part of PageTreeManager,
 * a page management solution for Laravel.
 *
 * @license Apache v2
 * @package SelfComposer\PageTreeManager
 */

return [

	/*
    |--------------------------------------------------------------------------
    | Pages Table
    |--------------------------------------------------------------------------
    |
    | This is the trees table used by PageTreeManager to store the tree
    | structure of the pages to the database.
    |
    */
    'pages_table' => 'pages',

    /*
    |--------------------------------------------------------------------------
    | Slugs Table
    |--------------------------------------------------------------------------
    |
    | This is the slugs table used by PageTreeManager to store the multilanguage
    | slugs to the database.
    |
    */
    'slugs_table' => 'slugs',

    /*
    |--------------------------------------------------------------------------
    | Page Model
    |--------------------------------------------------------------------------
    |
    | This is the Page model used by PageTreeManager to create the pages.
    |
    */
    'page_model' => 'SelfComposer\PageTreeManager\Tests\Models\Page',

    /*
    |--------------------------------------------------------------------------
    | Page Foreign key on slugs Table
    |--------------------------------------------------------------------------
    */
    'page_foreign_key' => 'page_id',

    /*
    |--------------------------------------------------------------------------
    | Slug Separator
    |--------------------------------------------------------------------------
    |
    | The separator used by Cocur\Slugify to generate the slug.
    */
   'slug_separator' => '-',


    /*
    |--------------------------------------------------------------------------
    | Slug Suffix
    |--------------------------------------------------------------------------
    |
    | By default each unique URL will get an incremental value appended at
    | the end of the slug, in order to ensure uniqueness. You can override
    | this value by using a function that creates this value for you
    */
   'slug_suffix' => null
];