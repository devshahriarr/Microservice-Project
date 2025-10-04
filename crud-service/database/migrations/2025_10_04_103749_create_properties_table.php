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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // seller reference (no foreign key constraint for microservice separation)
            $table->unsignedBigInteger('seller_id')->nullable()->index();

            $table->string('property_type', 50);
            $table->string('project_name', 150)->nullable();
            $table->string('developer_name', 150)->nullable();
            $table->string('unit_number', 50)->nullable();
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('size_sqft', 10, 2)->nullable();
            $table->string('floor_number', 20)->nullable();
            $table->string('view_type', 50)->nullable();
            $table->integer('parking_slots')->nullable();

            $table->enum('status_type', ['Ready', 'Off-plan', 'Rented'])->nullable();
            $table->boolean('is_rented')->default(false);
            $table->decimal('rent_amount', 12, 2)->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('handover_date')->nullable();
            $table->decimal('outstanding_balance', 12, 2)->nullable();

            $table->decimal('asking_price', 12, 2)->nullable();

            $table->string('title_deed_url', 255)->nullable();
            $table->string('spa_document_url', 255)->nullable();
            $table->json('extra_files')->nullable();

            $table->enum('source', ['Website', 'WhatsApp', 'Manual Entry'])->default('Website');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
