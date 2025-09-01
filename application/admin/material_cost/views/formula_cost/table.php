<?php 
	foreach($detail as $m1) { 
		$bgedit ="";
		$contentedit ="false" ;
		$group_formula = '';
		if(strtoupper($m1->group_formula) == 'A') {
			$group_formula = 'Bottle';
		}elseif(strtoupper($m1->group_formula)=='B') {
			$group_formula = 'Content';
		}elseif(strtoupper($m1->group_formula)=='C'){
			$group_formula = 'Packing';
		}elseif(strtoupper($m1->group_formula) == 'D'){
			$group_formula = 'Set';
		}else{
			$group_formula = '';
		}

		$bm_amt = $m1->total_price * ($m1->bm/100);
		$pph = ($bm_amt + $m1->total_price) * ($m1->pph/100);
		$ppn = ($bm_amt + $m1->total_price) * ($m1->ppn/100);
		$price_budget = $m1->total_price + $bm_amt + $m1->bank_charges + $m1->handling_charges ;


		?>
		<tr>

			<td><?php echo isset($m1->parent_item) ? $m1->parent_item : ''; ?></td>
			<td><?php echo $m1->component_item; ?></td>
			<td><?php echo isset($m1->material_name) ? $m1->material_name : ''; ?></td>
			<td><?php echo $group_formula; ?></td>
			<td><?php echo $m1->um; ?></td>
			<?php


  			echo '<td class="text-right">'.number_format($m1->quantity,7).'</td>';
			echo '<td class="text-right">'.number_format($price_budget,7).'</td>';
            echo '<td class="text-right">'.number_format(round($m1->quantity, 5) * $price_budget,5).'</td>';

			?>

		</tr>
	<?php 
	} ?>
