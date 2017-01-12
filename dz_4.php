<!DOCTYPE html>
	<meta charset="UTF-8">
	<style>
		table {width: 100%; text-align: center; color: #111; border-spacing: 0;border-collapse:collapse;}
		th {font: .75em/1 helvetica; border-bottom: 1px solid #999; padding: 15px}
		td {font: .95em/1 arial; height: 80px; border-bottom: 1px solid #999; }
		td:last-child {font-weight: bold; }
		h2 {font: 2em helvetica;}
		i {font-size: .75em; }
		div {float: right; width: 30%; border: 1px solid grey; }
		span {color: red; font:.7em/1 arial;}
		tfoot td{text-align: right; border: none; line-height: 1.4}
	</style>
	<h2>Корзина</h3>
<?php
$ini_string='
[Игрушка мягкая мишка белый]
цена = '.  mt_rand(10, 100).';
количество заказано = '.  mt_rand(1, 10).';
осталось на складе = '.  mt_rand(0, 10).';
diskont ='.  mt_rand(0, 2).';    
[Одежда детская куртка синяя синтепон]
цена = '.  mt_rand(10, 100).';
количество заказано = '.  mt_rand(1, 10).';
осталось на складе = '.  mt_rand(0, 10).';
diskont ='.  mt_rand(0, 2).';  
[Игрушка детская велосипед]
цена = '.  mt_rand(10, 100).';
количество заказано = '.  mt_rand(1, 10).';
осталось на складе = '.  mt_rand(0, 10).';
diskont = '.mt_rand(0, 2).';';
$bd =  parse_ini_string($ini_string, true);

	//структурируем данные 
foreach ($bd as $product => $specification) {
	 $product;
	foreach ($specification as $key => $value) 
		 $key.':' .$value;
		 

		//создадим и заполним индексные массивы данными о товарах
	$prod[] = $product; #массив названий заказанных товаров
	$cost[] = $specification['цена']; #массив цен всех товаров
	$discounted_cost[] =  $specification['цена']; #массив для цен со скидкой
	$quantity[] = $specification['количество заказано'];#кол-во одного товара заказано
	$balance[] = $specification['осталось на складе'];#в наличии
	$discount[] = $specification['diskont']; #массив скидок на каждый товар
}
$N = 3; #количество товаров нужно заказать для скидки от объёма
$D = 30; #скидка от объёма заказа в %

	//вычислим стоимость товаров с учётом скидок
for ($i = 0; $i < count($prod); $i++) {
	switch ($discount[$i]) {
		case 0: //скидка 0%
			$discount[$i] = 0; 
			sumCost($i); break;
		case 1: //скидка 10%
			$discount[$i] = (10/100)*$cost[$i];
			sumCost($i); break;
		case 2: //скидка 20%
			$discount[$i] = (20/100)*$cost[$i];
			sumCost($i); break;
	}	
}
	//функция рассчёта суммарной стоимости товаров
function sumCost($i) {
	global $cost;
	global $discounted_cost;
	global $quantity;
	global $balance;
	global $discount;
	global $N;
	global $D;
	global $ind_discount;
	global $vol_order_disc;
	global $summa;
	global $not_available;

	$ind_discount[$i] = $discount[$i]; #добавим индивидуальную скидку в соответствующий массив 
	if ($quantity[$i]>$balance[$i]) { #если заказано больше, чем имеется наличии
		$not_available[$i] = 'В наличии только '.$balance[$i].' шт.'; #добавить в массив предупреждение
		} else { $not_available[$i]='';}
	if ($quantity[$i]>=$N) { #если объём заказа более 3-х единиц
		$discount[$i]+=$cost[$i]*$D/100; #дать доп. скидку 30% и увеличить суммарную скидку на её размер  
		$vol_order_disc[$i]=$cost[$i]*$D/100; #добавим в массив скидку от объёма
	} else {$vol_order_disc[$i]=0;} #если кол-во товара < 3, то скидка от объёма равна 0
	$discounted_cost[$i]-=$discount[$i]; #стоимость товара со скидкой
	$summa[$i] = $discounted_cost[$i]*$balance[$i]; #суммарная стоимость товаров каждой позиции
}

//отобразим все данные в таблице
echo'
<table>
	<tr>
		<th>ПРОДУКТ</th>
		<th>ЦЕНА</th>
		<th>СКИДКА</th>
		<th>СКИДКА ОТ ОБЪЁМА</th>
		<th>СУММАРНАЯ СКИДКА</th>
		<th>СТОИМОСТЬ СО СКИДКОЙ</th>
		<th>КОЛИЧЕСТВО</th>
		<th>В НАЛИЧИИ</th>
		<th>СУММА</th>
	</tr>';
	for ($i = 0; $i < count($prod); $i++) {
		echo'<tr>
			   <td>'.$prod[$i].'</td>
			   <td>'.$cost[$i].' <i>руб.</i></td>
			   <td>'.$ind_discount[$i].' <i>руб.</i></td>
			   <td>'.$vol_order_disc[$i].' <i>руб.</i></td>
			   <td>'.$discount[$i].' <i>руб.</i></td>
			   <td>'.$discounted_cost[$i].' <i>руб.</i></td>
			   <td>'.$quantity[$i].' <i>шт.</i><br><span>'.$not_available[$i].'</span></i></td>
			   <td>'.$balance[$i].' <i>шт.</i></td>
			   <td>'.$summa[$i].' <i>руб.</i></td>
			 <tr>';
	}
	echo '
	<tfoot>
		<tr>
			<td colspan="8"><br>Итого:<br>
					Сумма заказа: '.array_sum($summa).' руб.<br>
					Всего наименований: '.count($prod).' шт.<br>
					Заказано товаров: '.array_sum($quantity).' шт.<br>
			</td>
		</tr>
	</tfoot>
</table>';
?>