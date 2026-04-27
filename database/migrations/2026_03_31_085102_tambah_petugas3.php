<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahPetugas3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_tanggal', function (Blueprint $table) {
            $table->string('tanggal_petugas3_uid',6)->nullable()->after('tanggal_petugas2_uid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_tanggal', function (Blueprint $table) {
            //
        });
    }
}
