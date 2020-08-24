<?php 
if(isset($data)) :
	$in_rows	 	= (isset($data->in_rows)) ? $data->in_rows : '' ;
endif; ?>
<?php if(!empty($in_rows)): ?>
	</div>
<?php endif; ?>
</div>
<?php if($data->max_num_pages > 1) : ?>
<div class="pagination-container margin-top-20 margin-bottom-40">
	<nav class="pagination">
		<?php realteo_pagination(  $data->max_num_pages ); ?>
	</nav>
</div>
<?php endif; ?>