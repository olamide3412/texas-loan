<?php

use App\Enums\GenderEnums;
use App\Enums\OccupationEnums;
use App\Enums\VerificationStatusEnums;
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
        Schema::create('Clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('other_name')->nullable();
            $table->enum('gender', array_map(fn($gender) => $gender->value, GenderEnums::cases()))->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('bvn', 11)->unique()->nullable();
            $table->string('nin', 11)->unique()->nullable();
            $table->boolean('bvn_verified')->default(false);
            $table->boolean('nin_verified')->default(false);
            $table->enum('verification_status', array_map(fn($status) => $status->value, VerificationStatusEnums::cases()))->default(VerificationStatusEnums::Pending->value);
            $table->string('photo')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('local_government')->nullable();
            $table->string('state')->nullable();
            $table->enum('occupation', array_map(fn($status) => $status->value, OccupationEnums::cases()));
            $table->decimal('annual_income', 10, 2)->default(0.00);
            $table->foreignId('registered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Clients');
    }
};
