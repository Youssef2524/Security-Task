<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('task_status_updates', function (Blueprint $table) {
            // حذف الأعمدة القديمة
            $table->dropColumn('status');
    
            // إضافة الأعمدة الجديدة
            $table->string('old_status')->after('task_id');
            $table->string('new_status')->after('old_status');
            $table->unsignedBigInteger('updated_by')->after('new_status');
    
            // إضافة القيود الخارجية
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down()
    {
        Schema::table('task_status_updates', function (Blueprint $table) {
            // إعادة الأعمدة القديمة
            $table->enum('status', ['Open', 'In Progress', 'Completed', 'Blocked'])->after('task_id');
    
            // حذف الأعمدة الجديدة
            $table->dropColumn(['old_status', 'new_status', 'updated_by']);
        });
    }
    
    
};
