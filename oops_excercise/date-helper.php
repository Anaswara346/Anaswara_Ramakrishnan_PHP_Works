<?php 

class DateHelper {
    public static function age($birthDate) {
        return (new DateTime($birthDate))->diff(new DateTime())->y;
    }

    public static function dayName($date){
        return date("1", strtotime($date));
    }

    public static function daysBetween($d1, $d2) {
        return (new DateTime($d1))->diff(new DateTime($d2))->days;
    }
}

echo "Age : " . DateHelper::age("2000-05-15") . "\n";
echo "Day : " . DateHelper::dayName("2026-01-26") . "\n";
echo "Days between : " . DateHelper::daysBetween("2026-01-01", "2026-03-01");