<?php
    get_header();
    $container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper">

    <div class="<?php echo esc_attr( $container ); ?>" id="content">

        <div class="row">

            <div class="col-md-12 content-area" id="primary">

                <main class="site-main" id="main" role="main">

                    <?php if ( $image = get_field( 'image' ) ): ?>
                        <div class="image">
                            <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $house_name = get_field( 'house_name' ) ): ?>
                        <h1 class="name"><?php echo $house_name ?></h1>
                    <?php else: ?>
                        <h1 class="name"><?php echo get_the_title() ?></h1>
                    <?php endif; ?>

                    <div class="estate-info">
                        <div class="estate-info-heading">
                            <span>Місцезнаходження</span>
                            <span>Кільскість поверхів</span>
                            <span>Тип будівлі</span>
                            <span>Екологічність</span>
                            <span>Приміщення</span>
                        </div>
                        <div class="estate-info-main">
                            <?php if ( $location_coordinates = get_field( 'location_coordinates' ) ): ?>
                                <span class="location"><?php echo $location_coordinates ?></span>
                            <?php endif; ?>

                            <?php if ( $number_of_floors = get_field( 'number_of_floors' ) ): ?>
                                <span class="floors"><?php echo $number_of_floors ?></span>
                            <?php endif; ?>

                            <?php if ( $building_type = get_field( 'building_type' ) ): ?>
                                <span class="type"><?php echo $building_type ?></span>
                            <?php endif; ?>

                            <?php if ( $eco_friendliness = get_field( 'eco-friendliness' ) ): ?>
                                <span class="eco"><?php echo $eco_friendliness ?></span>
                            <?php endif; ?>

                            <?php if ( $premises = get_field( 'premises' ) ): ?>
                                <div class="premises">
                                    <?php if ( $area = $premises['area'] ): ?>
                                        <p class="area">Площа: <?php echo $area ?></p>
                                    <?php endif; ?>

                                    <?php if ( $number_of_rooms = $premises['number_of_rooms'] ): ?>
                                        <p class="rooms">Кіл. кімнат: <?php echo $number_of_rooms ?></p>
                                    <?php endif; ?>

                                    <?php if ( $balcony = $premises['balcony'] ): ?>
                                        <p class="balcony">Балкон: <?php echo $balcony ?></p>
                                    <?php endif; ?>

                                    <?php if ( $bathroom = $premises['bathroom'] ): ?>
                                        <p class="bathroom">Санвузол: <?php echo $bathroom ?></p>
                                    <?php endif; ?>

                                    <?php if ( $image = $premises['image'] ): ?>
                                        <div class="image">
                                            <?php echo wp_get_attachment_image( $image['ID'], 'large' ); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </main>

            </div><!-- #primary -->

        </div><!-- .row -->

    </div><!-- #content -->

</div>

<?php get_footer() ?>
