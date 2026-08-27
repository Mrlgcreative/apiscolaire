<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->string('domaine', 60)->nullable()->index()->after('section');
        });
    }

    public function down(): void
    {
        Schema::table('cours', function (Blueprint $table) {
            $table->dropIndex(['domaine']);
            $table->dropColumn('domaine');
        });
    }
};
