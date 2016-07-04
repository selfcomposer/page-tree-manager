<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class Pages extends Migration
{
    public function up()
    {
        $config = config('page-tree-manager');

        Schema::create($config['pages_table'], function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable()->index();
            $table->integer('lft')->nullable()->index();
            $table->integer('rgt')->nullable()->index();
            $table->integer('depth')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();
        });

        Schema::create($config['slugs_table'], function (Blueprint $table) use ($config) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->char('lang_code', 2);
            $table->string('name');
            $table->string('slug');

            $table->timestamps();

            $table->unique(['page_id', 'lang_code']);

            $table->foreign('page_id')
                ->references('id')->on($config['pages_table'])
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $config = config('page-tree-manager');

        Schema::drop($config['pages_table']);

        Schema::drop($config['slugs_table']);
    }
}