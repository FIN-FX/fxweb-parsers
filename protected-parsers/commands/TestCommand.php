<?php
/*
 * Console command which execute parsers
 */
class TestCommand extends CConsoleCommand
{
    /**
     * Run parsers
     */
    public function actionRun()
    {
        date_default_timezone_set("UTC");
        $document = new DomDocument();
        $site = Sites::model()->findByPk(3);
        $idSite = $site->id;
        
        // Generate filters
        $searchBegin = "/poisk/avto/";
        

            $searchUri = 'make1=&model1=&region1=2031&price_from=3500&price_to=4300&year_from=&year_to=&only_photo=on';
            $url = $site->url.$searchBegin.'?'.$searchUri;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HEADER,         0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $response = curl_exec($ch);
            $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status != 200)
                continue;

            if (!@$document->loadHTML($response))
                continue;

            $xpath = new DOMXPath($document);

            $trs = '//div[@class="vehicles_search_res2"]/div';
            $linkPath = '/div/div[1]/@data-href';
            $links = array();

            $nodes = $xpath->query($trs, $document);
            
            for ($i = 1; $i < $nodes->length; $i++)
            {
                $resXpath = $xpath->query($trs.'['.$i.']'.$linkPath, $document)->item(0)->nodeValue;
                $links[md5($resXpath)] = $resXpath;
            }
            var_dump($links); die();
            $newItemsLinks = [];
            $existItems = Items::model()->findAllByAttributes(['id_site' => $idSite, 'id_search' => $model->id]);
            $existItems = array_values(CHtml::listData($existItems, 'id', 'id_item'));
            
            foreach($links as $id => $val)
            {
                if (!in_array($id, $existItems))
                {
                    $newItemsLinks[] = $val;
                }
            }

            if (!empty($newItemsLinks))
            {
                mail('ifranzhev@gmail.com', 
                    'AUTO AvtoBazar Test', 
                    count($newItemsLinks) . " новых авто.\r\n" . implode("\r\n", $newItemsLinks));
            }
        
    }
}