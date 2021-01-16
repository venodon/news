<?php
/* @var string $host */
/* @var $item */

?>

<?php $str = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
foreach ($items as $item) {
    $str .= "<url>
            <loc>" . $host . $item->getUrl() . "</loc>
            <lastmod>" . date(DATE_W3C, strtotime($item->updated_at)) . "</lastmod>
            <changefreq>weekly</changefreq>
            </url>";
}
$str .= '</urlset>';
$file = file_put_contents(Yii::getAlias('@frontend') . '/web/sitemap.xml', $str)
?>