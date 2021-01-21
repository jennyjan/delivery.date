<?php

namespace Delivery\Date\Provider;
use \Datetime;

class DeliveryDateProvider
{
    private $date;

    public function __construct()
    {                
        $this->date = new DateTime();        
    }

    public function getPrintDate($currentDate) 
    {    
        $monthsList = array(".01." => "января", ".02." => "февраля", 
          ".03." => "марта", ".04." => "апреля", ".05." => "мая", ".06." => "июня", 
          ".07." => "июля", ".08." => "августа", ".09." => "сентября",
          ".10." => "октября", ".11." => "ноября", ".12." => "декабря");
                
        $monthChange = $currentDate->format('.m.');
        return $currentDate->format("j ") . $monthsList[$monthChange];
    }
        
    public function calculateDeliveryDate($elementQuantity, $deliveryShoulder)
    {    
        $currentDate = $this->date;        
        $currentDayWeek = $currentDate->format('w');

        if ($elementQuantity > 0) {    
            /* если суббота +2 дня */
            if($currentDayWeek == 6) {            
                $currentDate =  $currentDate->modify('+2 day');    
                return "Доставим ".$this->getPrintDate($currentDate); 
            } else {
                $currentDate = 'Доставим завтра';
                return $currentDate;
            } 
        } else {
            if ($deliveryShoulder > 0 && $deliveryShoulder < 14) {
                $deliveryShoulder++;
                $currentDateClon = clone $currentDate;
                $futureDate = $currentDateClon->modify('+'.$deliveryShoulder.' day');    
                $futureDayWeek = $futureDate->format('w');
                if($futureDayWeek == 0) {
                    $deliveryShoulder++;
                } elseif ($futureDayWeek == 6) { 
                    $deliveryShoulder += 2;    
                }
                $currentDate =  $currentDate->modify('+'.$deliveryShoulder.' day');
            } else {
                /** вс +12 дней
                  * сб +13 дней
                  * остальные дни +14 дней
                  */
                if($currentDayWeek == 0) {                
                    $currentDate =  $currentDate->modify('+12 day');            
                } elseif ($currentDayWeek == 6) {                
                    $currentDate =  $currentDate->modify('+13 day');        
                } else {                
                    $currentDate =  $currentDate->modify('+14 day');        
                }
            }
            return "Доставим до ".$this->getPrintDate($currentDate);       
        }    
    }    
}