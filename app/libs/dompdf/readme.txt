/*Required modification after update lib*/

//src/Options.php in funciton __construct
$this->setChroot(DIR_FS_CATALOG);
$this->setTempDir(DIR_FS_TMP);
$this->setFontDir(CFG_PATH_TO_DOMPDF_FONTS);

//src/FontMetrics.php in function loadFontFamilies
$file = $fontDir . "/dompdf_font_family_cache.dist.php";