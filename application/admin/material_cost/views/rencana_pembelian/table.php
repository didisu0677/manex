<?php 
if(empty($produk)) {
    echo '<tr><td colspan="14" class="text-center">No data found for selected criteria</td></tr>';
    return;
}

foreach($produk as $m2 => $m1) { 
    $bgedit ="#f0f0f0";
    $contentedit ="false" ;
    ?>
    <tr>
        <td style="width: 250px; background-color: #f8f9fa; font-weight: bold; position: sticky; left: 0; z-index: 10;"><?php echo $m1->material_name; ?></td>
        <td style="width: 150px; background-color: #f8f9fa; font-weight: bold; position: sticky; left: 250px; z-index: 10;"><?php echo $m1->material_code; ?></td>
        <?php
        
        // Pre-calculate values untuk semua bulan sekaligus untuk efisiensi
        $monthly_values = [];
        for ($i = 1; $i <= 12; $i++) {
            $field0 = 'P_' . sprintf('%02d', $i);
            
            // Priority logic yang sudah dioptimasi
            $display_value = 0;
            $source_info = '';
            
            // Cek PBL dulu (arrival quantity manual)
            if (isset($arival[$m1->material_code][$field0]) && $arival[$m1->material_code][$field0] > 0) {
                $display_value = $arival[$m1->material_code][$field0];
                $source_info = 'PBL';
            }
            // Jika tidak ada PBL, cek ERQ (edited requirement)
            elseif (isset($erd[$m1->material_code][$field0]) && $erd[$m1->material_code][$field0] == 1 &&
                isset($erq[$m1->material_code][$field0]) && !empty($erq[$m1->material_code][$field0])) {
                $display_value = $erq[$m1->material_code][$field0];
                $source_info = 'ERQ';
            }
            // Fallback ke ARQ (production requirement)
            elseif (isset($prod[$m1->material_code][$field0])) {
                $display_value = $prod[$m1->material_code][$field0];
                $source_info = 'ARQ';
            }
            
            $monthly_values[$i] = [
                'value' => $display_value,
                'source' => $source_info,
                'display' => ($display_value > 0) ? number_format($display_value) : '-'
            ];
        }
        
        // Output semua cell sekaligus
        for ($i = 1; $i <= 12; $i++) {
            echo '<td style="background: '.$bgedit.';" class="text-right" title="Source: '.$monthly_values[$i]['source'].'">'.$monthly_values[$i]['display'].'</td>';
        }
        ?>
    </tr>
<?php 
} ?>
