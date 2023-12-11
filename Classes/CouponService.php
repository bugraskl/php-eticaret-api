<?php
/**
 * 8.12.2023
 * 15:37
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

namespace CouponService;

class CouponService {
    /**
     * @param $couponCode
     * @return bool
     */
    public function validateCouponCode($couponCode) {
        return $this->validateCouponFormat($couponCode);
    }

    /**
     * Kupon kodu kontrol algoritması
     * İki sayı arasında en az 3 adet 'T' karakteri kontrolü
     * @param $couponCode
     * @return bool
     */
    private function validateCouponFormat($couponCode) {
        $pattern = '/[0-9]TT{3,}[0-9]/';
        return preg_match($pattern, $couponCode) === 1;
    }
}