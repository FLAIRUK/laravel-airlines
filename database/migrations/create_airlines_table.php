<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('airlines.connection');
    }

    public function up(): void
    {
        Schema::create(config('airlines.table', 'airlines'), function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->char('code', 2)->index();
            $table->string('name');
            $table->char('country_code', 2)->nullable()->index();
            $table->string('country_name')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('airlines.table', 'airlines'));
    }
};
