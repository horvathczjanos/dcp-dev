<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ManageWGETController extends Controller
{
    public function doIt()
    {

        $startPageID = 17388794;


        $stopPageID = 17388867;

        for ($i = 0; $i <= ($stopPageID - $startPageID); $i++) {

            $actPageID = $startPageID + $i;
            //$process = new Process(['wget', '--limit-rate=50k', '--wait=30', '--random-wait', '--output-document=book01/' . $actPageID . '.jpg', 'https://collections.library.yale.edu/iiif/2/' . $actPageID . '/full/3125,/0/default.jpg']);
            //$process = new Process(['wget', '--limit-rate=50k', '--wait=30', '--random-wait', '--output-document=book02/' . $actPageID . '.jpg', 'https://collections.library.yale.edu/iiif/2/' . $actPageID . '/full/2340,/0/default.jpg']);

            //$process = new Process(['wget', '--limit-rate=50k', '--wait=30', '--random-wait', '--output-document=book03/' . $actPageID . '.jpg', 'https://collections.library.yale.edu/iiif/2/' . $actPageID . '/full/3294,/0/default.jpg']);

            $process = new Process(['wget', '--limit-rate=50k', '--wait=30', '--random-wait', '--output-document=book04/' . $actPageID . '.jpg', 'https://collections.library.yale.edu/iiif/2/' . $actPageID . '/full/2100,/0/default.jpg']);


            $process->run();

            // executes after the command finishes
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            } else {
                echo ($i . '<br />');
            }
            //echo $process->getOutput();
        }
        /*  $output = null;
        $retval = null;
        exec('df -h', $output, $retval);
        echo "Returned with status $retval and output:\n";
        print_r($output); */
    }
}
