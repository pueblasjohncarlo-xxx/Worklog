<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_edited')->default(false)->after('read_at');
            $table->softDeletes()->after('is_edited');
            $table->boolean('is_pinned')->default(false)->after('deleted_at');
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null')->after('is_pinned');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['is_edited', 'is_pinned', 'edited_by']);
            $table->dropSoftDeletes();
        });
    }
};
