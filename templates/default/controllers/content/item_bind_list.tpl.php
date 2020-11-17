<ul>
	<?php if ($total) { ?>
		<?php foreach($items as $item) { ?>
            <?php
                $url = $mode == 'childs' ?
                        href_to($child_ctype['name'], $item['slug'].'.html') :
                        href_to($ctype['name'], $item['slug'].'.html');
            ?>
			<li data-id="<?php echo $item['id']; ?>">
				<div class="title">
					<a href="<?php echo $url; ?>" target="_blank"><?php html($item['title']); ?></a>
				</div>
				<div class="details">
					<span class="user">
						<a href="<?php echo href_to_profile($item['user']); ?>"><?php html($item['user']['nickname']); ?></a>
					</span>
					<span class="date"><?php echo html_date_time($item['date_pub']); ?></span>
				</div>
                    <div class="add">
                        <input type="button" class="button" value="<?php if ($mode == 'unbind') { ?>X<?php } else { ?>+<?php } ?>">
                    </div>
			</li>
		<?php } ?>
	<?php } ?>
</ul>