<?php 
	$hno = 0;
	$sumstotal_stock = 0;

	foreach($grup[0] as $m0) { ?>
		<tr>
            <?php $colspan = 4 + (12 ); ?>
			<th colspan="<?php echo $colspan ; ?>" style="background: #757575;" style="min-height: 10px; width: 50px; overflow: hidden;"><font color="#fff"><?php echo $m0->cost_centre; ?></font></th>
		</tr>		
  	<?php


	$stotal_stock = 0;

	foreach($produk[$m0->id] as $m2 => $m1) { 
		// debug($m1->product_name);die;
		// debug(isset($m1['product_name']) ? $m1['product_name'] : '');die;
			$no++;
						
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->product_name) ? $m1->product_name : ''; ?></td>
			<td><?php echo isset($m1->code) ? $m1->code : ''; ?></td>
			<td><?php echo isset($m1->batch_size) ? $m1->batch_size : ''; ?></td>
			<?php


			$bgedit ="";
			$contentedit ="true" ;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format($m1->total_stock).'</b></td>';

			?>

		</tr>
	<?php 
	} ?>
	<tr>
		<td class="sub-1" colspan="3"><b>TOTAL STOCK<?php echo $m0->cost_centre  ?></b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;

			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$stotal_stock.'"><b>'.number_format($stotal_stock).'</b></td>';
			?>
	</tr
	
<?php } ;?>
	<tr>
		<td class="sub-1" colspan="3"><b>GRAND TOTAL</b></td>
		<?php
			$bgedit ="";
			$contentedit ="false" ;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate" data-name="" data-id="'.$m1->id.'" data-value="'.$sumstotal_budget.'"><b>'.number_format($sumstotal_stock).'</b></td>';
			?>
	</tr