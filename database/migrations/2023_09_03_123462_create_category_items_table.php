<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryItemsTable extends Migration
{
    public function up()
    {
        Schema::create('category_items', function (Blueprint $table) {
            $table->comment('See Hexatex\LaravelCategory\CategoryItem\Contracts\Categorizable');

            $table->ulid('id')->index();

            $table->foreignUlid('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->index('category_id');

            $table->ulidMorphs('categorizable');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_items');
    }
}
