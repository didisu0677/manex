<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	for ($i = 1; $i <= 12; $i++) {
		$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}

	$stotal_budget = 0;

	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 5 + 12; ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->sub_product; ?></font></th>
		</tr>		
  	<?php

	// Reset total untuk setiap grup produk
	for ($i = 1; $i <= 12; $i++) {
		$sub_totalfield0 = 'sub_TotalB_' . sprintf('%02d', $i);
		$$sub_totalfield0 = 0;
	}

	$sub_stotal_budget = 0;

	foreach($produk[$m0->product_line] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td><?php echo isset($m1->code) ? $m1->code : ''; ?></td>
			<td><?php echo isset($m1->segment) ? $m1->segment : ''; ?></td>

			<?php

			$bgedit ="";
			$contentedit ="true" ;
			for ($i = 1; $i <= 12; $i++) {
				$field0 = 'B_' . sprintf('%02d', $i);
				$x1 = ($contentedit == 'true' ? number_format($m1->$field0) : '');

				$sub_totalfield0 = 'sub_TotalB_' . sprintf('%02d', $i);
				$$sub_totalfield0 += $m1->$field0;

				$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
				$$totalfield0 += $m1->$field0;

				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</div></td>';
			}
			$sub_stotal_budget += $m1->total_budget;
			$stotal_budget += $m1->total_budget;
            echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_budget" data-name="total_budget" data-id="'.$m1->total_budget.'" data-value="">'.number_format($m1->total_budget).'</div></td>';

			?>

		</tr>
	<?php 
	} ?>
	<tr>
		<td class="sub-1" colspan="3"><b>TOTAL <?php echo $m0->sub_product  ?></b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			for ($i = 1; $i <= 12; $i++) {
				$sub_totalfield0 = 'sub_TotalB_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sub_totalfield0.'"><b>'.number_format($$sub_totalfield0).'</b></div></td>';
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sub_stotal_budget.'"><b>'.number_format($sub_stotal_budget).'</b></div></td>';

			?>
	</tr>
	<?php
} ?>

<tr>
	<td class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
	<?php
		$bgedit ="";
		$contentedit ="false" ;
		for ($i = 1; $i <= 12; $i++) {
			$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$totalfield0.'"><b>'.number_format($$totalfield0).'</b></td>';
		}
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$stotal_budget.'"><b>'.number_format($stotal_budget).'</b></div></td>';

		?>
</tr>