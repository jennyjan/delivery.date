<?php
namespace Delivery\Date\Supply;

use Delivery\Date\Provider\DeliveryDateProvider;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class ProductSupply
{
    private $itemQuantity;
    private $mfrPropertyId;
    private $arMfrNotAvailable;
    private $arSectionDeliveryIn3days;
    private $sectionId;
    
    public function __construct($item)
    {
        $this->itemQuantity = $item["CATALOG_QUANTITY"];
        $this->mfrPropertyId = $item["DISPLAY_PROPERTIES"]["Manufacturer"]["VALUE"];
        $this->arMfrNotAvailable = $item["MANUFACTURE_ID_NOT_AVAILABLE"];
        $this->arSectionDeliveryIn3days = $item["SECTION_ID_AVAILABLE_IN_3_DAYS"];
        $this->sectionId = $item["IBLOCK_SECTION_ID"];
        $this->active = $item["ACTIVE"];
        $this->deliveryShoulder = $item["PROPERTIES"]["PLECHO_DOSTAVKI"]["VALUE"];
    }
    
    private function isAvailable()
    {
        if ($this->itemQuantity > 0) {
            return true;
        }
        return false;
    }
    
    private function isActive()
    {
        if ($this->active == 'Y') {
            return true;
        } else {
            return false;
        }
    }
    
    private function isFillDeliveryShoulder()
    {
        if (!empty($this->deliveryShoulder) && $this->deliveryShoulder > 0) {
            return true;
        }
        return false;
    }
    
    public function getText()
    {
        $productSupplyText = '';
        if ($this->isActive()) {
            if ($this->isAvailable()) {  
                $productSupplyText = Loc::getMessage("AVAILABLE");
            } else {
                if (in_array($this->mfrPropertyId, $this->arMfrNotAvailable)) {
                    $productSupplyText = Loc::getMessage("NOT_AVAILABLE");
                } elseif (in_array($this->sectionId, $this->arSectionDeliveryIn3days)) {
                    $productSupplyText = Loc::getMessage("DELIVERY_IN_3_DAYS");
                } else {
                    $deliveryDate = new DeliveryDateProvider();
                    $productSupplyText = $deliveryDate->calculateDeliveryDate($this->itemQuantity, null);
                }
            }
        } else {
            $productSupplyText = Loc::getMessage("NOT_IN_SALE");
        }    
        return $productSupplyText;
    }
}