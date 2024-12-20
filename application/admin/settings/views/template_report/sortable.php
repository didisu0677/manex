<ol class="sortable">
	<?php foreach($template_report[0] as $m0) { ?>
	<li id="template_reportItem_<?php echo $m0->id; ?>" class="module" data-module="<?php echo $m0->account_code; ?>">
		<div class="sort-item">
			<span class="item-title"><?php echo $m0->account_name; ?></span>
		</div>
		<?php if(isset($template_report[$m0->id]) && count($template_report[$m0->id]) > 0) { ?>
		<ol>
			<?php foreach($template_report[$m0->id] as $m1) { ?>
			<li id="template_reportItem_<?php echo $m1->id; ?>" data-module="<?php echo $m0->account_code; ?>">
				<div class="sort-item">
					<span class="item-title"><?php echo $m1->account_name; ?></span>
				</div>
				<?php if(isset($template_report[$m1->id]) && count($template_report[$m1->id]) > 0) { ?>
				<ol>
					<?php foreach($template_report[$m1->id] as $m2) { ?>
					<li id="template_reportItem_<?php echo $m2->id; ?>" data-module="<?php echo $m0->account_code; ?>">
						<div class="sort-item">
							<span class="item-title"><?php echo $m2->account_name; ?></span>
						</div>
						<?php if(isset($template_report[$m2->id]) && count($template_report[$m2->id]) > 0) { ?>
						<ol>
							<?php foreach($template_report[$m2->id] as $m3) { ?>
							<li id="template_reportItem_<?php echo $m3->id; ?>" data-module="<?php echo $m0->account_code; ?>">
								<div class="sort-item">
									<span class="item-title"><?php echo $m3->account_name; ?></span>
								</div>
							</li>
							<?php } ?>
						</ol>
						<?php } ?>
					</li>
					<?php } ?>
				</ol>
				<?php } ?>
			</li>
			<?php } ?>
		</ol>
		<?php } ?>
	</li>
	<?php } ?>
</ol>