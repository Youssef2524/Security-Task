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
        Schema::table('attachments', function (Blueprint $table) {
            $table->string('name'); 
            $table->string('path'); 
            $table->string('mime_type'); 
            $table->string('alt_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attachments', function (Blueprint $table) {
            Schema::table('attachments', function (Blueprint $table) {
                $table->dropColumn(['name', 'path', 'mime_type', 'alt_text']);
            });        });
    }
};
