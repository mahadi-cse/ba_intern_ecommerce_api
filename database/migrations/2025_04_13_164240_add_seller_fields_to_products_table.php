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
        Schema::table('products', function (Blueprint $table) {
            // Only add seller_id if it doesn't exist
            if (!Schema::hasColumn('products', 'seller_id')) {
                $table->foreignId('seller_id')
                    ->after('category_id')
                    ->constrained('users')
                    ->onDelete('cascade');
            }

            // Only add seller_email if it doesn't exist
            if (!Schema::hasColumn('products', 'seller_email')) {
                $table->string('seller_email')
                    ->after('seller_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Only drop foreign key if it exists
            if (Schema::hasColumn('products', 'seller_id')) {
                $table->dropForeign(['seller_id']);
            }
            
            // Only drop columns if they exist
            $table->dropColumnIfExists('seller_id');
            $table->dropColumnIfExists('seller_email');
        });
    }
};