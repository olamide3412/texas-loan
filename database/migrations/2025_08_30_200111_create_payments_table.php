<?php

use App\Enums\PaymentMethodEnums;
use App\Enums\PaymentStatusEnums;
use App\Models\Order;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->string('tx_ref')->nullable()->unique();
            $table->string('flw_ref')->nullable(); // I just added it
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date')->default(now());
            $table->enum('payment_method', array_map(fn($method) => $method->value, PaymentMethodEnums::cases()));
            $table->enum('payment_status', array_map(fn($status) => $status->value, PaymentStatusEnums::cases()));
            $table->foreignId('collected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
