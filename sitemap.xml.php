<?php
require_once 'config/seo.php';

header('Content-Type: application/xml; charset=utf-8');
echo generateSitemap();
?>