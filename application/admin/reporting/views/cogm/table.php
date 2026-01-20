<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	$sum_totalfixed = 0;
	$sum_totalvariable = 0;
	$sum_totalovh = 0;
	$sum_unitcost = 0;
	$sum_qty = 0;
	$sum_cogm = 0;
	$sum_rm = 0;
	foreach($grup[0] as $m0) { ?>
		<tr class="bg-grey-3">
			<th colspan="21" style="background: #778899 !important; color: white !important; font-weight: bold !important;">
				<?php echo $m0->cost_centre; ?>
			</th>
		</tr>		
  	<?php

	$stotal_ovh = 0 ;
	$stotal_unitcost = 0 ;
	$stotal_qty = 0;
	$stotal_cogm = 0;
	$stotal_rm = 0;

	$stotal_fixed = 0;
	$stotal_variable = 0;

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
			$subrm_total = $m1->bottle + $m1->content + $m1->packing + $m1->set ;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->bottle,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->content,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->packing,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value="">'.number_format($m1->set,2).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right money-custom alokasi product_qty" data-name="product_qty" data-id="'.$m1->id.'" data-value=""><b>'.number_format($subrm_total,2).'</b></td>';
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

			$stotal_rm += $subrm_total;
			$sum_rm += $subrm_total ;

			$total_variable = ($m1->direct_labour / $m1->qty_production) + ($m1->utilities / $m1->qty_production) + ($m1->supplies / $m1->qty_production) ;
			$total_fixed = ($m1->indirect_labour / $m1->qty_production) + ($m1->repair / $m1->qty_production)  + ($depreciation) + ($m1->rent/ $m1->qty_production) + ($m1->others/ $m1->qty_production);
			$total_ovh = $total_variable+$total_fixed;
			$unit_cost = $total_ovh + $subrm_total;

			$total_cogm = @(intval($unit_cost) ?? 0) * intval($m1->qty_production) ;

			$stotal_fixed += $total_fixed;
			$stotal_variable += $total_variable;
			$sum_totalfixed += $total_fixed ;
			$sum_totalvariable += $total_variable;

			$stotal_ovh += $total_ovh;
			$sum_totalovh += $total_ovh;

			$stotal_unitcost += $unit_cost ;
			$sum_unitcost += $unit_cost ;

			$stotal_qty += $m1->qty_production;
			$sum_qty += $m1->qty_production;

			$stotal_cogm += $total_cogm;
			$sum_cogm += $total_cogm;

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
	<tr class="bg-grey-3">
		<?php
			echo '<td colspan ="2" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>TOTAL '.$m0->cost_centre.'</b></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_rm,2).'</b></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_variable,2).'</b></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td style="background: #778899 !important; color: white !important;"></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_fixed,2).'</b></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_ovh,2).'</b></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_unitcost,2).'</b></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_qty).'</b></td>';
			echo '<td class="text-right" style="background: #778899 !important; color: white !important; font-weight: bold !important;"><b>'.number_format($stotal_cogm,2).'</b></td>';
		?>
	</tr>
<?php } ?>
<tr class="bg-grey-2">
	<?php
		echo '<td colspan ="2" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>GRAND TOTAL</b></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_rm,2).'</b></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalvariable,2).'</b></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td style="background: #D2691E !important; color: white !important;"></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalfixed,2).'</b></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_totalovh,2).'</b></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_unitcost,2).'</b></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_qty).'</b></td>';
		echo '<td class="text-right" style="background: #D2691E !important; color: white !important; font-weight: bold !important;"><b>'.number_format($sum_cogm,2).'</b></td>';
	?>
</tr>

			
