<?php 
	for ($i = 1; $i <= 12; $i++) {
		$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
		$$sumtotalfield0 = 0;
	}
	$sumstotal_budget = 0;
	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 5 + (12 ); ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->sub_product; ?></font></th>
		</tr>		
  	<?php

	for ($i = 1; $i <= 12; $i++) {
		$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
		$$totalfield0 = 0;
	}
	$stotal_budget = 0;
	foreach($produk[$m0->product_line] as $m2 => $m1) { 

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
				if($i <= setting('actual_budget')) {
					$bgedit = '#F7F7EB';
					$contentedit = "false";
				}else{
					$bgedit = '';
					$contentedit = "false";
				}
				
				$field0 = 'EST_' . sprintf('%02d', $i);
				$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
				$$totalfield0 += $m1->$field0;

				$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
				$$sumtotalfield0 += $m1->$field0;

				$x1 = (number_format($m1->$field0));
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget '.$field0.'" data-name="'.$field0.'" data-id="'.$m1->id.'" data-value="'.$x1.'">'.$x1.'</td>';
			}
			$stotal_est = $m1->EST_01+$m1->EST_02+$m1->EST_03+$m1->EST_04+$m1->EST_05+$m1->EST_06+$m1->EST_07+$m1->EST_08+$m1->EST_09+$m1->EST_10+$m1->EST_11+$m1->EST_12;
            $stotal_budget += $stotal_est;
			$sumstotal_budget += $stotal_est;
            echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format($stotal_est).'</b></td>';
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

				if($i <= setting('actual_budget')) {
					$bgedit = '#F7F7EB';
					$contentedit = "false";
				}else{
					$bgedit = '';
					$contentedit = "false";
				}
				
				$totalfield0 = 'TotalB_' . sprintf('%02d', $i);
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$totalfield0.'"><b>'.number_format($$totalfield0).'</b></td>';
			}
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$stotal_est.'"><b>'.number_format( $stotal_budget).'</b></td>';

			?>
	</tr
<?php } ?>

<tr>
	<td class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
	<?php
		$bgedit ="";
		$contentedit ="false" ;
		for ($i = 1; $i <= 12; $i++) {
			if($i <= setting('actual_budget')) {
				$bgedit = '#F7F7EB';
				$contentedit = "false";
			}else{
				$bgedit = '';
				$contentedit = "false";
			}

			$sumtotalfield0 = 'sumTotalB_' . sprintf('%02d', $i);
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right budget " data-name="" data-id="'.$m1->id.'" data-value="'.$$sumtotalfield0.'"><b>'.number_format($$sumtotalfield0).'</b></td>';
		}
		echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_budget).'</b></td>';

		?>
</tr