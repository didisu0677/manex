<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$sum_totalfixed = 0;
	$sum_totalvariable = 0;
	$sum_totalovh = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
			<th colspan="21" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php

	$total_fixed = 0;
	$total_variable = 0 ;
	$total_ovh = 0;
	$unit_cost = 0;


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
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->bottle,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->content,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->packing,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->set,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($m1->subrm_total,2).'</b></td>';
			$bgedit ="";
			$contentedit ="false" ;
			
			$depreciation = $m1->depreciation/ $m1->qty_production;
			foreach($depr as $d => $k) {
				if($m1->product_code == $d) {
					if($m1->product_code != 'CIGSPRC1DM'){
						$depreciation = ($k / $m1->qty_production) + ($m1->depreciation/ $m1->qty_production);
					}else{
						$depreciation = $k;
					}
				}
			}

			$total_variable = ($m1->direct_labour / $m1->qty_production) + ($m1->utilities / $m1->qty_production) + ($m1->supplies / $m1->qty_production) ;
			$total_fixed = ($m1->indirect_labour / $m1->qty_production) + ($m1->repair / $m1->qty_production)  + ($depreciation) + ($m1->rent/ $m1->qty_production) + ($m1->others/ $m1->qty_production);
			$total_ovh = $total_variable+$total_fixed;
			$unit_cost = $total_ovh + $m1->subrm_total;

			$total_cogm = @(intval($unit_cost) ?? 0) * intval($m1->qty_production) ;

			$sum_totalfixed += $total_fixed ;
			$sum_totalvariable += $total_variable;
			$sum_totalovh += ($total_fixed + $total_variable);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->direct_labour / $m1->qty_production,2).'</td>';

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->utilities / $m1->qty_production,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->supplies/ $m1->qty_production,2).'</td>';
		
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_variable.'"><b>'.number_format($total_variable,2).'</b></td>';
			
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->indirect_labour/ $m1->qty_production,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->repair/ $m1->qty_production,2).'</td>';

			// echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->depreciation/ $m1->qty_production,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($depreciation,2).'</td>';
		
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->rent/ $m1->qty_production,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->others/ $m1->qty_production,2).'</td>';
			

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_fixed.'"><b>'.number_format($total_fixed,2).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($total_ovh,2).'</b></td>';

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($unit_cost,2).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($m1->qty_production).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$total_ovh.'"><b>'.number_format($total_cogm).'</b></td>';
			?>

		</tr>
	<?php 
	} ?>

<?php } ?>

			
