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
        // property groups
        Schema::create('property_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('position');
            $table->timestamps();
        });
        Schema::create('property_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_group_id')->constrained(null, 'id', 'pgt_property_group_id_foreign')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['property_group_id', 'locale']);
        });

        // properties
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('alias')->unique();
            $table->foreignId('property_group_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedInteger('property_type_id');
            $table->timestamps();
        });
        Schema::create('property_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['property_id', 'locale']);
        });

        // property values
        Schema::create('property_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->timestamps();
        });
        Schema::create('property_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_value_id')->constrained(null, 'id', 'pvt_property_value_id_foreign')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['property_value_id', 'locale']);
        });

        // entity properties
        Schema::create('entity_property', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('entity_id');
            $table->string('entity_type');
            $table->foreignId('property_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['entity_id', 'entity_type', 'property_id']);
        });

        // entity property values
        Schema::create('entity_property_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_property_id')->constrained('entity_property')->cascadeOnDelete();
            $table->integer('int_value')->nullable()->index();
            $table->decimal('float_value', 24, 12)->nullable()->index();
            $table->foreignId('property_value_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('entity_property_value_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_property_value_id')->constrained(null, 'id', 'epvt_property_value_id_foreign')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('string_value')->nullable()->index();
            $table->text('text_value')->nullable();
            $table->timestamps();

            $table->unique(['entity_property_value_id', 'locale'], 'entity_property_value_translations_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('entity_property_value_translations');
        Schema::dropIfExists('entity_property_values');

        Schema::dropIfExists('entity_property');

        Schema::dropIfExists('property_value_translations');
        Schema::dropIfExists('property_values');

        Schema::dropIfExists('property_translations');
        Schema::dropIfExists('properties');

        Schema::dropIfExists('property_group_translations');
        Schema::dropIfExists('property_groups');
    }
};
