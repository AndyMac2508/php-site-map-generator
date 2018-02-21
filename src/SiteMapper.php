<?php
namespace AndyMac\SiteMapper;

use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

class SiteMapper
{
  private $urlsToCrawl = [];
  private $urlsCrawled = [];
  private $goutteClient;
  private $homeUrl;
  private $urlSanitisers = [];

  public function __construct($homeUrl)
  {
    $this->urlsToCrawl[] = $homeUrl;
    if(substr($homeUrl,'-1') == "/"){
      $this->homeUrl = substr($homeUrl,0,-1);
    }else{
      $this->homeUrl = $homeUrl;
    }


    $this->goutteClient = new Client();
    $this->goutteClient->setClient($this->getGuzzleClient());
  }



  public function crawl()
  {
 
    while(count($this->urlsToCrawl) > 0 )
    {
      foreach($this->urlsToCrawl as $key => $urlToCrawl){
        var_dump($urlToCrawl);
        var_dump("-------------------");
        $crawler = $this->goutteClient->request('GET',$urlToCrawl);

        $hrefs = $crawler->filter('a')->each(function($node){
            return $node->attr('href');
        });
        $this->urlsCrawled[] = $urlToCrawl;
        unset($this->urlsToCrawl[$key]);

        $filteredHrefs = $this->filterUrls($hrefs);
        
              //add to urls to crawl if url has not already been crawled
        foreach($filteredHrefs as $filteredHref){
          if(!in_array($filteredHref,$this->urlsCrawled) && !in_array($filteredHref,$this->urlsToCrawl)){
              $this->urlsToCrawl[] = $filteredHref;
            }
        }
              var_dump($this->urlsToCrawl);
              var_dump("_|_|_|_|_|_|_|_|_|_|_|_|");
      }     
      
    }

    
  

    

  }
  public function addSanitisers(array $sanitisers)
  {     
    $this->urlSanitisers = $sanitisers;

  }

  public function exportToCsv($fileName)
  {

    $this->stripSanitisers();
    
    $output = fopen($fileName.'.csv','w');

    fputcsv($output,array('Url'));

    foreach($this->urlsCrawled as $urlCrawled){
      fputcsv($output,array($urlCrawled));
    }
    fclose($output);

  }
  private function stripSanitisers()
  {
    foreach ($this->urlsCrawled as $key => $urlcrawled)
    {
      foreach($this->urlSanitisers as $urlSanitiser){
        if(strpos($urlcrawled, $urlSanitiser) !== false) {
          unset($this->urlsCrawled[$key]);
        }
      }
    }
  }
  private function filterUrls($hrefs)
  {
    foreach($hrefs as $key => $href){
      if(substr($href,0,1) == "/"){
        if(substr($href,-1) == "/" && $href !== "/"){
          $href  = substr($href,-1);
    
        }
        $hrefs[$key] = $this->homeUrl.$href;
      }
      elseif(strpos($href, $this->homeUrl) === false)
      {
        unset($hrefs[$key]);
      }

  
      
        
         
      }
      return $hrefs;
  }
  private function getGuzzleClient()
  {
      return new GuzzleClient([
          'timeout' => 60,
      ]);
  }
}