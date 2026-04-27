<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FlagSKD extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_kunjungan', function (Blueprint $table) {
            $table->boolean('kunjungan_flag_skd')->default(false)->after('kunjungan_petugas_username')->comment('Flag apakah pengunjung menjadi responden SKD (true = ya, false = tidak)');
            $table->string('kunjungan_pdf')->nullable()->after('kunjungan_foto')->comment('File PDF Permintaan');
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
            $table->dropColumn('kunjungan_flag_skd');
            $table->dropColumn('kunjungan_pdf');
        });
    }
}
