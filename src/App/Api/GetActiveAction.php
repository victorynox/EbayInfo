<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.04.16
 * Time: 14:03
 */

namespace App\Api;

use DTS\eBaySDK\Trading\Services\TradingService;
use DTS\eBaySDK\Trading\Types\GetMyeBaySellingRequestType;
use DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType;
use DTS\eBaySDK\Trading\Types\PaginationType;
use DTS\eBaySDK\Trading\Types\ItemListCustomizationType;
use DTS\eBaySDK\Trading\Enums\DetailLevelCodeType;
use DTS\eBaySDK\Constants\SiteIds;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Zend\Diactoros\Response\JsonResponse;

class GetActiveAction
{
    private $service;

    private $req;

    public function __construct($ebayConfig)
    {
        $this->service = new TradingService(
            [
                'siteId' => SiteIds::MOTORS,
                'apiVersion' => $ebayConfig['tradingApiVersion']
            ]
        );
        $this->req = new GetMyeBaySellingRequestType();

        $this->req->RequesterCredentials = new CustomSecurityHeaderType();
        $this->req->RequesterCredentials->eBayAuthToken = $ebayConfig['credentials']['userToken'];

        $this->req->DetailLevel = [DetailLevelCodeType::C_ITEM_RETURN_ATTRIBUTES];

        $this->req->ActiveList = new ItemListCustomizationType();
        $this->req->ActiveList->Include = true;
        $this->req->ActiveList->Pagination = new PaginationType();
        $this->req->ActiveList->Pagination->EntriesPerPage = 200;

    }

    public function __invoke(Request $request, Response $response, callable $next){


        $pageNumber = 1;
        do{
            $this->req->ActiveList->Pagination->PageNumber = $pageNumber;
            $resp = $this->service->getMyeBaySelling($this->req);

            if(isset($resp->Errors)){
                throw new \Exception("");
            }

            if($resp->Ack !== 'Failure' && isset($resp->ActiveList)){
                foreach ($resp->ActiveList->ItemArray->Item as $item){
                    
                }
            }

        }while(isset($this->req->ActiveList) && $pageNumber <= $resp->ActiveList->PaginationResult->TotalNumberOfPages);

        return new JsonResponse([]);
    }
}