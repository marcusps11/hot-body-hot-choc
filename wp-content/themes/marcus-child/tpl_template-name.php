<?php
// Template Name: Home Page Template
get_header();

?>

<div class="hb-home">
<div class="hb-home__hero-section">

<div class="hb-home__hero-section-content">

</div>
<div class="hb-home__hero-section-image">

</div>
</div>
<div id="woo-commerce"></div>
</div>
<?php query_posts( array(
    'category_name' => 'news',
    'posts_per_page' => 3,
  )); ?>
</div>
<div class="product-home__inspiration">
<?php if( have_posts() ): while ( have_posts() ) : the_post(); ?>

   <?php the_excerpt(); ?>
   <?php endwhile; ?>

<?php else : ?>

   <p><?php __('No News'); ?></p>

<?php endif; ?>
</div>

<div class="product-home__container">
  <h2>28 days to get a hot body</h2>
  <?php
  if ( storefront_is_woocommerce_activated() ) {
    echo storefront_do_shortcode( 'best_selling_products', array(
      'per_page' => 1,
      'columns'  => 1,
      ) );

      echo '</section>';
    }
    ?>
  <?php query_posts( array(
    'category_name' => 'news',
    'posts_per_page' => 3,
  )); ?>
</div>
<div class="testimonials">
<?php
get_template_part( 'testimonials', 'tpl_template-name' );

?>
</div>
<div class="hb-why">
<?php
get_template_part( 'why', 'tpl_template-name' );

?>
</div>
<div class="social">
<?php query_posts( array(
    'category_name' => 'instagram',
    'posts_per_page' => 3,
  )); ?>
</div>
<div class="product-home__inspiration">
<?php if( have_posts() ): while ( have_posts() ) : the_post(); ?>
<?php echo do_shortcode('[instagram-feed]'); ?>

   <?php the_excerpt(); ?>
   <?php endwhile; ?>

<?php else : ?>

   <p><?php __('No News'); ?></p>

<?php endif; ?>
</div>
<?php
get_footer();
