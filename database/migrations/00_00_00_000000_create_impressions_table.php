<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('impressions', function (Blueprint $table) {
            $table->id();
            $table->morphs('impressionable');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->string('variation')->default('default');
            $table->date('date');
            $table->unique(['impressionable_id', 'impressionable_type', 'variation', 'date'], 'impressionable_variation_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impressions');
    }
};
