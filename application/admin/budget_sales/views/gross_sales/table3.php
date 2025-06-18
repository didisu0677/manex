<?php 
//debug($dtx_core2018);die;
	$hno = 0;
	for ($i = 1; $i <= 10; $i++) {
		$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}
	$total_actual = 0;
	$total_curbudget = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 15; ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->sub_product; ?></font></th>
		</tr>		
  	<?php

	for ($i = 1; $i <= 10; $i++) {
		$sub_totalfield0 = 'sub_TotalTHN_' . sprintf('%02d', $i);
		$$sub_totalfield0 = 0;
	}

	$sub_total_actual = 0;
	$sub_total_curbudget = 0;
	foreach($produk[$m0->product_line] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
			$no++;
						
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

			$actual = 0;
			for($i = 1; $i <= setting('actual_budget'); $i++) {
				$field_actual = 'EST_' . sprintf('%02d', $i);
				$actual += $m1->$field_actual;
			}

			$total_actual += $actual ;
			$sub_total_actual += $actual ;

			$total_curbudget += $m1->total_budget;
			$sub_total_curbudget += $m1->total_budget;

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value="">'.number_format($actual).'</td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value="">'.number_format($m1->total_budget).'</td>';

			for ($i = 1; $i <= 10; $i++) {
				$field0 = 'THN_' . sprintf('%02d', $i);

				$sub_totalfield0 = 'sub_TotalTHN_' . sprintf('%02d', $i);
				$$sub_totalfield0 += $m1->$field0;

				$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
				$$totalfield0 += $m1->$field0;
				
				$x1 = ($contentedit == 'true' ? number_format($m1->$field0) : '');
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}
            // echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_budgetthn" data-name="total_budgetthn" data-id="'.$m1->id.'" data-value=""></td>';

			?>

		</tr>
	<?php 
	} ?>
	<tr>
		<td class="sub-1" colspan="3"><b>TOTAL <?php echo $m0->sub_product  ?></b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value=""><b>'.number_format($sub_total_actual).'</b></td>';
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value=""><b>'.number_format($sub_total_curbudget).'</td>';

			for ($i = 1; $i <= 10; $i++) {
				$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
				$sub_totalfield0 = 'sub_TotalTHN_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sub_totalfield0.'"><b>'.number_format($$sub_totalfield0).'</b></td>';
			}
			?>
	</tr
<?php } ?>

<tr>
	<td class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
	<?php
		$bgedit ="";
		$contentedit ="false" ;
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value=""><b>'.number_format($total_actual).'</td>';
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget actual" data-name="actual" data-id="'.$m1->id.'" data-value=""><b>'.number_format($total_curbudget).'</td>';

		for ($i = 1; $i <= 10; $i++) {
			$totalfield0 = 'TotalTHN_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$totalfield0.'"><b>'.number_format($$totalfield0).'</b></td>';
		}
		?>
</tr