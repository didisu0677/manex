<?php 
//debug($dtx_core2018);die;
	$grandtotal_variable = 0;
	$grandtotal_fixed = 0;
	$grandtotal_ovh = 0;

	$total = '';
	foreach($fixed as $f) {
		$total = 'total_' . $f->account_code;
		$$total = 0;
	}

	$total2 = '';
	foreach($variable as $v) {
		$total2 = 'total_' . $v->account_code;
		$$total2 = 0;
	}

	foreach($grup[0] as $m0) { ?>
		<tr class="bg-grey-3">
			<th colspan="13" style="background: #778899 !important;"><font color="#fff" style="color: #fff !important;"><?php echo $m0->cost_centre; ?></font></th>
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

				foreach($total_biaya['3100'] as $t =>$v1)
					{
					if($t==$v->account_code) {
						if(in_array($v->account_code,['7211'])) {
							$x = $v1 * ($m1->prsn_aloc / 100);
						}else{
							$x = $v1 * ($m1->prsn_aloc / 100);
						}
					}
				}

				$total_variable += $x;
				$grandtotal_variable += $x;
				$$total2 += $x;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$x.'">'.number_format($x).'</td>';
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_variable.'"><b>'.number_format($total_variable).'</b></td>';

			$total_fixed = 0;
			$total = '';
			foreach($fixed as $f) {
				$total = 'total_' . $f->account_code;
				foreach($total_biaya['3100'] as $t1 =>$v11)
					// debug($t);die;
					{
					if($t1==$f->account_code) {
						if(in_array($f->account_code,['7212'])) {
							$x1 = $v11 * ($m1->prsn_aloc / 100);
						}else{
							$x1 = $v11 * ($m1->prsn_aloc / 100);
						}
					}
				}
				$total_fixed += $x1;
				$grandtotal_fixed += $x1;
				$$total += $x1;
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
	echo '<td colspan ="2" style="background: #D2691E !important;" class="text-center"><b><font color="#fff" style="color: #fff !important;">TOTAL</font></b></td>';
	$bgedit ="";
	$contentedit ="false" ;
	$total2 = '';
	foreach($variable as $v) {
		$total2 = 'total_' . $v->account_code;
		echo '<td style="background: #D2691E !important;" class="text-right"><b><font color="#fff" style="color: #fff !important;">'.number_format($$total2).'</font></b></td>';
	}
	echo '<td style="background: #D2691E !important;" class="text-right"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_variable).'</font></b></td>';

	$total = '';
	foreach($fixed as $f) {	
		$total = 'total_' . $f->account_code;
		echo '<td style="background: #D2691E !important;" class="text-right"><b><font color="#fff" style="color: #fff !important;">'.number_format($$total).'</font></b></td>';
	}
	echo '<td style="background: #D2691E !important;" class="text-right"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_fixed).'</font></b></td>';
	echo '<td style="background: #D2691E !important;" class="text-right"><b><font color="#fff" style="color: #fff !important;">'.number_format($grandtotal_ovh).'</font></b></td>';

	?>

</tr>
			
