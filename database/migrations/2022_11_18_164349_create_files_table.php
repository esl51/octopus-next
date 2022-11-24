<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('position')->index();
            $table->bigInteger('filable_id')->index();
            $table->string('filable_type')->index();
            $table->string('type')->index();
            $table->string('file_name')->index();
            $table->string('original_name')->index();
            $table->string('mime_type')->index();
            $table->string('extension')->index();
            $table->integer('size')->index();
            $table->timestamps();

            $table->unique(['filable_id', 'filable_type', 'type', 'file_name'], 'files_unique');
        });

        Schema::create('file_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id');
            $table->string('locale')->index();
            $table->string('title');
            $table->timestamps();

            $table->unique(['file_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_translations');
        Schema::dropIfExists('files');
    }
};
