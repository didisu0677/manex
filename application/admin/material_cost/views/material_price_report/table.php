<?php 

	$bm_amt = 0;
	$pph = 0;
	$ppn = 0;
	$price_budget = 0;
	$fpo = 0;
	foreach($produk as $m2 => $m1) { 					
		$bgedit ="";
		$contentedit ="false" ;
		$bm_amt = $m1['total_price'] * ($m1['bm']);
		$pph = ($bm_amt + $m1['total_price']) * ($m1['pph']/100);
		$ppn = ($bm_amt + $m1['total_price']) * ($m1['ppn']/100);
		$price_budget = $m1['total_price'] + $bm_amt + $m1['bank_charges'] + $m1['handling_charges'] ;
		$fpo = $price_budget + $pph + $ppn;
		?>
		<tr>
			<td><?php echo isset($m1['year']) ? $m1['year'] : ''; ?></td>
			<td><?php echo isset($m1['material_code']) ? $m1['material_code'] : ''; ?></td>
			<td><?php echo isset($m1['nama']) ? $m1['nama'] : ''; ?></td>
			<td><?php echo isset($m1['vcode']) ? $m1['vcode'] : ''; ?></td>
			<td><?php echo isset($m1['loc']) ? $m1['loc'] : ''; ?></td>
			<td><?php echo isset($m1['bm']) ? $m1['bm'] : ''; ?></td>
			<td><?php echo isset($m1['curr']) ? $m1['curr'] : ''; ?></td>
			<td class="text-right"><?php echo isset($m1['kurs']) ? number_format($m1['kurs']) : ''; ?></td>
			<td class="text-right"><?php echo isset($m1['price_us']) ? number_format($m1['price_us']) : ''; ?></td>
			<td class="text-right"><?php echo isset($m1['total_price']) ? number_format($m1['total_price']) : ''; ?></td>
			<td class="text-right"><?php echo isset($bm_amt) ? number_format($bm_amt) : ''; ?></td>
			<td class="text-right"><?php echo isset($pph) ? number_format($pph) : ''; ?></td>
			<td class="text-right"><?php echo isset($ppn) ? number_format($ppn) : ''; ?></td>
			<td class="text-right"><?php echo isset($m1['bank_charges']) ? number_format($m1['bank_charges']) : ''; ?></td>
			<td class="text-right"><?php echo isset($m1['handling_charges']) ? number_format($m1['handling_charges']) : ''; ?></td>
			<td class="text-right"><?php echo isset($price_budget) ? number_format($price_budget) : ''; ?></td>
			<td class="text-right"><?php echo isset($fpo) ? number_format($fpo) : ''; ?></td>
			<td><?php echo isset($m1['update_by']) ? $m1['update_by'] : ''; ?></td>
			<?php


	
			?>

		</tr>
	<?php 
	} ?>

