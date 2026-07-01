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
        Schema::create('link_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('short_link_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('ip_address', 45);

            $table->text('user_agent')
                ->nullable();

            $table->text('referer')
                ->nullable();

            $table->timestamps();
            $table->index('created_ad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_clicks');
    }
};
