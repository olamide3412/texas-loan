<?php

use App\Enums\GenderEnums;
use App\Models\User;
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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->onDelete('set null');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('other_name')->nullable();
            $table->string('staff_number')->unique()->nullable();
            $table->enum('gender',array_map(fn($gender) => $gender->value, GenderEnums::cases()))->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('email')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('residential_address')->nullable();
            $table->string('local_government')->nullable();
            $table->string('state')->nullable();
            $table->decimal('monthly_salary', 10, 2)->nullable();
            $table->string('nin')->unique()->nullable();
            $table->string('bvn')->unique()->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('date_of_appointment')->nullable();
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
