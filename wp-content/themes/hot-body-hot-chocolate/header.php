<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" type="text/css" href="<?php bloginfo('template_url') ?>/style.css">

  <?php wp_head(); ?>
</head>

<body>
  <header class="fc-header" fc-header>
    <div class="fc-header__contents container">
      <h1 class="fc-header__logo"><a class="fc-header__logo-link" href="/"><span>Hot body hot chocolate</span></a></h1>

      <nav class="fc-header__menu fc-menu">
          <?php 
            wp_nav_menu( $args= array(
              'menu_class' => 'fc-menu__items',
              'theme_location' => 'primary'
            ));
          ?>
      </nav>
      <div class="fc-header__secondary-menu">
        <button class="fc-header__menu-toggle" type="button">
          <span class="accessible-text">Menu</span>
          <span class="visual-aid"></span>
        </button>
      </div>
    </div>
    <div class="fc-header--secondary-menu">
      <ul class="fc-header--secondary-menu__items">
      <li class="fc-header--secondary-menu__item">
         FREE WORLDWIDE DELIVERY Wherever You Are In The World.
      </li>
      <li class="fc-header--secondary-menu__item active">
       20% OFF YOUR FIRST ORDER Use Code NEWME. Offer Ends MIDNIGHT!
      </li>
      <li class="fc-header--secondary-menu__item">
         TRUSTED BY OVER 250,000 DIETERS Models, Personal Trainers.
      </li>
      <li class="fc-header--secondary-menu__item">
         92% NOTICED VISIBLE RESULTS* Fast And Long-Lasting Results.
      </li>
      </ul>
    </div>
    <div class="offer-header">
       ONLY 18: 31: 19 REMAINING TO GET 20% OFF YOUR FIRST ORDER - USE CODE: NEWME
    </div>
  </header>