<?php
// Configure variables.
$lang = array(
	'Design' =>		__( 'Design', 'boldgrid-inspirations' ),
	'Content' =>	__( 'Content', 'boldgrid-inspirations' ),
	'CoinBudget' =>	__( 'Coin Budget', 'boldgrid-inspirations'),
	'Coins' =>		__( 'Coins', 'boldgrid-inspirations' ),
	'Pageset' =>	__( 'Pageset', 'boldgrid-inspirations' ),
	'Free' =>		__( 'Free', 'boldgrid-inspirations' ),
);

?>
<div class='wrap'>

	<div class='top-menu' >
		<a class='active' data-step='design' ><?php echo $lang['Design'] ?></a>
		<a class='disabled' data-step='content' ><?php echo $lang['Content']; ?></a>
	</div>

	<div style='clear:both;' ></div>

	<div id='screen-design'>

		<div class='left' id='categories' >
			<strong>Category Filter</strong>
		</div>

		<div class='theme-browser rendered right'>
			<div class='themes wp-clearfix'>
			</div>
		</div>
	</div>

	<div style='clear:both;'></div>

	<div id='screen-content' class='hidden' >

		<div class='left'>
			<div class='coin-filter' ><?php echo $lang['CoinBudget']; ?> <span class='info-icon'></span></div>
			<div class='coin-option' ><input type="radio" name="coin-budget" data-coin="20" checked > 0 - 20 <?php echo $lang['Coins']; ?></div>
			<div class='coin-option' ><input type="radio" name="coin-budget" data-coin="40"> 0 - 40 <?php echo $lang['Coins']; ?></div>
			<div class='coin-option' ><input type="radio" name="coin-budget" data-coin="60"> 0 - 60 <?php echo $lang['Coins']; ?></div>
			<div class='coin-option' ><input type="radio" name="coin-budget" data-coin="80"> 0 - 80 <?php echo $lang['Coins']; ?></div>
			<div class='coin-option' ><input type="radio" name="coin-budget" data-coin="0"> <?php echo $lang['Free']; ?></div>

			<div class='page-set-filter' ><?php echo $lang['Pageset']; ?></div>
			<div id='pageset-options'></div>
		</div>

		<div class='right'>
			<div>
				<div style='float:left;'>
					<span id='theme-title'></span>
					<span id='sub-category-title'></span>
				</div>
				<div style='float:right;' class='coin-bg-s' >
					<?php echo $lang['Coins']; ?>: <span id='build-cost'>..</span>
				</div>
			</div>

			<div style='clear:both;'></div>

			<div style='margin:10px 0px;'>
				<button class="button button-primary">Install</button>
			</div>

			<iframe id='theme-preview'></iframe>

			<div class="loading-wrapper boldgrid-loading hidden"></div>
		</div>
	</div>

</div>

<form method="post" name="post_deploy" id="post_deploy" style="display: none;" action="admin.php?page=boldgrid-inspirations" >
	<input type="hidden" name="task"                           id="task"                           value="deploy" >
	<input type="hidden" name="_wpnonce"                       id="_wpnonce"                       value="0d14469600" >
	<input type="text"   name="boldgrid_cat_id"                id="boldgrid_cat_id"                value="-1" >
	<input type="text"   name="boldgrid_sub_cat_id"            id="boldgrid_sub_cat_id"            value="-1" >
	<input type="text"   name="boldgrid_theme_id"              id="boldgrid_theme_id"              value="-1" >
	<input type="text"   name="boldgrid_page_set_id"           id="boldgrid_page_set_id"           value="-1" >
	<input type="text"   name="boldgrid_api_key_hash"          id="boldgrid_api_key_hash"          value="87" >
	<input type="text"   name="boldgrid_new_path"              id="boldgrid_new_path"              value="0310254001464869383" >
	<input type="text"   name="boldgrid_pde"                   id="boldgrid_pde"                   value="" >
	<input type="text"   name="boldgrid_language_id"           id="boldgrid_language_id"           value="" >
	<input type="text"   name="boldgrid_build_profile_id"      id="boldgrid_build_profile_id"      value="" >
	<input type="text"   name="coin_budget"                    id="coin_budget"                    value="20" >
	<input type="text"   name="boldgrid_theme_version_type"    id="boldgrid_theme_version_type"    value="active" >
	<input type="text"   name="boldgrid_page_set_version_type" id="boldgrid_page_set_version_type" value="active" >
	<input type="text"   name="deploy-type"                                                        value="" >
	<input type="text"   name="pages"                                                              value="" >
	<input type="text"   name="staging"                                                            value="" >
	<input type="hidden" name="_wp_http_referer"                                                   value="/single-site/wp-admin/admin.php?page=boldgrid-inspirations&amp;boldgrid-tab=install" >
	<input type="hidden"                                       id="wp_language"                    value="en-US" >
	<input type="submit"                                                                           value="Deploy" >
</form>