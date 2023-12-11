<?php
/**
 * 8.12.2023
 * 15:30
 * Prepared by Buğra Şıkel @bugraskl
 * https://www.bugra.work/
 */

namespace OrderService;

use CouponService\CouponService;
use EmailService\EmailService;
use UserService\UserService;

class OrderService
{
    /**
     * Sipariş oluşturma işlemi
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data)
    {

        $gift = null;
        $discountAmount = 0;
        $discountRate = 0;

        // Ürün ve sipariş geçerliliği kontrolü
        $orderValidation = $this->checkOrder($data['products'] ?? [], $data['orderCode'] ?? null);
        if ($orderValidation !== true) {
            return $orderValidation;
        }

        // Ürün stok kontrolü
        $isStockAvailable = $this->checkProductStock($data['products']);
        if ($isStockAvailable !== true) {
            return $isStockAvailable;
        }

        if ($this->hasOrderCode($data)) {
            return $this->updateOrder($data);
        }

        // Toplam sipariş tutarına göre kargo bedeli hesaplamaları
        $totalAmount = $this->calculateTotalAmount($data['products']);
        $shippingFee = $totalAmount >= 500 ? 0 : 54.99;

        // Kupon kodu kontrolü
        $couponService = new CouponService();
        $couponCode = $data['couponCode'] ?? null;
        if ($couponCode) {
            $isCouponValid = $couponService->validateCouponCode($couponCode);
            if (!$isCouponValid) {
                return ['error' => 'Kupon kodu hatalı.'];
            }

            // Sepet tutarına göre indirimler
            $discounts = $this->applyDiscounts($totalAmount);
            $discountAmount = $discounts['discountAmount'];
            $gift = $discounts['gift'];
            $discountRate = $discounts['discountRate'];
        }

        // Sipariş kodu oluşturma
        $orderCode = $this->orderCodeCreator();

        global $db;
        $insert = $db->insert('orders')->set(array(
            'order_code' => $orderCode,
            'total_amount' => $totalAmount,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'discount_rate' => $discountRate,
            'coupon_code' => $couponCode,
            'gift' => $gift,
            'last_total' => $totalAmount + $shippingFee - $discountAmount,
            'products' => json_encode($data['products']),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ));
        if ($insert) {
            return [
                'status' => true,
                'message' => 'Sipariş oluşturuldu.',
                'orderCode' => $orderCode,
                'isStockAvailable' => $isStockAvailable,
                'totalAmount' => $totalAmount,
                'shippingFee' => $shippingFee,
                'discountAmount' => $discountAmount,
                'discountRate' => $discountRate,
                'couponCode' => $couponCode,
                'gift' => $gift,
                'lastTotal' => $totalAmount + $shippingFee - $discountAmount,
            ];
        } else {
            return ['status' => false, 'error' => 'Sipariş oluşturulamadı.'];
        }

    }

    /**
     * Ürün stok kontrolü
     *
     * @param array $products
     * @return array|bool
     */
    public function checkProductStock($products)
    {
        global $db;
        foreach ($products as $product) {
            $productId = $product['product_id'];
            $control = $db->from('products')->select('stock_quantity')->where('product_id', $productId)->first();

            if ($product['quantity'] > $control['stock_quantity']) {
                return ['status' => false, 'message' => $productId . ' idli ürün talep edilen adette stokta mevcut değil.'];
            }
        }

        return true;
    }

    /**
     * Toplam sipariş tutarını hesapla
     *
     * @param array $products
     * @return int
     */
    private function calculateTotalAmount($products)
    {
        $totalAmount = 0;
        global $db;

        foreach ($products as $product) {
            $productPrice = $db->from('products')->select('price')->where('product_id', $product['product_id'])->first()['price'];
            $totalAmount += $productPrice * $product['quantity'];
        }

        return $totalAmount;
    }

    /**
     * İndirimleri uygula
     *
     * @param int $totalAmount
     * @return array
     */
    private function applyDiscounts($totalAmount)
    {
        $discountedAmount = 0;
        $discountedRate = 0;
        $gift = null;

        if ($totalAmount > 3000) {
            $discountedRate = 25;
            $discountedAmount = $totalAmount * 0.25;
            $gift = "1 KG kahve";
        } elseif ($totalAmount > 2000) {
            $discountedRate = 20;
            $discountedAmount = $totalAmount * 0.2;
        } elseif ($totalAmount > 1500) {
            $discountedRate = 15;
            $discountedAmount = $totalAmount * 0.15;
        } elseif ($totalAmount > 1000) {
            $discountedRate = 10;
            $discountedAmount = $totalAmount * 0.1;
        }

        return [
            'discountRate' => $discountedRate,
            'discountAmount' => $discountedAmount,
            'gift' => isset($gift) ? $gift : null,
        ];
    }

    /**
     * Sipariş geçerliliği kontrolü
     *
     * @param array $products
     * @param null $orderCode
     * @return array|bool
     */
    private function checkOrder(array $products, $orderCode = null)
    {
        if (empty($orderCode)) {
            foreach ($products as $product) {
                if ($product['quantity'] <= 0) {
                    return ['status' => false, 'message' => 'Ürünler eksik girilmiş.'];
                }
            }
        }
        if (empty($products)) {
            return ['status' => false, 'message' => 'Ürünler eksik girilmiş.'];
        } else {
            return true;
        }
    }

    /**
     * Siparişi güncelleme işlemi
     *
     * @param array $data
     * @return array
     */
    public function updateOrder(array $data)
    {
        $gift = null;
        $discountAmount = 0;
        $discountRate = 0;

        // $products array'i içinde bir orderCode bulunmuyorsa işlem yapma
        if (!$this->hasOrderCode($data)) {
            return ['status' => false, 'message' => 'Sipariş kodu bulunamadı.'];
        }

        // Sipariş detaylarını getir
        $orderCode = $data['orderCode'];
        $orderDetails = $this->getOrderDetails($orderCode);

        // Eğer sipariş bulunamadıysa hata mesajı döndür
        if (!$orderDetails) {
            return ['status' => false, 'message' => 'Sipariş detayları bulunamadı.'];
        }

        $control = $this->orderStatusControl($orderCode);
        if ($control == 1) {
            return ['status' => false, 'error' => 'Tamamlanmış sipariş güncellenemez.'];
        }

        $oldProducts = json_decode($orderDetails['products'], true);
        $newProducts = $oldProducts; // Sepetin sıfırlanmaması için yeni ürünleri eski ürünlerle başlat

        foreach ($data['products'] as $product) {
            $productExists = false;

            foreach ($newProducts as $key => $newProduct) {
                if ($product['product_id'] == $newProduct['product_id']) {
                    $newProducts[$key]['quantity'] += $product['quantity'];

                    // Eğer miktar negatifse, ürünü tamamen kaldır
                    if ($newProducts[$key]['quantity'] <= 0) {
                        unset($newProducts[$key]);
                    }

                    $productExists = true;
                    break;
                }
            }

            if (!$productExists && $product['quantity'] > 0) {
                $newProducts[] = $product;
            }
        }

        // Eğer tüm ürünler eksilerek yok olmuşsa, siparişi sil
        if (empty($newProducts)) {
            return $this->deleteOrder($orderCode);
        }

        $isStockAvailable = $this->checkProductStock($newProducts);
        if ($isStockAvailable !== true) {
            return $isStockAvailable;
        }

        // Toplam sipariş tutarına göre kargo bedeli hesaplamaları
        $totalAmount = $this->calculateTotalAmount($newProducts);
        $shippingFee = $totalAmount >= 500 ? 0 : 54.99;

        // Kupon kodu kontrolü
        $couponService = new CouponService();

        if (isset($data['couponCode']) && !empty($data['couponCode'])) {
            $isCouponValid = $couponService->validateCouponCode($data['couponCode']);
            if (!$isCouponValid) {
                return ['error' => 'Kupon kodu hatalı.'];
            }

            // Sepet tutarına göre indirimler
            $discounts = $this->applyDiscounts($totalAmount);
            $discountAmount = $discounts['discountAmount'];
            $gift = $discounts['gift'];
            $discountRate = $discounts['discountRate'];
        }

        global $db;
        $update = $db->update('orders')->where('order_code', $orderCode)->set(array(
            'total_amount' => $totalAmount,
            'shipping_fee' => $shippingFee,
            'discount_amount' => $discountAmount,
            'discount_rate' => $discountRate,
            'coupon_code' => $data['couponCode'] ?? null,
            'gift' => $gift,
            'last_total' => $totalAmount + $shippingFee - $discountAmount,
            'products' => json_encode($newProducts),
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        if ($update) {
            return [
                'status' => true,
                'message' => 'Sipariş güncellendi.',
                'orderCode' => $orderCode,
                'isStockAvailable' => $isStockAvailable,
                'totalAmount' => $totalAmount,
                'shippingFee' => $shippingFee,
                'discountAmount' => $discountAmount,
                'discountRate' => $discountRate,
                'couponCode' => $data['couponCode'] ?? null,
                'gift' => $gift,
                'lastTotal' => $totalAmount + $shippingFee - $discountAmount,
            ];
        } else {
            return ['status' => false, 'error' => 'Sipariş güncellenirken bir problem oluştu.'];
        }
    }

    /**
     * $products array'inde orderCode kontrolü
     *
     * @param array $data
     * @return bool
     */
    private function hasOrderCode(array $data)
    {
        global $db;
        if (isset($data['orderCode']) && !empty($data['orderCode'])) {
            $orderCheck = $db->from('orders')->where('order_code', $data['orderCode'])->first();
            if (!$orderCheck) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Sipariş detaylarını getirme
     *
     * @param string $orderCode
     * @return array|null
     */
    public function getOrderDetails($orderCode)
    {
        global $db;
        return $db->from('orders')->where('order_code', $orderCode)->first();
    }

    private function orderCodeCreator()
    {
        global $db;
        $orderCode = rand(1000000, 9999999);
        while ($db->from('orders')->where('order_code', $orderCode)->first()) {
            $orderCode = rand(1000000, 9999999);
        }
        return $orderCode;
    }

    private function orderStatusControl($orderCode)
    {
        global $db;
        return $db->from('orders')->where('order_code', $orderCode)->first()['status'];
    }

    /**
     * Siparişi silme işlemi
     *
     * @param string $orderCode
     * @return array
     */
    public function deleteOrder($orderCode)
    {
        global $db;
        $control = $this->orderStatusControl($orderCode);
        if ($control == 1) {
            return ['status' => false, 'error' => 'Tamamlanmış sipariş silinemez.'];
        }
        $delete = $db->delete('orders')->where('order_code', $orderCode)->done();

        if ($delete) {
            return ['status' => true, 'message' => 'Sipariş silindi.'];
        } else {
            return ['status' => false, 'error' => 'Sipariş silinirken bir problem oluştu.'];
        }
    }

    public function completeOrder($orderCode, $userId)
    {
        if (!$orderCode) {
            return ['status' => false, 'error' => 'Sipariş kodu eksik.'];
        }
        if (!$userId) {
            return ['status' => false, 'error' => 'Kullanıcı id eksik.'];
        }
        if ($this->orderStatusControl($orderCode) == 1) {
            return ['status' => false, 'error' => 'Sipariş zaten tamamlandı.'];
        }
        $user = new UserService();
        $userInfo = $user->getUserInfo($userId);
        if (!$userInfo) {
            return ['status' => false, 'error' => 'Kullanıcı bilgisi bulunamadı.'];
        }
        global $db;
        $update = $db->update('orders')->where('order_code', $orderCode)->set(array(
            'status' => '1',
            'user_id' => $userId,
        ));
        if ($update) {
            $email = new EmailService();
            $email->sendMail('Siparişiniz Oluşturuldu', 'Siparişiniz başarıyla oluşturuldu. Takip numaranız ' . $orderCode, $userInfo['email']);
            return ['status' => true, 'message' => 'Sipariş tamamlandı.'];
        } else {
            return ['status' => false, 'error' => 'Sipariş tamamlanırken bir problem oluştu.'];
        }
    }
}
