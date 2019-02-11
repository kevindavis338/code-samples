<?php
/**
  * Created in Eclipse
  * Developer: Kevin Davis
  * Date: 12/10/2018
  * 
  */


namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncGetFileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
           ->setName('app:sync_getfiles')
           ->setDescription('Get the files and move it to the new pub site');
    }
    
    
    protected function exectute(InputInterface $input, OutputInterface $output)
    {
        $csv = array();
        $file = fopen('results.csv', 'r');
        
        //This will read the csv file. Note the first line of the csv file will be skipped.
        fgetcsv($file);
        while (($result = fgetcsv($file, 1000, ",")) !== FALSE) {
            $csv[] = $result;
        }
               
        fclose($file);
        
        //This will process the information from the csv file.
        
        foreach ($csv as $t => $item) {
            //This is for the file type
            $words = ltrim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $item[2]));
            $test = preg_replace('/\s+/', '_', $words);
            
            //This is to check the to see if the model number has the spaces
            $item[1] =  preg_replace('/[^A-Za-z0-9\. -]/', ' ', $item[1]);
            $no_space = preg_replace('/\s+/', '-', $item[1]);
            
            //This takes out the ext of the file so it can be renamed.
            $type = substr($item[4], strpos($item[4], ".") + 1);
            
            //This will create the file type for the directory. 
            $item[6] = strtolower($test);
            
            //This is create the new file name after it has been downloaded
            $item[7] = $no_space."__en-US_".$item[2]."-".$item[5].".".$type;
            
            //This will set the id for the information 
            $download_id = $item[5];
            $type = $item[6];
            $file_name = $item[4];
            
            //This will be the url to download the file. 
            $source = "https://".$type."/upload/".$download_id."/".$file_name;
            
            //This will get the information via php curl
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
            $data = curl_exec ($ch);
            $error = curl_error($ch);
            curl_close ($ch);
            
            //This will the desitnation location, create the file, and renmae the file. 
            $destination = "C:\\wamp64\\www\\test\\temp\\".$download_id."__".$file_name;
            $new = "C:\\wamp64\\www\\test\\temp\\".$item[7];
            $file = fopen($destination, "w+");
            fputs($file, $data);
            fclose($file);
            
            //This will change the file name
            rename($destination , $new);
         
        } 
    }
}