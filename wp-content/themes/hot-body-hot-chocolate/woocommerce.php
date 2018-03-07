<?php get_header(); ?>
<div class="breadcrumb-cover">
	<div class="container-fluid fashion-breadcrumb">
		<div class="container">
			<div class="fashion-bredcum-title">
				<div class="fashion-bredcum">
					<?php woocommerce_breadcrumb(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
	<div class="container-fluid">
		<div class="container blogs space">
			<div class="row fashion-blog-3 blog_gallery">
				<div class="col-md-8 col-sm-12">
					<div class="row fashion-blog-desc">
						<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="col-md-12 col-sm-12 fashion-blog-text blog-gallerys">
						<?php woocommerce_content(); ?>
						</div>
						</div>
					</div>
				</div>
       	    </div>
		</div>
	</div>
