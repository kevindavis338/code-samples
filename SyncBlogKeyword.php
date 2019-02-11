<?php
/**
 * Created in Eclipse
 * Developer: Kevin Davis
 * Date: 12/20/2018
 * Description: This appliction will sync the keywords to the blog postings via graphql.
 *
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncBlogKeyword extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
        ->setName('app:sync_blogkeywords')
        ->setDescription('This application will check the blog and the product table to check to see if there are any keywords mentioned in the blog.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // This is the login information to the api 

        //Note this was removed due to security reasons
        
        $fileStackApiKey = 
        $bucketName = 
        $endpoint = 
        
        //These are the graphsql query to get the Products and the Blog Posts. 
        
        $products = ["query" => "query { allProducts {title,id}}"];
        $blog = ["query" => "query { allBlogPosts {id,createdAt,isPublished,translations{id,createdAt,title,slug,content,isPublished}}}"];
        
        // This is Curl information for the blog postings. 
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($blog));
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: bearer '
            )
            );
        
        $blog_response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $blog_data = json_decode(trim(substr($blog_response,curl_getinfo($ch,CURLINFO_HEADER_SIZE))));
        $blog_data = $blog_data->data->allBlogPosts;
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($products));
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Authorization: bearer '
            )
            );
        
        // This is Curl information for the product inforamtion.
        
        $prod_response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $prod_data = json_decode(trim(substr($prod_response,curl_getinfo($ch,CURLINFO_HEADER_SIZE))));
        $prod_data = $prod_data->data->allProducts;
        
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        
        foreach ($blog_data as $entry) {
            // This is the Blog Id, Content, and title
            
            $content = $entry->translations[0]->content;
            $title = $entry->translations[0]->title;
            $blogId = $entry->id;
            
            foreach ($prod_data as $prod) {
                // This is the product title and product id
                $productcode = $prod->title;
                $productid   = $prod->id;
                
                // This will compare the entire conent of the blog and the product code. 
                
                if (strpos($content,$productcode) !== false) {
                    
                    echo "Found    ".$productcode." in >>>>".$title.'<BR>';
                    
                    // This will link blog with product id
                    
                    $linkBlogs = ["query" => 'mutation { updateBlogPost( id: "'.$blogId.'" productsIds: ["'.$productid.'"]) { id } }'];
                    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $endpoint);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_VERBOSE, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($linkBlogs));
                    curl_setopt($ch, CURLOPT_HTTPHEADER,
                        array(
                            'Content-Type: application/json',
                            'Authorization: bearer '
                        )
                        );
                    
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
            }
        }
    }
 } 