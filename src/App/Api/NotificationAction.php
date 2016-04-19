<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14.04.16
 * Time: 15:27
 */

namespace App\Api;


use DTS\eBaySDK\ReturnManagement\Enums\NotificationEventNameType;
use DTS\eBaySDK\Trading\Enums\NotificationEventPropertyNameCodeType;
use DTS\eBaySDK\Trading\Enums\NotificationEventTypeCodeType;
use DTS\eBaySDK\Trading\Enums\NotificationRoleCodeType;
use DTS\eBaySDK\Trading\Services\TradingService;
use DTS\eBaySDK\Trading\Types\AbstractResponseType;
use DTS\eBaySDK\Trading\Types\ApplicationDeliveryPreferencesType;
use DTS\eBaySDK\Trading\Types\GetNotificationPreferencesRequestType;
use DTS\eBaySDK\Trading\Types\GetNotificationsUsageRequestType;
use DTS\eBaySDK\Trading\Types\NotificationEventPropertyType;
use DTS\eBaySDK\Trading\Types\NotificationDetailsType;
use DTS\eBaySDK\Trading\Types\NotificationMessageType;
use DTS\eBaySDK\Trading\Types\NotificationUserDataType;
use DTS\eBaySDK\Trading\Types\SetNotificationPreferencesRequestType;
use DTS\eBaySDK\Trading\Types\CustomSecurityHeaderType;

use DTS\eBaySDK\Trading\Types\PaginationType;
use DTS\eBaySDK\Trading\Types\ItemListCustomizationType;
use DTS\eBaySDK\Trading\Enums\DetailLevelCodeType;

use DTS\eBaySDK\Constants\SiteIds;

use DTS\eBaySDK\Trading\Types\UserIdPasswordType;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use zaboy\res\DataStores\DataStoresAbstract;
use zaboy\res\DataStores\DataStoresInterface;
use Zend\Db\Sql\Ddl\Column\Blob;
use Zend\Diactoros\Response\JsonResponse;
use DTS\eBaySDK\Parser\XmlParser;

class NotificationAction
{
    private $ebayConfig;
    /** @var  DataStoresInterface */
    private $store;

    public function __construct($ebayConfig, DataStoresInterface $store)
    {
        $this->ebayConfig = $ebayConfig;
        $this->store = $store;
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $xml = file_get_contents('php://input');

        $xml = file_get_contents('php://input');
        $xml = preg_replace('/[\n\r]/', '', $xml);
        $xml = preg_replace('/>\s+/', '>', $xml);

        $rootBodyClass = 'DTS\eBaySDK\Trading\Types\AbstractResponseType';
        $parserBody = new XmlParser($rootBodyClass);

        $body = mb_strstr($xml, "<soapenv:Body>", false);
        $body = trim($body, "<soapenv:Body>");
        $body = mb_strstr($body, "</soapenv:Body>", true);
        $body = '<' . $body;
        /** @var AbstractResponseType $notification */
        $notification = $parserBody->parse($body);

        $notification->NotificationSignature = mb_strstr($xml, '<ebl:NotificationSignature xmlns:ebl="urn:ebay:apis:eBLBaseComponents">', false);
        $notification->NotificationSignature = trim($notification->NotificationSignature, '<ebl:NotificationSignature xmlns:ebl="urn:ebay:apis:eBLBaseComponents">');
        $notification->NotificationSignature = mb_strstr($notification->NotificationSignature, "</ebl:NotificationSignature>", true);

        $timestamp = mb_strstr($body, "<Timestamp>", false);
        $timestamp = trim($timestamp, "<Timestamp>");
        $timestamp = mb_strstr($timestamp, "</Timestamp>", true);

        if ($this->CalculationSignature($timestamp) !== $notification->NotificationSignature) {
            throw new \Exception("Not Equalse signature", 403);
        }

        $item = [
            'add_date' => $notification->Timestamp->format("Y-m-d h:i:s"),
            'soapaction' => $notification->NotificationEventName,
            'data' => $body
        ];

        $this->store->create($item);


        return $response->withStatus(200);
    }
    private function CalculationSignature($timestamp){
        $signature = base64_encode(pack('H*', md5("{$timestamp}{$this->ebayConfig['credentials']['devId']}{$this->ebayConfig['credentials']['appId']}{$this->ebayConfig['credentials']['certId']}")));
        return $signature;
    }

}

