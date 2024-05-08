<?php

namespace Chuva\Php\WebScrapping;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;
use Box\Spout\Common\Entity\Style\Style;
use Box\Spout\Common\Entity\Style\Color;
use Box\Spout\Common\Entity\Style\CellAlignment;
use Box\Spout\Writer\Common\Creator\Style\BorderBuilder;

/**
 * Runner for the Webscrapping exercise.
 */
class Main {

  /**
   * Main runner, instantiates a Scrapper and runs.
   */
  public static function run(): void {
    require_once __DIR__ . '/../../vendor/autoload.php'; //Include Composer autoload

    $dom = new \DOMDocument('1.0', 'utf-8');
    $dom->loadHTMLFile(__DIR__ . '/../../assets/origin.html');

    $data = (new Scrapper())->scrap($dom);

    //Create the writer
    $writer = WriterEntityFactory::createXLSXWriter();

    //Open the file to write
    $writer->openToFile(__DIR__ . '/../../assets/papers.xlsx'); 

    //Add header row
    $header = WriterEntityFactory::createRowFromArray([
      'ID', 
      'Title', 
      'Type', 
      'Author 1', 'Institution 1', 
      'Author 2', 'Institution 2', 
      'Author 3', 'Institution 3', 
      'Author 4', 'Institution 4', 
      'Author 5', 'Institution 5', 
      'Author 6', 'Institution 6',
      'Author 7', 'Institution 7', 
      'Author 8', 'Institution 8', 
      'Author 9', 'Institution 9'
    ]);

    //Style for the header
    $headerStyle = (new Style())
        ->setFontBold()
        ->setFontColor(Color::BLACK)
        ->setFontSize(11)
        ->setFontName('Arial')
        ->setCellAlignment(CellAlignment::CENTER);

    //Apply style to each cell in the header row
    foreach ($header->getCells() as $cell) {
        $cell->setStyle($headerStyle);
    }

    //Add header row to the writer
    $writer->addRow($header);

    //Iterate over each paper
    foreach ($data as $paper) {
        //Initialize arrays for authors and institutions
        $authorsArray = [];
        $institutionsArray = [];

        //Populate arrays
        foreach ($paper->authors as $author) {
            $authorsArray[] = $author->name;
            $institutionsArray[] = $author->institution;
        }

        //Create cells for each column
        $cells = [
            WriterEntityFactory::createCell($paper->id),
            WriterEntityFactory::createCell($paper->title),
            WriterEntityFactory::createCell($paper->type)
        ];

        //Add author and institution cells
        for ($i = 0; $i < 9; $i++) {
            //Check if author and institution exist
            $author = isset($authorsArray[$i]) ? $authorsArray[$i] : null;
            $institution = isset($institutionsArray[$i]) ? $institutionsArray[$i] : null;

            //Only add cells if author and institution are not empty
            if ($author !== null || $institution !== null) {
                $authorCell = WriterEntityFactory::createCell($author);
                $institutionCell = WriterEntityFactory::createCell($institution);

                //Apply style to author and institution cells
                $authorCell->setStyle(self::getAuthorStyle()->setShouldWrapText(true));
                $institutionCell->setStyle(self::getInstitutionStyle()->setShouldWrapText(true));

                $cells[] = $authorCell;
                $cells[] = $institutionCell;
            }
        }

        //Create a row with the cells
        $row = WriterEntityFactory::createRow($cells);

        //Add the row to the writer
        $writer->addRow($row);
    }

    //Close the writer
    $writer->close();

    //Verification
    print_r($data);
  }

  /**
   * Style for author cells
   */
  private static function getAuthorStyle(): Style {
    return (new Style())
        ->setFontColor(Color::BLACK)
        ->setFontSize(10)
        ->setFontName('Arial')
        ->setCellAlignment(CellAlignment::CENTER);
  }

  /**
   * Style for institution cells
   */
  private static function getInstitutionStyle(): Style {
    return (new Style())
        ->setFontColor(Color::BLACK)
        ->setFontSize(10)
        ->setFontName('Arial')
        ->setCellAlignment(CellAlignment::CENTER);
  }
}