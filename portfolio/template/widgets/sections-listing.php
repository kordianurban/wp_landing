<?php
/**
 * Sections listing
 * widget
 */

$sections = get_query_var('sections');

if ( $sections->have_posts() ):
    while ( $sections->have_posts() ): $sections->the_post(); ?>

        <section id="<?php echo $post->post_name; ?>">
            <div class="container">
                <?php the_content(); ?>
            </div>
        </section>

    <?php endwhile;
endif;