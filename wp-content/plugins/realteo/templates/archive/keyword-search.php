<?php 
if(isset($_GET['keyword_search'])) {
	$value = $_GET['keyword_search'];
} else {
	$value = '';
} ?>
<form action="" method="GET">
	<!-- Main Search Input -->
	<div class="main-search-input margin-bottom-35">
		<input type="text" class="ico-01" id="keyword_search" name="keyword_search" placeholder="<?php esc_html_e('Enter address e.g. street, city and state or zip','realteo') ?>" value="<?php if(isset($value)) { echo esc_attr($value);}?>"/>
		<button class="button"><?php esc_html_e('Search','realteo'); ?></button>
	</div>
</form>