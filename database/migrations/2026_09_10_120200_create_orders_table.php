<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email');

            // Champs "figés" au moment de la commande : le prix ou le libellé
            // d'une option de livraison/paiement peut changer plus tard sans
            // jamais réécrire l'historique des commandes déjà passées.
            $table->foreignId('shipping_option_id')->nullable()->constrained('shipping_options')->nullOnDelete();
            $table->string('shipping_label')->nullable();
            $table->unsignedInteger('shipping_price_cents')->default(0);

            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('payment_method_label')->nullable();
            $table->text('payment_instructions')->nullable();

            $table->string('status')->default('pending'); // pending|paid|shipped|completed|cancelled
            $table->unsignedInteger('items_total_cents');
            $table->unsignedInteger('total_cents');
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
