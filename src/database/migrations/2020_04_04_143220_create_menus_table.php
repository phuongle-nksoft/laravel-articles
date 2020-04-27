<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('parent_id')->default(0);
            $table->bigInteger('url_to')->default(0);
            $table->boolean('is_active')->nullable()->default(0);
            $table->integer('order_by')->nullable()->default(0);
            $table->string('slug')->nullable();
            $table->string('icon')->nullable();
            $table->integer('position')->nullable()->default(1);
            $table->string('type')->nullable()->default('pages');
            $table->text('meta_description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
