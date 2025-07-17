<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$sum_totalfixed = 0;
	$sum_totalvariable = 0;
	$sum_totalovh = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
			<th colspan="15" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php

	$total_fixed = 0;
	$total_variable = 0 ;
	$total_ovh = 0;
	$total_foh = 0;

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
			
			$depreciation = round($m1->depreciation/ $m1->qty_production,10);
			foreach($depr as $d => $k) {
				if($m1->product_code == $d) {
					if($m1->product_code != 'CIGSPRC1DM'){
						$depreciation = (round($k / $m1->qty_production,10)) + (round($m1->depreciation/ $m1->qty_production,10));
					}else{
						$depreciation = $k;
					}
				}
			}

			$total_variable = (round($m1->direct_labour / $m1->qty_production,10)) + (round($m1->utilities / $m1->qty_production,10)) + (round($m1->supplies / $m1->qty_production,10)) ;
			$total_fixed = (round($m1->indirect_labour / $m1->qty_production,10)) + (round($m1->repair / $m1->qty_production,10))  + ($depreciation) + (round($m1->rent/ $m1->qty_production,10)) + (round($m1->others/ $m1->qty_production,10));
			$total_ovh = $total_variable+$total_fixed;

			$sum_totalfixed += $total_fixed ;
			$sum_totalvariable += $total_variable;
			$sum_totalovh += ($total_fixed + $total_variable);
			
			
			$total_foh =  @(intval($total_ovh) ?? 0) * intval($m1->qty_production) ;

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->direct_labour / $m1->qty_production,4).'</td>';

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->utilities / $m1->qty_production,4).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->supplies/ $m1->qty_production,4).'</td>';
		
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_variable.'"><b>'.number_format($total_variable,4).'</b></td>';
			
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right  money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->indirect_labour/ $m1->qty_production,4).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right  money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->repair/ $m1->qty_production,4).'</td>';

			// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->depreciation/ $m1->qty_production,4).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'. number_format($depreciation,4).'</td>';
		
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->rent/ $m1->qty_production,4).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->others/ $m1->qty_production,4).'</td>';
			

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_fixed.'"><b>'.number_format($total_fixed,4).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($total_ovh,4).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($m1->qty_production).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($total_foh).'</b></td>';
			?>

		</tr>
	<?php 
	} ?>

<?php } ?>
<tr>
	<?php
	echo '<td colspan ="2"><div style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="false" class="text-centre"><b>TOTAL</b></div></td>';
	$bgedit ="";
	$contentedit ="false" ;
	foreach($variable as $v) {
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$m1->product_qty.'"></td>';
	}
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($sum_totalvariable,4).'</b></td>';

	foreach($fixed as $f) {

		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""></td>';
	}
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($sum_totalfixed,4).'</b></td>';
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($sum_totalovh,4).'</b></td>';
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b></b></td>';
	echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b></b></td>';

	?>

</tr>
			
