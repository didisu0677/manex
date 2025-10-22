<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$grandtotal_variable = 0;
	$grandtotal_fixed = 0;
	$grandtotal_ovh = 0;
	foreach($grup[0] as $m0) { ?>
		<tr class="bg-grey-3">
			<th colspan="13" style="background: #778899 !important; min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff" style="color: #fff !important;"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php


	foreach($produk[$m0->id] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td><?php echo isset($m1->product_code) ? $m1->product_code : ''; ?></td>
			<?php

			$bgedit ="";
			$contentedit ="false" ;
			$x = 0;
			$x1 = 0;
			$total_variable = 0;
			$total_ovh = 0;
			foreach($variable as $v) {
				// debug($total_biaya[$m1->kode]);die;
				foreach($total_biaya[$m1->kode] as $t =>$v1)
					// debug($t);die;
					{
					if($t==$v->account_code) {
						if(in_array($v->account_code,['7211'])) {
							$x = $v1 * ($m1->manwh_prsn);
						}else{
							$x = $v1 * ($m1->macwh_prsn);
						}
					}
				}
				$total_variable += $x;
				$grandtotal_variable += $x;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$x.'">'.number_format($x).'</td>';
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_variable.'"><b>'.number_format($total_variable).'</b></td>';
			
			$total_fixed = 0;
			foreach($fixed as $f) {
				foreach($total_biaya[$m1->kode] as $t1 =>$v11)
					// debug($t);die;
					{
					if($t1==$f->account_code) {
						if(in_array($f->account_code,['7212'])) {
							$x1 = $v11 * ($m1->manwh_prsn);
						}else{
							$x1 = $v11 * ($m1->macwh_prsn);
						}
					}
				}
				$total_fixed += $x1;
				$grandtotal_fixed += $x1;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$x1.'">'.number_format($x1).'</td>';
			}
			$total_ovh = $total_variable + $total_fixed ;
			$grandtotal_ovh += $total_ovh;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_fixed.'"><b>'.number_format($total_fixed).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($total_ovh).'</b></td>';

			?>

		</tr>
	<?php 
	} ?>

<?php } ?>
<tr class="bg-grey-2" style="background: #D2691E !important;">
	<?php
	echo '<td colspan ="2" style="background: #D2691E !important;"><div style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="text-centre"><b><font color="#fff" style="color: #fff !important;">TOTAL</font></b></div></td>';
	$bgedit ="";
	$contentedit ="false" ;
	foreach($variable as $v) {

		echo '<td style="background: #D2691E !important;"><div style="background: #D2691E !important; min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$m1->product_qty.'"><font color="#fff" style="color: #fff !important;">'.$m1->product_qty.'</font></div></td>';
	}
	echo '<td style="background: #D2691E !important;"><div style="background: #D2691E !important; min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$grandtotal_variable.'"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_variable).'</font></b></div></td>';

	foreach($fixed as $f) {

		echo '<td style="background: #D2691E !important;"><div style="background: #D2691E !important; min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$m1->product_qty.'"><font color="#fff" style="color: #fff !important;">'.$m1->product_qty.'</font></div></td>';
	}
	echo '<td style="background: #D2691E !important;"><div style="background: #D2691E !important; min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$grandtotal_fixed.'"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_fixed).'</font></b></div></td>';
	echo '<td style="background: #D2691E !important;"><div style="background: #D2691E !important; min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$grandtotal_ovh.'"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_ovh).'</font></b></div></td>';

	?>

</tr>
			
