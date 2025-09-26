<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$total_qty = 0;
	$total_point = 0;
	$total_prsn = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
			<th colspan="9" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php
	$stotal_qty = 0;
	$stotal_point = 0;
	$stotal_prsn = 0;
	foreach($produk[$m0->id] as $m2 => $m1) { 
		$stotal_qty += $m1->product_qty;
		$total_qty += $m1->product_qty;

		$stotal_point += $m1->total_point;
		$total_point += $m1->total_point;

		$stotal_prsn += $m1->prsn_aloc;
		$total_prsn += $m1->prsn_aloc;

		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
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
				$contentedit ="false" ;

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$m1->product_qty.'">'.number_format($m1->product_qty).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi point_perunit" data-name="point_perunit" data-id="'.$m1->id.'" data-value="'.$m1->point_perunit.'">'.$m1->point_perunit.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi total_point" data-name="total_point" data-id="'.$m1->id.'" data-value="'.$m1->total_point.'">'.$m1->total_point.'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi prsnalokasi" data-name="prsn_aloc" data-id="'.$m1->id.'" data-value="'.$m1->prsn_aloc.'">'.$m1->prsn_aloc.'</td>';

			?>

		</tr>
	<?php 
	} 
	
	echo '<td colspan ="3"><b>SUB TOTAL '.$m0->cost_centre.'</b></td>';
	echo '<td class="text-right">'.number_format($stotal_qty).'</td>';	
	echo '<td class="text-right"></td>';	
	echo '<td class="text-right" money-custom-2>'.$stotal_point.'</td>';	
	echo '<td class="text-right" money-custom-6>'.$stotal_prsn.'</td>';	
	?>

<?php } ?>
<tr>
	<?php
	echo '<td colspan ="3"><b>TOTAL </b></div></td>';
	echo '<td class="text-right"><b>'.number_format($total_qty).'</b></td>';	
	echo '<td class="text-right"></td>';	
	echo '<td class="text-right" money-custom-2><b>'.$total_point.'</b></td>';	
	echo '<td class="text-right" money-custom-6><b>'.$total_prsn.'</b></td>';	

	?>

</tr>
			
