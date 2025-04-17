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
        Schema::create('my_client', function (Blueprint $table) {
            $table->id();
            $table->char('name', 250)->notNull();
            $table->char('slug', 100)->unique()->notNull();
            $table->string('is_project', 30)->default('0')->check("is_project in ('0','1')");
            $table->char('self_capture', 1)->default('1')->notNull();
            $table->char('client_prefix', 4)->notNull();
            $table->char('client_logo', 255)->default('no-image.jpg')->notNull();
            $table->text('address')->nullable();
            $table->char('phone_number', 50)->nullable();
            $table->char('city', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('my_clients');
    }
};


// CREATE TABLE my_client (
//     id int NOT NULL GENERATED ALWAYS AS IDENTITY,
//     name char(250) NOT NULL,
//     slug char(100) NOT NULL,
//     is_project varchar(30) check (is_project in ('0','1')) NOT NULL DEFAULT '0',
//     self_capture char(1) NOT NULL DEFAULT '1',
//     client_prefix char(4) NOT NULL,
//     client_logo char(255) NOT NULL DEFAULT 'no-image.jpg',
//     address text DEFAULT NULL,
//     phone_number char(50) DEFAULT NULL,
//     city char(50) DEFAULT NULL,
//     created_at timestamp(0) DEFAULT NULL,
//     updated_at timestamp(0) DEFAULT NULL,
//     deleted_at timestamp(0) DEFAULT NULL,
//     PRIMARY KEY (id)
//     ) 