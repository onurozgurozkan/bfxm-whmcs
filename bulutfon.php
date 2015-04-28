<?php
require "HipChat.php";

require "configuration.php";

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

// Adresinizi korumak icin basit bir rasgele deger.Bu değeri url
// degerinize hash parametresi olarak eklemeniz gerek.
// Ornegin http://adresinz.com/bulutfon.php?hash=rgLvnZ76TuPqakVkZFve
$hashValue = 'rgLvnZ76TuPqakVkZFve';

function json($array){
	header('Content-Type: application/json');
	die(json_encode($array));
}

// Bulutfon uzerinden bize gonderilen telefon numaralari ulke kodu ile gonderilmekte
// veritabanimizda telefon numaralarinin benzer formatta olmadini varsayarak
// gonderilen telefon numarasindan 90 ibaresini kaldiralim.
$caller = isset($_POST['caller']) ? ltrim($_POST['caller'],'90') : false;

// BulutfonXM icin panelleri uzerinde yetkilendirme secenegi olsada basit
// bir hash ile guvenligimizi biraz daha artirabiliriz.
$hash = isset($_GET['hash']) ? $_GET['hash'] : false;

if($hash!=$hashValue || !$caller) json(['error'=>'parameters missing']);

$conn = new PDO("mysql:host=localhost;dbname={$db_name}",$db_username,$db_password);

// Gonderilen veriye bfxm formatinda cevap vermemiz gerekmekte.
// Herhangi bir zincirleme islem( kullanicidan bir tusa basmasini istemek ) yapmayacagimizdan 
// kullaniciyi karsilama menu grubuna yonlendirebilirz.
$cevap = array(
	"bfxm"=>array("version"=>1),
	"seq"=>array(
		array(
			"action"=>"dial",
			// 8 bizim yonlendirecegimiz grup numarasi.
			"args"=>array("destination"=>$welcome)
		)
	)
);

// WHMCS uzerinden telefon numarasina gore kullaniciyi bulalim. 
$query = $conn->prepare("SELECT tblclients.id,
		tblclients.firstname,
		tblclients.lastname,
		tblcustomfieldsvalues.value AS telefon 
	FROM tblcustomfieldsvalues 
	LEFT JOIN tblclients ON tblclients.id=tblcustomfieldsvalues.relid 
	WHERE tblcustomfieldsvalues.value LIKE ? AND tblcustomfieldsvalues.fieldid=?");

$query->execute(["%$caller%",$fieldID]);

$profile = $query->fetch(PDO::FETCH_OBJ);

if($profile){
	
	// Kullanicimiz varsa kullanicinin son 5 destek biletine ulasalim.
	$query = $conn->prepare("SELECT id,title FROM tbltickets WHERE userid=? AND status!='Closed' ORDER BY id DESC LIMIT 5");

	$query->execute([$profile->id]);

	$tickets= $query->fetchAll(PDO::FETCH_OBJ);

	$liste= "";

	if($tickets){
		$liste = "<ul>";

		foreach($tickets as $p){
			$liste.="<br><li> <a href='{$url}supporttickets.php?action=view&id={$p->id}'>{$p->title}</a></li>";
		}

		$liste .="</ul>";
	}

	$hc = new HipChat\HipChat($hcToken);

	$hc->message_room($roomName,$from,"<b>{$profile->firstname}  {$profile->lastname} </b> ({$caller}) arıyor. {$liste}",true,'random');
}

json($cevap);