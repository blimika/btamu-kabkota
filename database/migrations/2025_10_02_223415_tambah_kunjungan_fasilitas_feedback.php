<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahKunjunganFasilitasFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_kunjungan', function (Blueprint $table) {
            $table->tinyInteger('kunjungan_sarpras_feedback')->default(0)->after('kunjungan_nilai_feedback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_kunjungan', function (Blueprint $table) {
            //
        });
    }
}
