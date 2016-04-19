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
    }

    public function __invoke(Request $request, Response $response, callable $next)
    {
        $xml = file_get_contents('php://input');

        $xml = file_get_contents('php://input');
        $xml = preg_replace('/[\n\r]/', '', $xml);
        $xml = preg_replace('/>\s+/', '>', $xml);

        $rootClass = 'DTS\eBaySDK\Trading\Types\AbstractResponseType';
        $parser = new XmlParser($rootClass);

        $xml = mb_strstr($xml, "<soapenv:Body>", false);
        $xml = trim($xml, "<soapenv:Body>");
        $xml = mb_strstr($xml, "<soapenv:Body>", true);

        /** @var AbstractResponseType $notification */
        $notification = $parser->parse($xml);

        $item = [
            'add_date' => $notification->Timestamp->format("YY-mm-ddThh:ii:ss"),
            'soapaction' => $notification->NotificationEventName,
            'data' => $notification->toRequestXml(),
        ];

        $this->store->create($item);

        return $response->withStatus(200);
    }
}

