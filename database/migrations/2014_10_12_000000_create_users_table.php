<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_uid',6)->unique()->nullable();
            $table->string('name',254);
            $table->string('username',50)->unique();
            $table->string('email')->unique();
            $table->string('ganti_email',254)->nullable();
            $table->string('email_kodever',10)->default(0);
            $table->string('user_foto',254)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('user_verified_at')->nullable();
            $table->string('password');
            $table->enum('user_level',['operator','admin'])->default('operator');
            $table->dateTime('user_last_login')->nullable();
            $table->string('user_last_ip', 20)->nullable();
            $table->enum('user_flag',['tidak aktif','aktif'])->default('tidak aktif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
