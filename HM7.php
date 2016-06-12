<!--Написать сайт словарь, где пользователь вводит слово и его перевод. После добавления 
слова отображаются в виде таблицы. На страницу выводить по 20 слов. Сделать пагинацию.
-->
<pre>
<form action="hm7.php" method="POST">
Слово
<input type="text" name="word">
Перевод
<input type="text" name="translate"><br>
<input type="submit">
</form>
<?php
$symRu="абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
$symEn="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
/*
echo "<pre>";
print_r ($_SERVER);
echo "</pre>";
*/

//вносим в базу слова и их перевод с проверкой на ввод только разрешённых символов

if(isset($_POST['word']) && isset($_POST['translate']) && !empty($_POST['word']) && !empty($_POST['translate'])){
	if(strlen($_POST['word'])==strspn($_POST['word'], $symRu) && strlen($_POST['translate'])==strspn($_POST['translate'], $symEn)){
		$db=fopen("datebase", "a+");
		fputcsv($db, [$_POST['word'], $_POST['translate']]);
		fclose($db);
	}else{
		echo "Последнее введенное слово или его перевод не соответствует ожиданиям...<br>";
	}
}

$scr=substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'],"/")+1);		//имя скрипта

$db=fopen("datebase", 'a+');
fseek($db, 0);
$j=0;
while(!feof($db)){
	$a=fgetcsv($db);
	$j++;													//Считаем количество строк в базе
}
fseek($db, 0);
if(isset($_GET['pn'])){										//определяем номер текущей страницы
	$pn=(integer)$_GET['pn'];
}else{
	$pn=1;
}

$pages=ceil($j/20);											//получаем количество страниц
echo "<table border=\"1\">\n";								//реализуем таблицу

echo "<tr><td></td><td><a href=\"".
$scr."?".((isset($_GET['hide']) && $_GET['hide']=="left") ? "hide=none" : "hide=left")
."&pn=".$pn."\">Слово</a></td>";							//Шапка таблицы первая половина

echo "<td><a href=\"".
$scr."?".((isset($_GET['hide']) && $_GET['hide']=="right") ? "hide=none" : "hide=right")
."&pn=".$pn."\">Перевод</td></tr>\n";						//Шапка таблицы вторая половина

for ($i=1; $i<$j; $i++){									//проходим по всей базе отбирая нужные строки(возмождно нужно использовать $i<=$j)
	$ln=($pn-1)*20+$i;										//вычесляем номер строки
	$b=fgetcsv($db);
	if ($i>=($pn-1)*20 && $i<$pn*20) {
		if(isset($b[0]) &&  isset($b[1])){					//Исключаем пустые ячейки
			echo "<tr><td>".($ln+1)."</td><td>".((isset($_GET['hide']) && $_GET['hide']!="left") ? $b[0] : "").
			"</td> <td>".((isset($_GET['hide']) && $_GET['hide']!="right") ? $b[1] : "")."</td></tr>\n";
		}
	}
}

echo "</table>";

fclose($db);												//Отключаем базу

//реализуем пагинацию

if ($pn>3){
	echo "<a href=\"".$scr."?pn=1".(isset($_GET['hide'])? "&hide=".$_GET['hide'] : "")."\"> Первая </a>";
}

for ($j = -2; $j <= 2; $j++) {
	$thisPage = $pn + $j;
	if ($thisPage > 0 && $thisPage <= $pages) {
		echo "<a href=\"" . $scr . "?pn=" . $thisPage . (isset($_GET['hide']) ? "&hide=" . $_GET['hide'] : "") . "\"> " . $thisPage . " </a>";
	}
}

if ($pn+3<=$pages){
	echo "<a href=\"".$scr."?pn=".$pages.(isset($_GET['hide'])? "&hide=".$_GET['hide'] : "")."\"> Последняя </a>";	
}

 
?>
</pre>