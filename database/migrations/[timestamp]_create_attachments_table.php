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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('size');
            $table->string('type')->default('document'); // logo, avatar, document
            $table->morphs('attachable'); // polymorphic relation
            $table->string('alt_text')->nullable();
            $table->json('metadata')->nullable(); // dimensions, etc.
            $table->timestamps();
            
            $table->index(['attachable_type', 'attachable_id']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
