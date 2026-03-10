<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTOLOADER CONFIGURATION
 * -------------------------------------------------------------------
 *
 * This file defines the namespaces and class maps so the Autoloader
 * can find the files as needed.
 *
 * NOTE: If you use an identical key in $psr4 or $classmap, then
 * the values in this file will overwrite the framework's values.
 */
class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
     * This maps the locations of any namespaces in your application to
     * their location on the file system. These are used by the autoloader
     * to locate files the first time they have been instantiated.
     *
     * The '/app' and '/system' directories are already mapped for you.
     * you may change the name of the 'App' namespace if you wish,
     * but this should be done prior to creating any namespaced classes,
     * else you will need to modify all of those classes for this to work.
     *
     * Prototype:
     *```
     *   $psr4 = [
     *       'CodeIgniter' => SYSTEMPATH,
     *       'App'	       => APPPATH
     *   ];
     *```
     *
     * @var array<string, string>
     */

    public $psr4 = [
        APP_NAMESPACE => APPPATH, // For custom app namespace
        'App' => APPPATH,
        'Config' => APPPATH . 'Config',
        'DataTables' => APPPATH . 'Libraries/DataTables',

        'Modules' => APPPATH . 'Modules',
        'Core' => APPPATH . 'Modules/Core',
        'Auth' => APPPATH . 'Modules/Core/Auth',
        'Layout' => APPPATH . 'Modules/Core/Layout',
        'Access' => APPPATH . 'Modules/Core/Access',
        'Group' => APPPATH . 'Modules/Core/Group',
        'Menu' => APPPATH . 'Modules/Core/Menu',
        'Parameter' => APPPATH . 'Modules/Core/Parameter',
        'Permission' => APPPATH . 'Modules/Core/Permission',
        'Reference' => APPPATH . 'Modules/Core/Reference',
        'User' => APPPATH . 'Modules/Core/User',
        'Flip' => APPPATH . 'Modules/Core/Flip',
        'Visitor' => APPPATH . 'Modules/Core/Visitor',

        'Dashboard' => APPPATH . 'Modules/Backend/Dashboard',
        'Report' => APPPATH . 'Modules/Backend/Report',

        'Banner' => APPPATH . 'Modules/Cms/Banner',
        'Pegawai' => APPPATH . 'Modules/Cms/Pegawai',
        'Profil' => APPPATH . 'Modules/Cms/Profil',
        'Layanan' => APPPATH . 'Modules/Cms/Layanan',
        'Organisasi' => APPPATH . 'Modules/Cms/Organisasi',

        'Berita' => APPPATH . 'Modules/Cms/Berita',
        'Pengumuman' => APPPATH . 'Modules/Cms/Pengumuman',
        'Agenda' => APPPATH . 'Modules/Cms/Agenda',
        'Pameran' => APPPATH . 'Modules/Cms/Pameran',
        'Testimoni' => APPPATH . 'Modules/Cms/Testimoni',

        'BukuBaru' => APPPATH . 'Modules/Cms/BukuBaru',
        'Publikasi' => APPPATH . 'Modules/Cms/Publikasi',
        'KoleksiUmum' => APPPATH . 'Modules/Cms/KoleksiUmum',
        'MajalahOnline' => APPPATH . 'Modules/Cms/MajalahOnline',
        'PaketInformasi' => APPPATH . 'Modules/Cms/PaketInformasi',

        'Kamus' => APPPATH . 'Modules/Cms/Kamus',
        'Direktori' => APPPATH . 'Modules/Cms/Direktori',

        'Media' => APPPATH . 'Modules/Cms/Media',
        'Faq' => APPPATH . 'Modules/Cms/Faq',
        'Kontak' => APPPATH . 'Modules/Cms/Kontak',
        'Qna' => APPPATH . 'Modules/Cms/Qna',
        'Survey' => APPPATH . 'Modules/Cms/Survey',

        // Deposit
        'DepositLaporanpengadaan' => APPPATH . 'Modules/Cms/Deposit/Laporanpengadaan',
        'DepositPengajuanNaskahkuno' => APPPATH . 'Modules/Cms/Deposit/Pengajuan/Naskahkuno',
        'DepositPengajuanBahanpustaka' => APPPATH . 'Modules/Cms/Deposit/Pengajuan/Bahanpustaka',
        'DepositPublication' => APPPATH . 'Modules/Cms/Deposit/Publication',


        // Global
        'Tentang'   => APPPATH . 'Modules/Cms/Tentang',
        'Historidirektur'   => APPPATH . 'Modules/Cms/Historidirektur',
        'Peraturan'   => APPPATH . 'Modules/Cms/Peraturan',
        'Publication'   => APPPATH . 'Modules/Cms/Publication',
        'RulesGuide'   => APPPATH . 'Modules/Cms/RulesGuide',
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
     * The class map provides a map of class names and their exact
     * location on the drive. Classes loaded in this manner will have
     * slightly faster performance because they will not have to be
     * searched for within one or more directories as they would if they
     * were being autoloaded through a namespace.
     *
     * Prototype:
     *```
     *   $classmap = [
     *       'MyClass'   => '/path/to/class/file.php'
     *   ];
     *```
     *
     * @var array<string, string>
     */
    public $classmap = [];

    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     * The files array provides a list of paths to __non-class__ files
     * that will be autoloaded. This can be useful for bootstrap operations
     * or for loading functions.
     *
     * Prototype:
     * ```
     *	  $files = [
     *	 	   '/path/to/my/file.php',
     *    ];
     * ```
     *
     * @var array<int, string>
     */
    public $files = [''];
}
