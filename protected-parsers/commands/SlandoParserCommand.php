<?php
/*
 * Console command which execute parsers
 */
class SlandoParserCommand extends CConsoleCommand
{
    /**
     * Run parsers
     */
    public function actionRun()
    {
        date_default_timezone_set("UTC");
        $document = new DomDocument();
        $site = Sites::model()->findByPk(2);
        $idSite = $site->id;

        // Generate filters
        $searchBegin = "/ajax/search/list/";
        $models = Slando::model()->findAllByAttributes(array('active' => 1));

        foreach($models as $model)
        {
            $searchUri = $model->uri;
            $url = $site->url.$searchBegin;
            $ch = curl_init($url);
            
            curl_setopt($ch, CURLOPT_HEADER,         0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $searchUri);

            $response = curl_exec($ch);
            $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status != 200)
                continue;

            if (!@$document->loadHTML($response))
                continue;

            $xpath = new DOMXPath($document);

            $trs = '//*[@id="offers_table"]/tbody/tr';
            $linkPath = '/td/table/tbody/tr/td[3]/h3/a/@href';
            $links = array();

            $nodes = $xpath->query($trs, $document);
            
            for ($i = 1; $i < $nodes->length; $i++)
            {
                $resXpath = $xpath->query($trs.'['.$i.']'.$linkPath, $document)->item(0)->nodeValue;
                $links[md5($resXpath)] = $resXpath;
            }

            $newItemsLinks = [];
            $existItems = Items::model()->findAllByAttributes(['id_site' => $idSite, 'id_search' => $model->id]);
            $existItems = array_values(CHtml::listData($existItems, 'id', 'id_item'));
            
            foreach($links as $id => $val)
            {
                if (!in_array($id, $existItems))
                {
                    $item = new Items();
                    $item->id_site = $idSite;
                    $item->id_item = $id;
                    $item->id_search = $model->id;
                    $item->save();
                    $newItemsLinks[] = $val;
                }
            }
            
            if (!empty($newItemsLinks))
            {
                mail($model->emails, 
                    'AUTO Slando #' . $model->id, 
                    count($newItemsLinks) . " новых авто.\r\n" . implode("\r\n", $newItemsLinks));
            }
        }
    }
}