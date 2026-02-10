<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('slug');
            $table->string('locale', 15);
            $table->string('template');
            $table->unsignedInteger('locale_parent_id')->nullable()->index();
            $table->string('aria_label')->nullable();
            $table->timestamps();

            $table->unique(['slug', 'locale']);
        });

        Schema::create('menu_items', static function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('menu_id');
            NestedSet::columns($table);
            $table->string('link_type');
            $table->string('external_link')->nullable();
            $table->string('internal_route')->nullable();
            $table->string('linkable_type')->nullable();
            $table->unsignedInteger('linkable_id')->nullable();
            $table->text('html')->nullable();
            $table->boolean('target_blank')->default(false);
            $table->string('html_classes', 255)->nullable();
            $table->longText('extras')->nullable();
            $table->timestamps();

            $table->index(['linkable_type', 'linkable_id']);

            $table->foreign('menu_id')
                ->references('id')
                ->on('menus')
                ->cascadeOnDelete();

            $table->foreign('parent_id')
                ->references('id')
                ->on('menu_items')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('menu_items');
        Schema::drop('menus');
    }
};
