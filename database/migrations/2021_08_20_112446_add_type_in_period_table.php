<?php
/*
 * Copyright (c) 2021.
 * This code is the property of the Fulliton developer.
 * Write all questions and suggestions on the Vkontakte social network https://vk.com/fulliton
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Тип (На ночь на день и тд)
 */
class AddTypeInPeriodTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up(): void
  {
    Schema::table('periods', function (Blueprint $table) {
      $table->dropColumn(['cost_type_id']);
    });

    Schema::table('periods', function (Blueprint $table) {
      $table->foreignId('cost_type_id')
        ->after('end_at')
        ->constrained('cost_types')
        ->onUpdate('cascade')
        ->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down(): void
  {
    Schema::table('periods', function (Blueprint $table) {
      $table->dropForeign(['cost_type_id']);
    });
  }
}
