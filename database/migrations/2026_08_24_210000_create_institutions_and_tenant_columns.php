<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('code', 10)->unique();
            $table->string('type', 50)->default('ecole');
            $table->text('adresse')->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('est_active')->default(true);
            $table->timestamps();
        });

        $addInstitution = function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->after('id')
                ->constrained('institutions')->cascadeOnDelete();
        };

        Schema::table('users', fn (Blueprint $t) => $addInstitution($t));
        Schema::table('professeurs', fn (Blueprint $t) => $addInstitution($t));

        Schema::table('classes', function (Blueprint $t) use ($addInstitution) {
            $addInstitution($t);
            $t->index(['institution_id', 'section']);
        });

        Schema::table('cours', fn (Blueprint $t) => $addInstitution($t));
        Schema::table('eleves', fn (Blueprint $t) => $addInstitution($t));
        Schema::table('frais', fn (Blueprint $t) => $addInstitution($t));
        Schema::table('paiements_frais', fn (Blueprint $t) => $addInstitution($t));
        Schema::table('absences', fn (Blueprint $t) => $addInstitution($t));
    }

    public function down(): void
    {
        foreach (['absences', 'paiements_frais', 'frais', 'eleves', 'cours', 'classes', 'professeurs', 'users'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['institution_id']);
                $t->dropColumn('institution_id');
            });
        }

        Schema::dropIfExists('institutions');
    }
};
