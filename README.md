# BulutfonXM ile WHMCS,HipChat Entegrasyonu

Bu basit uygulama ile [BulutfonXM](http://www.bulutfon.com) yardımıyla sizi arayan kullanıcıları ve gönderdikleri son 5 destek bildirimini HipChat kanalınıza mesaj olarak gönderebilirsiniz.

Uygulama WHMCS konfigurasyon dosyanız ve [hipchat/hipchat-php](https://github.com/hipchat/hipchat-php) kütüphanesi kullanmaktadır.

Oldukça basit olan bu uygulamayı WHMCS dışında da diğer uygulamalarınız ile birkaç değişiklikle kullanabilirsiniz.

### Kullanım
İlk olarak HipChat kütüphaneniz ve WHMCS konfigürasyon dosyanızı bulunduğu dizin yolunu ekleyiniz.

```php
require "HipChat.php";
require "configuration.php";
```	
Sonrasında aşağıdaki basit ayarları yaparak kullanmaya başlayabilirsiniz.
```php
//HipChat API token.
$hcToken = "hipchat_api_token";

// WHMCS panelinizde kullanicinin telefonun bulundugu alan id'si.
$fieldID = 5;

// HipChat'te kullacagimiz oda ismi
$roomName = 'HIPCHAT ROOM NAME';

// Mesaj gonderen ismi.
$from = 'BULUTFON';

// Karsilama menusu id'si
$welcome = 8;

// WHMCS admin paneli urlsi
$url = 'http://www.adres.com/admin/';

$hashValue = 'rgLvnZ76TuPqakVkZFve';
```
**$hashValue** değerini değiştirdikten sonra BulutfonXM url'nize hash parametresi olarak ekleyiniz.

Örneğin
> http://www.adresiniz.com/bulutfon.php?hash=rgLvnZ76TuPqakVkZFve



Lütfen PHP hata raporlama özelliğinin kapalı olduğundan emin olunuz.

```php
error_reporting(0);
ini_set('display_errors', 'Off');
```
