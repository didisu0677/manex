<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$total_qty = 0;
	$total_manwh = 0;
	$total_macwh = 0;

	$total_prsnmanwh = 0;
	$total_prsnmacwh = 0;

	foreach($grup[0] as $m0) { ?>
		<tr>
			<th colspan="10" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php
	$stotal_qty = 0;
	$stotal_manwh = 0;
	$stotal_macwh = 0;

	$stotal_prsnmanwh = 0;
	$stotal_prsnmacwh = 0;
	foreach($produk[$m0->id] as $m2 => $m1) { 

		$stotal_qty += $m1->qty_production;
		$total_qty += $m1->qty_production;

		$stotal_manwh += $m1->manwh_total;
		$stotal_macwh += $m1->macwh_total;

		$total_manwh += $m1->manwh_total;
		$total_macwh += $m1->macwh_total;

		$stotal_prsnmanwh += $m1->manwh_prsn;
		$stotal_prsnmacwh += $m1->macwh_prsn;

		$total_prsnmanwh += $m1->manwh_prsn;
		$total_prsnmacwh += $m1->macwh_prsn;

			$no++;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td><?php echo isset($m1->product_code) ? $m1->product_code : ''; ?></td>
			<td><?php echo isset($m1->destination) ? $m1->initial : ''; ?></td>
			<?php


				$bgedit ="";
				$contentedit ="true" ;

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate qty_production" data-name="qty_production" data-id="'.$m1->id.'" data-value="'.$m1->qty_production.'">'.number_format($m1->qty_production).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate manwh_productivity" data-name="manwh_productivity" data-id="'.$m1->id.'" data-value="'.number_format($m1->manwh_productivity,4).'">'.$m1->manwh_productivity.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate macwh_productivity" data-name="macwh_productivity" data-id="'.$m1->id.'" data-value="'.number_format($m1->macwh_productivity,4).'">'.$m1->macwh_productivity.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" contenteditable="false" class="edit-value text-right manwh_total" data-name="manwh_total" data-id="'.$m1->id.'" data-value="'.$m1->manwh_total.'">'.number_format($m1->manwh_total,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" contenteditable="false" class="edit-value text-right macwh_total" data-name="macwh_total" data-id="'.$m1->id.'" data-value="'.$m1->macwh_total.'">'.number_format($m1->macwh_total,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" contenteditable="false" class="edit-value text-right money-custom-6 manwh_prsn" data-name="manwh_total" data-id="'.$m1->id.'" data-value="'.$m1->manwh_prsn.'">'.$m1->manwh_prsn.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" contenteditable="false" class="edit-value text-right money-custom-6 macwh_prsn" data-name="macwh_total" data-id="'.$m1->id.'" data-value="'.$m1->macwh_prsn.'">'.$m1->macwh_prsn.'</td>';

			?>

		</tr>
	<?php 
	} 
	echo '<tr>';
	echo '<td colspan ="3"><b>SUB TOTAL '.$m0->cost_centre.'</b></td>';
	echo '<td class="text-right">'.number_format($stotal_qty).'</td>';
	echo '<td></td>';
	echo '<td></td>';
	echo '<td class="text-right">'.number_format($stotal_manwh).'</td>';
	echo '<td class="text-right">'.number_format($stotal_macwh).'</td>';
	echo '<td class="text-right" money-custom-6>'.$stotal_prsnmanwh.'</td>';
	echo '<td class="text-right" money-custom-6>'.$stotal_prsnmacwh.'</td>';

	echo '</tr>';
	
	
	?>

	

<?php } 
echo '<tr>';
echo '<td colspan ="3"><b>TOTAL </b></div></td>';
echo '<td class="text-right"><b>'.number_format($total_qty).'</b></td>';
echo '<td></td>';
echo '<td></td>';
echo '<td class="text-right"><b>'.number_format($total_manwh).'</b></td>';
echo '<td class="text-right"><b>'.number_format($total_macwh).'</b></td>';
echo '<td class="text-right" money-custom-6>'.$total_prsnmanwh.'</td>';
echo '<td class="text-right" money-custom-6>'.$total_prsnmacwh.'</td>';

echo '</tr>';



?>
			
