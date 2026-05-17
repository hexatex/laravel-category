<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->comment('See Hexatex\LaravelCategory\CategoryType\CategoryType');

            $table->ulid('id')->index();
            $table->string('title');
            $table->string('page_title');
            $table->text('description')->nullable();
            $table->string('sort_by')->nullable();

            $table->boolean('sort_desc')->default(false);
            $table->boolean('is_hidden')->default(true)->index();

            $table->foreignUlid('main_image_id')->nullable()->references('id')->on('images')->onUpdate('cascade')->onDelete('set null');
            $table->index('main_image_id');

            $table->foreignUlid('metadata_id')->references('id')->on('metadata')->onUpdate('cascade')->onDelete('cascade');
            $table->index('metadata_id');

            $table->foreignUlid('parent_id')->nullable()->references('id')->on('categories')->onUpdate('cascade')->onDelete('set null');
            $table->index('parent_id');

            $table->nullableUlidMorphs('type');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
