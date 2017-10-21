<?php

namespace Phf\Beslist;

use SoapClient;

class BeslistManager
{
    private $username;
    private $password;
    private $wsdl;
    private $client;
    private $serviceUrl = "http://www.beslist.nl";

    private $wsdlList = [
        "shops" => "/api/v2/wsdl/shops.wsdl",
        "reviews" => "/api/v2/wsdl/reviews.wsdl",
        "category" => "/api/v2/wsdl/category.wsdl",
        "products" => "/api/v2/wsdl/products.wsdl"
    ];

    public function __construct($username, $password, $wsdl)
    {
        $this->username = $username;
        $this->password = $password;
        $this->wsdl = $wsdl;
    }

    public function connect($function, $param)
    {
        ini_set("default_socket_timeout", 25);

        $this->client = new SoapClient($this->serviceUrl . $this->wsdlList[$this->wsdl], array(
            "cache_wsdl" => WSDL_CACHE_NONE,
            "login" => $this->username,
            "password" => $this->password,
            "soap_version" => SOAP_1_2
        ));

        $arResult = $this->client->__soapCall($function, [$param]);

        return $arResult;
    }

    public function categorySearch($strQuery = "")
    {
        return $this->connect("categorysearch", ["query" => $strQuery]);
    }

    public function categoryListing($catId)
    {
        return $this->connect("categorylisting", ["cat_id" => $catId]);
    }

    public function reviewSearch($param)
    {
        return $this->connect("reviewsearch", ["input" =>
                [
                    "reviewid" => (isset($param['reviewid'])) ? $param['reviewid'] : null,
                    "cat_id" => (isset($param['cat_id'])) ? $param['cat_id'] : null,
                    "item_id" => (isset($param['item_id'])) ? $param['item_id'] : null,
                    "offset" => (isset($param['offset'])) ? $param['offset'] : null,
                    "numresults" => (isset($param['numresults'])) ? $param['numresults'] : null
                ]
            ]
        );
    }

    public function filterOptions($catId)
    {
        return $this->connect("FilterOptions", ["cat_id" => $catId]);
    }

    function productShopSearch($param)
    {

        $return = $this->connect("productshopsearch", ["input" =>
                [
                    "link_id" => (isset($param['link_id'])) ? $param['link_id'] : null,
                    "query" => (isset($param['query'])) ? $param['query'] : "",
                    "shop_id" => (isset($param['shop_id'])) ? $param['shop_id'] : null,
                    "cat_id" => (isset($param['cat_id'])) ? $param['cat_id'] : null,
                    "offset" => (isset($param['offset'])) ? $param['offset'] : null,
                    "numresults" => (isset($param['numresults'])) ? $param['numresults'] : null,
                    "filter" => (isset($param['filter'])) ? $param['filter'] : array()
                ]
            ]
        );
        return $return;
    }

    public function productItemSearch($param)
    {
        return $this->connect("productitemsearch", ["input" =>
                [
                    "link_id" => (isset($param['link_id'])) ? $param['link_id'] : null,
                    "item_id" => $param['item_id'],
                    "cat_id" => $param['cat_id'],
                    "shopinfo" => (isset($param['shopinfo'])) ? $param['shopinfo'] : false,
                    "shopinfosort" => (isset($param['shopinfosort'])) ? $param['shopinfosort'] : "",
                    "itemspecs" => (isset($param['itemspecs'])) ? $param['itemspecs'] : ""
                ]
            ]
        );
    }

    public function productSearch($param)
    {
        return $this->connect("productsearch", ["input" =>
                [
                    "cat_id" => (isset($param['cat_id'])) ? $param['cat_id'] : null,
                    "link_id" => (isset($param['link_id'])) ? $param['link_id'] : "",
                    "query" => (isset($param['query'])) ? $param['query'] : "",
                    "querynote" => (isset($param['querynote'])) ? $param['querynote'] : "",
                    "offset" => (isset($param['offset'])) ? $param['offset'] : null,
                    "numresults" => (isset($param['numresults'])) ? $param['numresults'] : null,
                    "sortby" => (isset($param['sortby'])) ? $param['sortby'] : "",
                    "shopinfo" => (isset($param['shopinfo'])) ? $param['shopinfo'] : false,
                    "shopinfosort" => (isset($param['shopinfosort'])) ? $param['shopinfosort'] : "",
                    "filter" => (isset($param['filter'])) ? $param['filter'] : array()
                ]
            ]
        );
    }

    public function shopInfo($shopId)
    {
        return $this->connect("shopinfo", ["shopid" => $shopId]);
    }

    public function getShops($result)
    {
        $shops = array();

        if (isset($result->ProductItemSearchResult->productdata->shops->Shops)) {
            if (!is_array($result->ProductItemSearchResult->productdata->shops->Shops)) {
                $shops[] = $result->ProductItemSearchResult->productdata->shops->Shops;
            } else {
                $shops = $result->ProductItemSearchResult->productdata->shops->Shops;
            }
        }

        return $shops;
    }
}