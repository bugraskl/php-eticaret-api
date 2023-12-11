# API Kullanım Kılavuzu

Bu API, sipariş işlemleri üzerinde CRUD (Create, Read, Update, Delete) operasyonları gerçekleştirmek üzere tasarlanmıştır. Aşağıda API'nin kullanımı ve kurulumu hakkında bilgiler bulunmaktadır.

## Kurulum

1. `Config/config.php` dosyasını düzenleyerek gerekli veritabanı bağlantı bilgilerinizi ayarlayın.
2. Projeyi sunucunuza yükleyin.
3. Sql dosyası içerisinde yer alan .sql uzantılı veritabanı dosyasını yeni veritabanı oluşturmak için kullanın.
4. API'yi kullanmaya başlayabilirsiniz.

## Endpoint'ler  

### 1. Sipariş Oluştur
  
**Endpoint:**  `POST /CreateOrder`
JSON olarak `products ` verileri zorunlu olarak eklenmesi gerekmektedir. `couponCode ` opsiyoneldir.

**Örnek Girdi:**
```json
{
	"couponCode": "TTN2024TTTT01",
	"products": [
	{
		"product_id": 1,
		"quantity":5
	},
	{
		"product_id": 2,
		"quantity": 1
	}]
}
```

### 2. Sipariş Düzenle
  
**Endpoint:**  `PUT /UpdateOrder`
JSON olarak `products ` ve `orderCode` verileri zorunlu olarak eklenmesi gerekmektedir. `couponCode ` opsiyoneldir.

**Örnek Girdi:**
```json
{
	"orderCode": "12312312",
	"couponCode": "TTN2024TTTT01",
	"products": [
	{
		"product_id": 1,
		"quantity":5
	},
	{
		"product_id": 2,
		"quantity": -1
	}]
}
```
`orderCode` alanına düzenlenecek sipariş kodu girilmelidir. Siparişe ürün ekleme ve çıkartma işlemleri bu endpoint ile yapılabilir. Yukarıdaki örnekte `12312312` numaralı siparişe 1 product_id li üründen 5 adet eklenmiş, 2 product_id li üründen 1 adet çıkartılmıştır.

### 3. Sipariş Sil
  
**Endpoint:**  `DELETE /DeleteOrder/{orderCode}`
Silinmesi istenen siparişin numarası DELETE  yöntemi ile gönderilerek gerekli işlem gerçekleştirilir.

### 4. Sipariş Detayını Görüntüle
  
**Endpoint:**  `GET /OrderDetails/{orderCode}`
Görüntülenmesi istenen siparişin numarası GET yöntemi ile gönderilerek gerekli işlem gerçekleştirilir. 
**Örnek Çıktı:**
```json
{
	"id":  2,
	"order_code":  7884396,
	"total_amount":  "114.96",
	"shipping_fee":  "54.99",
	"discount_amount":  "0.00",
	"discount_rate":  0,
	"coupon_code":  null,
	"gift":  null,
	"last_total":  "169.95",
	"products":  "[{\"product_id\":1,\"quantity\":1},{\"product_id\":2,\"quantity\":3}]",
	"created_at":  "2023-12-10 19:43:21",
	"updated_at":  "2023-12-11 13:32:39",
	"status":  0,
	"user_id":  null
}
```

### 5. Sipariş Tamamla
  
**Endpoint:**  `POST /OrderDetails/{orderCode}/{userId}`
Görüntülenmesi istenen siparişin numarası ve siparişi veren kullanıcının id bilgisi POST yöntemi ile gönderilerek gerekli işlem gerçekleştirilir. 
