<?php

namespace App\DCP;

use DOMDocument;
use Illuminate\Support\Facades\Storage;

class BadgeExplorer
{

    protected $filePath = '';
    protected $fileData;
    protected $fileSize;
    protected $chuckMarkPNG;

    public function __construct(String $filePath)
    {
        $this->filePath = $filePath;
        $this->fileData = Storage::get($this->filePath);
        $this->fileSize = strlen($this->fileData);
    }

    private function setChuckMark()
    {
        $this->chuckMarkPNG = [];
        $position = 0;

        $this->chuckMarkPNG[] = ['Magic header', $position];

        // Skip 8 bytes of IHDR image header.
        $position = 8;

        do {
            $chunk = @unpack('Nsize/a4type', substr($this->fileData, $position, 8));

            $this->chuckMarkPNG[] = [$chunk['type'], $position];

            //$retArray[$chunk['type']][] = $position;

            // Skip 12 bytes chunk overhead.
            $position += $chunk['size'] + 12;
        } while ($position < $this->fileSize);

    
    }

    private function showPartsOfPNG($filePath)
    {

        $retArray = [];
        $position = 0;
        $contents = Storage::get($filePath);

        $fileSize = strlen($contents);

        $header = substr($contents, $position, 8);

        $retArray['Magic header'][] = $position;

        // Skip 8 bytes of IHDR image header.
        $position = 8;

        do {
            $chunk = @unpack('Nsize/a4type', substr($contents, $position, 8));

            $retArray[$chunk['type']][] = $position;

            // Skip 12 bytes chunk overhead.
            $position += $chunk['size'] + 12;
        } while ($position < $fileSize);

        //ddd($retArray);
        return ($retArray);
    }


    private function isPNG($content)
    {

        $png_signature = pack("C8", 137, 80, 78, 71, 13, 10, 26, 10);
        $header = substr($content, 0, 8);

        if ($header != $png_signature) {
            return (false);
        }

        return (true);
    }


    private function isSVG($content)
    {

        $testSVG = [];

        try {
            $doc = new DOMDocument();
            $doc->loadXML($content);

            $testSVG = $doc->getElementsByTagName("svg");
        } catch (\Throwable $th) {
            //throw $th;
        }


        if (sizeof($testSVG) == 1) {
            return (true);
        }

        return (false);
    }


    private function checkFileHeader($filePath)
    {

        $contents = Storage::get($filePath);


        if ($this->isPNG($contents)) {
            return ('PNG');
        } elseif ($this->isSVG($contents)) {
            return ('SVG');
        }

        return (null);
    }

    private function getPNGchunkByNumber($pngArray, $index)
    {

        $contents = Storage::get($this->filePath);

        if ($index > 0) {

            //ddd(array_slice($pngArray, $index, 1));
            $position = count(array_values(array_slice($pngArray, $index, 1))[0]);

            //ddd(array_values(array_values(array_values($position))[0])[0]);

            ddd($position);

            return $position;
        } else {
            $chunk = substr($contents, 0, 8);
            return $chunk;
        }
    }

    private function decoratePNGstructure($pngArray)
    {
        $retTableStart = '<table><tr><th>Name of field</th><th>Starting position</th><th>Content</th></tr>';
        $retTableContent = '';
        $retTableEnd = '</table>';

        $index = 0;
        foreach ($pngArray as $key => $value) {

            $content = $this->getPNGchunkByNumber($pngArray, $index);
            $hexData = chunk_split((unpack("H*", $content))[1], 2, ' ');

            $retTableContent .= '<tr>' . '<td>' . $key . '</td>' . '<td>' . $value[0] . '</td>' . '<td>' . $hexData . '</td>' . '</tr>';
            $index++;
        }


        $retTable = $retTableStart . $retTableContent . $retTableEnd;

        return ($retTable);
    }

    public function getHello()
    {

        $fileType = '';

        $isError = false;
        $errorMesage[] = 'Error!';



        if ($fileType = $this->checkFileHeader($this->filePath)) {
        } else {
            $isError = true;
            $errorMesage[] = "This file is with bad header (no PNG nor SVG)!";
        }


        if ($isError) {


            return ($errorMesage);
        } else {

            if ($fileType == 'PNG') {

                $this->setChuckMark();
                //$retArray = $this->showPartsOfPNG($this->filePath);

                //return ($this->decoratePNGstructure($retArray));

                print_r($this->chuckMarkPNG);

                //ddd($this->chuckMarkPNG);


            } elseif ($fileType == 'SVG') {
                
                return ('SVG details are coming soon!');
            }
        }
    }
}
