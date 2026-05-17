<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCategoryTypesTable extends Migration
{
    public function up()
    {
        Schema::create('test_category_types', function (Blueprint $table) {
            $table->ulid('id')->index();
            $table->string('test');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('test_category_types');
    }
}
