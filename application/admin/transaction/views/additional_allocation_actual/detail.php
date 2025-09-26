<div class ="table-download">
<div class="card mb-2">
    <div class="card-header"><?php echo lang('allocation_info'); ?></div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-app table-detail table-normal">
         <tr>
            <th width="130" style="text-align:left"><?php echo lang('tahun'); ?></th>
            <td colspan="3" style="text-align:left"><?php echo $tahun; ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('product_code'); ?></th>
            <td colspan="3"><?php echo $product_code; ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('product_name'); ?></th>
            <td colspan="3"><?php echo $product_name; ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('account_code'); ?></th>
            <td colspan="3"><?php echo $account_code; ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('jumlah'); ?></th>
            <td colspan="3"><?php echo number_format($jumlah_asal); ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('jumlah_penyesuaian'); ?></th>
            <td colspan="3"><?php echo number_format($jumlah_penyesuaian) ?></td>
        </tr>
        <tr>
            <th style="text-align:left"><?php echo lang('jumlah_allocation'); ?></th>
            <td colspan="3"><?php echo number_format($jumlah_allocation); ?></td>
        </tr>   
        <tr>
            <th style="text-align:left"><?php echo lang('alloc_cc_product'); ?></th>
            <td colspan="3"><?php echo $cost_centre; ?></td>
        </tr>   
        </table>
    </div>
</div>
<div class="card mb-2">
    <div class="card-header"><?php echo lang('list_product_allocation'); ?></div>
</div>
    <div class="card-body table-responsive">
        <table class="table table-bordered table-app table-detail table-normal table-download1">
        <thead>
            <tr>
            	<th><?php echo lang('no'); ?></th>
            	<th><?php echo lang('product_code'); ?></th>
                <th><?php echo lang('product_name'); ?></th>
                <th><?php echo lang('jumlah_alokasi'); ?></th>
                <th><?php echo lang('rasio_mesin'); ?></th>
                <th><?php echo lang('alokasi'); ?></th>
                <th><?php echo lang('qty_production'); ?></th>
                <th><?php echo lang('alokasi_produk'); ?></th>
            </tr>
        </thead>
        <tbody>
        	<?php $no=0;
            $total_ratio = 0;
            $total_alokasi = 0;
            $total_produksi = 0;
            ?>
            <?php foreach($new_allocated as $d) { ?>
            <?php 
                $no++;
                $total_ratio += $d->rasio_mesin;
                $total_alokasi += $d->nilai_akun_current;
                $total_produksi += $d->qty_production;
            
            ?>	
            <tr>
            	<td><?php echo $no; ?></td>
            	<td><?php echo $d->product_code; ?></td>
                <td><?php echo $d->product_name; ?></td>
            	<td class="text-right"><?php echo number_format($d->unit_produksi_alokasi); ?></td>
				<td class="text-right"><?php echo number_format($d->rasio_mesin,6); ?></td>
                <td class="text-right"><?php echo number_format($d->nilai_akun_current); ?></td>
                <td class="text-right"><?php echo number_format($d->qty_production); ?></td>
                <td class="text-right"><?php echo ($d->qty_production > 0 ? number_format($d->nilai_akun_current / $d->qty_production) : 0); ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="3">Total</td>
                <td></td>
                <td class="text-right"><?php echo $total_ratio ;?></td>
                <td class="text-right"><?php echo number_format($total_alokasi) ;?></td>
                <td class="text-right"><?php echo number_format($total_produksi) ;?></td>
                <td></td>
            </tr>
        </tbody>
        </table> 
    </div>
</div>
</div>
