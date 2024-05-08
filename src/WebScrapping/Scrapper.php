<?php

namespace Chuva\Php\WebScrapping;

use Chuva\Php\WebScrapping\Entity\Paper;
use Chuva\Php\WebScrapping\Entity\Person;

/**
 * Does the scraping of a webpage.
 */
class Scrapper {

    private $currentId = 1;

    /**
     * Loads paper information from the HTML and returns the array with the data.
     */
    public function scrap(\DOMDocument $dom): array {

        $papers = [];

        //Get all 'a' elements with class 'paper-card'
        $paperElements = $dom->getElementsByTagName('a');
    
        foreach ($paperElements  as $paperElement) {
            //Check if the 'a' element has the class 'paper-card'
            if ($paperElement->getAttribute('class') === 'paper-card p-lg bd-gradient-left') {
                //Get title
                $titleElement = $paperElement->getElementsByTagName('h4')->item(0);
                $title = $titleElement ? $titleElement->nodeValue : '';
            
                //Get authors
                $authors = [];
                $authorElements = $paperElement->getElementsByTagName('span');
                foreach ($authorElements as $authorElement) {
                    $name = $authorElement->nodeValue;
                    $institution = $authorElement->getAttribute('title');
                    //Create and add Author object to authors list
                    $authors[] = new Person($name, $institution);
                }

                //Get type
                $type = '';
                $typeElements = $paperElement->getElementsByTagName('div');
                foreach ($typeElements as $typeElement) {
                    if ($typeElement->getAttribute('class') === 'tags mr-sm') {
                        $type = trim($typeElement->nodeValue);
                        break;
                    }
                }
            
                //Create and add Paper object to papers list
                $papers[] = new Paper($this->currentId++, $title, $type, $authors);
            }
        }
        
        return $papers;
    }
}