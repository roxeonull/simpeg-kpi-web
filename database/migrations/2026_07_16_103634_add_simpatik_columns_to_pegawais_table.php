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
        Schema::table('pegawais', function (Blueprint $table) {
            // Data Personal
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_marital')->nullable();
            $table->string('pendidikan_terakhir')->nullable();
            $table->string('jurusan_pendidikan')->nullable();
            $table->string('universitas')->nullable();
            $table->string('email_pribadi')->nullable();
            $table->string('telepon')->nullable();
            $table->string('fax')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();

            // Data Kepegawaian
            $table->string('tipe_pegawai')->nullable(); // Struktural/Fungsional
            $table->string('jabatan_plt')->nullable();
            $table->string('jabatan_plh')->nullable();
            $table->date('tmt_cpns')->nullable();
            $table->date('tmt_pns')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->date('tmt_kepangkatan')->nullable();
            $table->date('tmt_pangkat_berikutnya')->nullable();
            $table->string('portal_status')->nullable();
            $table->string('simpatik_status')->nullable();
            $table->boolean('mendapat_tunkin')->nullable()->default(false);

            // Data Lain-Lain
            $table->string('no_karis_karsu')->nullable();
            $table->string('file_karis_karsu')->nullable();
            $table->string('no_bpjs_kesehatan')->nullable();
            $table->string('file_bpjs_kesehatan')->nullable();
            $table->string('no_taspen')->nullable();
            $table->string('file_taspen')->nullable();
            $table->string('no_npwp')->nullable();
            $table->string('file_npwp')->nullable();
            $table->string('no_kartu_asn_virtual')->nullable();
            $table->string('file_kartu_asn_virtual')->nullable();
            $table->string('bkn_pns_id')->nullable();
            $table->string('no_bpjs_ketenagakerjaan')->nullable();
            $table->string('file_bpjs_ketenagakerjaan')->nullable();
            $table->string('no_kartu_keluarga')->nullable();
            $table->string('file_kartu_keluarga')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('berat_badan')->nullable();
            $table->string('jenis_rambut')->nullable();
            $table->string('bentuk_muka')->nullable();
            $table->string('warna_kulit')->nullable();
            $table->string('ciri_khas')->nullable();
            $table->string('cacat_tubuh')->nullable()->default('Tidak ada');
            $table->string('hobi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                // Data Personal
                'gelar_depan',
                'gelar_belakang',
                'nama_panggilan',
                'tempat_lahir',
                'tanggal_lahir',
                'jenis_kelamin',
                'golongan_darah',
                'agama',
                'status_marital',
                'pendidikan_terakhir',
                'jurusan_pendidikan',
                'universitas',
                'email_pribadi',
                'telepon',
                'fax',
                'kelurahan',
                'kecamatan',
                'kota',
                'provinsi',
                'kode_pos',

                // Data Kepegawaian
                'tipe_pegawai',
                'jabatan_plt',
                'jabatan_plh',
                'tmt_cpns',
                'tmt_pns',
                'pangkat_golongan',
                'tmt_kepangkatan',
                'tmt_pangkat_berikutnya',
                'portal_status',
                'simpatik_status',
                'mendapat_tunkin',

                // Data Lain-Lain
                'no_karis_karsu',
                'file_karis_karsu',
                'no_bpjs_kesehatan',
                'file_bpjs_kesehatan',
                'no_taspen',
                'file_taspen',
                'no_npwp',
                'file_npwp',
                'no_kartu_asn_virtual',
                'file_kartu_asn_virtual',
                'bkn_pns_id',
                'no_bpjs_ketenagakerjaan',
                'file_bpjs_ketenagakerjaan',
                'no_kartu_keluarga',
                'file_kartu_keluarga',
                'tinggi_badan',
                'berat_badan',
                'jenis_rambut',
                'bentuk_muka',
                'warna_kulit',
                'ciri_khas',
                'cacat_tubuh',
                'hobi',
            ]);
        });
    }
};
