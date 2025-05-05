<?php foreach($mst_account[0] as $m0) { ?>
	<tr>
		<td><b><?php echo $m0->parent_item . '-' .$m0->item_name; ?></b></td>
		<td><?php echo $m0->description; ?></td>
		<td class="text-center"><?php echo $m0->item_name; ?></td>
		<!-- <td class="text-center"><?php echo $m0->urutan; ?></td> -->
		<!-- <td class="text-center"><?php echo $m0->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td> -->
		<td class="button">
			<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
			<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
			<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
			<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
			<?php } ?>
		</td>
	</tr>
	<?php foreach($mst_account[$m0->id] as $m1) { ?>
		<tr>
			<td class="sub-1"><b><?php echo $m1->component_item . '-' .$m1->material_name; ?></b></td>
			<!-- <td><?php echo $m1->description; ?></td>
			<td class="text-center"><?php echo $m1->account_code; ?></td> -->
			<!-- <td class="text-center"><?php echo $m1->urutan; ?></td>
			<td class="text-center"><?php echo $m1->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td>
			<td class="button">
				<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
				<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
				<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
				<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
				<?php } ?>
			</td> -->
		</tr>
	<?php } ?>
<?php } ?>