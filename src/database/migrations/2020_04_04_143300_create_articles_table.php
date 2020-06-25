<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('categories_id')->index('article_categories_categories_id_index');
            $table->boolean('is_active')->nullable()->default(0);
            $table->integer('order_by')->nullable()->default(0);
            $table->string('slug')->unique();
            $table->string('video_id')->nullable();
            $table->longText('description')->nullable();
            $table->longText('short_content')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_canonical')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('categories_id', 'article_categories_categories_id_foreign')->references('id')->on('article_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign('article_categories_categories_id_foreign');
            $table->dropIndex('article_categories_categories_id_index');
        });
        Schema::dropIfExists('articles');
    }
}
