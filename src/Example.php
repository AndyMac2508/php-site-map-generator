<?php
use AndyMac\SiteMapper\SiteMapper;

require_once(__DIR__.'/../vendor/autoload.php');

$siteMapper = new SiteMapper('https://testsite.com/');

$siteMapper->addSanitisers(['/page/','/author/']);


$siteMapper->crawl();

$siteMapper->exportToCsv('siteMap');

