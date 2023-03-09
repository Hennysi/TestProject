<?php
    /** Template Name: Real Estate Page */

    get_header();
    $container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper">

    <div class="<?php echo esc_attr( $container ); ?>" id="content">

        <div class="row">

            <div class="col-md-12 content-area" id="primary">

                <main class="site-main" id="main" role="main">
                    <?php echo do_shortcode( '[ERE_filter]' ) ?>
                    <?php
                        $estate_args = array(
                            'post_type'      => 'real_estate',
                            'posts_per_page' => 5,
                        );
                        $estate = new WP_Query( $estate_args ); ?>
                    <?php if ( $estate->have_posts() ): ?>
                        <div class="row">
                            <div class="estate-wrapper row">
                                <?php while ( $estate->have_posts() ): $estate->the_post(); ?>
                                    <div class="col-md-4 estate-item">
                                        <?php if ( $image = get_field( 'image' ) ): ?>
                                            <div class="image">
                                                <a href="<?php echo get_the_permalink() ?>">
                                                    <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
                                                </a>
                                            </div>

                                            <?php if ( $house_name = get_field( 'house_name' ) ): ?>
                                                <div class="name"><?php echo $house_name ?></div>
                                            <?php else: ?>
                                                <div class="name"><?php the_title() ?></div>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php
                                $max_num_pages = $estate->max_num_pages;
                                if ( $max_num_pages > 1 ) {
                                    echo '<nav aria-label="...">';
                                    echo ' <ul class="pagination">';
                                    for ( $i = 1; $i <= $max_num_pages; $i ++ ) {
                                        $current = $i === 1 ? 'active' : null;

                                        echo '<li class="page-item">';
                                        echo '<a href="#" data-page="' . $i . '" class="page-link ' . $current . '">' . $i . '</a>';
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                    echo '</nav>';
                                }
                            ?>

                        </div>
                        <?php wp_reset_postdata(); ?>
                    <?php endif; ?>

                </main>

            </div><!-- #primary -->

        </div><!-- .row -->

    </div><!-- #content -->

</div>

<?php get_footer() ?>
