<?php 
	$hno = 0;

	foreach($produk as $m2 => $m1) { 
		$bgedit ="";
		$contentedit ="false" ;
		?>
		<tr>

			<td><?php echo isset($m1->material_code) ? $m1->material_code : ''; ?></td>
			<td><?php echo isset($m1->material_name) ? $m1->material_name : ''; ?></td>
			<td><?php echo isset($m1->um) ? $m1->um : ''; ?></td>
			<td><?php echo isset($m1->supplier) ? $m1->supplier : ''; ?></td>
			<td><?php echo isset($m1->moq) ? number_format($m1->moq) : ''; ?></td>
			<td><?php echo isset($m1->order_multiple) ? number_format($m1->order_multiple) : ''; ?></td>
			<td><?php echo isset($m1->m_cov) ? custom_format($m1->m_cov,2) : ''; ?></td>
			<?php

			$bgedit ="";
			$contentedit ="true" ;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" class="edit-value text-right calculate total_est" data-name="" data-id="'.$m1->id.'" data-value=""><b>'.number_format($m1->total_stock).'</b></td>';

			?>

		</tr>
	<?php 
	} ?>
	