<?php

use App\Enums\PaymentMethodEnums;
use App\Models\Order;
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
            $table->string('order_ref')->nullable()->unique();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date')->default(now());
            $table->enum('payment_method', array_map(fn($status) => $status->value, PaymentMethodEnums::cases()));
            $table->enum('payment_status', ['success','failed']);
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
