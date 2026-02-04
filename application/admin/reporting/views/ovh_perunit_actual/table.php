<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$grand_totalfixed = 0;
	$grand_totalvariable = 0;
	$grand_totalovh = 0;
	$grand_totalqty = 0;
	$grand_totalfoh = 0;
	$grand_budget_ovh = 0;
	foreach($grup[0] as $m0) { ?>
		<tr class="bg-grey-3">
			<th colspan="2" style="background: #778899 !important; color: white !important; font-weight: bold !important; position: sticky !important; left: 0 !important; z-index: 11 !important;"><?php echo $m0->cost_centre; ?></th>
			<th colspan="15" style="background: #778899 !important; color: white !important; font-weight: bold !important;"></th>
		</tr>
	<?php

	$sum_totalfixed = 0;
	$sum_totalvariable = 0;
	$sum_totalovh = 0;
	$sum_totalqty = 0;
	$sum_totalfoh = 0;
	$sum_budget_ovh = 0;

	$total_fixed = 0;
	$total_variable = 0 ;
	$total_ovh = 0;
	$total_foh = 0;
	
	// Create budget lookup array
	$budget_lookup = [];
	if(isset($produk_budget[$m0->id])) {
		foreach($produk_budget[$m0->id] as $budget_item) {
			$budget_lookup[$budget_item->product_code] = $budget_item;
		}
	}

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
			$sum_totalovh += $total_ovh;
			$sum_totalqty += $m1->qty_production;

			$grand_totalfixed += $total_fixed ;
			$grand_totalvariable += $total_variable;
			$grand_totalovh += $total_ovh;
			$grand_totalqty += $m1->qty_production;


			// $sum_totalovh += ($total_fixed + $total_variable);
			
			
			$total_foh =  ($total_ovh * $m1->qty_production) ;
			$sum_totalfoh += $total_foh ;
			$grand_totalfoh += $total_foh ;


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
			
			// Calculate budget ovh per unit from ovh_perunit
			$budget_ovh_perunit = 0;
			$variance_pct = 0;
			if(isset($budget_lookup[$m1->product_code])) {
				$budget_item = $budget_lookup[$m1->product_code];
				
				// Calculate budget depreciation
				$budget_depreciation = round($budget_item->depreciation / $budget_item->qty_production, 10);
				foreach($depr as $d => $k) {
					if($budget_item->product_code == $d) {
						if($budget_item->product_code != 'CIGSPRC1DM'){
							$budget_depreciation = (round($k / $budget_item->qty_production, 10)) + (round($budget_item->depreciation / $budget_item->qty_production, 10));
						}else{
							$budget_depreciation = $k;
						}
					}
				}
				
				$budget_total_variable = (round($budget_item->direct_labour / $budget_item->qty_production, 10)) + (round($budget_item->utilities / $budget_item->qty_production, 10)) + (round($budget_item->supplies / $budget_item->qty_production, 10));
				$budget_total_fixed = (round($budget_item->indirect_labour / $budget_item->qty_production, 10)) + (round($budget_item->repair / $budget_item->qty_production, 10)) + ($budget_depreciation) + (round($budget_item->rent / $budget_item->qty_production, 10)) + (round($budget_item->others / $budget_item->qty_production, 10));
				$budget_ovh_perunit = $budget_total_variable + $budget_total_fixed;
				
				// Calculate variance percentage: (actual - budget) / budget * 100
				if($budget_ovh_perunit != 0) {
					$variance_pct = (($total_ovh - $budget_ovh_perunit) / $budget_ovh_perunit) * 100;
				}
			}
			
			$sum_budget_ovh += $budget_ovh_perunit;
			$grand_budget_ovh += $budget_ovh_perunit;
			
			echo '<td style="background: #E6F2FF;"><div style="background:#E6F2FF;" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom-6 alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$budget_ovh_perunit.'"><b>'.number_format($budget_ovh_perunit,4).'</b></td>';
			echo '<td style="background: #FFFACD;"><div style="background:#FFFACD;" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="'.$variance_pct.'"><b>'.number_format($variance_pct,2).'%</b></div></td>';
			?>
		</tr>
	<?php 
	} 
	?>
	<tr class="bg-grey-3">
		<?php
		echo '<td colspan ="2" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>TOTAL '.$m0->cost_centre.'</b></td>';
		foreach($variable as $v) {
			echo '<td class="text-right" style="background: #778899 !important; color: white !important;"></td>';
		}
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalvariable,4).'</b></td>';

		
		foreach($fixed as $f) {
			echo '<td class="text-right" style="background: #778899 !important; color: white !important;"></td>';
		}
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalfixed,4).'</b></td>';
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalovh,4).'</b></td>';
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalqty).'</b></td>';
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalfoh).'</b></td>';
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_budget_ovh,4).'</b></td>';
		$sum_variance_pct = 0;
		if($sum_budget_ovh != 0) {
			$sum_variance_pct = (($sum_totalovh - $sum_budget_ovh) / $sum_budget_ovh) * 100;
		}
		echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_variance_pct,2).'%</b></td>';
		?>
	</tr>
<?php } ?>
<tr class="bg-grey-3">
	<?php
		echo '<td colspan ="2" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>GRAND TOTAL</b></td>';
	
	foreach($variable as $v) {
		echo '<td class="text-right" style="background: #778899 !important; color: white !important;"></td>';
	}
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_totalvariable,4).'</b></td>';

	foreach($fixed as $f) {
		echo '<td class="text-right" style="background: #778899 !important; color: white !important;"></td>';
	}
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_totalfixed,4).'</b></td>';
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_totalovh,4).'</b></td>';
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_totalqty).'</b></td>';
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_totalfoh).'</b></td>';
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_budget_ovh,4).'</b></td>';
	$grand_variance_pct = 0;
	if($grand_budget_ovh != 0) {
		$grand_variance_pct = (($grand_totalovh - $grand_budget_ovh) / $grand_budget_ovh) * 100;
	}
	echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($grand_variance_pct,2).'%</b></td>';

	?>
</tr>