<?php
use AndyMac\SiteMapper\SiteMapper;

require_once(__DIR__.'/../vendor/autoload.php');

$siteMapper = new SiteMapper('https://jordanhall.co.uk/');

$siteMapper->addSanitisers(['/page/','/author/']);
$siteMapper->addPrioritiesToDirectories(['open-source' => 0.8,'media-mentions' => 0.1]);
$siteMapper->addPrioritiesToUrls(['https://jordanhall.co.uk/' => 1.0]);


$siteMapper->crawl();

$siteMapper->exportToXml('siteMap');

