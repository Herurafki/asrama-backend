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
        //
        Schema::table('kamars', function (Blueprint $t) {
            $t->unsignedInteger('kapasitas')->change();                
            $t->enum('jenis_kelamin', ['L','P'])->nullable()->after('kapasitas'); 
        });

        Schema::table('siswas', function (Blueprint $t) {
            if (Schema::hasColumn('siswas','kamar_id')) {
                $t->dropConstrainedForeignId('kamar_id'); 
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::table('siswas', function (Blueprint $t) {
            $t->foreignId('kamar_id')->nullable()->constrained('kamars')->nullOnDelete();
        });
        Schema::table('kamars', function (Blueprint $t) {
            $t->string('kapasitas')->change();
            $t->dropColumn('jenis_kelamin');
        });

    }
};
