<?php

class ApiController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout=false;

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow',  // allow all users
                'users'=>array('*'),
            ),
        );
    }

    private function parse($url)
    {
        $cookie = 'cookie.txt';
        $document = new DomDocument();
        
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER,         0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
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

                $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[3]/a/@data-bid-id', $document)->item(0)->nodeValue;
                $bidid = (int) $resXpath + 1;
                $url = 'http://minfin.com.ua/modules/connector/connector.php?action=auction-get-contacts&bid='.$bidid.'&r=true';
                $ch = curl_init($url);
                
                $postfields = htmlspecialchars('bid='.$resXpath.'&action=auction-get-contacts&r=true');
                curl_setopt($ch, CURLOPT_HEADER,         0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                curl_setopt($ch, CURLOPT_POST, 3);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
                curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
                $response = curl_exec($ch);
                $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                $response = CJSON::decode($response);
                if (isset($response['data']))
                {
                    $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[3]', $document)->item(0)->nodeValue;
                    $resItem['phone'] = str_replace('xxx-x', $response['data'], $resXpath);
                }

                $resXpath = @$xpath->query($trs.'['.$i.']'.'/div/span[4]', $document)->item(0)->nodeValue;
                $resItem['text'] = $resXpath;
                $result[] = $resItem;
            }
        }
        return $result;
    }

    public function actionBuy()
    {
        // $url = 'http://minfin.com.ua/currency/auction/usd/buy/dnepropetrovsk/';
        // $result = $this->parse($url);
        // echo CJSON::encode($result);
        $f = fopen('datacachebuy.txt', 'r');
        $data = '';
        while($dd = fread($f, 1000))
        {
            $data .= $dd;
        }
        echo $data;
    }

    public function actionSell()
    {
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/dnepropetrovsk/';
        // $result = $this->parse($url);
        // echo CJSON::encode($result);
        $f = fopen('datacachesell.txt', 'r');
        $data = '';
        while($dd = fread($f, 1000))
        {
            $data .= $dd;
        }
        echo $data;
    }

    public function actionBanks()
    {
        // $url = 'http://minfin.com.ua/currency/auction/usd/sell/dnepropetrovsk/';
        // $result = $this->parse($url);
        // echo CJSON::encode($result);
        $f = fopen('banks.txt', 'r');
        $data = '';
        while($dd = fread($f, 1000))
        {
            $data .= $dd;
        }
        echo $data;
    }
}
?>