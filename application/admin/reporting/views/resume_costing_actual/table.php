<?php 
foreach($production as $p) {
	$totalovh = 'totalovh_' . $p->kode;
	$$totalovh = 0;
}
$totalz1 = 0;
foreach($mst_account[0] as $m0) { 
	?>
	<tr>
		<td><b><?php echo strtoupper($m0->grup); ?></b></td>
	</tr>

	<?php 
	foreach($production as $p) {
		$total = 'total_' . $p->kode;
		$$total = 0;
	}
	$xtotal0 = 0;
	foreach($mst_account[$m0->grup] as $m1) { 
		foreach($total_budget as $v => $t){
			if($m1->account_code == $v) {
		?>
		<tr>
			<td class="sub-1"><b><?php echo $m1->account_code . '-' .$m1->account_name; ?></b></td>
			<?php
			foreach($production as $p) { 
				$x1 = 0 ;
				$totalz = 0;
				foreach($t as $t1 => $vt) {
					if($p->kode == $t1) {
						$x1 = number_format($vt);

						$total = 'total_' . $p->kode;
						$totalovh = 'totalovh_' . $p->kode;
		
						$xtotal1 = number_format($t['total']);
						$$total += $vt;
						$$totalovh += $vt;
						$xtotal0 += $vt;
					}
					$totalz += $vt;
				}
	
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget " data-name="" data-id="" data-value="'.$x1.'">'.$x1.'</td>';
			}
			$totalz1 += $totalz;
			echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="total_budget" data-id="" data-value="'.$xtotal1.'">'.number_format($totalz).'</td>';

			?>
		</tr>
		<?php 
		 ?>
         
	<?php }}} ?>
    <tr>
        <td><b><?php echo 'TOTAL ' . strtoupper($m0->grup);?></b></td>
        <?php
			foreach($production as $p) { 

				$total = 'total_' . $p->kode;

				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget " data-name="" data-id="" data-value="'.$$total.'"><b>'.number_format($$total).'</b></td>';
			}
                echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="total_budget" data-id="'.$m0->id_trx.'" data-value="'.$xtotal0.'"><b>'.number_format($xtotal0).'</b></td>';
    
        ?>
    </tr>
 
<?php } ?>

<tr>
        <td><b><?php echo 'TOTAL OVERHEAD' ;?></b></td>
        <?php
			foreach($production as $p) { 

				$totalovh = 'totalovh_' . $p->kode;
				echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right budget " data-name="" data-id="" data-value="'.$$totalovh.'"><b>'.number_format($$totalovh).'</td>';
			}
                echo '<td style="background: '.$bgedit.'"><div style="background:'.$bgedit.'" style="min-height: 10px; width: 50px; overflow: hidden;" contenteditable="'.$contentedit.'" contenteditable="true" class="edit-value text-right total_budget" data-name="" data-id="" data-value="'.$totalz1.'"><b>'.number_format($totalz1).'</b></td>';
    
        ?>
    </tr>



	





			
