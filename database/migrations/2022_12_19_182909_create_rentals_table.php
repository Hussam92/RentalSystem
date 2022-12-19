<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('apartment_id')->nullable();
            $table->foreign('apartment_id')
                ->references('id')
                ->on('apartments')
                ->nullOnDelete();

            $table->double('price_per_day');
            $table->dateTime('begins_at');
            $table->dateTime('ends_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentals');
    }
};
