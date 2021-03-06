<?php
/*
 * Console command which execute parsers
 */
class AutoRiaParserCommand extends CConsoleCommand
{
    /**
     * Run parsers
     */
    public function actionRun()
    {
        date_default_timezone_set("UTC");
        $site = Sites::model()->findByPk(1);
        $idSite = $site->id;

        // Generate filters
        $searchBegin = "/blocks_search_ajax/search/";
        $viewBegin = "/search/?target=search&";
        
        $models = AutoRia::model()->findAllByAttributes(array('active' => 1));

        foreach($models as $model)
        {
            $searchUri = $model->uri;
            $url = $site->url.$searchBegin . '?' . $searchUri;
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HEADER,         0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

            $response = curl_exec($ch);
            $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($status != 200)
                continue;
	    
            $result = CJSON::decode($response);
	    
            $result = $result['result']['search_result']['ids'];
	    
            $newItemsLinks = [];
            $existItems = Items::model()->findAllByAttributes(['id_site' => $idSite, 'id_search' => $model->id]);
            $existItems = array_values(CHtml::listData($existItems, 'id', 'id_item'));
            
            foreach($result as $id)
            {
                if (!in_array($id, $existItems))
                {
                    $item = new Items();
                    $item->id_site = $idSite;
                    $item->id_item = $id;
                    $item->id_search = $model->id;
                    $item->save();
                    $newItemsLinks[] = "http://auto.ria.com/blocks_search/view/auto/$id";
                }
            }

            if (!empty($newItemsLinks))
            {
                mail($model->emails, 
                    'AUTO AutoRia #' . $model->id, 
                    count($newItemsLinks) . " новых авто.\r\n" . implode("\r\n", $newItemsLinks) . "\r\n Search url: " . $site->url . $viewBegin . '&' . $searchUri);
            
            }
        }
    }

    protected function getData($url, $cookie)
    {
        $document = new DomDocument();
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER,         1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status != 200)
        {
            echo CJSON::encode(array('error' => $status, 'response' => $response, 'url' => $url));
            exit();
        }

        if (!@$document->loadHTML($response))
        {
            echo CJSON::encode(array('error'));
            exit();
        }

        $xpath = new DOMXPath($document);

        $trs = '//div[@class="au-deals-list"]/div';
        $nodes = $xpath->query($trs, $document);
        $result = array();
        for ($i = 1; $i < $nodes->length; $i++)
        {
            $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/small', $document)->item(0)->nodeValue;
            $resItem = array();
            if ($resXpath)
            {
                $resItem['time'] = $resXpath;
                $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[1]', $document)->item(0)->nodeValue;
                $resItem['price'] = $resXpath;
                $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[2]', $document)->item(0)->nodeValue;
                $resItem['sum'] = $resXpath;

                //$resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[3]/a/@data-bid-id', $document)->item(0)->nodeValue;
                //$bidid = (int) $resXpath + 1;
                //$url = 'http://minfin.com.ua/modules/connector/connector.php?action=auction-get-contacts&bid='.$bidid.'&r=true';
                //$ch = curl_init($url);
                //$postfields = htmlspecialchars('bid='.$resXpath.'&action=auction-get-contacts&r=true');
                //curl_setopt($ch, CURLOPT_HEADER,         0);
                //curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                //curl_setopt($ch, CURLOPT_POST, 3);
                //curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                //curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                //$response = curl_exec($ch);
                //$status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //curl_close($ch);
                //$response = CJSON::decode($response);
                //if (isset($response['data']))
                //{
                //    $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[3]', $document)->item(0)->nodeValue;
                //    $resItem['phone'] = str_replace('xxx-x', $response['data'], $resXpath);
                //}
        $resItem['phone'] = '';
                $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[4]', $document)->item(0)->nodeValue;
                $resItem['text'] = $resXpath;
                $result[] = $resItem;
            }
        }
        return $result;
    }

    public function actionMinfinbuy()
    {
        $allres = [];
        $datacache = '/home/fxweb-parsers/parsers/datacachebuy.txt';
        $url = 'http://minfin.com.ua/currency/auction/usd/buy/dnepropetrovsk/';
        $cookie = 'cookiebuy.txt';
        $result = $this->getData($url, $cookie);
        $allres['dnepropetrovsk'] = $result;

        $url = 'http://minfin.com.ua/currency/auction/usd/buy/kiev/';
        $result = $this->getData($url, $cookie);
        $allres['kiev'] = $result;

        $url = 'http://minfin.com.ua/currency/auction/usd/buy/vinnitsa/';
        $result = $this->getData($url, $cookie);
        $allres['vinnitsa'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/donetsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['donetsk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/zhitomir/';
        // $result = $this->getData($url, $cookie);
        // $allres['zhitomir'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/zaporozhye/';
        // $result = $this->getData($url, $cookie);
        // $allres['zaporozhye'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/ivano-frankovsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['ivano-frankovsk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/kirovograd/';
        // $result = $this->getData($url, $cookie);
        // $allres['kirovograd'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/lugansk/';
        // $result = $this->getData($url, $cookie);
        // $allres['lugansk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/lutsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['lutsk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/lvov/';
        // $result = $this->getData($url, $cookie);
        // $allres['lvov'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/nikolaev/';
        // $result = $this->getData($url, $cookie);
        // $allres['nikolaev'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/odessa/';
        // $result = $this->getData($url, $cookie);
        // $allres['odessa'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/poltava/';
        // $result = $this->getData($url, $cookie);
        // $allres['poltava'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/rovno/';
        // $result = $this->getData($url, $cookie);
        // $allres['rovno'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/sumy/';
        // $result = $this->getData($url, $cookie);
        // $allres['sumy'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/ternopol/';
        // $result = $this->getData($url, $cookie);
        // $allres['ternopol'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/uzhgorod/';
        // $result = $this->getData($url, $cookie);
        // $allres['uzhgorod'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/kharkov/';
        // $result = $this->getData($url, $cookie);
        // $allres['kharkov'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/kherson/';
        // $result = $this->getData($url, $cookie);
        // $allres['kherson'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/khmelnitskiy/';
        // $result = $this->getData($url, $cookie);
        // $allres['khmelnitskiy'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/cherkassy/';
        // $result = $this->getData($url, $cookie);
        // $allres['cherkassy'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/chernigov/';
        // $result = $this->getData($url, $cookie);
        // $allres['chernigov'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/chernovtsy/';
        // $result = $this->getData($url, $cookie);
        // $allres['chernovtsy'] = $result;

        $f = fopen($datacache, 'w+');
        fwrite($f, CJSON::encode($allres));
        fclose($f);
    }

    public function actionMinfinsell()
    {
        $allres = [];
        $cookie = 'cookiesell.txt';
        $datacache = '/home/fxweb-parsers/parsers/datacachesell.txt';
        $url = 'http://minfin.com.ua/currency/auction/usd/sell/dnepropetrovsk/';
        $result = $this->getData($url, $cookie);
        $allres['dnepropetrovsk'] = $result;

        $url = 'http://minfin.com.ua/currency/auction/usd/sell/kiev/';
        $result = $this->getData($url, $cookie);
        $allres['kiev'] = $result;

        $url = 'http://minfin.com.ua/currency/auction/usd/sell/vinnitsa/';
        $result = $this->getData($url, $cookie);
        $allres['vinnitsa'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/donetsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['donetsk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/zhitomir/';
        // $result = $this->getData($url, $cookie);
        // $allres['zhitomir'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/zaporozhye/';
        // $result = $this->getData($url, $cookie);
        // $allres['zaporozhye'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/ivano-frankovsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['ivano-frankovsk'] = $result;

        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/kirovograd/';
        // $result = $this->getData($url, $cookie);
        // $allres['kirovograd'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/lugansk/';
        // $result = $this->getData($url, $cookie);
        // $allres['lugansk'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/lutsk/';
        // $result = $this->getData($url, $cookie);
        // $allres['lutsk'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/lvov/';
        // $result = $this->getData($url, $cookie);
        // $allres['lvov'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/nikolaev/';
        // $result = $this->getData($url, $cookie);
        // $allres['nikolaev'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/odessa/';
        // $result = $this->getData($url, $cookie);
        // $allres['odessa'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/poltava/';
        // $result = $this->getData($url, $cookie);
        // $allres['poltava'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/rovno/';
        // $result = $this->getData($url, $cookie);
        // $allres['rovno'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/sumy/';
        // $result = $this->getData($url, $cookie);
        // $allres['sumy'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/ternopol/';
        // $result = $this->getData($url, $cookie);
        // $allres['ternopol'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/uzhgorod/';
        // $result = $this->getData($url, $cookie);
        // $allres['uzhgorod'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/kharkov/';
        // $result = $this->getData($url, $cookie);
        // $allres['kharkov'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/kherson/';
        // $result = $this->getData($url, $cookie);
        // $allres['kherson'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/khmelnitskiy/';
        // $result = $this->getData($url, $cookie);
        // $allres['khmelnitskiy'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/cherkassy/';
        // $result = $this->getData($url, $cookie);
        // $allres['cherkassy'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/chernigov/';
        // $result = $this->getData($url, $cookie);
        // $allres['chernigov'] = $result;
        // 
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/chernovtsy/';
        // $result = $this->getData($url, $cookie);
        // $allres['chernovtsy'] = $result;

        $f = fopen($datacache, 'w+');
        fwrite($f, CJSON::encode($allres));
        fclose($f);
    }

    public function actionBanks()
    {
        $datacache = '/home/fxweb-parsers/parsers/banks.txt';
        $document = new DomDocument();
        $url = 'http://minfin.com.ua/currency/banks/usd/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER,         0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status != 200)
        {
            echo CJSON::encode(array('error'));
            exit();
        }

        if (!@$document->loadHTML($response))
        {
            echo CJSON::encode(array('error'));
            exit();
        }

        $xpath = new DOMXPath($document);
        $trs = '//*[@id="smTable"]/tbody/tr';
        $nodes = $xpath->query($trs, $document);
        $result = array();
        for ($i = 1; $i < $nodes->length; $i++)
        {
            $resXpath = @$xpath->query($trs.'['.$i.']'.'/td[1]/a', $document)->item(0)->nodeValue;
            $resItem = array();
            if ($resXpath)
            {
                $resItem['name'] = $resXpath;
                $resXpath = @$xpath->query($trs.'['.$i.']'.'/td[2]', $document)->item(0)->nodeValue;
                $resItem['buy'] = trim($resXpath);
                $resXpath = @$xpath->query($trs.'['.$i.']'.'/td[4]', $document)->item(0)->nodeValue;
                $resItem['sell'] = trim($resXpath);
                $result[] = $resItem;
            }
        }
        $f = fopen($datacache, 'w+');
        fwrite($f, CJSON::encode($result));
        fclose($f);
    }
}