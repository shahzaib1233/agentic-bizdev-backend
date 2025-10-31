<?php
// database/migrations/2025_10_27_000000_create_clients_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            // Store everything in one JSON column
            $table->json('data');

            // (Optional but useful) quick lookups / filtering
            // Uncomment if you want these for faster queries:
            // $table->string('business_name')->nullable()->index();
            // $table->string('contact_email')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
