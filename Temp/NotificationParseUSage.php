<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 18.04.16
 * Time: 16:15
 */
function a($request)
{
    $xml = file_get_contents('php://input');
    $xml = preg_replace('/[\n\r]/', '', $xml);
    $xml = preg_replace('/>\s+/', '>', $xml);

    $soapactionArr = [
        "AskSellerQuestion" => "GetMemberMessagesResponseType",
        "AuctionCheckoutComplete" => "GetItemTransactionsResponseType",
        "BestOffer" => "GetBestOffersResponseType",
        "BestOfferDeclined" => "GetBestOffersResponseType",
        "BestOfferPlaced" => "GetBestOffersResponseType",
        "BidPlaced" => "GetItemResponseType",
        "BidReceived" => "GetItemResponseType",
        "CheckoutBuyerRequestsTotal" => "GetItemTransactionsResponseType",
        "Checkout" => "GetItemTransactionsResponseType",
        "CounterOfferReceived" => "GetBestOffersResponseType",
        "EndOfAuction" => "GetItemTransactionsResponseType",
        "Feedback" => "GetFeedbackResponseType",
        "FeedbackLeft" => "GetFeedbackResponseType",
        "FeedbackReceived" => "GetFeedbackResponseType",
        "FeedbackStarChanged" => "GetFeedbackResponseType",
        "FixedPriceTransaction" => "GetItemTransactionsResponseType",
        "INR" => "GetDisputeResponseType",
        "ItemNotReceived" => "GetDisputeResponseType",
        "ItemAddedToBidGroup" => "GetItemResponseType",
        "ItemAddedToWatchList" => "GetItemResponseType",
        "ItemClosed" => "GetItemResponseType",
        "ItemListed" => "GetItemResponseType",
        "ItemLost" => "GetItemResponseType",
        "ItemRemovedFromBidGroup" => "GetItemResponseType",
        "ItemRemovedFromWatchList" => "GetItemResponseType",
        "ItemRevised" => "GetItemResponseType",
        "ItemSold" => "GetItemResponseType",
        "ItemUnsold" => "GetItemResponseType",
        "ItemWon" => "GetItemResponseType",
        "MyMessages" => "GetMyMessagesResponseType",
        "OutBid" => "GetItemResponseType",
        "SecondChanceOffer" => "GetItemResponseType",
        "BuyerResponseDispute" => "GetDisputeResponseType",
        "SellerClosedDispute" => "GetDisputeResponseType",
        "SellerOpenedDispute" => "GetDisputeResponseType",
        "SellerRespondedToDispute" => "GetDisputeResponseType",
        "TokenRevocation" => "GetTokenStatusResponseType",
        "WatchedItemEndingSoon" => "GetIteResponseTypem"
    ];

    $pattern = '/^([\w\W]+\/)([\w]*)$/';
    $soapaction = trim($request->getHeaderLine("SOAPACTION"), '\\\"');
    $matcher = [];

    if (!preg_match($pattern, $soapaction, $matcher)) {
        $error = 1;
    } else {
        $notificationEventName = $matcher[2];
        if (isset($soapactionArr[$notificationEventName])) {
            $notificationBodyObjectClassName = $soapactionArr[$matcher[2]];
            $rootClass = 'DTS\eBaySDK\Trading\Types\\' . $notificationBodyObjectClassName;
            $parser = new XmlParser($rootClass);

            $notificationBodyName = preg_replace('/Type/', '', $notificationBodyObjectClassName);

            $xml = mb_strstr($xml, $notificationBodyName, false);
            $xml = trim($xml, $notificationBodyName);
            $xml = mb_strstr($xml, $notificationBodyName, true);
            $xml = "<" . $notificationBodyName . $xml . $notificationBodyName . ">";

            /** @var AbstractResponseType $notification */
            $notification = $parser->parse($xml);
        } else {
            $error = 2;
        }
    }
}

function b($request)
{
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

}