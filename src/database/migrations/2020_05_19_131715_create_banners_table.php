<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedBigInteger('pages_id')->index('banners_pages_pages_id_index');
            $table->boolean('is_active')->nullable()->default(0);
            $table->integer('order_by')->nullable()->default(0);
            $table->longText('description')->nullable();
            $table->string('slug')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('pages_id', 'banners_pages_pages_id_foreign')->references('id')->on('pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropForeign('banners_pages_pages_id_foreign');
            $table->dropIndex('banners_pages_pages_id_index');
        });
        Schema::dropIfExists('banners');
    }
}
