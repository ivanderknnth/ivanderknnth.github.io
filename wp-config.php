<?php
/**
 * Konfigurasi dasar WordPress.
 *
 * Berkas ini berisi konfigurasi-konfigurasi berikut: Pengaturan MySQL, Awalan Tabel,
 * Kunci Rahasia, Bahasa WordPress, dan ABSPATH. Anda dapat menemukan informasi lebih
 * lanjut dengan mengunjungi Halaman Codex {@link http://codex.wordpress.org/Editing_wp-config.php
 * Menyunting wp-config.php}. Anda dapat memperoleh pengaturan MySQL dari web host Anda.
 *
 * Berkas ini digunakan oleh skrip penciptaan wp-config.php selama proses instalasi.
 * Anda tidak perlu menggunakan situs web, Anda dapat langsung menyalin berkas ini ke
 * "wp-config.php" dan mengisi nilai-nilainya.
 *
 * @package WordPress
 */

// ** Pengaturan MySQL - Anda dapat memperoleh informasi ini dari web host Anda ** //
/** Nama basis data untuk WordPress */
define( 'DB_NAME', 'testsite1' );

/** Nama pengguna basis data MySQL */
define( 'DB_USER', 'root' );

/** Kata sandi basis data MySQL */
define( 'DB_PASSWORD', '' );

/** Nama host MySQL */
define( 'DB_HOST', 'localhost' );

/** Set Karakter Basis Data yang digunakan untuk menciptakan tabel basis data. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Jenis Collate Basis Data. Jangan ubah ini jika ragu. */
define('DB_COLLATE', '');

/**#@+
 * Kunci Otentifikasi Unik dan Garam.
 *
 * Ubah baris berikut menjadi frase unik!
 * Anda dapat menciptakan frase-frase ini menggunakan {@link https://api.wordpress.org/secret-key/1.1/salt/ Layanan kunci-rahasia WordPress.org}
 * Anda dapat mengubah baris-baris berikut kapanpun untuk mencabut validasi seluruh cookies. Hal ini akan memaksa seluruh pengguna untuk masuk log ulang.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'c,MKE+hJ9Qi4Pu2;DEz.n[?s{~qg_mfA#vfOmVD(!]2tmyj3!ob_`5Ehch&~lVIp' );
define( 'SECURE_AUTH_KEY',  ']./8VBfLTY|xKET@D[mbbyW_;BwT*kVG-/xRAaLjh|],[tJh-25ViiA[qJcT[N(G' );
define( 'LOGGED_IN_KEY',    '?w/tqV$NNa)>g9MLqIhAaab>vpi6~XXh@d/{_p`G?tP$B<X~GS6o/6#tZL=fwvcw' );
define( 'NONCE_KEY',        'ZHlp_&AuA-onr9ga8w@g*} Tld#L+e)Sh3pLTY5bMJ}5cC8&k&Hng5[gR5HoJ~ o' );
define( 'AUTH_SALT',        'hjY5*VL4F8-djj5f?*@!)Tkv=]oS0<0PjgP]YM&ZPVCR*`J5}chfR.y/0!r+8!`f' );
define( 'SECURE_AUTH_SALT', 'YqSkKQDcMk)/-Tn* z[%KTYAjSsfcTQ(xnNBktq^}QAFxa{(e&+|}@m U@tPtg&d' );
define( 'LOGGED_IN_SALT',   'r;.c@!7EzD3a7]*GiynTS_:ftZy-+]DW(pYOi/n^LzpIOD5b?BoDO/7|oOJtKy6T' );
define( 'NONCE_SALT',       ',o4pGYyMoGYPx.C k:t:*;V6eCBx[F?O=:y8n;TDA=*^*|R!gBCk_<5o3]Lko/E)' );

/**#@-*/

/**
 * Awalan Tabel Basis Data WordPress.
 *
 * Anda dapat memiliki beberapa instalasi di dalam satu basis data jika Anda memberikan awalan unik
 * kepada masing-masing tabel. Harap hanya masukkan angka, huruf, dan garis bawah!
 */
$table_prefix = 'testsite1_';

/**
 * Untuk pengembang: Moda pengawakutuan WordPress.
 *
 * Ubah ini menjadi "true" untuk mengaktifkan tampilan peringatan selama pengembangan.
 * Sangat disarankan agar pengembang plugin dan tema menggunakan WP_DEBUG
 * di lingkungan pengembangan mereka.
 */
define('WP_DEBUG', false);

/* Cukup, berhenti menyunting! Selamat ngeblog. */

/** Lokasi absolut direktori WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Menentukan variabel-variabel WordPress berkas-berkas yang disertakan. */
require_once(ABSPATH . 'wp-settings.php');
