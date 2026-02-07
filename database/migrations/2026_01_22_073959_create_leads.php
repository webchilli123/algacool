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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
             $table->date('date');
            $table->unsignedBigInteger('lead_source_id');
            $table->boolean('is_new');

            $table->string('customer_name', 80)->nullable();
            $table->string('firm_name', 255)->nullable();
            $table->string('customer_email', 80)->nullable();
            $table->string('customer_number', 255)->nullable();
            $table->string('alternate_number', 255)->nullable();
            $table->string('customer_website', 255)->nullable();
            $table->string('customer_address', 255)->nullable();

            $table->unsignedBigInteger('party_id')->nullable();
            $table->boolean('is_include_items')->nullable();

            $table->string('level', 45);
            $table->string('status', 45);

            $table->string('not_in_interested_reason', 255)->nullable();

            $table->unsignedBigInteger('follow_up_user_id')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_type', 45)->nullable();
            $table->string('mature_action_type', 45)->nullable();

            $table->text('comments')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('follow_up_user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('lead_source_id')->references('id')->on('sources')->onDelete('restrict');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
