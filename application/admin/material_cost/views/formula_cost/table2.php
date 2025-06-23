<?php  
$bm_amt = 0 ;
$pph = 0;
$ppn =0;
$quantiti = 0;
$price_budget = 0;
foreach($produk as $m0) {
	foreach($detail as $m1) { 
		$bgedit ="";
		$contentedit ="false" ;
		$group_formula = '';
		if($m1->group_formula == 'A') {
			$group_formula = 'Bottle';
		}elseif($m1->group_formula=='B') {
			$group_formula = 'Content';
		}elseif($m1->group_formula=='C'){
			$group_formula = 'Packing';
		}elseif($m1->group_formula == 'D'){
			$group_formula = 'Set';
		}else{
			$group_formula = '';
		}

		if($m1->component_item == $m0->component_item && $m1->parent_item == $m0->parent_item) {
			$bm_amt += $m1->total_price * ($m1->bm/100);
			$pph += ($bm_amt + $m1->total_price) * ($m1->pph/100);
			$ppn += ($bm_amt + $m1->total_price) * ($m1->ppn/100);
			$quantity += $m1->quantitiy;
			$price_budget += $m1->total_price + $bm_amt + $m1->bank_charges + $m1->handling_charges ;
		}
	
	} 
		?>
		<tr>

			<td><?php echo isset($m1->parent_item) ? $m0->parent_item : ''; ?></td>
			<td><?php echo $m0->component_item; ?></td>
			<td><?php echo isset($m1->material_name) ? $m0->material_name : ''; ?></td>
			<td><?php echo $group_formula; ?></td>
			<td><?php echo $m0->um; ?></td>
			<?php


  			echo '<td class="text-right">'.number_format($quantity,5).'</td>';
			echo '<td class="text-right">'.number_format($price_budget,5).'</td>';
            echo '<td class="text-right">'.number_format($$price_budget,5).'</td>';

			?>

		</tr>
<?php
} ?>
