<?php

use App\Enums\OrderStatusEnums;
use App\Enums\PaymentMethodEnums;
use App\Enums\PaymentStatusEnums;
use App\Enums\PaymentTypeEnums;
use App\Models\Client;
use App\Models\Customer;
use App\Models\EmploymentDetail;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class)->constrained()->cascadeOnDelete();
            $table->string('order_ref')->unique();
            $table->decimal('total_price', 10, 2)->default(0.00);

            $table->decimal('remaining_balance', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->enum('repayment_frequency', ['weekly','monthly'])->default('monthly');
            $table->integer('repayment_term')->default(1);

            $table->enum('payment_type', array_map(fn($payment_type) => $payment_type->value, PaymentTypeEnums::cases()));
            $table->enum('status', array_map(fn($status) => $status->value, OrderStatusEnums::cases()))->default(OrderStatusEnums::Pending->value);

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('given_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
